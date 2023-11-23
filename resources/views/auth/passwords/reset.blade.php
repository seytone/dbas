@extends('layouts.app')
@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card-group">
            <div class="card p-4">
                <div class="card-body">
                    <form method="POST" action="{{ route('password.request') }}">
                        {{ csrf_field() }}
                        <div class="text-center">
                            <img class="img-fluid" src="{{ asset('img/logo.png') }}" alt="Logo Distribuidora Bit" width="400">
                        </div>
                        <h4 class="text-center text-muted my-5">Restauración de contraseña</h4>
                        <p class="text-muted">Escribe el email con el que accedes a la plataforma.</p>
                        <div>
                            <input name="token" value="{{ $token }}" type="hidden">
							<div class="input-group mb-3">
								<div class="input-group-prepend">
									<span class="input-group-text">
										<i class="fa fa-user"></i>
									</span>
								</div>
								<input name="email" type="text" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" required autofocus placeholder="Email" value="{{ old('email', null) }}">
								@if($errors->has('email'))
									<div class="invalid-feedback">
										{{ $errors->first('email') }}
									</div>
								@endif
							</div>
							<div class="input-group mb-3">
								<div class="input-group-prepend">
									<span class="input-group-text"><i class="fa fa-lock"></i></span>
								</div>
								<input name="password" type="password" class="form-control password {{ $errors->has('password') ? 'is-invalid' : '' }}" minlength="8" required placeholder="Contraseña">
								@if($errors->has('password'))
									<div class="invalid-feedback">
										{{ $errors->first('password') }}
									</div>
								@endif
								<p class="helper-block text-danger">
                                    <small>La contraseña debe ser mínimo de 8 caracteres y debe contener al menos 1 mayúscula, 1 minúsculas, 1 número, y  1 carácter especial.</small>
                                </p>
							</div>
							<div class="input-group mb-3">
								<input name="password_confirmation" type="password" class="form-control password {{ $errors->has('password_confirmation') ? 'is-invalid' : '' }}" minlength="8" required placeholder="Confirmar Contraseña">
								<div class="input-group-append">
									<span class="input-group-text" id="toggle-password"><i class="fa fa-eye"></i></span>
								</div>
								@if($errors->has('password_confirmation'))
									<div class="invalid-feedback">
										{{ $errors->first('password_confirmation') }}
									</div>
								@endif
							</div>
                        </div>
                        <div class="row">
                            <div class="col-12 text-right">
                                <button type="submit" class="btn btn-custom btn-block btn-flat">
                                    Restaurar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
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