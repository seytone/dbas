@extends('layouts.admin')
@section('content')
<div class="card">
	<div class="card-header">
		<b>Editar Cotización #{{ $quotation->formatted_number }}</b>
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
								<input type="date" name="emission_date" id="emission_date" class="form-control" value="{{ $quotation->emission_date->format('Y-m-d') }}" required>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="expiration_date"><b>Fecha de Vencimiento *</b></label>
								<input type="date" name="expiration_date" id="expiration_date" class="form-control" value="{{ $quotation->expiration_date->format('Y-m-d') }}" required>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label for="currency"><b>Moneda</b></label>
								<select name="currency" id="currency" class="form-control">
									<option value="USD" {{ $quotation->currency == 'USD' ? 'selected' : '' }}>Dólar (USD)</option>
									<option value="BS" {{ $quotation->currency == 'BS' ? 'selected' : '' }}>Bolívares (BS)</option>
								</select>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="status"><b>Estado</b></label>
								<select name="status" id="status" class="form-control">
									<option value="draft" {{ $quotation->status == 'draft' ? 'selected' : '' }}>Borrador</option>
									<option value="sent" {{ $quotation->status == 'sent' ? 'selected' : '' }}>Enviada</option>
									<option value="accepted" {{ $quotation->status == 'accepted' ? 'selected' : '' }}>Aceptada</option>
									<option value="rejected" {{ $quotation->status == 'rejected' ? 'selected' : '' }}>Rechazada</option>
								</select>
							</div>
						</div>
					</div>
				</div>
				<div class="col-md-6">
					<h5 class="mb-3"><i class="fa fa-user mr-2"></i>Datos del Cliente</h5>
					<div class="form-group">
						<label for="client_id"><b>Cliente *</b></label>
						<select name="client_id" id="client_id" class="selectize-client" required>
							<option value="">Seleccione un cliente...</option>
							@foreach ($clients as $client)
								<option value="{{ $client->id }}" {{ $quotation->client_id == $client->id ? 'selected' : '' }}>
									{{ $client->getIdentification() }}
								</option>
							@endforeach
						</select>
					</div>
					<div id="client-details" class="{{ $quotation->client_id ? '' : 'd-none' }}">
						<div class="form-group">
							<label>RIF</label>
							<input type="text" class="form-control" id="client_document" value="{{ $quotation->client->document ?? '' }}" readonly>
						</div>
						<div class="form-group">
							<label>Dirección</label>
							<input type="text" class="form-control" id="client_address" value="{{ $quotation->client->address ?? '' }}" readonly>
						</div>
						<div class="form-group">
							<label>Teléfono</label>
							<input type="text" class="form-control" id="client_phone" value="{{ $quotation->client->phone ?? '' }}" readonly>
						</div>
					</div>
				</div>
			</div>

			<hr>

			{{-- PRODUCTOS --}}
			<h5 class="mb-3"><i class="fa fa-list mr-2"></i>Productos</h5>

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
								<th width="90">Desc. (%)</th>
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
							<input type="checkbox" class="custom-control-input" id="iva_toggle" {{ $quotation->iva_rate > 0 ? 'checked' : '' }}>
							<label class="custom-control-label" for="iva_toggle"><b>Aplicar IVA (16%)</b></label>
						</div>
					</div>
					<input type="hidden" name="iva_rate" id="iva_rate" value="{{ $quotation->iva_rate }}">
					<input type="hidden" name="igtf_rate" id="igtf_rate" value="{{ $quotation->igtf_rate }}">
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
							<input type="number" name="subtotal" id="subtotal" class="form-control text-right" step="0.01" value="{{ $quotation->subtotal }}" readonly>
						</div>
					</div>
					<div class="form-group row">
						<label class="col-sm-3 col-form-label"><b>Descuento 1</b></label>
						<div class="col-sm-3">
							<div class="input-group">
								<input type="number" name="discount_1" id="discount_1" class="form-control text-right" step="0.01" value="{{ $quotation->discount_1 }}" min="0" max="100">
								<span class="input-group-text">%</span>
							</div>
						</div>
						<div class="col-sm-6">
							<input type="number" name="discount_1_amount" id="discount_1_amount" class="form-control text-right" step="0.01" value="{{ $quotation->discount_1_amount }}" readonly>
						</div>
					</div>
					<div class="form-group row">
						<label class="col-sm-3 col-form-label"><b>Descuento 2</b></label>
						<div class="col-sm-3">
							<div class="input-group">
								<input type="number" name="discount_2" id="discount_2" class="form-control text-right" step="0.01" value="{{ $quotation->discount_2 }}" min="0" max="100">
								<span class="input-group-text">%</span>
							</div>
						</div>
						<div class="col-sm-6">
							<input type="number" name="discount_2_amount" id="discount_2_amount" class="form-control text-right" step="0.01" value="{{ $quotation->discount_2_amount }}" readonly>
						</div>
					</div>
					<div class="form-group row">
						<label class="col-sm-5 col-form-label"><b>Flete</b></label>
						<div class="col-sm-7">
							<input type="number" name="freight" id="freight" class="form-control text-right" step="0.01" value="{{ $quotation->freight }}" min="0">
						</div>
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group row">
						<label class="col-sm-5 col-form-label"><b>Total Exento</b></label>
						<div class="col-sm-7">
							<input type="number" name="tax_exempt" id="tax_exempt" class="form-control text-right" step="0.01" value="{{ $quotation->tax_exempt }}" readonly>
						</div>
					</div>
					<div class="form-group row">
						<label class="col-sm-5 col-form-label"><b>Base Imponible</b></label>
						<div class="col-sm-7">
							<input type="number" name="tax_base" id="tax_base" class="form-control text-right" step="0.01" value="{{ $quotation->tax_base }}" readonly>
						</div>
					</div>
					<div class="form-group row">
						<label class="col-sm-5 col-form-label"><b>IVA (<span id="iva_label">{{ $quotation->iva_rate }}</span>%)</b></label>
						<div class="col-sm-7">
							<input type="number" name="iva_amount" id="iva_amount" class="form-control text-right" step="0.01" value="{{ $quotation->iva_amount }}" readonly>
						</div>
					</div>
					<div class="form-group row">
						<label class="col-sm-5 col-form-label"><b>IGTF</b></label>
						<div class="col-sm-7">
							<input type="number" name="igtf_amount" id="igtf_amount" class="form-control text-right" step="0.01" value="{{ $quotation->igtf_amount }}" readonly>
						</div>
					</div>
					<div class="form-group row bg-light p-2 rounded">
						<label class="col-sm-5 col-form-label"><b class="text-success" style="font-size: 1.2em;">TOTAL</b></label>
						<div class="col-sm-7">
							<input type="number" name="total" id="total" class="form-control text-right font-weight-bold" step="0.01" value="{{ $quotation->total }}" readonly style="font-size: 1.2em;">
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
	// CLIENT SELECTION (data passed via JS)
	// ========================================
	var clientsData = @json($clients->keyBy('id')->map(function ($c) {
		return ['document' => $c->document, 'address' => $c->address, 'phone' => $c->phone];
	}));

	$('.selectize-client').selectize({
		persist: false,
		sortField: 'text',
		onChange: function(value) {
			if (!value || !clientsData[value]) {
				$('#client_document').val('');
				$('#client_address').val('');
				$('#client_phone').val('');
				$('#client-details').addClass('d-none');
				return;
			}
			var info = clientsData[value];
			$('#client_document').val(info.document || '');
			$('#client_address').val(info.address || '');
			$('#client_phone').val(info.phone || '');
			$('#client-details').removeClass('d-none');
		}
	});

	// ========================================
	// PRE-POPULATE EXISTING ITEMS
	// ========================================
	@foreach($quotation->items as $index => $item)
		(function() {
			var idx = {{ $index }};
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
							<input type="text" class="form-control form-control-sm" value="{{ addslashes($item->product->title ?? $item->description) }}" readonly>
							<input type="hidden" name="items[${idx}][description]" value="{{ addslashes($item->description) }}">
						</td>
						<td>
							<input type="number" name="items[${idx}][quantity]" class="form-control form-control-sm text-right quantity" value="{{ (int) $item->quantity }}" min="1" step="1">
						</td>
						<td>
							<input type="number" name="items[${idx}][unit_price]" class="form-control form-control-sm text-right unit-price" value="{{ $item->unit_price }}" min="0" step="0.01">
						</td>
						<td>
							<input type="number" name="items[${idx}][discount_percent]" class="form-control form-control-sm text-right discount-pct" value="{{ $item->discount_percent }}" min="0" max="100" step="0.01">
							<input type="hidden" name="items[${idx}][discount_amount]" class="discount-amount" value="{{ $item->discount_amount }}">
						</td>
						<td>
							<input type="number" name="items[${idx}][total]" class="form-control form-control-sm text-right line-total font-weight-bold" value="{{ $item->total }}" readonly>
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
							<textarea name="items[${idx}][description]" class="form-control form-control-sm" rows="2" required>{{ $item->description }}</textarea>
						</td>
						<td>
							<input type="number" name="items[${idx}][quantity]" class="form-control form-control-sm text-right quantity" value="{{ (int) $item->quantity }}" min="1" step="1">
						</td>
						<td>
							<input type="number" name="items[${idx}][unit_price]" class="form-control form-control-sm text-right unit-price" value="{{ $item->unit_price }}" min="0" step="0.01">
						</td>
						<td>
							<input type="number" name="items[${idx}][discount_percent]" class="form-control form-control-sm text-right discount-pct" value="{{ $item->discount_percent }}" min="0" max="100" step="0.01">
							<input type="hidden" name="items[${idx}][discount_amount]" class="discount-amount" value="{{ $item->discount_amount }}">
						</td>
						<td>
							<input type="number" name="items[${idx}][total]" class="form-control form-control-sm text-right line-total font-weight-bold" value="{{ $item->total }}" readonly>
						</td>
						<td>
							<button type="button" class="btn btn-sm btn-danger btn-remove-item" data-idx="${idx}"><i class="fa fa-times"></i></button>
						</td>
					</tr>`;
			@endif
			$('#products-list').append(row);
		})();
	@endforeach

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
		var row = `
			<tr class="item" id="item-${idx}">
				<td>
					<input type="text" class="form-control form-control-sm" value="${product.code}" readonly>
					<input type="hidden" name="items[${idx}][product_id]" value="${product.id}">
					<input type="hidden" name="items[${idx}][code]" value="${product.code}">
					<input type="hidden" name="items[${idx}][sort_order]" value="${idx}">
				</td>
				<td>
					<input type="text" class="form-control form-control-sm" value="${product.title}" readonly>
					<input type="hidden" name="items[${idx}][description]" value="${product.title}${product.description ? ' - ' + product.description : ''}">
				</td>
				<td>
					<input type="number" name="items[${idx}][quantity]" class="form-control form-control-sm text-right quantity" value="1" min="1" step="1">
				</td>
				<td>
					<input type="number" name="items[${idx}][unit_price]" class="form-control form-control-sm text-right unit-price" value="${product.price}" min="0" step="0.01">
				</td>
				<td>
					<input type="number" name="items[${idx}][discount_percent]" class="form-control form-control-sm text-right discount-pct" value="0" min="0" max="100" step="0.01">
					<input type="hidden" name="items[${idx}][discount_amount]" class="discount-amount" value="0">
				</td>
				<td>
					<input type="number" name="items[${idx}][total]" class="form-control form-control-sm text-right line-total font-weight-bold" value="${product.price}" readonly>
				</td>
				<td>
					<button type="button" class="btn btn-sm btn-danger btn-remove-item" data-idx="${idx}"><i class="fa fa-times"></i></button>
				</td>
			</tr>`;
		$('#products-list').append(row);
		calculateLineTotal($('#item-' + idx));
	}

	$('#btn-add-free').on('click', function() {
		var idx = itemIndex++;
		var row = `
			<tr class="item" id="item-${idx}">
				<td>
					<input type="text" name="items[${idx}][code]" class="form-control form-control-sm" value="LIBRE">
					<input type="hidden" name="items[${idx}][product_id]" value="">
					<input type="hidden" name="items[${idx}][sort_order]" value="${idx}">
				</td>
				<td>
					<textarea name="items[${idx}][description]" class="form-control form-control-sm" rows="2" placeholder="Descripción del producto o servicio..." required></textarea>
				</td>
				<td>
					<input type="number" name="items[${idx}][quantity]" class="form-control form-control-sm text-right quantity" value="1" min="1" step="1">
				</td>
				<td>
					<input type="number" name="items[${idx}][unit_price]" class="form-control form-control-sm text-right unit-price" value="0" min="0" step="0.01">
				</td>
				<td>
					<input type="number" name="items[${idx}][discount_percent]" class="form-control form-control-sm text-right discount-pct" value="0" min="0" max="100" step="0.01">
					<input type="hidden" name="items[${idx}][discount_amount]" class="discount-amount" value="0">
				</td>
				<td>
					<input type="number" name="items[${idx}][total]" class="form-control form-control-sm text-right line-total font-weight-bold" value="0.00" readonly>
				</td>
				<td>
					<button type="button" class="btn btn-sm btn-danger btn-remove-item" data-idx="${idx}"><i class="fa fa-times"></i></button>
				</td>
			</tr>`;
		$('#products-list').append(row);
	});

	$('body').on('click', '.btn-remove-item', function() {
		$('#item-' + $(this).data('idx')).remove();
		calculateTotals();
		if ($('#products-list .item').length === 0) {
			$('#no-products-alert').removeClass('d-none');
		}
	});

	$('body').on('change input', '.quantity, .unit-price, .discount-pct', function() {
		calculateLineTotal($(this).closest('.item'));
	});

	function calculateLineTotal(row) {
		var qty = parseFloat(row.find('.quantity').val()) || 0;
		var price = parseFloat(row.find('.unit-price').val()) || 0;
		var discPct = parseFloat(row.find('.discount-pct').val()) || 0;
		var lineSubtotal = qty * price;
		var discountAmt = lineSubtotal * (discPct / 100);
		var lineTotal = lineSubtotal - discountAmt;
		row.find('.discount-amount').val(discountAmt.toFixed(2));
		row.find('.line-total').val(lineTotal.toFixed(2));
		calculateTotals();
	}

	function calculateTotals() {
		var subtotal = 0;
		$('#products-list .item').each(function() {
			subtotal += parseFloat($(this).find('.line-total').val()) || 0;
		});
		var disc1Pct = parseFloat($('#discount_1').val()) || 0;
		var disc2Pct = parseFloat($('#discount_2').val()) || 0;
		var disc1Amt = subtotal * (disc1Pct / 100);
		var disc2Amt = subtotal * (disc2Pct / 100);
		var freight = parseFloat($('#freight').val()) || 0;
		var baseAfterDiscounts = subtotal - disc1Amt - disc2Amt;
		var ivaRate = parseFloat($('#iva_rate').val()) || 0;
		var igtfRate = parseFloat($('#igtf_rate').val()) || 0;
		var taxExempt = (ivaRate === 0) ? baseAfterDiscounts : 0;
		var taxBase = (ivaRate > 0) ? baseAfterDiscounts : 0;
		var ivaAmount = taxBase * (ivaRate / 100);
		var igtfAmount = taxBase * (igtfRate / 100);
		var total = taxBase + taxExempt + ivaAmount + igtfAmount + freight;
		$('#subtotal').val(subtotal.toFixed(2));
		$('#discount_1_amount').val(disc1Amt.toFixed(2));
		$('#discount_2_amount').val(disc2Amt.toFixed(2));
		$('#tax_exempt').val(taxExempt.toFixed(2));
		$('#tax_base').val(taxBase.toFixed(2));
		$('#iva_amount').val(ivaAmount.toFixed(2));
		$('#igtf_amount').val(igtfAmount.toFixed(2));
		$('#total').val(total.toFixed(2));
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
	});
});
</script>
@endsection
