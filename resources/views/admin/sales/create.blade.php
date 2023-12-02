@extends('layouts.admin')
@section('content')
    <div class="card">
        <div class="card-header">
            Registrar Venta
        </div>
        <div class="card-body">
            <form action="{{ route('admin.sales.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
				{{-- Fecha --}}
				<div class="row">
					<div class="col-sm-4">
						<div class="form-group {{ $errors->has('registered_at') ? 'has-error' : '' }}">
							<label for="registered_at">Fecha&nbsp;<b class="text-danger">*</b></label>
							<input type="date" id="registered_at" name="registered_at" class="form-control" value="{{ old('registered_at', date('Y-m-d')) }}" required>
							@if ($errors->has('registered_at'))
								<em class="invalid-feedback">
									{{ $errors->first('registered_at') }}
								</em>
							@endif
						</div>
					</div>
				</div>
				{{-- Tipo/Número/Pago --}}
				<div class="row">
					<div class="col-sm-4">
						<div class="form-group {{ $errors->has('invoice_type') ? 'has-error' : '' }}">
							<label for="invoice_type">Tipo&nbsp;<b class="text-danger">*</b></label>
							<select name="invoice_type" id="invoice_type" class="custom-select" required onchange="calculateValues()">
								<option value="nota" {{ old('invoice_type') == 'nota' ? 'selected' : '' }}>Nota de Entrega</option>
								<option value="factura" {{ old('invoice_type') == 'factura' ? 'selected' : '' }}>Factura</option>
							</select>
							@if ($errors->has('invoice_type'))
								<em class="invalid-feedback">
									{{ $errors->first('invoice_type') }}
								</em>
							@endif
						</div>
					</div>
					<div class="col-sm-4">
						<div class="form-group {{ $errors->has('invoice_number') ? 'has-error' : '' }}">
							<label for="invoice_number">Número&nbsp;<b class="text-danger">*</b></label>
							<input type="text" id="invoice_number" name="invoice_number" class="form-control" value="{{ old('invoice_number') }}" required>
							@if ($errors->has('invoice_number'))
								<em class="invalid-feedback">
									{{ $errors->first('invoice_number') }}
								</em>
							@endif
						</div>
					</div>
					<div class="col-sm-4">
						<div class="form-group {{ $errors->has('payment_method') ? 'has-error' : '' }}">
							<label for="payment_method">Forma de Pago&nbsp;<b class="text-danger">*</b></label>
							<select name="payment_method" id="payment_method" class="custom-select" required onchange="calculateValues()">
								<option value="bolivares" {{ old('payment_method') == 'bolivares' ? 'selected' : '' }}>Bolívares</option>
								<option value="dolares" {{ old('payment_method') == 'dolares' ? 'selected' : '' }}>Dólares</option>
								<option value="zelle" {{ old('payment_method') == 'zelle' ? 'selected' : '' }}>Zelle</option>
								<option value="paypal" {{ old('payment_method') == 'paypal' ? 'selected' : '' }}>Paypal</option>
								<option value="binance" {{ old('payment_method') == 'binance' ? 'selected' : '' }}>Binance</option>
								<option value="panama" {{ old('payment_method') == 'panama' ? 'selected' : '' }}>Panamá</option>
							</select>
							@if ($errors->has('payment_method'))
								<em class="invalid-feedback">
									{{ $errors->first('payment_method') }}
								</em>
							@endif
						</div>
					</div>
				</div>
				{{-- Trello --}}
				<div class="form-group {{ $errors->has('trello') ? 'has-error' : '' }}">
                    <label for="trello">Trello&nbsp;<b class="text-danger">*</b></label>
                    <input type="url" id="trello" name="trello" class="form-control" value="{{ old('trello') }}" required>
                    @if ($errors->has('trello'))
                        <em class="invalid-feedback">
                            {{ $errors->first('trello') }}
                        </em>
                    @endif
                </div>
				{{-- Vendedor/Cliente --}}
				<div class="row" id="cliente">
					@if ($user->hasRole('Superadmin'))
						<div class="col-sm-6">
							<div class="form-group {{ $errors->has('seller_id') ? 'has-error' : '' }}">
								<label for="seller_id">Vendedor&nbsp;<b class="text-danger">*</b></label>
								<select name="seller_id" id="seller" class="selectize-seller" required>
									<option value="" selected>Seleccione</option>
									@foreach ($sellers as $seller)
										<option value="{{ $seller->id }}" data-data="{{ json_encode($seller) }}">{{ $seller->user->getFullname() }}</option>
									@endforeach
								</select>
								@if ($errors->has('seller_id'))
									<em class="invalid-feedback">
										{{ $errors->first('seller_id') }}
									</em>
								@endif
							</div>
						</div>
					@else
						<input type="hidden" id="seller" name="seller_id" value="{{ $user->seller->id }}">
					@endif
					<input type="hidden" id="margin_perpetual" value="{{ $user->seller->commission_1 ?? 1 }}">
					<input type="hidden" id="margin_annual" value="{{ $user->seller->commission_2 ?? 1 }}">
					<input type="hidden" id="margin_hardware" value="{{ $user->seller->commission_3 ?? 1 }}">
					<input type="hidden" id="margin_services" value="{{ $user->seller->commission_4 ?? 50 }}">
					<div class="col-sm-6">
						<div class="form-group {{ $errors->has('client_id') ? 'has-error' : '' }}">
							<label for="client_id">Cliente&nbsp;<b class="text-danger">*</b></label>
							<select name="client_id" class="selectize-client" required>
								<option value="">Seleccione</option>
								@foreach ($clients as $client)
									<option value="{{ $client->id }}">{{ $client->getIdentification() }}</option>
								@endforeach
							</select>
							@if ($errors->has('client_id'))
								<em class="invalid-feedback">
									{{ $errors->first('client_id') }}
								</em>
							@endif
						</div>
					</div>
				</div>
				{{-- Productos/Servicios --}}
				<div class="row">
					<div class="col-sm-6">
						<div class="form-group {{ $errors->has('product_id') ? 'has-error' : '' }}">
							<label for="products">Productos</label>
							<select name="products_selected[]" class="selectize-products" multiple>
								<option value="">Seleccione</option>
								@foreach ($categories as $category)
									@if ($category->products->count() > 0)
										<optgroup label="{{ mb_strtoupper($category->title) }}">
											@foreach ($category->products as $product)
												<option value="{{ $product->id }}" data-data="{{ $product->toJson() }}" class="pl-3">{{ $product->title . ' (' . $product->code . ')' }}</option>
											@endforeach
										</optgroup>
									@endif
								@endforeach
							</select>
							@if ($errors->has('product_id'))
								<em class="invalid-feedback">
									{{ $errors->first('product_id') }}
								</em>
							@endif
						</div>
						<div class="table-responsive d-none" id="products-cont">
							<table class="table table-hover align-middle">
								<thead class="bg-light">
									<th class="text-left" width="250" style="min-width: 250px">Producto</th>
									<th class="text-left" width="150" style="min-width: 150px">Código</th>
									<th class="text-left">Tipo</th>
									<th class="text-right">Costo</th>
									<th class="text-right">Precio</th>
									<th class="text-center" width="150" style="min-width: 150px">Cantidad</th>
									<th class="text-right" width="80" style="min-width: 80px">Subtotal</th>
									<th class="text-right" width="80" style="min-width: 80px">Desc.</th>
									<th class="text-right" width="80" style="min-width: 80px">Prov.</th>
									<th class="text-right" width="80" style="min-width: 80px">Total</th>
								</thead>
								<tbody id="products-list"></tbody>
								<tfoot class="bg-light">
									<th colspan="8"></th>
									<th class="text-right">Costo<br><strong id="costo_prods">0.00</strong></th>
									<th class="text-right">Total<br><strong id="total_prods">0.00</strong></th>
								</tfoot>
							</table>
							<input type="hidden" id="provider" name="provider" value="0">
						</div>
					</div>
					<div class="col-sm-6">
						<div class="form-group {{ $errors->has('service_id') ? 'has-error' : '' }}">
							<label for="services">Servicios</label>
							<select name="services_selected[]" class="selectize-services" multiple>
								<option value="">Seleccione</option>
								@foreach ($services as $service)
									<option value="{{ $service->id }}" data-data="{{ $service->toJson() }}">{{ $service->title . ' (' . $service->code . ')' }}</option>
								@endforeach
							</select>
							@if ($errors->has('service_id'))
								<em class="invalid-feedback">
									{{ $errors->first('service_id') }}
								</em>
							@endif
						</div>
						<div class="table-responsive d-none" id="services-cont">
							<table class="table table-hover align-middle">
								<thead class="bg-light">
									<th class="text-left" width="250" style="min-width: 250px">Servicio</th>
									<th class="text-left" width="150" style="min-width: 150px">Código</th>
									<th class="text-right">Precio</th>
									<th class="text-center" width="150" style="min-width: 150px">Cantidad</th>
									<th class="text-right" width="80" style="min-width: 80px">Subtotal</th>
									<th class="text-right" width="80" style="min-width: 80px">Desc.</th>
									<th class="text-right" width="80" style="min-width: 80px">Total</th>
								</thead>
								<tbody id="services-list"></tbody>
								<tfoot class="bg-light">
									<th colspan="6"></th>
									<th class="text-right">Total<br><strong id="total_servs">0.00</strong></th>
								</tfoot>
							</table>
						</div>
					</div>
					<div class="col-12">
						<hr>
					</div>
				</div>
				{{-- Totalización --}}
				<div class="row">
					<div class="col-sm-3">
						<div class="form-group {{ $errors->has('subtotal') ? 'has-error' : '' }}">
							<label for="subtotal">Base Imponible <span class="text-muted">(subtotal)</span>&nbsp;<b class="text-danger">*</b></label>
							<input type="number" id="subtotal" name="subtotal" class="form-control" value="{{ old('subtotal', 0) }}" min="0" required readonly>
							@if ($errors->has('subtotal'))
								<em class="invalid-feedback">
									{{ $errors->first('subtotal') }}
								</em>
							@endif
						</div>
					</div>
					<div class="col-sm-3">
						<div class="form-group {{ $errors->has('cityhall') ? 'has-error' : '' }}">
							<label for="cityhall">Alcaldía <span class="text-muted">(9% sobre subtotal cuando factura)</span>&nbsp;<b class="text-danger">*</b></label>
							<input type="number" id="cityhall" name="cityhall" class="form-control" value="{{ old('cityhall', 0) }}" min="0" required readonly>
							@if ($errors->has('cityhall'))
								<em class="invalid-feedback">
									{{ $errors->first('cityhall') }}
								</em>
							@endif
						</div>
					</div>
					<div class="col-sm-3">
						<div class="form-group {{ $errors->has('iva') ? 'has-error' : '' }}">
							<label for="iva">IVA <span class="text-muted">(16% sobre [subtotal + alcaldía] cuando factura)</span>&nbsp;<b class="text-danger">*</b></label>
							<input type="number" id="iva" name="iva" class="form-control" value="{{ old('iva', 0) }}" min="0" required readonly>
							@if ($errors->has('iva'))
								<em class="invalid-feedback">
									{{ $errors->first('iva') }}
								</em>
							@endif
						</div>
					</div>
					<div class="col-sm-3">
						<div class="form-group {{ $errors->has('igtf') ? 'has-error' : '' }}">
							<label for="igtf">IGTF <span class="text-muted">(3% sobre pago en USD)</span>&nbsp;<b class="text-danger">*</b></label>
							<input type="number" id="igtf" name="igtf" class="form-control" value="{{ old('igtf', 0) }}" min="0" required readonly>
							@if ($errors->has('igtf'))
								<em class="invalid-feedback">
									{{ $errors->first('igtf') }}
								</em>
							@endif
						</div>
					</div>
					<div class="col-sm-3">
						<div class="form-group {{ $errors->has('total') ? 'has-error' : '' }}">
							<label for="total">Total Venta <span class="text-muted">(subtotal + alcaldía + iva + igtf)</span>&nbsp;<b class="text-danger">*</b></label>
							<input type="number" id="total" name="total" class="form-control" value="{{ old('total', 0) }}" min="0" required readonly>
							@if ($errors->has('total'))
								<em class="invalid-feedback">
									{{ $errors->first('total') }}
								</em>
							@endif
						</div>
					</div>
					<div class="col-sm-3">
						<div class="form-group {{ $errors->has('profit') ? 'has-error' : '' }}">
							<label for="profit">Ganancia <span class="text-muted">(total productos - costos)</span>&nbsp;<b class="text-danger">*</b></label>
							<input type="number" id="profit" name="profit" class="form-control" value="{{ old('profit', 0) }}" min="0" required readonly>
							@if ($errors->has('profit'))
								<em class="invalid-feedback">
									{{ $errors->first('profit') }}
								</em>
							@endif
						</div>
					</div>
				</div>
				{{-- Desglose Comisión --}}
				<div class="row">
					<div class="col-sm-3">
						<div class="form-group {{ $errors->has('commission_perpetual') ? 'has-error' : '' }}">
							<label for="commission_perpetual">Comisión Licencias Perpetuas <span class="text-muted">(<span id="margin_1">{{ $user->seller->commission_1 ?? 1 }}</span>%)</span>&nbsp;<b class="text-danger">*</b></label>
							<input type="number" id="commission_perpetual" name="commission_perpetual" class="form-control" value="{{ old('commission_perpetual', 0) }}" min="0" required readonly>
							@if ($errors->has('commission_perpetual'))
								<em class="invalid-feedback">
									{{ $errors->first('commission_perpetual') }}
								</em>
							@endif
						</div>
					</div>
					<div class="col-sm-3">
						<div class="form-group {{ $errors->has('commission_annual') ? 'has-error' : '' }}">
							<label for="commission_annual">Comisión Suscripciones Anuales <span class="text-muted">(<span id="margin_2">{{ $user->seller->commission_2 ?? 1 }}</span>%)</span>&nbsp;<b class="text-danger">*</b></label>
							<input type="number" id="commission_annual" name="commission_annual" class="form-control" value="{{ old('commission_annual', 0) }}" min="0" required readonly>
							@if ($errors->has('commission_annual'))
								<em class="invalid-feedback">
									{{ $errors->first('commission_annual') }}
								</em>
							@endif
						</div>
					</div>
					<div class="col-sm-3">
						<div class="form-group {{ $errors->has('commission_hardware') ? 'has-error' : '' }}">
							<label for="commission_hardware">Comisión Hardware y Otros <span class="text-muted">(<span id="margin_3">{{ $user->seller->commission_3 ?? 1 }}</span>%)</span>&nbsp;<b class="text-danger">*</b></label>
							<input type="number" id="commission_hardware" name="commission_hardware" class="form-control" value="{{ old('commission_hardware', 0) }}" min="0" required readonly>
							@if ($errors->has('commission_hardware'))
								<em class="invalid-feedback">
									{{ $errors->first('commission_hardware') }}
								</em>
							@endif
						</div>
					</div>
					<div class="col-sm-3">
						<div class="form-group {{ $errors->has('commission_services') ? 'has-error' : '' }}">
							<label for="commission_services">Comisión Servicios <span class="text-muted">(<span id="margin_4">{{ $user->seller->commission_4 ?? 50 }}</span>%)</span>&nbsp;<b class="text-danger">*</b></label>
							<input type="number" id="commission_services" name="commission_services" class="form-control" value="{{ old('commission_services', 0) }}" min="0" required readonly>
							@if ($errors->has('commission_services'))
								<em class="invalid-feedback">
									{{ $errors->first('commission_services') }}
								</em>
							@endif
						</div>
					</div>
					<div class="col-sm-3">
						<div class="form-group {{ $errors->has('commission_total') ? 'has-error' : '' }}">
							<label for="commission_total">Comisión Total <span class="text-muted">(productos + servicios)</span>&nbsp;<b class="text-danger">*</b></label>
							<input type="number" id="commission_total" name="commission_total" class="form-control" value="{{ old('commission_total', 0) }}" min="0" required readonly>
							@if ($errors->has('commission_total'))
								<em class="invalid-feedback">
									{{ $errors->first('commission_total') }}
								</em>
							@endif
						</div>
					</div>
				</div>
				{{-- Dólares/Bolivares --}}
				<div class="row d-none" id="payment_currency">
					<div class="col-12">
						<hr>
					</div>
					<div class="col-md-4">
						<br>
						<div class="custom-control custom-radio">
							<input type="radio" id="payment_usd_total" name="payment_currency" class="custom-control-input" value="usd" onchange="calculateValues()" checked>
							<label class="custom-control-label" for="payment_usd_total">Pago total en dólares</label>
						</div>
						<div class="custom-control custom-radio">
							<input type="radio" id="payment_usd_mixed" name="payment_currency" class="custom-control-input" value="mix" onchange="calculateValues()">
							<label class="custom-control-label" for="payment_usd_mixed">Pago en moneda combinada</label>
						</div>
					</div>
					<div class="col-md-4">
						<div class="form-group">
							<label for="payment_amount_usd">Monto a pagar en dólares</label>
							<input type="number" id="payment_amount_usd" name="payment_amount_usd" class="form-control" value="{{ old('payment_amount_usd', 0) }}" min="0" readonly onchange="calculateValues(true)">
						</div>
					</div>
					<div class="col-md-4">
						<div class="form-group">
							<label for="payment_amount_bsf">Monto a pagar en bolívares (expresado en USD)</label>
							<input type="number" id="payment_amount_bsf" name="payment_amount_bsf" class="form-control" value="{{ old('payment_amount_bsf', 0) }}" min="0" readonly>
						</div>
					</div>
					<div class="col-12">
						<hr>
					</div>
				</div>
				{{-- Notas --}}
				<div class="form-group {{ $errors->has('notes') ? 'has-error' : '' }}">
                    <label for="notes">Anotaciones</label>
                    <textarea id="notes" name="notes" rows="1" class="form-control notes" maxlength="300">{{ old('notes') }}</textarea>
                    @if ($errors->has('notes'))
                        <em class="invalid-feedback">
                            {{ $errors->first('notes') }}
                        </em>
                    @endif
                </div>
				{{-- Envío --}}
                <div class="text-center text-md-right mt-4">
					<hr>
                    <input class="btn btn-success" type="submit" value="Registrar">
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@8"></script>
    <script>
		function calculateValues(adjust = false)
		{
			console.log('Calculating...');

			var type = $('#invoice_type').val();
			var method = $('#payment_method').val();
			var margin_perpetual = parseFloat($('#margin_perpetual').val());
			var margin_annual = parseFloat($('#margin_annual').val());
			var margin_hardware = parseFloat($('#margin_hardware').val());
			var margin_services = parseFloat($('#margin_services').val());
			var payment_usd = parseFloat($('#payment_amount_usd').val());
			var payment_total = $('input[name="payment_currency"]:checked').val();

			var costs = costs_perpetual = costs_annual = costs_hardware = subtotal = subtotal_perpetual = subtotal_annual = subtotal_hardware = subtotal_products = subtotal_services = commission_perpetual = commission_annual = commission_hardware = commission_services = commission_total = iva = igtf = cityhall = total = profit = payment_amount_usd = payment_amount_bsf = 0;

			var seller_margin_perpetual = margin_perpetual / 100; // 1%
			var seller_margin_annual = margin_annual / 100; // 1%
			var seller_margin_hardware = margin_hardware / 100; // 1%
			var seller_margin_services = margin_services / 100; // 50%

			if (method != 'bolivares') {
				$('#payment_currency').removeClass('d-none');
			} else {
				$('#payment_currency').addClass('d-none');
			}

			// Calculate total costs for products
			$('.item .provider').each(function() {
				var value = parseFloat($(this).val());
				var group = $(this).attr('rel');
				switch (group) {
					case 'perpetual':
						costs_perpetual += value;
						break;
					case 'annual':
						costs_annual += value;
						break;
					case 'hardware':
						costs_hardware += value;
						break;
				}
			});

			// Calculate total value for products
			$('.item .product').each(function() {
				var value = parseFloat($(this).val());
				var group = $(this).attr('rel');
				switch (group) {
					case 'perpetual':
						subtotal_perpetual += value;
						break;
					case 'annual':
						subtotal_annual += value;
						break;
					case 'hardware':
						subtotal_hardware += value;
						break;
				}
			});

			// Calculate total value for services
			$('.item .service').each(function() {
				var value = parseFloat($(this).val());
				subtotal_services += value;
			});

			subtotal_products = subtotal_perpetual + subtotal_annual + subtotal_hardware;
			subtotal = subtotal_products + subtotal_services;
			costs = costs_perpetual + costs_annual + costs_hardware;

			if (type == 'factura')
			{
				cityhall = subtotal * 0.09;
				iva = (subtotal + cityhall) * 0.16;

				if (method != 'bolivares') {
					if (payment_total == 'usd') {
						igtf = subtotal * 0.03;
					} else {
						igtf = (adjust ? payment_usd : subtotal) * 0.03;
					}
				}
			}

			if (subtotal > 0) {
				profit = subtotal - costs;
				total = subtotal + cityhall + igtf + iva;
				commission_perpetual = (subtotal_perpetual - costs_perpetual) * seller_margin_perpetual;
				commission_annual = (subtotal_annual - costs_annual) * seller_margin_annual;
				commission_hardware = (subtotal_hardware - costs_hardware) * seller_margin_hardware;
				commission_services = subtotal_services * seller_margin_services;
			}

			if (payment_total == 'usd') {
				payment_amount_usd = total;
				payment_amount_bsf = 0;
				$('#payment_amount_usd').attr('readonly', true);
			} else {
				payment_amount_usd = payment_usd > 0 && adjust ? payment_usd : subtotal;
				payment_amount_bsf = payment_usd > 0 ? total - payment_amount_usd : 0;
				$('#payment_amount_usd').attr('readonly', false);
			}

			commission_total = parseFloat(commission_perpetual + commission_annual + commission_hardware + commission_services);

			console.log(subtotal.toFixed(2), cityhall.toFixed(2), iva.toFixed(2), igtf.toFixed(2), total.toFixed(2), profit.toFixed(2), commission_hardware.toFixed(2), commission_services.toFixed(2), payment_amount_usd.toFixed(2), payment_amount_bsf.toFixed(2), commission_total.toFixed(2));
		
			$('#iva').val(iva.toFixed(2));
			$('#igtf').val(igtf.toFixed(2));
			$('#total').val(total.toFixed(2));
			$('#profit').val(profit.toFixed(2));
			$('#provider').val(costs.toFixed(2));
			$('#subtotal').val(subtotal.toFixed(2));
			$('#cityhall').val(cityhall.toFixed(2));
			$('#costo_prods').html(costs.toFixed(2));
			$('#total_prods').html(subtotal_products.toFixed(2));
			$('#total_servs').html(subtotal_services.toFixed(2));
			$('#commission_perpetual').val(commission_perpetual.toFixed(2));
			$('#commission_annual').val(commission_annual.toFixed(2));
			$('#commission_hardware').val(commission_hardware.toFixed(2));
			$('#commission_services').val(commission_services.toFixed(2));
			$('#commission_total').val(commission_total.toFixed(2));
			$('#payment_amount_usd').val(payment_amount_usd.toFixed(2));
			$('#payment_amount_bsf').val(payment_amount_bsf.toFixed(2));
		}

        $(function()
		{
			function productHandler(action)
			{
				return function() {
					var item = arguments[0];
					var data = this.options[item].data;
					if (action == 'ADD') {
						$('#products-list').append('<tr class="item" id="prod-' + data.id + '"><td><b>' + data.title + '</b><input type="hidden" name="products[' + item + '][id]" value="' + data.id + '"><input type="hidden" name="products[' + item + '][group]" value="' + data.group + '"></td><td><i>' + data.code + '</i></td><td><span class="badge badge-secondary">' + data.type + '</span></td><td class="text-right cost">' + data.cost + '</td><td class="text-right price"><input type="hidden" name="products[' + item + '][price]" value="' + data.price + '">' + data.price + '</td><td><input type="number" name="products[' + item + '][quantity]" min="1" value="1" class="form-control p-0 quantity quantity_prod"></td><td><input type="number" min="0" class="form-control p-0 text-right subtotal" value="' + data.price + '" readonly></td><td><input type="number" name="products[' + item + '][discount]" min="0" class="form-control p-0 text-right discount" value="0"></td><td><input type="number" min="0" class="form-control p-0 text-right provider" rel="' + data.group + '" value="' + data.cost + '" readonly></td><td><input type="number" name="products[' + item + '][total]" min="0" class="form-control p-0 text-right total product" rel="' + data.group + '" value="' + data.price + '" readonly></td></tr>');
						$('#products-list #prod-' + data.id + ' .quantity_prod').inputSpinner();
						$('#products-cont').removeClass('d-none');
					} else {
						$('#products-list #prod-' + data.id).remove();
					}
					calculateValues();
				};
			}

			function serviceHandler(action)
			{
				return function() {
					var item = arguments[0];
					var data = this.options[item].data;
					if (action == 'ADD') {
						$('#services-list').append('<tr class="item" id="serv-' + data.id + '"><td><b>' + data.title + '</b><input type="hidden" name="services[' + item + '][id]" value="' + data.id + '"></td><td><i>' + data.code + '</i></td><td class="price text-right"><input type="hidden" name="services[' + item + '][price]" value="' + data.price + '">' + data.price + '</td><td><input type="number" name="services[' + item + '][quantity]" min="1" value="1" class="form-control p-0 quantity quantity_serv"></td><td><input type="number" min="0" class="form-control p-0 text-right subtotal" value="' + data.price + '" readonly></td><td><input type="number" name="services[' + item + '][discount]" min="0" class="form-control p-0 text-right discount" value="0"></td><td><input type="number" name="services[' + item + '][total]" min="0" class="form-control p-0 text-right total service" value="' + data.price + '" readonly></td></tr>');
						$('#services-list #serv-' + data.id + ' .quantity_serv').inputSpinner();
						$('#services-cont').removeClass('d-none');
					} else {
						$('#services-list #serv-' + data.id).remove();
					}
					calculateValues();
				};
			}

			function sellerHandler()
			{
				return function() {
					var item = arguments[0];
					var data = this.options[item].data;
					$('#margin_perpetual').val(data.commission_1);
					$('#margin_1').html(data.commission_1);
					$('#margin_annual').val(data.commission_2);
					$('#margin_2').html(data.commission_2);
					$('#margin_hardware').val(data.commission_3);
					$('#margin_3').html(data.commission_3);
					$('#margin_services').val(data.commission_4);
					$('#margin_4').html(data.commission_4);
					calculateValues();
				};
			}

			function clientHandler(action)
			{
				return function() {
					if (action == 'ADD') {
						console.log('Cliente nuevo agregado');
						var item = arguments[0];
						var name = item.trim();
						var client = '<div class="row new_client"><div class="col-sm-6"><div class="form-group"><label for="title">Razón Social&nbsp;<b class="text-danger">*</b></label><input type="text" id="title" name="cli_title" class="form-control" value="' + name + '" required></div></div><div class="col-sm-2"><div class="form-group"><label for="document">Identificación&nbsp;<b class="text-danger">*</b></label><input type="text" id="document" name="cli_document" class="form-control" required></div></div><div class="col-sm-2"><div class="form-group"><label for="email">Email&nbsp;<b class="text-danger">*</b></label><input type="email" id="email" name="cli_email" class="form-control" required></div></div><div class="col-sm-2"><div class="form-group"><label for="phone">Télefono&nbsp;<b class="text-danger">*</b></label><input type="phone" id="phone" name="cli_phone" class="form-control" required></div></div></div>';
						$('#cliente').after(client);
					} else {
						console.log('Cliente nuevo eliminado');
						$('.new_client').remove();
					}
				};
			}

			$('body').on('change', '.quantity_prod', function() {
				var quantity = $(this).val();
				var parent = $(this).parents('.item');
				var cost = parent.find('.cost').text();
				var price = parent.find('.price').text();
				var total = parseFloat(price) * parseInt(quantity);
				var provider = parseFloat(cost) * parseInt(quantity);
				parent.find('.provider').val(provider);
				parent.find('.subtotal').val(total);
				parent.find('.product').val(total);
				calculateValues();
			});

			$('body').on('change', '.quantity_serv', function() {
				var quantity = $(this).val();
				var parent = $(this).parents('.item');
				var price = parent.find('.price').text();
				var total = parseFloat(price) * parseInt(quantity);
				parent.find('.subtotal').val(total);
				parent.find('.service').val(total);
				calculateValues();
			});

			$('body').on('input', '.discount', function() {
				var discount = $(this).val();
				var parent = $(this).parents('.item');
				var subtotal = parent.find('.subtotal').val();
				var total = parseFloat(subtotal) - parseFloat(discount);
				parent.find('.total').val(total);
				calculateValues();
			});

			$('body').on('change', '#invoice_number', function() {
				var invoice_number = $(this).val();
				$.ajax({
					url: "{{ route('admin.sales.exists') }}",
					type: 'GET',
					data: { invoice_number: invoice_number },
					success: function(data) {
						if (data) {
							$('#invoice_number').addClass('is-invalid');
							$('#invoice_number').focus();
							Swal.fire({
								type: 'error',
								title: 'Error',
								text: 'Ya existe una venta registrada con ese número, que quizá incluso, pertenece a otro vendedor. Por favor, verifique.',
							});
						} else {
							$('#invoice_number').removeClass('is-invalid');
						}
					}
				});
			});

			$('body').on('change', '#document', function() {
				var doc = $(this).val();
				$.ajax({
					url: "{{ route('admin.clients.exists') }}",
					type: 'GET',
					data: { document: doc },
					success: function(data) {
						if (data) {
							$('#document').addClass('is-invalid');
							$('#document').focus();
							Swal.fire({
								type: 'error',
								title: 'Error',
								text: 'Ya existe un cliente con esa identificación',
							});
						} else {
							$('#document').removeClass('is-invalid');
						}
					}
				});
			});

			$('.selectize-products').selectize({
				persist: false,
				sortField: 'text',
				plugins: ["remove_button"],
				onItemAdd: productHandler('ADD'),
				onItemRemove: productHandler('DEL'),
			});

			$('.selectize-services').selectize({
				persist: false,
				sortField: 'text',
				plugins: ["remove_button"],
				onItemAdd: serviceHandler('ADD'),
				onItemRemove: serviceHandler('DEL'),
			});
			
			$('.selectize-seller').selectize({
				persist: false,
				sortField: 'text',
				onItemAdd: sellerHandler(),
			});
			
			$('.selectize-client').selectize({
				create: true,
				persist: false,
				sortField: 'text',
				onOptionAdd: clientHandler('ADD'),
				onOptionRemove: clientHandler('DEL'),
			});

			$('#notes').maxlength({
                threshold: 300
            });
        });
    </script>
@endsection
