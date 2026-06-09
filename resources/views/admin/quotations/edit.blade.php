@extends('layouts.admin')
@section('content')
<style>
	.description-editor {
		min-height: 50px;
		max-height: 250px;
		overflow-y: auto;
		border: 1px solid #ced4da;
		border-radius: 4px;
		padding: 6px 8px;
		background: white;
		font-size: 13px;
		line-height: 1.4;
	}
	.description-editor:focus {
		outline: none;
		border-color: #192440;
		box-shadow: 0 0 0 2px rgba(25, 36, 64, 0.15);
	}
	.description-editor img {
		max-width: 180px !important;
		max-height: 180px !important;
		width: auto !important;
		height: auto !important;
		object-fit: contain;
		margin: 4px 4px 4px 0;
		border-radius: 3px;
		display: inline-block !important;
		vertical-align: top;
	}
	.description-editor:empty:before {
		content: attr(data-placeholder);
		color: #999;
		pointer-events: none;
	}
</style>
<div class="card">
	<div class="card-header d-flex justify-content-between align-items-center flex-wrap">
		<b>Editar Cotización #{{ $quotation->formatted_number }}</b>
		@include('admin.quotations.partials.rates-widget')
	</div>
	<div class="card-body">
		<form action="{{ route('admin.quotations.update', $quotation->id) }}" method="POST" id="quotation-form">
			@csrf
			@method('PUT')

			{{-- DATOS GENERALES --}}
			<div class="row">
				<div class="col-md-6">
					<h5 class="mb-3"><i class="fa fa-building mr-2"></i>Datos de la Cotización</h5>
					<div class="form-group">
						<label for="quotation_number"><b>Nro. Presupuesto</b></label>
						<input type="text" name="quotation_number" id="quotation_number" class="form-control" value="{{ $quotation->quotation_number }}" readonly>
					</div>
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label for="emission_date"><b>Fecha de Emisión *</b></label>
								<input type="date" name="emission_date" id="emission_date" class="form-control" value="{{ old('emission_date', $quotation->emission_date->format('Y-m-d')) }}" required>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="expiration_date"><b>Fecha de Vencimiento *</b></label>
								<input type="date" name="expiration_date" id="expiration_date" class="form-control" value="{{ old('expiration_date', $quotation->expiration_date->format('Y-m-d')) }}" required>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label for="price_mode_select"><b>Moneda</b></label>
								<select name="price_mode" id="price_mode_select" class="form-control">
									<option value="usd" {{ old('price_mode', $quotation->price_mode) == 'usd' ? 'selected' : '' }}>DOLAR CASH</option>
									<option value="bcv" {{ old('price_mode', $quotation->price_mode) == 'bcv' ? 'selected' : '' }}>DOLAR EN BS A BCV</option>
								</select>
								<input type="hidden" name="currency" value="USD">
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="status"><b>Estado</b></label>
								<select name="status" id="status" class="form-control">
									<option value="draft" {{ old('status', $quotation->status) == 'draft' ? 'selected' : '' }}>Borrador</option>
									<option value="sent" {{ old('status', $quotation->status) == 'sent' ? 'selected' : '' }}>Enviada</option>
									<option value="accepted" {{ old('status', $quotation->status) == 'accepted' ? 'selected' : '' }}>Aceptada</option>
									<option value="rejected" {{ old('status', $quotation->status) == 'rejected' ? 'selected' : '' }}>Rechazada</option>
								</select>
							</div>
						</div>
						<div class="col-md-12">
							<div class="form-group" id="status-comment-group" style="display: none;">
								<label for="status_comment"><b>Comentario sobre el estado</b> <small class="text-muted">(opcional)</small></label>
								<textarea name="status_comment" id="status_comment" class="form-control" rows="2" maxlength="1000" placeholder="Ej: Aceptada por el cliente el 25/05/2026 vía email, rechazada por precio, etc.">{{ old('status_comment', $quotation->status_comment) }}</textarea>
							</div>
						</div>
					</div>
				</div>
				<div class="col-md-6">
					<h5 class="mb-3"><i class="fa fa-user mr-2"></i>Datos del Cliente</h5>
					<div class="form-group">
						<label for="client_id"><b>Cliente</b></label>
						<div class="d-flex">
							<div class="flex-grow-1 mr-2">
								<select name="client_id" id="client_id" class="selectize-client">
									<option value="">Seleccione un cliente existente...</option>
									@foreach ($clients as $client)
										<option value="{{ $client->id }}" {{ old('client_id', $quotation->client_id) == $client->id ? 'selected' : '' }}>
											{{ $client->getIdentification() }}
										</option>
									@endforeach
								</select>
							</div>
							<button type="button" class="btn btn-outline-secondary" id="btn-new-client" title="Limpiar para nuevo cliente">
								<i class="fa fa-user-plus"></i>
							</button>
						</div>
						<small class="text-muted">Modifica los datos para actualizar el cliente, o limpia el selector para crear uno nuevo.</small>
					</div>
					<div class="form-group">
						<label for="cli_title"><b>Razón Social *</b></label>
						<input type="text" name="cli_title" id="cli_title" class="form-control" required maxlength="100" value="{{ old('cli_title', $quotation->client->title ?? '') }}">
					</div>
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label for="cli_document"><b>RIF *</b></label>
								<input type="text" name="cli_document" id="cli_document" class="form-control" required maxlength="20" value="{{ old('cli_document', $quotation->client->document ?? '') }}">
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="cli_email">Email</label>
								<input type="email" name="cli_email" id="cli_email" class="form-control" maxlength="100" value="{{ old('cli_email', $quotation->client->email ?? '') }}">
							</div>
						</div>
					</div>
					<div class="form-group">
						<label for="cli_phone">Teléfono</label>
						<input type="text" name="cli_phone" id="cli_phone" class="form-control" maxlength="30" value="{{ old('cli_phone', $quotation->client->phone ?? '') }}">
					</div>
					<div class="form-group">
						<label for="cli_address">Dirección</label>
						<textarea name="cli_address" id="cli_address" class="form-control" rows="2" maxlength="500">{{ old('cli_address', $quotation->client->address ?? '') }}</textarea>
					</div>
				</div>
			</div>

			<hr>

			{{-- PRODUCTOS --}}
			<h5 class="mb-3"><i class="fa fa-list mr-2"></i>Productos</h5>

			{{-- Rate snapshot (set via JS) --}}
			<input type="hidden" name="binance_rate" id="binance_rate_hidden" value="{{ old('binance_rate', $quotation->binance_rate ?? 0) }}">
			<input type="hidden" name="bcv_rate" id="bcv_rate_hidden" value="{{ old('bcv_rate', $quotation->bcv_rate ?? 0) }}">

			{{-- Info banner shown only when BCV mode is selected (form only, not in PDF) --}}
			<div class="alert alert-info {{ old('price_mode', $quotation->price_mode) === 'bcv' ? '' : 'd-none' }}" id="bcv-mode-notice">
				<i class="fa fa-info-circle mr-2"></i>
				<b>Modo "Dólar en Bs a BCV" activo.</b> Los precios que ingreses se ajustarán automáticamente con la fórmula
				(precio &times; tasa Binance &divide; tasa BCV). En el formulario verás el precio base y el ajustado como referencia;
				en la cotización final solo aparecerá el precio ajustado.
			</div>

			<div class="row mb-3">
				<div class="col-md-8">
					<select id="product-selector" class="selectize-products" placeholder="Buscar producto por código o nombre...">
						<option value="">Seleccione...</option>
						@foreach ($categories as $category)
							@if ($category->products->count() > 0)
								<optgroup label="{{ mb_strtoupper($category->title) }}">
									@foreach ($category->products as $product)
										<option value="{{ $product->id }}" data-data='@json($product)'>
											{{ $product->title }} ({{ $product->code }})
										</option>
									@endforeach
								</optgroup>
							@endif
						@endforeach
					</select>
				</div>
				<div class="col-md-4">
					<button type="button" class="btn btn-outline-success btn-block" id="btn-add-free">
						<i class="fa fa-plus mr-1"></i> Producto Libre
					</button>
				</div>
			</div>

			<div id="products-cont">
				<div class="table-responsive">
					<table class="table table-bordered table-sm" id="products-table">
						<thead class="bg-light">
							<tr>
								<th width="100">Código</th>
								<th>Descripción</th>
								<th width="80">Cant.</th>
								<th width="120">P. Unitario</th>
								<th width="90">Tributos (%)</th>
								<th width="120">Total</th>
								<th width="40"></th>
							</tr>
						</thead>
						<tbody id="products-list">
							{{-- Pre-populate existing items --}}
						</tbody>
					</table>
				</div>
			</div>

			<div class="alert alert-warning d-none" id="no-products-alert">
				<i class="fa fa-exclamation-triangle mr-2"></i>Agregue al menos un producto a la cotización.
			</div>

			<hr>

			{{-- IVA TOGGLE --}}
			<div class="row">
				<div class="col-md-6">
					<h5 class="mb-3"><i class="fa fa-calculator mr-2"></i>Impuestos</h5>
					<div class="form-group">
						<div class="custom-control custom-switch">
							<input type="checkbox" class="custom-control-input" id="iva_toggle" {{ old('iva_rate', $quotation->iva_rate) > 0 ? 'checked' : '' }}>
							<label class="custom-control-label" for="iva_toggle"><b>Aplicar IVA (16%)</b></label>
						</div>
					</div>
					<input type="hidden" name="iva_rate" id="iva_rate" value="{{ old('iva_rate', $quotation->iva_rate) }}">
					<input type="hidden" name="igtf_rate" id="igtf_rate" value="{{ old('igtf_rate', $quotation->igtf_rate) }}">
				</div>
				<div class="col-md-6">
					<h5 class="mb-3"><i class="fa fa-sticky-note-o mr-2"></i>Notas</h5>
					<div class="form-group">
						<textarea name="notes" id="notes" class="form-control" rows="3">{{ $quotation->notes }}</textarea>
					</div>
				</div>
			</div>

			<hr>

			{{-- TOTALES --}}
			<h5 class="mb-3"><i class="fa fa-money mr-2"></i>Totales</h5>
			<div class="row">
				<div class="col-md-6">
					<div class="form-group row">
						<label class="col-sm-5 col-form-label"><b>Sub-Total</b></label>
						<div class="col-sm-7">
							<input type="text" name="subtotal" id="subtotal" class="form-control text-right" value="{{ number_format($quotation->subtotal, 2, ',', '.') }}" readonly>
						</div>
					</div>
					<div class="form-group row">
						<label class="col-sm-3 col-form-label"><b>Descuento 1</b></label>
						<div class="col-sm-3">
							<div class="input-group">
								<input type="number" name="discount_1" id="discount_1" class="form-control text-right" step="0.01" value="{{ old('discount_1', $quotation->discount_1) }}" min="0" max="100">
								<span class="input-group-text">%</span>
							</div>
						</div>
						<div class="col-sm-6">
							<input type="text" name="discount_1_amount" id="discount_1_amount" class="form-control text-right" value="{{ number_format($quotation->discount_1_amount, 2, ',', '.') }}" readonly>
						</div>
					</div>
					<div class="form-group row">
						<label class="col-sm-3 col-form-label"><b>Descuento 2</b></label>
						<div class="col-sm-3">
							<div class="input-group">
								<input type="number" name="discount_2" id="discount_2" class="form-control text-right" step="0.01" value="{{ old('discount_2', $quotation->discount_2) }}" min="0" max="100">
								<span class="input-group-text">%</span>
							</div>
						</div>
						<div class="col-sm-6">
							<input type="text" name="discount_2_amount" id="discount_2_amount" class="form-control text-right" value="{{ number_format($quotation->discount_2_amount, 2, ',', '.') }}" readonly>
						</div>
					</div>
					<div class="form-group row">
						<label class="col-sm-5 col-form-label"><b>Flete</b></label>
						<div class="col-sm-7">
							<input type="text" name="freight" id="freight" class="form-control text-right" inputmode="decimal" value="{{ old('freight', number_format($quotation->freight, 2, ',', '.')) }}">
						</div>
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group row">
						<label class="col-sm-5 col-form-label"><b>Total Exento</b></label>
						<div class="col-sm-7">
							<input type="text" name="tax_exempt" id="tax_exempt" class="form-control text-right" value="{{ number_format($quotation->tax_exempt, 2, ',', '.') }}" readonly>
						</div>
					</div>
					<div class="form-group row">
						<label class="col-sm-5 col-form-label"><b>Base Imponible</b></label>
						<div class="col-sm-7">
							<input type="text" name="tax_base" id="tax_base" class="form-control text-right" value="{{ number_format($quotation->tax_base, 2, ',', '.') }}" readonly>
						</div>
					</div>
					<div class="form-group row">
						<label class="col-sm-5 col-form-label"><b>IVA (<span id="iva_label">{{ $quotation->iva_rate }}</span>%)</b></label>
						<div class="col-sm-7">
							<input type="text" name="iva_amount" id="iva_amount" class="form-control text-right" value="{{ number_format($quotation->iva_amount, 2, ',', '.') }}" readonly>
						</div>
					</div>
					<div class="form-group row">
						<label class="col-sm-5 col-form-label"><b>IGTF</b></label>
						<div class="col-sm-7">
							<input type="text" name="igtf_amount" id="igtf_amount" class="form-control text-right" value="{{ number_format($quotation->igtf_amount, 2, ',', '.') }}" readonly>
						</div>
					</div>
					<div class="form-group row bg-light p-2 rounded">
						<label class="col-sm-5 col-form-label"><b class="text-success" style="font-size: 1.2em;">TOTAL</b></label>
						<div class="col-sm-7">
							<input type="text" name="total" id="total" class="form-control text-right font-weight-bold" value="{{ number_format($quotation->total, 2, ',', '.') }}" readonly style="font-size: 1.2em;">
						</div>
					</div>
				</div>
			</div>

			<hr>

			<div class="row">
				<div class="col-12 text-right">
					<a href="{{ route('admin.quotations.index') }}" class="btn btn-secondary mr-2">Cancelar</a>
					<button type="submit" class="btn btn-success"><i class="fa fa-save mr-2"></i>Actualizar Cotización</button>
				</div>
			</div>
		</form>
	</div>
