@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        Detalle de Producto
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
                            {{ $product->code }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Tipo
                        </th>
                        <td>
                            {{ ucwords($product->type) }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Categoría
                        </th>
                        <td>
                            {{ $product->category->title }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Marca
                        </th>
                        <td>
                            {{ $product->brand->title }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Producto
                        </th>
                        <td>
                            {{ $product->title }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Descripción
                        </th>
                        <td>
                            {{ $product->description }}
                        </td>
                    </tr>
					<tr>
                        <th>
                            Cost
                        </th>
                        <td>
                            {{ $product->cost }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Precio
                        </th>
                        <td>
                            {{ $product->price }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="text-center text-md-right mt-4">
				<hr>
				<a class="btn btn-dark" href="{{ url()->previous() }}">
                    Regresar
                </a>
				<a class="btn btn-warning" href="{{ route('admin.products.edit', $product->id) }}">
					Editar
				</a>
			</div>
        </div>

    </div>
</div>
@endsection