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
				<div class="col-sm-3">
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
				<div class="col-sm-3">
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
					<div class="form-group {{ $errors->has('category') ? 'has-error' : '' }}">
						<label for="category">Categoría&nbsp;<b class="text-danger">*</b></label>
						<select name="category" class="custom-select" required>
							<option value="">Seleccione</option>
							@foreach ($categories as $category)
								<option value="{{ $category->id }}" {{ $category->id == $product->category_id ? 'selected' : '' }}>{{ $category->title }}</option>
							@endforeach
						</select>
						@if ($errors->has('category'))
							<em class="invalid-feedback">
								{{ $errors->first('category') }}
							</em>
						@endif
					</div>
				</div>
				<div class="col-sm-3">
					<div class="form-group {{ $errors->has('brand') ? 'has-error' : '' }}">
						<label for="brand">Marca&nbsp;<b class="text-danger">*</b></label>
						<select name="brand" class="custom-select" required>
							<option value="">Seleccione</option>
							@foreach ($brans as $brand)
								<option value="{{ $brand->id }}" {{ $brand->id == $product->brand_id ? 'selected' : '' }}>{{ $brand->title }}</option>
							@endforeach
						</select>
						@if ($errors->has('brand'))
							<em class="invalid-feedback">
								{{ $errors->first('brand') }}
							</em>
						@endif
					</div>
				</div>
				<div class="col-sm-3">
					<div class="form-group {{ $errors->has('category') ? 'has-error' : '' }}">
						<label for="category">Categoría&nbsp;<b class="text-danger">*</b></label>
						<input type="text" id="category" name="category" class="form-control" value="{{ old('category', isset($product) ? $product->category : '') }}" required>
						@if ($errors->has('category'))
							<em class="invalid-feedback">
								{{ $errors->first('category') }}
							</em>
						@endif
					</div>
				</div>
				<div class="col-sm-3">
					<div class="form-group {{ $errors->has('brand') ? 'has-error' : '' }}">
						<label for="brand">Marca&nbsp;<b class="text-danger">*</b></label>
						<input type="text" id="brand" name="brand" class="form-control" value="{{ old('brand', isset($product) ? $product->brand : '') }}" required>
						@if ($errors->has('brand'))
							<em class="invalid-feedback">
								{{ $errors->first('brand') }}
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
						<label for="cost">Precio&nbsp;<b class="text-danger">*</b></label>
						<input type="number" id="cost" name="cost" class="form-control" value="{{ old('cost', isset($product) ? $product->cost : 0) }}" min="0" required>
						@if ($errors->has('cost'))
							<em class="invalid-feedback">
								{{ $errors->first('cost') }}
							</em>
						@endif
					</div>
				</div>
				<div class="col-sm-4">
					<div class="form-group {{ $errors->has('stock') ? 'has-error' : '' }}">
						<label for="stock">Stock&nbsp;<b class="text-danger">*</b></label>
						<input type="number" id="stock" name="stock" class="form-control" value="{{ old('stock', isset($product) ? $product->stock : 0) }}" min="0" required>
						@if ($errors->has('stock'))
							<em class="invalid-feedback">
								{{ $errors->first('stock') }}
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