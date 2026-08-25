@extends('layouts.admin')
@section('content')
	@php
		$editing = isset($document) && $document;
		// Datos por default del sender — se toman del config de la empresa
		// (sección dispatch). Si el usuario los edita en el form quedan
		// congelados en el snapshot del envío.
		$defaultDispatch = config('companies.ve.dispatch', []);
	@endphp
	<div class="row mb-3">
		<div class="col-md-8"><h1>{{ $editing ? 'Editar Guía de Envío' : 'Nueva Guía de Envío' }} @if($editing)<small class="text-muted">{{ $document->formatted_number }}</small>@endif</h1></div>
		<div class="col-md-4 text-right">
			<a href="{{ $editing ? route('admin.admin_docs.show', [$type, $document->id]) : route('admin.admin_docs.index', $type) }}" class="btn btn-secondary"><i class="fa fa-arrow-left mr-1"></i> Cancelar</a>
		</div>
	</div>

	@if($errors->any())
		<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach</ul></div>
	@endif

	<form action="{{ $editing ? route('admin.admin_docs.update', [$type, $document->id]) : route('admin.admin_docs.store', $type) }}" method="POST">
		@csrf
		@if($editing) @method('PUT') @endif

		{{-- La empresa se fija internamente. La guía no muestra header con
		     datos fiscales, así que este campo va oculto. --}}
		<input type="hidden" name="company" value="{{ old('company', 've') }}">

		<div class="card mb-3"><div class="card-body">
			@if(!$editing && isset($previous_shippings) && $previous_shippings->count())
				{{-- Plantilla: copiar de un envío anterior (clientes frecuentes).
				     Al elegir, se autollena todo el formulario. --}}
				<div class="form-group">
					<label for="import_shipping"><b><i class="fa fa-copy mr-1"></i> Copiar de un envío anterior</b> <small class="text-muted">(opcional)</small></label>
					<select id="import_shipping" class="selectize-import-shipping">
						<option value="">Buscar por cliente o número…</option>
						@foreach($previous_shippings as $prev)
							@php
								$prevPayload = json_encode([
									'sender_name'     => $prev->data['sender_name']     ?? '',
									'sender_document' => $prev->data['sender_document'] ?? '',
									'sender_address'  => $prev->data['sender_address']  ?? '',
									'sender_phones'   => $prev->data['sender_phones']   ?? '',
									'client_name'     => $prev->data['client_name']     ?? '',
									'client_document' => $prev->data['client_document'] ?? '',
									'client_phone'    => $prev->data['client_phone']    ?? '',
									'client_address'  => $prev->data['client_address']  ?? '',
								]);
							@endphp
							<option value="{{ $prev->id }}" data-data='{{ $prevPayload }}'>
								{{ $prev->formatted_number }} — {{ $prev->data['client_name'] ?? '' }} ({{ $prev->created_at->format('d/m/Y') }})
							</option>
						@endforeach
					</select>
					<small class="text-muted">Sirve para clientes frecuentes: seleccionás un envío pasado y todos los datos se llenan solos. Podés editarlos después.</small>
				</div>
				<hr>
			@endif

			<div class="row">
				{{-- ENVIA — sender (default fijo, editable) --}}
				<div class="col-md-6">
					<h6 class="text-muted">ENVIA</h6>
					<div class="form-group">
						<label>Nombre *</label>
						<input type="text" name="sender_name" class="form-control" value="{{ old('sender_name', $defaultDispatch['name'] ?? '') }}" required maxlength="255">
					</div>
					<div class="form-group">
						<label>RIF / Documento</label>
						<input type="text" name="sender_document" class="form-control" value="{{ old('sender_document', $defaultDispatch['tax_id'] ?? '') }}" maxlength="50">
					</div>
					<div class="form-group">
						<label>Dirección *</label>
						<input type="text" name="sender_address" class="form-control" value="{{ old('sender_address', $defaultDispatch['address'] ?? '') }}" required maxlength="500">
					</div>
					<div class="form-group">
						<label>Teléfonos</label>
						<input type="text" name="sender_phones" class="form-control" value="{{ old('sender_phones', $defaultDispatch['phones'] ?? '') }}" maxlength="150">
					</div>
				</div>

				{{-- RECIBE — recipient (usa el client picker para clientes registrados) --}}
				<div class="col-md-6">
					<h6 class="text-muted">RECIBE</h6>
					@include('admin.admin_docs._client_picker')
					<div class="form-group">
						<label>Nombre / Razón Social *</label>
						<input type="text" name="client_name" class="form-control" value="{{ old('client_name') }}" required maxlength="255">
					</div>
					<div class="form-group">
						<label>RIF / Documento</label>
						<input type="text" name="client_document" class="form-control" value="{{ old('client_document') }}" maxlength="50">
					</div>
					<div class="form-group">
						<label>Teléfono</label>
						<input type="text" name="client_phone" class="form-control" value="{{ old('client_phone') }}" maxlength="50">
					</div>
					<div class="form-group">
						<label>ZOOM (dirección del courier) *</label>
						<input type="text" name="client_address" class="form-control" value="{{ old('client_address') }}" required maxlength="500" placeholder="ej. Zoom de av 8 santa rita maracaibo">
					</div>
				</div>
			</div>
		</div></div>

		<button type="submit" class="btn btn-success btn-lg"><i class="fa fa-save mr-2"></i>{{ $editing ? 'Guardar cambios' : 'Generar' }}</button>
	</form>
@endsection

@section('scripts')
<script>
(function() {
	function importFromShipping(payload) {
		if (!payload) return;
		['sender_name','sender_document','sender_address','sender_phones',
		 'client_name','client_document','client_phone','client_address'].forEach(function(f) {
			if (payload[f] != null) $('[name="' + f + '"]').val(payload[f]);
		});
	}

	$(function() {
		$('.selectize-import-shipping').selectize({
			persist: false,
			sortField: 'text',
			onChange: function(value) {
				if (!value) return;
				var payload = this.options[value] && this.options[value].data;
				if (payload) importFromShipping(payload);
				this.clear(true);
			},
		});
	});
})();
</script>
@endsection
