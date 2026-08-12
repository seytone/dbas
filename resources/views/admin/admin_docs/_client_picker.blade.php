{{-- Searchable client selector shared by all admin_docs forms.
     Auto-fills any input named client_name / client_document / client_phone /
     client_address when the user picks an existing client. Leaves the fields
     alone (and the user can type manually) if the picker is cleared. --}}

<div class="form-group">
	<label for="client_picker"><b>Cliente</b></label>
	<div class="d-flex">
		<div class="flex-grow-1 mr-2">
			<select id="client_picker" class="selectize-client-picker">
				<option value="">Seleccione un cliente existente...</option>
				@foreach($clients as $c)
					<option value="{{ $c->id }}">{{ $c->getIdentification() }}</option>
				@endforeach
			</select>
		</div>
		<button type="button" class="btn btn-outline-secondary" id="btn-clear-client" title="Limpiar para nuevo cliente">
			<i class="fa fa-user-plus"></i>
		</button>
	</div>
	<small class="text-muted">Selecciona un cliente existente para autollenar sus datos, o limpia el selector para capturar uno nuevo.</small>
</div>

{{-- The layout loads jQuery/Selectize AFTER content, so we can't use $(...) at
     parse time. window.load fires after all libs are loaded — safe to use them
     inside the callback. --}}
<script>
@php
	$clientsForJs = $clients->keyBy('id')->map(function ($c) {
		return [
			'name'     => $c->title,
			'document' => $c->document,
			'phone'    => $c->phone,
			'address'  => $c->address,
		];
	});
@endphp
window.addEventListener('load', function() {
	var clientsData = @json($clientsForJs);

	function fillFrom(c) {
		$('[name="client_name"]').val(c.name || '');
		$('[name="client_document"]').val(c.document || '');
		$('[name="client_phone"]').val(c.phone || '');
		$('[name="client_address"]').val(c.address || '');
	}

	$('#client_picker').selectize({
		persist: false,
		sortField: 'text',
		onChange: function(value) {
			if (!value || !clientsData[value]) return;
			fillFrom(clientsData[value]);
		},
	});

	$('#btn-clear-client').on('click', function() {
		$('#client_picker')[0].selectize.clear();
		$('[name="client_name"], [name="client_document"], [name="client_phone"], [name="client_address"]').val('');
		$('[name="client_name"]').focus();
	});
});
</script>
