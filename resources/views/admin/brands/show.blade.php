@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        Detalle de Marca
    </div>

    <div class="card-body">
        <div class="mb-2">
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            Titulo
                        </th>
                        <td>
                            {{ $brand->title }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Descripción
                        </th>
                        <td>
                            {{ $brand->description }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="text-center text-md-right mt-4">
				<hr>
				<a class="btn btn-dark" href="{{ url()->previous() }}">
                    Regresar
                </a>
				<a class="btn btn-warning" href="{{ route('admin.brands.edit', $brand->id) }}">
					Editar
				</a>
			</div>
        </div>

    </div>
</div>
@endsection