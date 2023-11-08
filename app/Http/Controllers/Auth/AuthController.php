<?php

namespace App\Http\Controllers\Auth;

use Auth;
use App\User;
use Carbon\Carbon;
use App\Models\Campus;
use App\Models\Career;
use App\Models\School;
use App\Models\Student;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Notifications\Welcome;
use App\Models\EnrolledStudent;
use Illuminate\Validation\Rule;
use App\Notifications\NewProspect;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Notifications\AccountPending;
use App\Notifications\SignupActivate;
use App\Imports\EnrolledStudentsImport;

class AuthController extends Controller
{
    /**
     * Create user
     *
     * @return [string] message
     */
    public function signup(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'lastname' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'phone' => 'required|string',
            'password' => 'required|string|confirmed',
            'birthdate' => 'required|date',
        ]);

        $user = User::create([
            'name' => $request->name,
            'lastname' => $request->lastname,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => bcrypt($request->password),
            'birthdate' => $request->birthdate,
            'activation_token' => Str::random(60),
        ]);

        $enrolledStudent = EnrolledStudent::where([
            'name' => $request->name,
            'lastname' => $request->lastname,
            'email' => $request->email,
            'birthdate' => $request->birthdate,
        ]);

        if ($enrolledStudent->exists()) {
            $this->createStudent($enrolledStudent->first(), $user->id);

            Role::firstOrCreate(
                ['name' => 'estudiante'],
                ['guard_name' => 'api']
            );

            $user->assignRole('estudiante');

            $user->update(['status' => 'active']);

            $user->notify(new Welcome($user));
        } else {
            Role::firstOrCreate(
                ['name' => 'prospecto'],
                ['guard_name' => 'api']
            );

            $user->assignRole('prospecto');

            // $user->update(['status' => 'pending']);
            $user->update(['status' => 'active']);

            // $user->notify(new AccountPending($user));
            $user->notify(new Welcome($user));

            // $administrators = User::all()->filter(function ($user) {
            //     return $user->hasRole('administrator');
            // });

            // foreach ($administrators as $key => $user) {
            //     $user->notify(new NewProspect($user));
            // }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Successfully created user!'
        ], 201);
    }

    /**
     * Upgrade prospect user to student
     *
     * @return [string] message
     */
    public function upgrade(Request $request)
    {
        $request->validate([
            'user' => 'required',
            'name' => 'required|string',
            'lastname' => 'required|string',
            'email' => 'required|string|email',
            'birthdate' => 'required|date',
        ]);

        $user = User::find($request->user);

        $enrolledStudent = EnrolledStudent::where([
            'name' => $request->name,
            'lastname' => $request->lastname,
            'email' => $request->email,
            'birthdate' => $request->birthdate,
        ]);

        if ($enrolledStudent->exists()) {
            $this->createStudent($enrolledStudent->first(), $user->id);

            Role::firstOrCreate(
                ['name' => 'estudiante'],
                ['guard_name' => 'api']
            );

            $user->assignRole('estudiante');

            $user->update(['status' => 'active']);

            $user->notify(new Welcome($user));

            $userData = User::with(['student.events'])->find($user->id);

            return response()->json([
                'status' => 'success',
                'message' => 'Successfully upgraded user!',
                'user' => $userData
            ], 201);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Data was not found in our student database'
        ], 204);
    }

    /**
     * Login user and create token
     *
     * @param  [string] email
     * @param  [string] password
     * @param  [boolean] remember_me
     * @return [string] access_token
     * @return [string] token_type
     * @return [string] expires_at
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
            'remember_me' => 'boolean'
        ]);

        $credentials = request(['email', 'password']);
        $credentials['deleted_at'] = null;

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        }

        $user = $request->user();

        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;

        if ($request->remember_me) {
            $token->expires_at = Carbon::now()->addWeeks(1);
        }

        $token->save();

        $userRoles = User::with(['student.events:id'])->find($user->id);

        return response()->json([
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse(
                $tokenResult->token->expires_at
            )->toDateTimeString(),
            'user' => $userRoles
        ]);
    }

    /**
     * Logout user (Revoke the token)
     *
     * @return [string] message
     */
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();

        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }

    /**
     * Get the authenticated User
     *
     * @return [json] user object
     */
    public function user(Request $request)
    {
        $user = $request->user()->load(['student.events:id']);

        return response()->json($user);
    }

    /**
     * Update the user profile.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function updateProfile(Request $request, User $user)
    {
        $validatedData = $request->validate([
            'name' => 'sometimes|string',
            'lastname' => 'sometimes|string',
            'email' => [
                Rule::unique('users', 'email')->where(function ($query) use ($user) {
                    return $query->where('id', '!=', $user->id);
                }),
                'sometimes',
                'string',
                'email'
            ],
            'password' => 'sometimes|string|confirmed',
            'status' => 'sometimes|string',
            'zipcode' => 'sometimes|string',
            'address' => 'sometimes|string',
            'phone' => 'sometimes|string',
            'picture' => 'sometimes|file',
            'birthdate' => 'sometimes|date',
        ]);

        if ($request->has('password')) {
            $validatedData['password'] = bcrypt($request->password);
        }

        if ($request->hasFile('picture')) {
            $fileName = strtoupper(time().'-'.Str::random(4)).'.'.$request->file('picture')->getClientOriginalExtension();
            $filePath = $request->file('picture')->storeAs('uploads/users', $fileName, 'public');
            $validatedData['picture'] = $filePath;
        }

        $user->update($validatedData);

        $user->load(['student.events:id']);

        return response()->json([
            'message' => 'Successfully updated User Profile!',
            'user' => $user
        ], 200);
    }

    /**
     * Uploads users by excel file.
     *
     * @param \Illuminate\Http\Request $request The request
     */
    public function uploadUsers(Request $request)
    {
        $request->validate([
            'excel' => 'required|file|mimes:xls,xlsx|max:4096'
        ]);

        Excel::import(new EnrolledStudentsImport, request()->file('excel'));

        return response()->json([
            'message' => 'Successfully Import to EnrolledStudents'
        ]);
    }

    /**
     * Activate user by token
     *
     * @param <string> $token
     *
     * @return \Illuminate\Http\Response
     */
    public function signupActivate($token)
    {
        $user = User::where('activation_token', $token)->first();

        if (!$user->exists()) {
            return response()->json(['message' => 'El token de activación es inválido'], 400);
        }

        $user->update([
            'active' => true,
            'email_verified_at' => Carbon::now(),
            'activation_token' => null
        ]);

        return response()->json([
            'message' => 'Successfully activate the user'
        ]);
    }

    /**
     * Creates a student.
     *
     * @param <type> $enrolledStudent The enrolled student
     * @param <type> $user_id The user identifier
     */
    protected function createStudent($enrolledStudent, $user_id)
    {
        $campus = Campus::firstOrCreate(['name' => $enrolledStudent->campus]);
        $school = School::firstOrCreate(['name' => $enrolledStudent->school], ['campus_id' => $campus->id]);
        $career = Career::firstOrCreate(['name' => $enrolledStudent->career], ['school_id' => $school->id]);

        Student::create([
            'inter_id' => $enrolledStudent->student_id,
            'user_id' => $user_id,
            'career_id' => $career->id,
            'semester' => $enrolledStudent->semester,
            'total_credits' => 0,
            'date_enrollment' => date('Y-m-d'),
            'status' => 'active'
        ]);
    }
}
