@extends('layouts.admin')
@section('content')
	@php $editing = isset($document) && $document; @endphp
	<div class="row mb-3">
		<div class="col-md-8"><h1>{{ $editing ? 'Editar Nota de Crédito' : 'Nueva Nota de Crédito' }} @if($editing)<small class="text-muted">{{ $document->formatted_number }}</small>@endif</h1></div>
		<div class="col-md-4 text-right">
			<a href="{{ $editing ? route('admin.admin_docs.show', [$type, $document->id]) : route('admin.admin_docs.index', $type) }}" class="btn btn-secondary"><i class="fa fa-arrow-left mr-1"></i> Cancelar</a>
		</div>
	</div>

	@if($errors->any())
		<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach</ul></div>
	@endif

	@if(!$editing && $invoices->isEmpty())
		<div class="alert alert-warning">
			No hay Notas de Entrega (Invoices) generadas en el sistema todavía. Primero crea al menos una para poder emitir una Nota de Crédito.
		</div>
	@else
	<form action="{{ $editing ? route('admin.admin_docs.update', [$type, $document->id]) : route('admin.admin_docs.store', $type) }}" method="POST" id="doc-form">
		@csrf
		@if($editing) @method('PUT') @endif
		<div class="card mb-3"><div class="card-body">
			<div class="row">
				<div class="col-md-6">
					<div class="form-group">
						<label><b>Empresa emisora *</b></label>
						<select name="company" class="form-control" required>
							@foreach(config('companies') as $code => $co)
								<option value="{{ $code }}" {{ old('company', 've') == $code ? 'selected' : '' }}>{{ $co['label'] }}</option>
							@endforeach
						</select>
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<label><b>Nota de Entrega afectada *</b></label>
						<select name="parent_document_id" id="parent_invoice" class="form-control" required>
							<option value="">Selecciona la Nota de Entrega a afectar…</option>
							@foreach($invoices as $inv)
								<option value="{{ $inv->id }}"
									data-payload='@json([
										"client_name"     => $inv->data["client_name"]     ?? "",
										"client_document" => $inv->data["client_document"] ?? "",
										"client_phone"    => $inv->data["client_phone"]    ?? "",
										"client_address"  => $inv->data["client_address"]  ?? "",
										"items"           => $inv->data["items"]           ?? [],
									])'
									{{ old('parent_document_id') == $inv->id ? 'selected' : '' }}>
									{{ $inv->formatted_number }} — {{ $inv->data['client_name'] ?? '' }} ({{ $inv->created_at->format('d/m/Y') }})
								</option>
							@endforeach
						</select>
						<small class="text-muted">Al seleccionar, se autollenan los datos del cliente y los productos de esa Nota de Entrega. Elimina las líneas que NO se van a acreditar.</small>
					</div>
				</div>
			</div>

			<h6 class="text-muted">DATOS DEL CLIENTE <small class="text-muted">(vienen de la Nota de Entrega)</small></h6>
			<div class="row">
				<div class="col-md-6"><div class="form-group"><label>Nombre *</label><input type="text" name="client_name" class="form-control" value="{{ old('client_name') }}" required maxlength="255"></div></div>
				<div class="col-md-6"><div class="form-group"><label>RIF / Documento</label><input type="text" name="client_document" class="form-control" value="{{ old('client_document') }}" maxlength="50"></div></div>
				<div class="col-md-6"><div class="form-group"><label>Teléfono</label><input type="text" name="client_phone" class="form-control" value="{{ old('client_phone') }}" maxlength="50"></div></div>
				<div class="col-md-6"><div class="form-group"><label>Dirección</label><input type="text" name="client_address" class="form-control" value="{{ old('client_address') }}" maxlength="500"></div></div>
			</div>

			<div class="form-group">
				<label>Concepto (ej. "Reintegro por cambio")</label>
				<input type="text" name="reason" class="form-control" value="{{ old('reason', 'Reintegro por cambio') }}" maxlength="255">
			</div>

			<h6 class="text-muted">ITEMS A ACREDITAR <small class="text-muted">(precios ya negativos; elimina los que no aplican)</small></h6>
			<table class="table table-sm">
				<thead>
					<tr>
						<th style="width: 15%;">Código</th>
						<th>Descripción</th>
						<th style="width: 10%;">Cantidad</th>
						<th style="width: 15%;">Precio ($)</th>
						<th style="width: 15%;">Amount</th>
						<th style="width: 40px;"></th>
					</tr>
				</thead>
				<tbody id="items-body">
					<tr id="items-empty-row"><td colspan="6" class="text-center text-muted py-3">Selecciona una Nota de Entrega para cargar sus productos.</td></tr>
				</tbody>
			</table>

			<div class="text-right mt-3"><h4><b>Total: $<span id="grand-total">0,00</span></b></h4></div>
		</div></div>
		<button type="submit" class="btn btn-success btn-lg"><i class="fa fa-save mr-2"></i>{{ $editing ? 'Guardar cambios' : 'Generar' }}</button>
	</form>
	@endif
