@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        Detalle de Servicio
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
                            {{ $service->code }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Servicio
                        </th>
                        <td>
                            {{ $service->title }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Descripción
                        </th>
                        <td>
                            {{ $service->description }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Precio
                        </th>
                        <td>
                            {{ $service->price }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="text-center text-md-right mt-4">
				<hr>
				<a class="btn btn-dark" href="{{ url()->previous() }}">
                    Regresar
                </a>
				<a class="btn btn-warning" href="{{ route('admin.services.edit', $service->id) }}">
					Editar
				</a>
			</div>
        </div>

    </div>
</div>
@endsection