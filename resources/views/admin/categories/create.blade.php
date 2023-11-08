@extends('layouts.admin')
@section('content')
    <div class="card">
        <div class="card-header">
            Creación de Categoría
        </div>
        <div class="card-body">
            <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
				<div class="row">
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
