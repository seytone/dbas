<?php

namespace App\Http\Controllers\Admin;

use App\User;
use App\Models\Seller;
use Spatie\Permission\Models\Role;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUsersRequest;
use App\Http\Requests\Admin\UpdateUsersRequest;

class UsersController extends Controller
{
    /**
     * Display a listing of User.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!Gate::allows('manage_security')) {
            return abort(401);
        }

        $users = User::where('email', '!=', 'sadmin@distribuidorabit.com')->get();

        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating new User.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!Gate::allows('manage_security')) {
            return abort(401);
        }

        $roles = Role::get()->pluck('name');

        return view('admin.users.create', compact('roles'));
    }

    /**
     * Store a newly created User in storage.
     *
     * @param  \App\Http\Requests\StoreUsersRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUsersRequest $request)
    {
        if (!Gate::allows('manage_security')) {
            return abort(401);
        }

		$validatedData = $request->validate([
            'name' => 'required|string|max:50',
            'lastname' => 'required|string|max:50',
            'email' => 'required|email|max:50|unique:users,deleted_at,NULL',
            'password' => 'required|string|min:8|confirmed',
			'roles' => 'required|array',
        ]);

        $user = User::create($validatedData);

		$roles = $request->input('roles') ? $request->input('roles') : [];
		$user->assignRole($roles);

		// dd($request->all(), $user, $user->hasRole('Vendedor'));

		if ($user->hasRole('Vendedor')) {
			Seller::create([
				'user_id' => $user->id,
				'commission_1' => $request->input('commission_1') ?? 1,
				'commission_2' => $request->input('commission_2') ?? 1,
				'commission_3' => $request->input('commission_3') ?? 1,
				'commission_4' => $request->input('commission_4') ?? 50,
			]);
		}

        return redirect()->route('admin.users.index');
    }

    /**
     * Show the form for editing User.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        if (!Gate::allows('manage_security')) {
            return abort(401);
        }

        $roles = Role::get()->pluck('name');

        return view('admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Update User in storage.
     *
     * @param  \App\Http\Requests\UpdateUsersRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUsersRequest $request, User $user)
    {
        if (!Gate::allows('manage_security')) {
            return abort(401);
        }

		if ($request->input('password') == '')
			$request->request->remove('password');

		$validatedData = $request->validate([
            'name' => 'required|string|max:50',
            'lastname' => 'required|string|max:50',
            'password' => 'sometimes|required|string|min:8|confirmed',
			'roles' => 'required|array',
        ]);

        $user->update($validatedData);

        $roles = $request->input('roles') ? $request->input('roles') : [];
        $user->syncRoles($roles);

		if ($user->hasRole('Vendedor'))
		{
			if ($user->seller) {
				$user->seller->update([
					'commission_1' => $request->input('commission_1') ?? 1,
					'commission_2' => $request->input('commission_2') ?? 1,
					'commission_3' => $request->input('commission_3') ?? 1,
					'commission_4' => $request->input('commission_4') ?? 50,
				]);
			} else {
				Seller::create([
					'user_id' => $user->id,
					'commission_1' => $request->input('commission_1') ?? 1,
					'commission_2' => $request->input('commission_2') ?? 1,
					'commission_3' => $request->input('commission_3') ?? 1,
					'commission_4' => $request->input('commission_4') ?? 50,
				]);
			}
		}

        return redirect()->route('admin.users.index');
    }

    public function show(User $user)
    {
        if (!Gate::allows('manage_security')) {
            return abort(401);
        }

        $user->load('roles');

        return view('admin.users.show', compact('user'));
    }

    /**
     * Remove User from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        if (!Gate::allows('manage_security')) {
            return abort(401);
        }

        $user->delete();

        return redirect()->route('admin.users.index');
    }

    /**
     * Delete all selected User at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        if (!Gate::allows('manage_security')) {
            return abort(401);
        }

        User::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
