@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        Detalle de Empleado
    </div>

    <div class="card-body">
        <div class="mb-2">
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            ID
                        </th>
                        <td>
                            {{ $employee->number }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Empleado
                        </th>
                        <td>
                            {{ $employee->name . ' ' . $employee->lastname }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Cédula
                        </th>
                        <td>
                            {{ $employee->pin }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Email
                        </th>
                        <td>
                            {{ $employee->email }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Teléfono
                        </th>
                        <td>
                            {{ $employee->phone }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Departamento
                        </th>
                        <td>
                            {{ $employee->department }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Cargo
                        </th>
                        <td>
                            {{ $employee->position }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Salario
                        </th>
                        <td>
                            {{ $employee->salary }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="text-center text-md-right mt-4">
				<hr>
				<a class="btn btn-dark" href="{{ url()->previous() }}">
                    Regresar
                </a>
				@can('manage_employees')
					<a class="btn btn-warning" href="{{ route('admin.employees.edit', $employee->id) }}">
						Editar
					</a>
				@endcan
			</div>
        </div>

    </div>
</div>
@endsection