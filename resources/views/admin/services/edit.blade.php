@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        Edición de Servicio
    </div>

    <div class="card-body">
        <form action="{{ route("admin.services.update", [$service->id]) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
			<div class="row">
				<div class="col-sm-3">
					<div class="form-group {{ $errors->has('code') ? 'has-error' : '' }}">
						<label for="code">Código&nbsp;<b class="text-danger">*</b></label>
						<input type="text" id="code" name="code" class="form-control" value="{{ old('code', isset($service) ? $service->code : '') }}" required>
						@if ($errors->has('code'))
							<em class="invalid-feedback">
								{{ $errors->first('code') }}
							</em>
						@endif
					</div>
				</div>
				<div class="col-sm-6">
					<div class="form-group {{ $errors->has('title') ? 'has-error' : '' }}">
						<label for="title">Titulo&nbsp;<b class="text-danger">*</b></label>
						<input type="text" id="title" name="title" class="form-control" value="{{ old('title', isset($service) ? $service->title : '') }}" required>
						@if ($errors->has('title'))
							<em class="invalid-feedback">
								{{ $errors->first('title') }}
							</em>
						@endif
					</div>
				</div>
				<div class="col-sm-3">
					<div class="form-group {{ $errors->has('price') ? 'has-error' : '' }}">
						<label for="price">Precio&nbsp;<b class="text-danger">*</b></label>
						<input type="number" id="price" name="price" class="form-control" value="{{ old('price', isset($service) ? $service->price : 0) }}" min="0" required>
						@if ($errors->has('price'))
							<em class="invalid-feedback">
								{{ $errors->first('price') }}
							</em>
						@endif
					</div>
				</div>
				<div class="col-sm-12">
					<div class="form-group {{ $errors->has('description') ? 'has-error' : '' }}">
						<label for="description">Descripción&nbsp;<b class="text-danger">*</b></label>
						<textarea id="description" name="description" rows="1" class="form-control description" maxlength="140">{{ old('description', isset($service) ? $service->description : '') }}</textarea>
						@if ($errors->has('description'))
							<em class="invalid-feedback">
								{{ $errors->first('description') }}
							</em>
						@endif
					</div>
				</div>
			</div>

			<div class="text-center text-md-right mt-4">
				<hr>
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