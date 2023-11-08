@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        Gestión de Ventas
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
                            {{ $sales->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Categoría
                        </th>
                        <td>
                            {{ $sales->category->name }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Titulo
                        </th>
                        <td>
                            {{ $sales->title }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Imagen
                        </th>
                        <td>
                            @if($sales->image)
                                <a href="{{ asset('storage/' . $sales->image) }}" target="_blank" class="btn btn-primary btn-sm">VER</a>
                            @else
                                No tiene
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Resumen
                        </th>
                        <td>
                            {{ $sales->resume }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Contenido
                        </th>
                        <td>
                            {{ $sales->content }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Enlace
                        </th>
                        <td>
                            {{ $sales->author }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <a style="margin-top:20px;" class="btn btn-dark" href="{{ url()->previous() }}">
                Regresar
            </a>
        </div>

    </div>
</div>
@endsection