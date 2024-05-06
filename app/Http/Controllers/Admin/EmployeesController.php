<?php

namespace App\Http\Controllers\Admin;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;

class EmployeesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $employees = Employee::all();

        return view('admin.employees.index', compact('employees'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.employees.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'number' => 'required|numeric|max:100',
            'name' => 'required|string|max:30',
            'lastname' => 'required|string|max:30',
            'pin' => 'nullable|sometimes|string|max:30',
            'email' => 'nullable|sometimes|email|max:100',
            'phone' => 'nullable|sometimes|string|max:30',
            'department' => 'nullable|sometimes|string|max:30',
            'position' => 'nullable|sometimes|string|max:30',
            'salary' => 'required|numeric|between:0,999999.99'
        ]);

        Employee::create($validatedData);

        return redirect()->route('admin.employees.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function show(Employee $employee)
    {
        return view('admin.employees.show', compact('employee'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function edit(Employee $employee)
    {
        return view('admin.employees.edit', compact('employee'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Employee $employee)
    {
        $validatedData = $request->validate([
            'number' => 'required|numeric|max:100',
            'name' => 'required|string|max:30',
            'lastname' => 'required|string|max:30',
            'pin' => 'nullable|sometimes|string|max:30',
            'email' => 'nullable|sometimes|email|max:100',
            'phone' => 'nullable|sometimes|string|max:30',
            'department' => 'nullable|sometimes|string|max:30',
            'position' => 'nullable|sometimes|string|max:30',
            'salary' => 'required|numeric|between:0,999999.99'
        ]);

        $employee->update($validatedData);

        return redirect()->route('admin.employees.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function destroy(Employee $employee)
    {
        $employee->delete();

        return redirect()->route('admin.employees.index');
    }

    /**
     * Delete all selected Employee at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        Employee::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }

    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function list(Request $request)
    {
        $employees = $request->limit ? Employee::orderBy('created_at', 'desc')->skip(0)->take($request->limit)->get() : Employee::paginate(20);

        return response()->json(
            $employees
        );
    }

    /**
        * Display the specified resource.
        *
        * @param  \App\Models\Employee  $employee
        * @return \Illuminate\Http\Response
        */
    public function details(Employee $employee)
    {
        return response()->json($employee);
    }
}
