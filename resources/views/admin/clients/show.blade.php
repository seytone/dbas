@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        Detalle de Cliente
    </div>

    <div class="card-body">
        <div class="mb-2">
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            Código
                        </th>
                        <td>
                            {{ $client->code }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Razón Social
                        </th>
                        <td>
                            {{ $client->title }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Identificación
                        </th>
                        <td>
                            {{ $client->document }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Email
                        </th>
                        <td>
                            {{ $client->email }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Teléfono
                        </th>
                        <td>
                            {{ $client->phone }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Dirección
                        </th>
                        <td>
                            {{ $client->address }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="text-center text-md-right mt-4">
				<hr>
				<a class="btn btn-dark" href="{{ url()->previous() }}">
                    Regresar
                </a>
				<a class="btn btn-warning" href="{{ route('admin.clients.edit', $client->id) }}">
					Editar
				</a>
			</div>
        </div>

    </div>
</div>
@endsection