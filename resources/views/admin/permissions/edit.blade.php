@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        Editar {{ trans('cruds.permission.title_singular') }}
    </div>

    <div class="card-body">
        <form action="{{ route("admin.permissions.update", [$permission->id]) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                <label for="name">{{ trans('cruds.permission.fields.title') }}*</label>
                <input type="text" id="name" name="name" class="form-control" value="{{ old('name', isset($permission) ? $permission->name : '') }}" required>
                @if($errors->has('name'))
                    <em class="invalid-feedback">
                        {{ $errors->first('name') }}
                    </em>
                @endif
                <p class="helper-block">
                    {{ trans('cruds.permission.fields.title_helper') }}
                </p>
            </div>
            <div class="mt-3">
                <a class="btn btn-dark" href="{{ url()->previous() }}">
                    Regresar
                </a>
                <input class="btn btn-success" type="submit" value="Guardar">
            </div>
        </form>


    </div>
</div>
@endsection