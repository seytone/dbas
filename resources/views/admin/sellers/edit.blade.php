@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        Edición de Vendedor
    </div>

    <div class="card-body">
        <form action="{{ route("admin.sellers.update", [$seller->id]) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
			<div class="row">
				<div class="col-sm-3">
					<div class="form-group {{ $errors->has('user_id') ? 'has-error' : '' }}">
						<label for="user_id">Usuario&nbsp;<b class="text-danger">*</b></label>
						<input type="text" class="form-control" value="{{ $seller->user->getFullname() }}" readonly disabled>
						@if($errors->has('user_id'))
							<em class="invalid-feedback">
								{{ $errors->first('user_id') }}
							</em>
						@endif
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-3">
					<div class="form-group {{ $errors->has('commission_1') ? 'has-error' : '' }}">
						<label for="commission_1">Comisión 1 (% Licencias Perpetuas)&nbsp;<b class="text-danger">*</b></label>
						<input type="number" id="commission_1" name="commission_1" class="form-control" value="{{ old('commission_1', isset($seller) ? $seller->commission_1 : '') }}" min="0" max="100" required>
						@if ($errors->has('commission_1'))
							<em class="invalid-feedback">
								{{ $errors->first('commission_1') }}
							</em>
						@endif
					</div>
				</div>
				<div class="col-sm-3">
					<div class="form-group {{ $errors->has('commission_2') ? 'has-error' : '' }}">
						<label for="commission_2">Comisión 2 (% Suscripciones Anuales)&nbsp;<b class="text-danger">*</b></label>
						<input type="number" id="commission_2" name="commission_2" class="form-control" value="{{ old('commission_2', isset($seller) ? $seller->commission_2 : '') }}" min="0" max="100" required>
						@if ($errors->has('commission_2'))
							<em class="invalid-feedback">
								{{ $errors->first('commission_2') }}
							</em>
						@endif
					</div>
				</div>
				<div class="col-sm-3">
					<div class="form-group {{ $errors->has('commission_3') ? 'has-error' : '' }}">
						<label for="commission_3">Comisión 3 (% Hardware y Otros)&nbsp;<b class="text-danger">*</b></label>
						<input type="number" id="commission_3" name="commission_3" class="form-control" value="{{ old('commission_3', isset($seller) ? $seller->commission_3 : '') }}" min="0" max="100" required>
						@if ($errors->has('commission_3'))
							<em class="invalid-feedback">
								{{ $errors->first('commission_3') }}
							</em>
						@endif
					</div>
				</div>
				<div class="col-sm-3">
					<div class="form-group {{ $errors->has('commission_4') ? 'has-error' : '' }}">
						<label for="commission_4">Comisión 4 (% Servicios)&nbsp;<b class="text-danger">*</b></label>
						<input type="number" id="commission_4" name="commission_4" class="form-control" value="{{ old('commission_4', isset($seller) ? $seller->commission_4 : '') }}" min="0" max="100" required>
						@if ($errors->has('commission_4'))
							<em class="invalid-feedback">
								{{ $errors->first('commission_4') }}
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