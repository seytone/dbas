@extends('layouts.admin')
@section('content')
	<div class="row mb-3">
		<div class="col-md-8">
			<h1>{{ $label }} <small class="text-muted">{{ $document->formatted_number }}</small></h1>
		</div>
		<div class="col-md-4 text-right">
			<a href="{{ route('admin.admin_docs.index', $type) }}" class="btn btn-secondary">
				<i class="fa fa-arrow-left mr-1"></i> Volver
			</a>
			<a href="{{ route('admin.admin_docs.pdf', [$type, $document->id]) }}" class="btn btn-primary">
				<i class="fa fa-file-pdf mr-1"></i> Descargar PDF
			</a>
		</div>
	</div>

	@if(session('message'))
		<div class="alert alert-success">{{ session('message') }}</div>
	@endif

	<div class="card">
		<div class="card-body">
			<div class="row">
				<div class="col-md-6">
					<h6 class="text-muted">DATOS DEL DOCUMENTO</h6>
					<table class="table table-sm table-borderless">
						<tr><td><b>Número</b></td><td>{{ $document->formatted_number }}</td></tr>
						<tr><td><b>Fecha</b></td><td>{{ $document->created_at->format('d/m/Y H:i') }}</td></tr>
						<tr><td><b>Empresa emisora</b></td><td>{{ config('companies.' . $document->company . '.label') }}</td></tr>
						<tr><td><b>Emitido por</b></td><td>{{ $document->author->name ?? '—' }}</td></tr>
						@if($document->parent)
							<tr><td><b>Referencia</b></td><td>{{ $document->parent->formatted_number }}</td></tr>
						@endif
					</table>
				</div>
				<div class="col-md-6">
					<h6 class="text-muted">DATOS DEL CLIENTE</h6>
					<table class="table table-sm table-borderless">
						<tr><td><b>Nombre</b></td><td>{{ $document->data['client_name'] ?? '—' }}</td></tr>
						<tr><td><b>Documento</b></td><td>{{ $document->data['client_document'] ?? '—' }}</td></tr>
						<tr><td><b>Teléfono</b></td><td>{{ $document->data['client_phone'] ?? '—' }}</td></tr>
						<tr><td><b>Dirección</b></td><td>{{ $document->data['client_address'] ?? '—' }}</td></tr>
					</table>
				</div>
			</div>

			@if(!empty($document->data['items']))
				<hr>
				<h6 class="text-muted">ITEMS</h6>
				<table class="table table-sm">
					<thead>
						<tr>
							@foreach(array_keys($document->data['items'][0]) as $col)
								<th>{{ ucfirst($col) }}</th>
							@endforeach
						</tr>
					</thead>
					<tbody>
						@foreach($document->data['items'] as $item)
							<tr>
								@foreach($item as $val)
									<td>{{ $val }}</td>
								@endforeach
							</tr>
						@endforeach
					</tbody>
				</table>
			@endif
		</div>
	</div>
@endsection
