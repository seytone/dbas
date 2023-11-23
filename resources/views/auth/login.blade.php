@extends('layouts.app')
@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card-group">
            <div class="card p-4">
                <div class="card-body">
                    @if(\Session::has('message'))
                        <p class="alert alert-info">
                            {{ \Session::get('message') }}
                        </p>
                    @endif
                    <form method="POST" action="{{ route('login') }}">
                        {{ csrf_field() }}
                        <div class="text-center">
                            <img class="img-fluid" src="{{ asset('img/logo.png') }}" alt="Logo Distribuidora Bit" width="400">
                        </div>
                        <h4 class="text-center text-muted my-5">Control Panel</h4>
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
							<div class="input-group-append">
								<span class="input-group-text" id="toggle-password"><i class="fa fa-eye"></i></span>
							</div>
                            @if($errors->has('password'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('password') }}
                                </div>
                            @endif
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <a class="btn btn-link px-0" href="{{ route('password.request') }}">
                                    Olvidé mi contraseña
                                </a>
                            </div>
                            <div class="col-6 text-right">
                                <button type="submit" class="btn btn-custom px-4">
                                    Acceder
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