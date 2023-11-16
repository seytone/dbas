@extends('layouts.admin')
@section('content')
    <div class="card">
        <div class="card-header">
            Creación de Producto
        </div>
        <div class="card-body">
            <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
				<div class="row">
					<div class="col-sm-3">
						<div class="form-group {{ $errors->has('code') ? 'has-error' : '' }}">
							<label for="code">Código&nbsp;<b class="text-danger">*</b></label>
							<input type="text" id="code" name="code" class="form-control" value="{{ old('code') }}" required>
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
								<option value="hardware">Hardware</option>
								<option value="software">Software</option>
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
							<select name="category" class="selectize-create" required>
								<option value="">Seleccione</option>
								@foreach ($categories as $category)
									<option value="{{ $category->id }}">{{ $category->title }}</option>
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
							<select name="brand" class="selectize-create" required>
								<option value="">Seleccione</option>
								@foreach ($brands as $brand)
									<option value="{{ $brand->id }}">{{ $brand->title }}</option>
								@endforeach
							</select>
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
							<input type="text" id="title" name="title" class="form-control" value="{{ old('title') }}" required>
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
							<textarea id="description" name="description" rows="1" class="form-control description" maxlength="140">{{ old('description') }}</textarea>
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
							<input type="number" id="cost" name="cost" class="form-control" value="{{ old('cost', 0) }}" min="0" required>
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
							<input type="number" id="price" name="price" class="form-control" value="{{ old('price', 0) }}" min="0" required>
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
                    <input class="btn btn-success" type="submit" value="Guardar">
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@8"></script>
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
