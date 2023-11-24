

@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        Change password
    </div>

    <div class="card-body">
        <form action="{{ route('auth.change_password') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PATCH')
            <div class="form-group {{ $errors->has('current_password') ? 'has-error' : '' }}">
                <label for="current_password">Contraseña actual *</label>
				<div class="input-group mb-3">
					<input name="current_password" type="password" class="form-control password {{ $errors->has('current_password') ? 'is-invalid' : '' }}" minlength="8" required>
					<div class="input-group-append">
						<span class="input-group-text" id="toggle-password"><i class="fa fa-eye"></i></span>
					</div>
					@if($errors->has('current_password'))
						<div class="invalid-feedback">
							{{ $errors->first('current_password') }}
						</div>
					@endif
				</div>
            </div>
            <div class="form-group {{ $errors->has('new_password') ? 'has-error' : '' }}">
                <label for="new_password">Contraseña nueva *</label>
                <input type="password" id="new_password" name="new_password" class="form-control password" pattern="(?=^.{8,}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$" required>
                @if($errors->has('new_password'))
                    <em class="invalid-feedback">
                        {{ $errors->first('new_password') }}
                    </em>
                @endif
                <p class="helper-block text-danger">
                    <small>La contraseña debe ser mínimo de 8 caracteres y debe contener al menos 1 mayúscula, 1 minúsculas, 1 número, y 1 carácter especial.</small>
                </p>
            </div>
            <div class="form-group {{ $errors->has('new_password_confirmation') ? 'has-error' : '' }}">
                <label for="new_password_confirmation">Confirma tu nueva contraseña *</label>
                <input type="password" id="new_password_confirmation" name="new_password_confirmation" class="form-control password" pattern="(?=^.{8,}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$" required>
                @if($errors->has('new_password_confirmation'))
                    <em class="invalid-feedback">
                        {{ $errors->first('new_password_confirmation') }}
                    </em>
                @endif
            </div>
            <div>
                <input class="btn btn-custom" type="submit" value="Guardar">
            </div>
        </form>
    </div>
</div>
@endsection

@section("scripts")
	<script>
		$(document).ready(function() {
			$("#toggle-password").click(function() {
				if($(".password").attr("type") == "password") {
					$(".password").attr("type", "text");
					$("#toggle-password").html('<i class="fa fa-eye-slash"></i>');
				} else {
					$(".password").attr("type", "password");
					$("#toggle-password").html('<i class="fa fa-eye"></i>');
				}
			});
		});
	</script>
@endsection