@endsection

@section('scripts')
<script>
(function() {
	var idx = 0;

	function addRow(data) {
		data = data || {};
		var i = idx++;
		$('#items-empty-row').remove();
		$('#items-body').append(
			'<tr>' +
			'<td><input type="text" name="items[' + i + '][code]" class="form-control form-control-sm" value="' + (data.code || '') + '" maxlength="100"></td>' +
			'<td><textarea name="items[' + i + '][description]" class="form-control form-control-sm" rows="2" required>' + (data.description || '') + '</textarea></td>' +
			'<td><input type="number" step="0.01" min="0" name="items[' + i + '][quantity]" class="form-control form-control-sm text-right qty" value="' + (data.quantity != null ? data.quantity : 1) + '" required></td>' +
			'<td><input type="number" step="0.01" name="items[' + i + '][price]" class="form-control form-control-sm text-right price" value="' + (data.price != null ? data.price : 0) + '" required></td>' +
			'<td class="text-right amount align-middle">0,00</td>' +
			'<td><button type="button" class="btn btn-sm btn-danger btn-remove" title="Quitar esta línea de la nota de crédito"><i class="fa fa-times"></i></button></td>' +
			'</tr>'
		);
		recalc();
	}

	function recalc() {
		var grand = 0;
		$('#items-body tr').each(function() {
			var q = parseFloat($(this).find('.qty').val()) || 0;
			var p = parseFloat($(this).find('.price').val()) || 0;
			var amt = q * p;
			grand += amt;
			$(this).find('.amount').text(amt.toFixed(2).replace('.', ','));
		});
		$('#grand-total').text(grand.toFixed(2).replace('.', ','));
	}

	function clearItems() {
		$('#items-body').empty().append(
			'<tr id="items-empty-row"><td colspan="6" class="text-center text-muted py-3">Selecciona una Nota de Entrega para cargar sus productos.</td></tr>'
		);
		idx = 0;
		recalc();
	}

	function loadInvoiceData(option) {
		var payload = option.data('payload');
		if (!payload) return;

		// Solo pisar los datos del cliente si están vacíos, para no atropellar
		// una corrección manual del usuario.
		['client_name', 'client_document', 'client_phone', 'client_address'].forEach(function(f) {
			var el = $('[name="' + f + '"]');
			if (!el.val() && payload[f]) el.val(payload[f]);
		});

		clearItems();
		(payload.items || []).forEach(function(item) {
			addRow({
				code: item.code || '',
				description: item.description || '',
				quantity: item.quantity != null ? item.quantity : 1,
				// Se negativiza para que sea un reintegro.
				price: -Math.abs(parseFloat(item.price) || 0),
			});
		});
	}

	$(function() {
		$('#parent_invoice').on('change', function() {
			var opt = $(this).find('option:selected');
			if (opt.val()) loadInvoiceData(opt);
			else clearItems();
		});

		$('#items-body').on('input', '.qty, .price', recalc);
		$('#items-body').on('click', '.btn-remove', function() {
			$(this).closest('tr').remove();
			recalc();
		});

		@if(is_array(old('items')) && count(old('items')))
			// Recargar items desde old() tras un error de validación.
			@foreach(old('items') as $it) addRow(@json($it)); @endforeach
		@elseif(isset($document) && !empty($document->data['items']))
			// Modo edición: sembrar con los items ya guardados.
			@foreach($document->data['items'] as $it) addRow(@json($it)); @endforeach
		@endif
	});
})();
</script>
@endsection
