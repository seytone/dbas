@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        Edición de Producto
    </div>

    <div class="card-body">
        <form action="{{ route("admin.products.update", [$product->id]) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
			<div class="row">
				<div class="col-sm-2">
					<div class="form-group {{ $errors->has('code') ? 'has-error' : '' }}">
						<label for="code">Código&nbsp;<b class="text-danger">*</b></label>
						<input type="text" id="code" name="code" class="form-control" value="{{ old('code', isset($product) ? $product->code : '') }}" required>
						@if ($errors->has('code'))
							<em class="invalid-feedback">
								{{ $errors->first('code') }}
							</em>
						@endif
					</div>
				</div>
				<div class="col-sm-2">
					<div class="form-group {{ $errors->has('group') ? 'has-error' : '' }}">
						<label for="group">Grupo&nbsp;<b class="text-danger">*</b></label>
						<select name="group" class="custom-select" required>
							<option value="">Seleccione</option>
							<option value="perpetual" {{ $product->group == 'perpetual' ? 'selected' : '' }}>Licencias Perpetuas</option>
							<option value="annual" {{ $product->group == 'annual' ? 'selected' : '' }}>Suscripciones Anuales</option>
							<option value="hardware" {{ $product->group == 'hardware' ? 'selected' : '' }}>Hardware y Otros</option>
						</select>
						@if ($errors->has('group'))
							<em class="invalid-feedback">
								{{ $errors->first('group') }}
							</em>
						@endif
					</div>
				</div>
				<div class="col-sm-2">
					<div class="form-group {{ $errors->has('type') ? 'has-error' : '' }}">
						<label for="type">Tipo&nbsp;<b class="text-danger">*</b></label>
						<select name="type" class="custom-select" required>
							<option value="">Seleccione</option>
							<option value="hardware" {{ $product->type == 'hardware' ? 'selected' : '' }}>Hardware</option>
							<option value="software" {{ $product->type == 'software' ? 'selected' : '' }}>Software</option>
						</select>
						@if ($errors->has('type'))
							<em class="invalid-feedback">
								{{ $errors->first('type') }}
							</em>
						@endif
					</div>
				</div>
				<div class="col-sm-3">
					<div class="form-group {{ $errors->has('category_id') ? 'has-error' : '' }}">
						<label for="category_id">Categoría&nbsp;<b class="text-danger">*</b></label>
						<select name="category_id" class="selectize-create" required>
							<option value="">Seleccione</option>
							@foreach ($categories as $category)
								<option value="{{ $category->id }}" {{ $product->category_id == $category->id ? 'selected' : '' }}>{{ $category->title }}</option>
							@endforeach
						</select>
						@if ($errors->has('category_id'))
							<em class="invalid-feedback">
								{{ $errors->first('category_id') }}
							</em>
						@endif
					</div>
				</div>
				<div class="col-sm-3">
					<div class="form-group {{ $errors->has('brand_id') ? 'has-error' : '' }}">
						<label for="brand_id">Marca&nbsp;<b class="text-danger">*</b></label>
						<select name="brand_id" class="selectize-create" required>
							<option value="">Seleccione</option>
							@foreach ($brands as $brand)
								<option value="{{ $brand->id }}" {{ $product->brand_id == $brand->id ? 'selected' : '' }}>{{ $brand->title }}</option>
							@endforeach
						</select>
						@if ($errors->has('brand_id'))
							<em class="invalid-feedback">
								{{ $errors->first('brand_id') }}
							</em>
						@endif
					</div>
				</div>
				<div class="col-sm-12">
					<div class="form-group {{ $errors->has('title') ? 'has-error' : '' }}">
						<label for="title">Titulo&nbsp;<b class="text-danger">*</b></label>
						<input type="text" id="title" name="title" class="form-control" value="{{ old('title', isset($product) ? $product->title : '') }}" required>
						@if ($errors->has('title'))
							<em class="invalid-feedback">
								{{ $errors->first('title') }}
							</em>
						@endif
					</div>
				</div>
				<div class="col-sm-12">
					<div class="form-group {{ $errors->has('description') ? 'has-error' : '' }}">
						<label for="description">Descripción&nbsp;<b class="text-danger">*</b></label>
						<textarea id="description" name="description" rows="1" class="form-control description" maxlength="140">{{ old('description', isset($product) ? $product->description : '') }}</textarea>
						@if ($errors->has('description'))
							<em class="invalid-feedback">
								{{ $errors->first('description') }}
							</em>
						@endif
					</div>
				</div>
				<div class="col-sm-4">
					<div class="form-group {{ $errors->has('cost') ? 'has-error' : '' }}">
						<label for="cost">Costo Proveedor&nbsp;<b class="text-danger">*</b></label>
						<input type="number" id="cost" name="cost" class="form-control" value="{{ old('cost', isset($product) ? $product->cost : 0) }}" min="0" step=".01" required>
						@if ($errors->has('cost'))
							<em class="invalid-feedback">
								{{ $errors->first('cost') }}
							</em>
						@endif
					</div>
				</div>
				<div class="col-sm-4">
					<div class="form-group {{ $errors->has('price') ? 'has-error' : '' }}">
						<label for="price">Precio de Venta&nbsp;<b class="text-danger">*</b></label>
						<input type="number" id="price" name="price" class="form-control" value="{{ old('price', isset($product) ? $product->price : 0) }}" min="0" step=".01" required>
						@if ($errors->has('price'))
							<em class="invalid-feedback">
								{{ $errors->first('price') }}
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