@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        Gestión de Ventas
    </div>

    <div class="card-body">
        <form action="{{ route("admin.sales.update", [$sales->id]) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="form-group {{ $errors->has('category_id') ? 'has-error' : '' }}">
                <label for="category_id">Categoría&nbsp;<b class="text-danger">*</b></label>
                <select name="category_id" class="custom-select" required>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ $sales->category_id == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
                @if($errors->has('category_id'))
                <em class="invalid-feedback">
                    {{ $errors->first('category_id') }}
                </em>
                @endif
            </div>
            <div class="form-group {{ $errors->has('title') ? 'has-error' : '' }}">
                <label for="title">Titulo&nbsp;<b class="text-danger">*</b></label>
                <input type="text" id="title" name="title" class="form-control title" value="{{ old('title', isset($sales) ? $sales->title : '') }}"  maxlength="50" required>
                @if($errors->has('title'))
                <em class="invalid-feedback">
                    {{ $errors->first('title') }}
                </em>
                @endif
            </div>
            <div class="form-group {{ $errors->has('image') ? 'has-error' : '' }}">
                <label for="image">Imagen&nbsp;<b class="text-danger">*</b></label>
                <div class="row">
                    <div class="col-11">
                        <div class="input-group">
                            <div class="custom-file">
                                <input type="file" name="image" class="custom-file-input" id="image" accept="image/*">
                                <label class="custom-file-label" for="image">Seleccionar</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-1">
                        @isset($sales->image)
                            <a href="{{ asset('storage/' . $sales->image) }}" target="_blank" class="btn btn-primary w-100">VER</a>
                        @endisset
                    </div>
                </div>
                @if($errors->has('image'))
                <em class="invalid-feedback">
                    {{ $errors->first('image') }}
                </em>
                @endif
            </div>
            <div class="form-group {{ $errors->has('resume') ? 'has-error' : '' }}">
                <label for="resume">Resumen&nbsp;<b class="text-danger">*</b></label>
                <textarea id="resume" name="resume" rows="1" class="form-control resume" maxlength="100"  maxlength="140" required>{{ old('resume', isset($sales) ? $sales->resume : '') }}</textarea>
                @if($errors->has('resume'))
                <em class="invalid-feedback">
                    {{ $errors->first('resume') }}
                </em>
                @endif
            </div>
            <div class="form-group {{ $errors->has('content') ? 'has-error' : '' }}">
                <label for="content">Contenido&nbsp;<b class="text-danger">*</b></label>
                <textarea id="editor" name="content" rows="10" class="form-control" required>{{ old('content', isset($sales) ? $sales->content : '') }}</textarea>
                @if($errors->has('content'))
                <em class="invalid-feedback">
                    {{ $errors->first('content') }}
                </em>
                @endif
            </div>
            <div class="form-group {{ $errors->has('author') ? 'has-error' : '' }}">
                <label for="author">Enlace&nbsp;<b class="text-danger">*</b></label>
                <input type="text" id="author" name="author" class="form-control" value="{{ old('author', isset($sales) ? $sales->author : '') }}" required>
                @if($errors->has('author'))
                <em class="invalid-feedback">
                    {{ $errors->first('author') }}
                </em>
                @endif
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

@section('scripts')
    <script>
        $(function() {
            $('.title').maxlength({
                threshold: 50
            });
            $('.resume').maxlength({
                threshold: 140
            });
        });
    </script>
@endsection