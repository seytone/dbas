@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        Editar {{ trans('cruds.user.title_singular') }}
    </div>
    <div class="card-body">
        <form action="{{ route("admin.users.update", [$user->id]) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
			<div class="form-group {{ $errors->has('roles') ? 'has-error' : '' }}">
                <label for="roles">Rol*
                    {{-- <span class="btn btn-info btn-xs select-all">{{ trans('global.select_all') }}</span>
                    <span class="btn btn-info btn-xs deselect-all">{{ trans('global.deselect_all') }}</span> --}}
				</label>
                <select name="roles[]" id="rol" class="selectize-roles" multiple required>
                    @foreach($roles as $id => $role)
                        <option value="{{ $role }}" {{ $user->hasRole($role) ? 'selected' : '' }}>{{ $role }}</option>
                    @endforeach
                </select>
                @if($errors->has('roles'))
                    <em class="invalid-feedback">
                        {{ $errors->first('roles') }}
                    </em>
                @endif
                <p class="helper-block">
                    {{ trans('cruds.user.fields.roles_helper') }}
                </p>
            </div>
            <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                <label for="name">{{ trans('cruds.user.fields.name') }}*</label>
                <input type="text" id="name" name="name" class="form-control" value="{{ old('name', isset($user) ? $user->name : '') }}" required>
                @if($errors->has('name'))
                    <em class="invalid-feedback">
                        {{ $errors->first('name') }}
                    </em>
                @endif
                <p class="helper-block">
                    {{ trans('cruds.user.fields.name_helper') }}
                </p>
            </div>
			<div class="form-group {{ $errors->has('lastname') ? 'has-error' : '' }}">
                <label for="lastname">{{ trans('cruds.user.fields.lastname') }}*</label>
                <input type="text" id="lastname" name="lastname" class="form-control" value="{{ old('lastname', isset($user) ? $user->lastname : '') }}" required>
                @if($errors->has('lastname'))
                    <em class="invalid-feedback">
                        {{ $errors->first('lastname') }}
                    </em>
                @endif
                <p class="helper-block">
                    {{ trans('cruds.user.fields.lastname_helper') }}
                </p>
            </div>
            <div class="form-group {{ $errors->has('email') ? 'has-error' : '' }}">
                <label for="email">{{ trans('cruds.user.fields.email') }}*</label>
                <input type="email" id="email" name="email" class="form-control" value="{{ old('email', isset($user) ? $user->email : '') }}" disabled>
                @if($errors->has('email'))
                    <em class="invalid-feedback">
                        {{ $errors->first('email') }}
                    </em>
                @endif
                <p class="helper-block">
                    {{ trans('cruds.user.fields.email_helper') }}
                </p>
            </div>
			<div class="row seller_fields {{ $user->hasRole('Vendedor') ? '' : 'd-none' }}">
				<div class="col-sm-3">
					<div class="form-group {{ $errors->has('commission_1') ? 'has-error' : '' }}">
						<label for="commission_1">Comisión 1 (% Licencias Perpetuas)&nbsp;<b class="text-danger">*</b></label>
						<input type="number" id="commission_1" name="commission_1" class="form-control commission-filed" value="{{ old('commission_1', isset($user->seller) ? $user->seller->commission_1 : '') }}" min="0" max="100">
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
						<input type="number" id="commission_2" name="commission_2" class="form-control commission-filed" value="{{ old('commission_2', isset($user->seller) ? $user->seller->commission_2 : '') }}" min="0" max="100">
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
						<input type="number" id="commission_3" name="commission_3" class="form-control commission-filed" value="{{ old('commission_3', isset($user->seller) ? $user->seller->commission_3 : '') }}" min="0" max="100">
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
						<input type="number" id="commission_4" name="commission_4" class="form-control commission-filed" value="{{ old('commission_4', isset($user->seller) ? $user->seller->commission_4 : '') }}" min="0" max="100">
						@if ($errors->has('commission_4'))
							<em class="invalid-feedback">
								{{ $errors->first('commission_4') }}
							</em>
						@endif
					</div>
				</div>
			</div>
			<div class="form-group">
				<label for="commission_4">Cambiar contraseña</label><br>
				<div class="form-check form-check-inline">
					<input class="form-check-input" type="radio" name="changePassOptions" id="changePass1" value="1">
					<label class="form-check-label" for="changePass1">Si</label>
				</div>
				<div class="form-check form-check-inline">
					<input class="form-check-input" type="radio" name="changePassOptions" id="changePass0" value="0" checked>
					<label class="form-check-label" for="changePass0">No</label>
				</div>
			</div>
			<div class="row changePassCont d-none">
				<hr>
				<div class="col-md-6">
					<div class="form-group {{ $errors->has('password') ? 'has-error' : '' }}">
						<label for="password">{{ trans('cruds.user.fields.password') }}*</label>
						<div class="input-group">
							<input name="password" type="password" class="form-control password {{ $errors->has('password') ? 'is-invalid' : '' }}" pattern="(?=^.{8,}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$" disabled>
							<div class="input-group-append">
								<span class="input-group-text" id="toggle-password"><i class="fa fa-eye"></i></span>
							</div>
							@if($errors->has('password'))
								<div class="invalid-feedback">
									{{ $errors->first('password') }}
								</div>
							@endif
						</div>
						<p class="helper-block text-danger">
							<small>La contraseña debe ser mínimo de 8 caracteres y debe contener al menos 1 mayúscula, 1 minúsculas, 1 número, y 1 carácter especial.</small>
						</p>
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group {{ $errors->has('password_confirmation') ? 'has-error' : '' }}">
						<label for="password_confirmation">Confirmar contraseña*</label>
						<input type="password" id="password_confirmation" name="password_confirmation" class="form-control password {{ $errors->has('password') ? 'is-invalid' : '' }}" pattern="(?=^.{8,}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$" disabled>
						@if($errors->has('password_confirmation'))
							<em class="invalid-feedback">
								{{ $errors->first('password_confirmation') }}
							</em>
						@endif
					</div>
				</div>
			</div>
            <div class="mt-4">
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
        $(function()
		{
			$('.selectize-roles').selectize({
				persist: false,
				plugins: ["remove_button"],
			});

			$('#rol').on('change', function(action)
			{
				var is_seller = false;

				$('#rol option:selected').each(function() {
					if ($(this).val() == "Vendedor") {
						is_seller = true;
					}
				});

				if (is_seller) {
					$('.seller_fields').removeClass('d-none');
					$('.seller_fields input').prop('disabled', false);
					$('.commission-filed').prop('required', true);
				} else {
					$('.seller_fields').addClass('d-none');
					$('.seller_fields input').prop('disabled', true);
					$('.commission-filed').prop('required', false).val('');
				}
			});

			$("#toggle-password").click(function() {
				if($(".password").attr("type") == "password") {
					$(".password").attr("type", "text");
					$("#toggle-password").html('<i class="fa fa-eye-slash"></i>');
				} else {
					$(".password").attr("type", "password");
					$("#toggle-password").html('<i class="fa fa-eye"></i>');
				}
			});

			$("input[name='changePassOptions']").on('change', function() {
				if ($(this).val() == 1) {
					$('.changePassCont').removeClass('d-none');
					$('.changePassCont input').prop('disabled', false);
					$('.changePassCont input').prop('required', true);
				} else {
					$('.changePassCont').addClass('d-none');
					$('.changePassCont input').prop('disabled', true);
					$('.changePassCont input').prop('required', false);
				}
			});
		});
	</script>
@endsection