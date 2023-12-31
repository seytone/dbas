@extends('layouts.' . $layout)
@section('content')
    <div class="card">
        <div class="card-header">
            Creación de Servicio
        </div>
        <div class="card-body">
            <form action="{{ route('admin.services.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
				<input type="hidden" name="layout" value="{{ $layout }}">
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
					<div class="col-sm-6">
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
					<div class="col-sm-3">
						<div class="form-group {{ $errors->has('price') ? 'has-error' : '' }}">
							<label for="price">Precio&nbsp;<b class="text-danger">*</b></label>
							<input type="number" id="price" name="price" class="form-control" value="{{ old('price', 0) }}" min="0" step=".01" required>
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
							<textarea id="description" name="description" rows="1" class="form-control description" maxlength="140">{{ old('description') }}</textarea>
							@if ($errors->has('description'))
								<em class="invalid-feedback">
									{{ $errors->first('description') }}
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
					url: "{{ route('admin.services.exists') }}",
					type: 'GET',
					data: { code: code },
					success: function(data) {
						if (data) {
							$('#code').addClass('is-invalid');
							$('#code').focus();
							Swal.fire({
								type: 'error',
								title: 'Error',
								text: 'Ya existe un servicio con ese código',
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
