@extends('layouts.' . $layout)
@section('content')
    <div class="card">
        <div class="card-header">
            Creación de Producto
        </div>
        <div class="card-body">
            <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
				<input type="hidden" name="layout" value="{{ $layout }}">
				<div class="row">
					<div class="col-sm-2">
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
					<div class="col-sm-2">
						<div class="form-group {{ $errors->has('group') ? 'has-error' : '' }}">
							<label for="group">Grupo&nbsp;<b class="text-danger">*</b></label>
							<select name="group" class="custom-select" required>
								<option value="">Seleccione</option>
								<option value="perpetual" {{ old('group') == 'perpetual' ? 'selected' : '' }}>Licencias Perpetuas</option>
								<option value="annual" {{ old('group') == 'annual' ? 'selected' : '' }}>Suscripciones Anuales</option>
								<option value="hardware" {{ old('group') == 'hardware' ? 'selected' : '' }}>Hardware y Otros</option>
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
								<option value="software" {{ old('type') == 'software' ? 'selected' : '' }}>Software</option>
								<option value="hardware" {{ old('type') == 'hardware' ? 'selected' : '' }}>Hardware</option>
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
									<option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->title }}</option>
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
						<div class="form-group {{ $errors->has('brand_id') ? 'has-error' : '' }}">
							<label for="brand_id">Marca&nbsp;<b class="text-danger">*</b></label>
							<select name="brand_id" class="selectize-create" required>
								<option value="">Seleccione</option>
								@foreach ($brands as $brand)
									<option value="{{ $brand->id }}" {{ old('brand_id') == $brand->id ? 'selected' : '' }}>{{ $brand->title }}</option>
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
							<input type="number" id="cost" name="cost" class="form-control" value="{{ old('cost', 0) }}" min="0" step=".01" required>
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
							<input type="number" id="price" name="price" class="form-control" value="{{ old('price', 0) }}" min="0" step=".01" required>
							@if ($errors->has('price'))
								<em class="invalid-feedback">
									{{ $errors->first('price') }}
								</em>
							@endif
						</div>
					</div>
				</div>
				@if ($layout == 'admin')
					<div class="text-center text-md-right mt-4">
						<hr>
						<input class="btn btn-success" type="submit" value="Guardar">
					</div>
				@endif
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
			$('body').on('change', '#code', function() {
				var code = $(this).val();
				$.ajax({
					url: "{{ route('admin.products.exists') }}",
					type: 'GET',
					data: { code: code },
					success: function(data) {
						if (data) {
							$('#code').addClass('is-invalid');
							$('#code').focus();
							Swal.fire({
								type: 'error',
								title: 'Error',
								text: 'Ya existe un producto con ese código',
							});
						} else {
							$('#code').removeClass('is-invalid');
						}
					}
				});
			});
        });
    </script>
@endsection