</div>

@endsection

@section('scripts')
@parent
<script>
$(function() {
	var itemIndex = {{ $quotation->items->count() }};

	// ========================================
	// SESSION HEARTBEAT (keep session alive while form is open)
	// ========================================
	setInterval(function() {
		$.get("{{ route('admin.quotations.heartbeat') }}").fail(function() {});
	}, 5 * 60 * 1000); // every 5 minutes

	// ========================================
	// DESCRIPTION EDITOR (contenteditable + paste image compression)
	// ========================================
	function htmlEscape(str) {
		return $('<div>').text(str || '').html();
	}

	function syncEditorToInput(editor) {
		var $editor = $(editor);
		$editor.closest('.item').find('.description-input').val($editor.html());
	}

	$('body').on('input', '.description-editor', function() {
		syncEditorToInput(this);
	});

	$('body').on('paste', '.description-editor', function(e) {
		var clipboardData = e.originalEvent.clipboardData || window.clipboardData;
		if (!clipboardData) return;

		var items = clipboardData.items;
		if (!items) return;

		var editor = this;
		for (var i = 0; i < items.length; i++) {
			if (items[i].type && items[i].type.indexOf('image') === 0) {
				e.preventDefault();
				var file = items[i].getAsFile();

				if (file.size > 5 * 1024 * 1024) {
					alert('La imagen excede el tamaño máximo permitido (5 MB).');
					return;
				}

				compressAndInsertImage(file, editor);
				return;
			}
		}

		// No image — intercept text paste to normalize smart dashes/quotes that
		// can be lost or rendered as "?" by the PDF font.
		var text = clipboardData.getData('text/plain');
		if (text) {
			e.preventDefault();
			document.execCommand('insertText', false, normalizeText(text));
			syncEditorToInput(editor);
		}
	});

	function compressAndInsertImage(file, editor) {
		var reader = new FileReader();
		reader.onload = function(evt) {
			var img = new Image();
			img.onload = function() {
				var canvas = document.createElement('canvas');
				var maxDim = 1024;
				var w = img.width, h = img.height;

				if (w > maxDim || h > maxDim) {
					var ratio = Math.min(maxDim / w, maxDim / h);
					w = Math.round(w * ratio);
					h = Math.round(h * ratio);
				}

				canvas.width = w;
				canvas.height = h;
				canvas.getContext('2d').drawImage(img, 0, 0, w, h);

				var dataUrl = canvas.toDataURL('image/jpeg', 0.85);
				var imgTag = '<img src="' + dataUrl + '" style="max-width:100%;height:auto;display:block;margin:5px 0;">';

				editor.focus();
				document.execCommand('insertHTML', false, imgTag);
				syncEditorToInput(editor);
			};
			img.src = evt.target.result;
		};
		reader.readAsDataURL(file);
	}

	// ========================================
	// CLIENT SELECTION (inline editable fields)
	// ========================================
	@php
		$clientsForJs = $clients->keyBy('id')->map(function ($c) {
			return [
				'title' => $c->title,
				'document' => $c->document,
				'email' => $c->email,
				'phone' => $c->phone,
				'address' => $c->address,
			];
		});
	@endphp
	var clientsData = {!! json_encode($clientsForJs) !!};

	$('.selectize-client').selectize({
		persist: false,
		sortField: 'text',
		onChange: function(value) {
			if (!value || !clientsData[value]) {
				clearClientFields();
				return;
			}
			fillClientFields(clientsData[value]);
		}
	});

	function fillClientFields(info) {
		$('#cli_title').val(info.title || '');
		$('#cli_document').val(info.document || '');
		$('#cli_email').val(info.email || '');
		$('#cli_phone').val(info.phone || '');
		$('#cli_address').val(info.address || '');
	}

	function clearClientFields() {
		$('#cli_title, #cli_document, #cli_email, #cli_phone, #cli_address').val('');
	}

	$('#btn-new-client').on('click', function() {
		$('#client_id')[0].selectize.clear();
		clearClientFields();
		$('#cli_title').focus();
	});

	// ========================================
	// STATUS COMMENT TOGGLE (show only when status != draft)
	// ========================================
	function toggleStatusComment() {
		var status = $('#status').val();
		if (status === 'draft') {
			$('#status-comment-group').slideUp(150);
		} else {
			$('#status-comment-group').slideDown(150);
		}
	}
	$('#status').on('change', toggleStatusComment);
	toggleStatusComment();

	// ========================================
	// MONEY FORMATTING HELPERS (Spanish: dot thousands, comma decimal)
	// ========================================
	var MONEY_INPUTS_SELECTOR = '.unit-price, .line-total, #subtotal, #discount_1_amount, #discount_2_amount, #freight, #tax_exempt, #tax_base, #iva_amount, #igtf_amount, #total';

	function parseMoney(value) {
		if (value === null || value === undefined || value === '') return 0;
		if (typeof value === 'number') return value;
		var s = String(value).trim();
		if (s === '') return 0;
		var hasComma = s.indexOf(',') >= 0;
		var hasDot = s.indexOf('.') >= 0;
		if (hasComma) {
			s = s.replace(/\./g, '').replace(',', '.');
		} else if (hasDot) {
			var dotCount = s.split('.').length - 1;
			var afterLast = s.length - s.lastIndexOf('.') - 1;
			if (dotCount > 1 || (dotCount === 1 && afterLast === 3)) {
				s = s.replace(/\./g, '');
			}
		}
		var n = parseFloat(s);
		return isNaN(n) ? 0 : n;
	}

	function formatMoney(num) {
		if (typeof num !== 'number') num = parseMoney(num);
		return num.toLocaleString('es-VE', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
	}

	function unformatMoneyForSubmit() {
		$(MONEY_INPUTS_SELECTOR).each(function() {
			$(this).val(parseMoney($(this).val()).toFixed(2));
		});
	}

	$('body').on('blur', MONEY_INPUTS_SELECTOR, function() {
		var raw = parseMoney($(this).val());
		$(this).val(formatMoney(raw));
	});

	// Format all visible money inputs on page load (handles raw old() values
	// after a validation error, where Laravel returns unformatted numbers).
	$(MONEY_INPUTS_SELECTOR).each(function() {
		var v = $(this).val();
		if (v !== '' && v !== null) $(this).val(formatMoney(parseMoney(v)));
	});

	function normalizeText(s) {
		return String(s)
			.replace(/[‐‑‒–—−]/g, '-')
			.replace(/[‘’‚‛]/g, "'")
			.replace(/[“”„‟]/g, '"')
			.replace(/…/g, '...');
	}

	// ========================================
	// EXCHANGE RATES + BCV PRICE MODE
	// ========================================
	// currentRates drives NEW calculations (today's config rates).
	var currentRates = {
		binance: parseFloat($('#rate_binance_input').val()) || 0,
		bcv: parseFloat($('#rate_bcv_input').val()) || 0
	};
	// snapshotFactor derives the USD base from the stored (already adjusted)
	// prices when this quotation was saved in BCV mode.
	var snapshotFactor = 1;
	@if($quotation->price_mode === 'bcv' && $quotation->binance_rate > 0 && $quotation->bcv_rate > 0)
		snapshotFactor = {{ $quotation->binance_rate }} / {{ $quotation->bcv_rate }};
		if (currentRates.binance <= 0 || currentRates.bcv <= 0) {
			currentRates.binance = {{ $quotation->binance_rate }};
			currentRates.bcv = {{ $quotation->bcv_rate }};
		}
	@endif

	function isBcvMode() {
		return $('#price_mode_select').val() === 'bcv';
	}

	function bcvFactor() {
		if (isBcvMode() && currentRates.binance > 0 && currentRates.bcv > 0) {
			return currentRates.binance / currentRates.bcv;
		}
		return 1;
	}

	function recalcRow(row) {
		var base = parseMoney(row.find('.unit-price').val());
		var factor = bcvFactor();
		var effective = base * factor;

		row.find('.unit-price-final').val(effective.toFixed(2));

		if (isBcvMode() && factor !== 1) {
			row.find('.ref-val').text(formatMoney(effective));
			row.find('.price-ref').removeClass('d-none');
		} else {
			row.find('.price-ref').addClass('d-none');
		}

		var qty = parseFloat(row.find('.quantity').val()) || 0;
		var tribPct = parseFloat(row.find('.discount-pct').val()) || 0;
		var lineSubtotal = qty * effective;
		var tributeAmt = lineSubtotal * (tribPct / 100);
		row.find('.discount-amount').val(tributeAmt.toFixed(2));
		row.find('.line-total').val(formatMoney(lineSubtotal + tributeAmt));
		calculateTotals();
	}

	function recalcAll() {
		$('#products-list .item').each(function() { recalcRow($(this)); });
		$('#binance_rate_hidden').val(currentRates.binance);
		$('#bcv_rate_hidden').val(currentRates.bcv);
	}

	$('#price_mode_select').on('change', function() {
		if ($(this).val() === 'bcv' && (currentRates.binance <= 0 || currentRates.bcv <= 0)) {
			alert('Debes configurar las tasas Binance y BCV antes de cotizar en Dólar en Bs a BCV.');
			$(this).val('usd');
		}
		$('#bcv-mode-notice').toggleClass('d-none', !isBcvMode());
		recalcAll();
	});

	$('body').on('change input', '.unit-price, .quantity, .discount-pct', function() {
		recalcRow($(this).closest('.item'));
	});

	// Quotation status — used to skip auto-refresh on locked (accepted) quotations
	// so their snapshot rates and stored prices remain untouched.
	var quotationStatus = @json($quotation->status);
	var quotationLocked = (quotationStatus === 'accepted');

	function fetchAndApplyRates(silent) {
		var $btn = $('#btn-fetch-rates');
		if (!silent) {
			$btn.prop('disabled', true);
			$('#rates-status').html('<span class="text-muted">Consultando...</span>');
		}
		return $.get("{{ route('admin.quotations.fetch_rates') }}")
			.done(function(res) {
				var oldBinance = currentRates.binance;
				var oldBcv = currentRates.bcv;
				$('#rate_binance_input').val(res.binance);
				$('#rate_bcv_input').val(res.bcv);
				currentRates.binance = parseFloat(res.binance) || 0;
				currentRates.bcv = parseFloat(res.bcv) || 0;
				var changed = (oldBinance !== currentRates.binance) || (oldBcv !== currentRates.bcv);
				var time = new Date().toLocaleTimeString();

				if (silent) {
					if (changed && isBcvMode()) {
						$('#rates-status').html('<span class="text-success" style="font-size:10px;">↻ ' + time + ' — Precios recalculados</span>');
					} else {
						$('#rates-status').html('<span class="text-muted" style="font-size:10px;">↻ Última actualización: ' + time + '</span>');
					}
				} else {
					if (res.success) {
						$('#rates-status').html('<span class="text-success">✓ Tasas actualizadas</span>');
					} else {
						$('#rates-status').html('<span class="text-warning">⚠ No se pudo conectar a alguna fuente. Revisa las tasas manualmente.</span>');
					}
				}
				recalcAll();
			})
			.fail(function() {
				if (!silent) {
					$('#rates-status').html('<span class="text-danger">✗ Error al consultar. Ingresa las tasas manualmente.</span>');
				}
			})
			.always(function() { if (!silent) $btn.prop('disabled', false); });
	}

	$('#btn-fetch-rates').on('click', function() { fetchAndApplyRates(false); });

	// Auto-refresh: every 15 minutes. Only on editable quotations — locked ones
	// (accepted) keep their snapshot rates untouched so stored prices don't drift.
	if (!quotationLocked) {
		setTimeout(function() { fetchAndApplyRates(true); }, 1500);
		setInterval(function() { fetchAndApplyRates(true); }, 15 * 60 * 1000);
	}

	$('#btn-save-rates').on('click', function() {
		var binance = parseFloat($('#rate_binance_input').val()) || 0;
		var bcv = parseFloat($('#rate_bcv_input').val()) || 0;
		if (binance <= 0 || bcv <= 0) {
			$('#rates-status').html('<span class="text-danger">Ingresa ambas tasas (mayores a 0).</span>');
			return;
		}
		$.ajax({
			url: "{{ route('admin.quotations.save_rates') }}",
			method: 'POST',
			headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
			data: { binance_rate: binance, bcv_rate: bcv },
			success: function() {
				currentRates.binance = binance;
				currentRates.bcv = bcv;
				$('#rates-status').html('<span class="text-success">✓ Tasas guardadas</span>');
				recalcAll();
			},
			error: function() {
				$('#rates-status').html('<span class="text-danger">✗ Error al guardar.</span>');
			}
		});
	});

	// ========================================
	// REPOPULATE PRODUCTS AFTER A VALIDATION ERROR (old input)
	// ========================================
	function buildOldRow(data) {
		var idx = itemIndex++;
		var isFree = !data.product_id;
		var effective = parseFloat(data.unit_price) || 0;
		var factor = bcvFactor();
		var base = factor ? effective / factor : effective;

		var codeCell = isFree
			? '<input type="text" name="items[' + idx + '][code]" class="form-control form-control-sm" value="' + (data.code || '1000') + '">'
			: '<input type="text" class="form-control form-control-sm" value="' + (data.code || '') + '" readonly><input type="hidden" name="items[' + idx + '][code]" value="' + (data.code || '') + '">';

		var row = '<tr class="item" id="item-' + idx + '">'
			+ '<td>' + codeCell
				+ '<input type="hidden" name="items[' + idx + '][product_id]" value="' + (data.product_id || '') + '">'
				+ '<input type="hidden" name="items[' + idx + '][sort_order]" value="' + idx + '">'
			+ '</td>'
			+ '<td><div class="description-editor" contenteditable="true" data-idx="' + idx + '" data-placeholder="Descripción del producto."></div>'
				+ '<input type="hidden" name="items[' + idx + '][description]" class="description-input" value=""></td>'
			+ '<td><input type="number" name="items[' + idx + '][quantity]" class="form-control form-control-sm text-right quantity" value="' + (data.quantity || 1) + '" min="1" step="1"></td>'
			+ '<td><input type="text" class="form-control form-control-sm text-right unit-price" inputmode="decimal" value="' + formatMoney(base) + '">'
				+ '<input type="hidden" name="items[' + idx + '][unit_price]" class="unit-price-final" value="' + effective.toFixed(2) + '">'
				+ '<div class="price-ref text-info text-right d-none" style="font-size: 11px;">BCV: <span class="ref-val"></span></div></td>'
			+ '<td><input type="number" name="items[' + idx + '][discount_percent]" class="form-control form-control-sm text-right discount-pct" value="' + (data.discount_percent || 0) + '" min="0" max="100" step="0.01">'
				+ '<input type="hidden" name="items[' + idx + '][discount_amount]" class="discount-amount" value="0"></td>'
			+ '<td><input type="text" name="items[' + idx + '][total]" class="form-control form-control-sm text-right line-total font-weight-bold" value="0,00" readonly></td>'
			+ '<td><button type="button" class="btn btn-sm btn-danger btn-remove-item" data-idx="' + idx + '"><i class="fa fa-times"></i></button></td>'
			+ '</tr>';

		$('#products-list').append(row);
		$('#item-' + idx + ' .description-editor').html(data.description || '');
		$('#item-' + idx + ' .description-input').val(data.description || '');
		recalcRow($('#item-' + idx));
		$('#no-products-alert').addClass('d-none');
	}

	// ========================================
	// PRE-POPULATE EXISTING ITEMS
	// ========================================
	@if(is_array(old('items')) && count(old('items')) > 0)
		(function() {
			var oldItems = @json(array_values(old('items')));
			oldItems.forEach(function(data) { buildOldRow(data); });
		})();
	@else
	@foreach($quotation->items as $index => $item)
		(function() {
			var idx = {{ $index }};
			var descHtml = @json($item->description);
			@if($item->product_id)
				var row = `
					<tr class="item" id="item-${idx}">
						<td>
							<input type="text" class="form-control form-control-sm" value="{{ $item->code }}" readonly>
							<input type="hidden" name="items[${idx}][product_id]" value="{{ $item->product_id }}">
							<input type="hidden" name="items[${idx}][code]" value="{{ $item->code }}">
							<input type="hidden" name="items[${idx}][sort_order]" value="${idx}">
						</td>
						<td>
							<div class="description-editor" contenteditable="true" data-idx="${idx}" data-placeholder="Descripción del producto. Puedes pegar imágenes (Ctrl+V)."></div>
							<input type="hidden" name="items[${idx}][description]" class="description-input" value="">
						</td>
						<td>
							<input type="number" name="items[${idx}][quantity]" class="form-control form-control-sm text-right quantity" value="{{ (int) $item->quantity }}" min="1" step="1">
						</td>
						<td>
							<input type="text" class="form-control form-control-sm text-right unit-price" inputmode="decimal" value="{{ number_format($item->unit_price, 2, ',', '.') }}">
							<input type="hidden" name="items[${idx}][unit_price]" class="unit-price-final" value="{{ $item->unit_price }}">
							<div class="price-ref text-info text-right d-none" style="font-size: 11px;">BCV: <span class="ref-val"></span></div>
						</td>
						<td>
							<input type="number" name="items[${idx}][discount_percent]" class="form-control form-control-sm text-right discount-pct" value="{{ $item->discount_percent }}" min="0" max="100" step="0.01">
							<input type="hidden" name="items[${idx}][discount_amount]" class="discount-amount" value="{{ $item->discount_amount }}">
						</td>
						<td>
							<input type="text" name="items[${idx}][total]" class="form-control form-control-sm text-right line-total font-weight-bold" value="{{ number_format($item->total, 2, ',', '.') }}" readonly>
						</td>
						<td>
							<button type="button" class="btn btn-sm btn-danger btn-remove-item" data-idx="${idx}"><i class="fa fa-times"></i></button>
						</td>
					</tr>`;
			@else
				var row = `
					<tr class="item" id="item-${idx}">
						<td>
							<input type="text" name="items[${idx}][code]" class="form-control form-control-sm" value="{{ $item->code }}">
							<input type="hidden" name="items[${idx}][product_id]" value="">
							<input type="hidden" name="items[${idx}][sort_order]" value="${idx}">
						</td>
						<td>
							<div class="description-editor" contenteditable="true" data-idx="${idx}" data-placeholder="Descripción del producto o servicio. Puedes pegar imágenes (Ctrl+V)."></div>
							<input type="hidden" name="items[${idx}][description]" class="description-input" value="">
						</td>
						<td>
							<input type="number" name="items[${idx}][quantity]" class="form-control form-control-sm text-right quantity" value="{{ (int) $item->quantity }}" min="1" step="1">
						</td>
						<td>
							<input type="text" class="form-control form-control-sm text-right unit-price" inputmode="decimal" value="{{ number_format($item->unit_price, 2, ',', '.') }}">
							<input type="hidden" name="items[${idx}][unit_price]" class="unit-price-final" value="{{ $item->unit_price }}">
							<div class="price-ref text-info text-right d-none" style="font-size: 11px;">BCV: <span class="ref-val"></span></div>
						</td>
						<td>
							<input type="number" name="items[${idx}][discount_percent]" class="form-control form-control-sm text-right discount-pct" value="{{ $item->discount_percent }}" min="0" max="100" step="0.01">
							<input type="hidden" name="items[${idx}][discount_amount]" class="discount-amount" value="{{ $item->discount_amount }}">
						</td>
						<td>
							<input type="text" name="items[${idx}][total]" class="form-control form-control-sm text-right line-total font-weight-bold" value="{{ number_format($item->total, 2, ',', '.') }}" readonly>
						</td>
						<td>
							<button type="button" class="btn btn-sm btn-danger btn-remove-item" data-idx="${idx}"><i class="fa fa-times"></i></button>
						</td>
					</tr>`;
			@endif
			$('#products-list').append(row);
			// Inject description HTML after row is in DOM (safer than embedding in template literal)
			$('#item-' + idx + ' .description-editor').html(descHtml);
			$('#item-' + idx + ' .description-input').val(descHtml);
		})();
	@endforeach

	// For pre-populated rows: the hidden .unit-price-final holds the stored
	// (possibly BCV-adjusted) price. Derive the USD base for the visible input
	// and show the reference. The stored value stays as saved (no recalc).
	$('#products-list .item').each(function() {
		var row = $(this);
		var stored = parseFloat(row.find('.unit-price-final').val()) || 0;
		var base = snapshotFactor ? stored / snapshotFactor : stored;
		row.find('.unit-price').val(formatMoney(base));
		if (isBcvMode()) {
			row.find('.ref-val').text(formatMoney(stored));
			row.find('.price-ref').removeClass('d-none');
		}
	});
	@endif

	// ========================================
	// PRODUCT SELECTOR (SELECTIZE)
	// ========================================
	$('.selectize-products').selectize({
		persist: false,
		sortField: 'text',
		onItemAdd: function(value, $item) {
			var data = this.options[value].data;
			if (data) {
				addProductRow(data);
			}
			this.clear(true);
		}
	});

	function addProductRow(product) {
		var idx = itemIndex++;
		var initialDesc = htmlEscape(product.title + (product.description ? ' - ' + product.description : ''));
		var row = `
			<tr class="item" id="item-${idx}">
				<td>
					<input type="text" class="form-control form-control-sm" value="${product.code}" readonly>
					<input type="hidden" name="items[${idx}][product_id]" value="${product.id}">
					<input type="hidden" name="items[${idx}][code]" value="${product.code}">
					<input type="hidden" name="items[${idx}][sort_order]" value="${idx}">
				</td>
				<td>
					<div class="description-editor" contenteditable="true" data-idx="${idx}" data-placeholder="Descripción del producto. Puedes pegar imágenes (Ctrl+V).">${initialDesc}</div>
					<input type="hidden" name="items[${idx}][description]" class="description-input" value="${initialDesc}">
				</td>
				<td>
					<input type="number" name="items[${idx}][quantity]" class="form-control form-control-sm text-right quantity" value="1" min="1" step="1">
				</td>
				<td>
					<input type="text" class="form-control form-control-sm text-right unit-price" inputmode="decimal" value="${formatMoney(product.price)}">
					<input type="hidden" name="items[${idx}][unit_price]" class="unit-price-final" value="${product.price}">
					<div class="price-ref text-info text-right d-none" style="font-size: 11px;">BCV: <span class="ref-val"></span></div>
				</td>
				<td>
					<input type="number" name="items[${idx}][discount_percent]" class="form-control form-control-sm text-right discount-pct" value="0" min="0" max="100" step="0.01">
					<input type="hidden" name="items[${idx}][discount_amount]" class="discount-amount" value="0">
				</td>
				<td>
					<input type="text" name="items[${idx}][total]" class="form-control form-control-sm text-right line-total font-weight-bold" value="${formatMoney(product.price)}" readonly>
				</td>
				<td>
					<button type="button" class="btn btn-sm btn-danger btn-remove-item" data-idx="${idx}"><i class="fa fa-times"></i></button>
				</td>
			</tr>`;
		$('#products-list').append(row);
		recalcRow($('#item-' + idx));
	}

	$('#btn-add-free').on('click', function() {
		var idx = itemIndex++;
		var row = `
			<tr class="item" id="item-${idx}">
				<td>
					<input type="text" name="items[${idx}][code]" class="form-control form-control-sm" value="1000">
					<input type="hidden" name="items[${idx}][product_id]" value="">
					<input type="hidden" name="items[${idx}][sort_order]" value="${idx}">
				</td>
				<td>
					<div class="description-editor" contenteditable="true" data-idx="${idx}" data-placeholder="Descripción del producto o servicio. Puedes pegar imágenes (Ctrl+V)."></div>
					<input type="hidden" name="items[${idx}][description]" class="description-input" value="">
				</td>
				<td>
					<input type="number" name="items[${idx}][quantity]" class="form-control form-control-sm text-right quantity" value="1" min="1" step="1">
				</td>
				<td>
					<input type="text" class="form-control form-control-sm text-right unit-price" inputmode="decimal" value="0,00">
					<input type="hidden" name="items[${idx}][unit_price]" class="unit-price-final" value="0">
					<div class="price-ref text-info text-right d-none" style="font-size: 11px;">BCV: <span class="ref-val"></span></div>
				</td>
				<td>
					<input type="number" name="items[${idx}][discount_percent]" class="form-control form-control-sm text-right discount-pct" value="0" min="0" max="100" step="0.01">
					<input type="hidden" name="items[${idx}][discount_amount]" class="discount-amount" value="0">
				</td>
				<td>
					<input type="text" name="items[${idx}][total]" class="form-control form-control-sm text-right line-total font-weight-bold" value="0,00" readonly>
				</td>
				<td>
					<button type="button" class="btn btn-sm btn-danger btn-remove-item" data-idx="${idx}"><i class="fa fa-times"></i></button>
				</td>
			</tr>`;
		$('#products-list').append(row);
		recalcRow($('#item-' + idx));
	});

	$('body').on('click', '.btn-remove-item', function() {
		$('#item-' + $(this).data('idx')).remove();
		calculateTotals();
		if ($('#products-list .item').length === 0) {
			$('#no-products-alert').removeClass('d-none');
		}
	});

	function calculateTotals() {
		var subtotal = 0;
		$('#products-list .item').each(function() {
			subtotal += parseMoney($(this).find('.line-total').val());
		});
		var disc1Pct = parseFloat($('#discount_1').val()) || 0;
		var disc2Pct = parseFloat($('#discount_2').val()) || 0;
		var disc1Amt = subtotal * (disc1Pct / 100);
		var disc2Amt = subtotal * (disc2Pct / 100);
		var freight = parseMoney($('#freight').val());
		var baseAfterDiscounts = subtotal - disc1Amt - disc2Amt;
		var ivaRate = parseFloat($('#iva_rate').val()) || 0;
		var igtfRate = parseFloat($('#igtf_rate').val()) || 0;
		var taxExempt = (ivaRate === 0) ? baseAfterDiscounts : 0;
		var taxBase = (ivaRate > 0) ? baseAfterDiscounts : 0;
		var ivaAmount = taxBase * (ivaRate / 100);
		var igtfAmount = taxBase * (igtfRate / 100);
		var total = taxBase + taxExempt + ivaAmount + igtfAmount + freight;
		$('#subtotal').val(formatMoney(subtotal));
		$('#discount_1_amount').val(formatMoney(disc1Amt));
		$('#discount_2_amount').val(formatMoney(disc2Amt));
		$('#tax_exempt').val(formatMoney(taxExempt));
		$('#tax_base').val(formatMoney(taxBase));
		$('#iva_amount').val(formatMoney(ivaAmount));
		$('#igtf_amount').val(formatMoney(igtfAmount));
		$('#total').val(formatMoney(total));
	}

	$('#iva_toggle').on('change', function() {
		var rate = $(this).is(':checked') ? 16 : 0;
		$('#iva_rate').val(rate);
		$('#iva_label').text(rate);
		calculateTotals();
	});

	$('#discount_1, #discount_2, #freight').on('input', function() {
		calculateTotals();
	});

	$('#quotation-form').on('submit', function(e) {
		if ($('#products-list .item').length === 0) {
			e.preventDefault();
			$('#no-products-alert').removeClass('d-none');
			$('html, body').animate({ scrollTop: $('#no-products-alert').offset().top - 100 }, 300);
			return false;
		}
		// Sync rate snapshot before submit (price_mode comes from the selector itself)
		$('#binance_rate_hidden').val(currentRates.binance);
		$('#bcv_rate_hidden').val(currentRates.bcv);
		// Convert formatted money displays back to raw numeric values for backend.
		unformatMoneyForSubmit();
	});
});
</script>
@endsection
