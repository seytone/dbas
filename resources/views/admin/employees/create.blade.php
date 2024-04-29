@extends('layouts.admin')
@section('content')
    <div class="card">
        <div class="card-header">
            Creación de Empleado
        </div>
        <div class="card-body">
            <form action="{{ route('admin.employees.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
				<div class="row">
					<div class="col-sm-12">
						<div class="form-group {{ $errors->has('number') ? 'has-error' : '' }}">
							<label for="number">Número Empleado&nbsp;<b class="text-danger">*</b></label>
							<input type="number" id="number" name="number" class="form-control" value="{{ old('number') }}" required>
							@if ($errors->has('number'))
								<em class="invalid-feedback">
									{{ $errors->first('number') }}
								</em>
							@endif
						</div>
					</div>
					<div class="col-sm-12">
						<div class="form-group {{ $errors->has('pin') ? 'has-error' : '' }}">
							<label for="pin">Cédula&nbsp;<b class="text-danger">*</b></label>
							<input type="text" id="pin" name="pin" class="form-control" value="{{ old('pin') }}" required>
							@if ($errors->has('pin'))
								<em class="invalid-feedback">
									{{ $errors->first('pin') }}
								</em>
							@endif
						</div>
					</div>
					<div class="col-sm-12">
						<div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
							<label for="name">Nombre&nbsp;<b class="text-danger">*</b></label>
							<input type="text" id="name" name="name" class="form-control" value="{{ old('name') }}" required>
							@if ($errors->has('name'))
								<em class="invalid-feedback">
									{{ $errors->first('name') }}
								</em>
							@endif
						</div>
					</div>
					<div class="col-sm-12">
						<div class="form-group {{ $errors->has('lastname') ? 'has-error' : '' }}">
							<label for="lastname">Apellido&nbsp;<b class="text-danger">*</b></label>
							<input type="text" id="lastname" name="lastname" class="form-control" value="{{ old('lastname') }}" required>
							@if ($errors->has('lastname'))
								<em class="invalid-feedback">
									{{ $errors->first('lastname') }}
								</em>
							@endif
						</div>
					</div>
					<div class="col-sm-12">
						<div class="form-group {{ $errors->has('email') ? 'has-error' : '' }}">
							<label for="email">Email&nbsp;<b class="text-danger">*</b></label>
							<input type="email" id="email" name="email" class="form-control" value="{{ old('email') }}" required>
							@if ($errors->has('email'))
								<em class="invalid-feedback">
									{{ $errors->first('email') }}
								</em>
							@endif
						</div>
					</div>
					<div class="col-sm-12">
						<div class="form-group {{ $errors->has('phone') ? 'has-error' : '' }}">
							<label for="phone">Teléfono&nbsp;<b class="text-danger">*</b></label>
							<input type="text" id="phone" name="phone" class="form-control" value="{{ old('phone') }}" required>
							@if ($errors->has('phone'))
								<em class="invalid-feedback">
									{{ $errors->first('phone') }}
								</em>
							@endif
						</div>
					</div>
					<div class="col-sm-12">
						<div class="form-group {{ $errors->has('department') ? 'has-error' : '' }}">
							<label for="department">Departamento&nbsp;<b class="text-danger">*</b></label>
							<input type="text" id="department" name="department" class="form-control" value="{{ old('department') }}" required>
							@if ($errors->has('department'))
								<em class="invalid-feedback">
									{{ $errors->first('department') }}
								</em>
							@endif
						</div>
					</div>
					<div class="col-sm-12">
						<div class="form-group {{ $errors->has('position') ? 'has-error' : '' }}">
							<label for="position">Cargo&nbsp;<b class="text-danger">*</b></label>
							<input type="text" id="position" name="position" class="form-control" value="{{ old('position') }}" required>
							@if ($errors->has('position'))
								<em class="invalid-feedback">
									{{ $errors->first('position') }}
								</em>
							@endif
						</div>
					</div>
					<div class="col-sm-12">
						<div class="form-group {{ $errors->has('salary') ? 'has-error' : '' }}">
							<label for="salary">Salario&nbsp;<b class="text-danger">*</b></label>
							<input type="number" id="salary" name="salary" class="form-control" value="{{ old('salary') }}" required>
							@if ($errors->has('salary'))
								<em class="invalid-feedback">
									{{ $errors->first('salary') }}
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
