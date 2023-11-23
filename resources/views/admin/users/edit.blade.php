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
            <div class="form-group {{ $errors->has('password') ? 'has-error' : '' }}">
                <label for="password">{{ trans('cruds.user.fields.password') }}</label>
                <input type="password" id="password" name="password" class="form-control" pattern="(?=^.{8,}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$">
                @if($errors->has('password'))
                    <em class="invalid-feedback text-danger">
                        <small>UpperCase, LowerCase, Number/SpecialChar and min 8 Chars</small>
                    </em>
                @endif
                <p class="helper-block">
                    {{ trans('cruds.user.fields.password_helper') }}
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
					$('.commission-filed').prop('required', false);
				}
			});
		});
	</script>
@endsection