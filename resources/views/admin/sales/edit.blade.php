@extends('layouts.admin')
@section('content')
    <div class="card">
        <div class="card-header">
            Edición de Venta
        </div>
        <div class="card-body">
			<input type="hidden" id="fee_mercadolibre" value="{{ $feeMercadolibre }}">
            <form action="{{ route('admin.sales.update', [$sale->id]) }}" method="POST" enctype="multipart/form-data">
                @csrf
				@method('PUT')
				{{-- Fecha --}}
				<div class="row">
					<div class="col-sm-4">
						<div class="form-group {{ $errors->has('registered_at') ? 'has-error' : '' }}">
							<label for="registered_at">Fecha&nbsp;<b class="text-danger">*</b></label>
							<input type="date" id="registered_at" name="registered_at" class="form-control" value="{{ old('registered_at', isset($sale) ? date('Y-m-d', strtotime($sale->registered_at)) : date('Y-m-d')) }}" required>
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
								<option value="nota" {{ $sale->invoice_type == 'nota' ? 'selected' : '' }}>Nota de Entrega</option>
								<option value="factura" {{ $sale->invoice_type == 'factura' ? 'selected' : '' }}>Factura</option>
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
							<input type="text" id="invoice_number" name="invoice_number" class="form-control" value="{{ old('invoice_number', isset($sale) ? $sale->invoice_number : '') }}" required>
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
								<option value="bolivares" {{ $sale->payment_method == 'bolivares' ? 'selected' : '' }}>Bolívares</option>
								<option value="dolares" {{ $sale->payment_method == 'dolares' ? 'selected' : '' }}>Dólares</option>
								<option value="zelle" {{ $sale->payment_method == 'zelle' ? 'selected' : '' }}>Zelle</option>
								<option value="paypal" {{ $sale->payment_method == 'paypal' ? 'selected' : '' }}>Paypal</option>
								<option value="binance" {{ $sale->payment_method == 'binance' ? 'selected' : '' }}>Binance</option>
								<option value="panama" {{ $sale->payment_method == 'panama' ? 'selected' : '' }}>Panamá</option>
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
                    <input type="url" id="trello" name="trello" class="form-control" value="{{ old('trello', isset($sale) ? $sale->trello : '') }}" required>
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
										<option value="{{ $seller->id }}" {{ $sale->seller_id == $seller->id ? 'selected' : '' }} data-data="{{ json_encode($seller) }}">{{ $seller->user->getFullname() }}</option>
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
						<input type="hidden" id="seller" name="seller_id" value="{{ $user_seller->id }}">
					@endif
					<input type="hidden" id="margin_perpetual" value="{{ $user_seller->commission_1 ?? 1 }}">
					<input type="hidden" id="margin_annual" value="{{ $user_seller->commission_2 ?? 1 }}">
					<input type="hidden" id="margin_hardware" value="{{ $user_seller->commission_3 ?? 1 }}">
					<input type="hidden" id="margin_services" value="{{ $user_seller->commission_4 ?? 50 }}">
					<div class="col-sm-6">
						<div class="form-group {{ $errors->has('client_id') ? 'has-error' : '' }}">
							<label for="client_id">Cliente&nbsp;<b class="text-danger">*</b></label>
							<select name="client_id" class="selectize-client" required>
								<option value="">Seleccione</option>
								@foreach ($clients as $client)
									<option value="{{ $client->id }}" {{ $sale->client_id == $client->id ? 'selected' : '' }}>{{ $client->getIdentification() }}</option>
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
					<div class="col-12">
						<div class="form-group {{ $errors->has('product_id') ? 'has-error' : '' }}">
							<div>
								<label for="products">Productos</label>
								<a href="#" class="link-primary float-right new-item" rel="producto"><small>NUEVO <i class="fa fa-wd fa-plus"></i></small></a>
							</div>
							<select name="products_selected[]" class="selectize-products" multiple>
								<option value="">Seleccione</option>
								@foreach ($categories as $category)
									@if ($category->products->count() > 0)
										<optgroup label="{{ mb_strtoupper($category->title) }}">
											@foreach ($category->products as $product)
												<option value="{{ $product->id }}" {{ in_array($product->id, $sale_products) ? 'selected' : '' }} data-data="{{ $product->toJson() }}" class="pl-3">{{ $product->title . ' (' . $product->code . ')' }}</option>
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
						<div class="table-responsive">
							<table class="table table-hover align-middle">
								<thead class="bg-light">
									<th class="text-left" width="250" style="min-width: 250px">Producto</th>
									<th class="text-left" width="150" style="min-width: 150px">Código</th>
									<th class="text-left">Tipo</th>
									<th class="text-right">Costo</th>
									<th class="text-right">Precio</th>
									<th class="text-center" width="150" style="min-width: 150px">Cantidad</th>
									<th class="text-right" width="80" style="min-width: 80px">Subtotal</th>
									<th class="text-center" width="80" style="min-width: 80px" title="Mercado Libre">ML</th>
									<th class="text-right" width="80" style="min-width: 80px">Desc.</th>
									<th class="text-right" width="80" style="min-width: 80px">Prov.</th>
									<th class="text-right" width="80" style="min-width: 80px">Total</th>
								</thead>
								<tbody id="products-list">
									@php
										$total_prods = 0;
									@endphp
									@foreach ($sale->products as $prod)
										@isset ($prod->product)
											<tr class="item" id="prod-{{ $prod->product->id }}">
												<td>
													<b>{{ $prod->product->title }}</b>
													<input type="hidden" name="products[{{ $prod->product->id }}][id]" value="{{ $prod->product->id }}">
												</td>
												<td>
													<i>{{ $prod->product->code }}</i>
												</td>
												<td>
													<span class="badge badge-secondary">{{ $prod->product->type }}</span>
												</td>
												<td class="text-right cost">
													{{ $prod->product->cost }}
												</td>
												<td class="text-right price">
													<input type="hidden" name="products[{{ $prod->product->id }}][price]" value="{{ $prod->price }}">
													{{ $prod->price }}
												</td>
												<td>
													<input type="number" name="products[{{ $prod->product->id }}][quantity]" min="1" value="{{ $prod->quantity }}" class="form-control p-0 quantity quantity_prod">
												</td>
												<td>
													<input type="number" min="0" step=".01" class="form-control p-0 text-right subtotal" value="{{ $prod->quantity * $prod->price }}" readonly>
												</td>
												<td>
													<div class="form-check">
														<input type="checkbox" name="products[{{ $prod->product->id }}][ml]" {{ $prod->mercadolibre == 1 ? 'checked' : '' }} class="form-check-input fee_mercadolibre mt-0 ml-0" title="Comisión: 0.00" style="top: -6px;">
													</div>
												</td>
												<td>
													<input type="number" name="products[{{ $prod->product->id }}][discount]" min="0" step=".01" value="{{ $prod->discount }}" class="form-control p-0 text-right discount">
												</td>
												<td>
													<input type="number" min="0" step=".01" class="form-control p-0 text-right provider" rel="{{ $prod->product->group }}" value="{{ $prod->quantity * $prod->product->cost }}" readonly>
												</td>
												<td>
													<input type="number" name="products[{{ $prod->product->id }}][total]" min="0" step=".01" value="{{ $prod->total }}" class="form-control p-0 text-right total product" rel="{{ $prod->product->group }}" readonly>
												</td>
											</tr>
											@php
												$total_prods += $prod->total;
											@endphp
										@endisset
									@endforeach
								</tbody>
								<tfoot class="bg-light">
									<th colspan="9"></th>
									<th class="text-right">Costo<br><strong id="costo_prods">{{ number_format($sale->provider, 2, '.', ',') }}</strong></th>
									<th class="text-right">Total<br><strong id="total_prods">{{ number_format($total_prods, 2, '.', ',') }}</strong></th>
								</tfoot>
							</table>
							<input type="hidden" id="provider" name="provider" value="{{ $sale->provider }}">
						</div>
					</div>
					<div class="col-12">
						<div class="form-group {{ $errors->has('service_id') ? 'has-error' : '' }}">
							<div>
								<label for="services">Servicios</label>
								<a href="#" class="link-primary float-right new-item" rel="servicio"><small>NUEVO <i class="fa fa-wd fa-plus"></i></small></a>
							</div>
							<select name="services_selected[]" class="selectize-services" multiple>
								<option value="">Seleccione</option>
								@foreach ($services as $service)
									<option value="{{ $service->id }}" {{ in_array($service->id, $sale_services) ? 'selected' : '' }} data-data="{{ $service->toJson() }}">{{ $service->title . ' (' . $service->code . ')' }}</option>
								@endforeach
							</select>
							@if ($errors->has('service_id'))
								<em class="invalid-feedback">
									{{ $errors->first('service_id') }}
								</em>
							@endif
						</div>
						<div class="table-responsive">
							<table class="table table-hover align-middle">
								<thead class="bg-light">
									<th class="text-left" width="250" style="min-width: 250px">Servicio</th>
									<th class="text-left" width="150" style="min-width: 150px">Código</th>
									<th class="text-right">Precio</th>
									<th class="text-center" width="150" style="min-width: 150px">Cantidad</th>
									<th class="text-center" width="80" style="min-width: 80px">Soporte</th>
									<th class="text-right" width="80" style="min-width: 80px">Subtotal</th>
									<th class="text-right" width="80" style="min-width: 80px">Desc.</th>
									<th class="text-right" width="80" style="min-width: 80px">Total</th>
								</thead>
								<tbody id="services-list">
									@php
										$total_servs = 0;
									@endphp
									@foreach ($sale->services as $serv)
										@isset ($serv->service)
											<tr class="item" id="serv-{{ $serv->service->id }}">
												<td>
													<b>{{ $serv->service->title }}</b>
													<input type="hidden" name="services[{{ $serv->service->id }}][id]" value="{{ $serv->service->id }}">
												</td>
												<td>
													<i>{{ $serv->service->code }}</i>
												</td>
												<td class="price text-right">
													<input type="hidden" name="services[{{ $serv->service->id }}][price]" value="{{ $serv->price }}">
													{{ $serv->price }}
												</td>
												<td>
													<input type="number" name="services[{{ $serv->service->id }}][quantity]" min="1" value="{{ $serv->quantity }}" class="form-control p-0 quantity quantity_serv">
												</td>
												<td>
													<div class="form-check">
														<input type="checkbox" name="services[{{ $serv->service->id }}][support]" {{ $serv->support == 1 ? 'checked' : '' }} class="form-check-input support mt-0 ml-0" style="top: -6px;">
													</div>
												</td>
												<td>
													<input type="number" min="0" step=".01" class="form-control p-0 text-right subtotal" value="{{ $serv->price }}" readonly>
												</td>
												<td>
													<input type="number" name="services[{{ $serv->service->id }}][discount]" min="0" step=".01" value="{{ $serv->discount }}" class="form-control p-0 text-right discount">
												</td>
												<td>
													<input type="number" name="services[{{ $serv->service->id }}][total]" min="0" step=".01" value="{{ $serv->total }}" class="form-control p-0 text-right total service" readonly>
												</td>
											</tr>
											@php
												$total_servs += $serv->total;
											@endphp
										@endisset
									@endforeach
								</tbody>
								<tfoot class="bg-light">
									<th colspan="7"></th>
									<th class="text-right">Total<br><strong id="total_servs">{{ number_format($total_servs, 2, '.', ',') }}</strong></th>
								</tfoot>
							</table>
						</div>
					</div>
					<div class="col-12">
						<hr>
					</div>
					<!-- Modal -->
					<div id="new-item" class="modal fade" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="new-item-title" aria-hidden="true">
						<div class="modal-dialog modal-dialog-centered modal-xl">
							<div class="modal-content">
								<div class="modal-header">
									<h5 class="modal-title" id="new-item-title">Nuevo registro</h5>
									<button type="button" class="close" data-dismiss="modal" aria-label="Close">
										<span aria-hidden="true">&times;</span>
									</button>
								</div>
								<div class="modal-body">
									
								</div>
								<div class="modal-footer">
									<button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
									<button type="button" class="btn btn-success" id="save-item">Guardar</button>
								</div>
							</div>
						</div>
					</div>
				</div>
				{{-- Totalización --}}
				<div class="row">
					<div class="col-sm-3">
						<div class="form-group {{ $errors->has('subtotal') ? 'has-error' : '' }}">
							<label for="subtotal">Base Imponible <span class="text-muted">(subtotal)</span>&nbsp;<b class="text-danger">*</b></label>
							<input type="number" id="subtotal" name="subtotal" class="form-control" value="{{ old('subtotal', isset($sale) ? $sale->subtotal : 0) }}" min="0" step=".01" required readonly>
							@if ($errors->has('subtotal'))
								<em class="invalid-feedback">
									{{ $errors->first('subtotal') }}
								</em>
							@endif
						</div>
					</div>
					<div class="col-sm-3">
						<div class="form-group {{ $errors->has('cityhall') ? 'has-error' : '' }}">
							<label for="cityhall">Alcaldía <span class="text-muted">(7% sobre subtotal cuando factura)</span>&nbsp;<b class="text-danger">*</b></label>
							<input type="number" id="cityhall" name="cityhall" class="form-control" value="{{ old('cityhall', isset($sale) ? $sale->cityhall : 0) }}" min="0" step=".01" required readonly>
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
							<input type="number" id="iva" name="iva" class="form-control" value="{{ old('iva', isset($sale) ? $sale->iva : 0) }}" min="0" step=".01" required readonly>
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
							<input type="number" id="igtf" name="igtf" class="form-control" value="{{ old('igtf', isset($sale) ? $sale->igtf : 0) }}" min="0" step=".01" required readonly>
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
							<input type="number" id="total" name="total" class="form-control" value="{{ old('total', isset($sale) ? $sale->total : 0) }}" min="0" step=".01" required readonly>
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
							<input type="number" id="profit" name="profit" class="form-control" value="{{ old('profit', isset($sale) ? $sale->profit : 0) }}" min="0" step=".01" required readonly>
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
							<label for="commission_perpetual">Comisión Licencias Perpetuas <span class="text-muted">(<span id="margin_p">{{ $user_seller->commission_1 ?? 1 }}</span>%)</span>&nbsp;<b class="text-danger">*</b></label>
							<input type="number" id="commission_perpetual" name="commission_perpetual" class="form-control" value="{{ old('commission_perpetual', isset($sale) ? $sale->commission_perpetual : 0) }}" min="0" step=".01" required readonly>
							@if ($errors->has('commission_perpetual'))
								<em class="invalid-feedback">
									{{ $errors->first('commission_perpetual') }}
								</em>
							@endif
						</div>
					</div>
					<div class="col-sm-3">
						<div class="form-group {{ $errors->has('commission_annual') ? 'has-error' : '' }}">
							<label for="commission_annual">Comisión Suscripciones Anuales <span class="text-muted">(<span id="margin_p">{{ $user_seller->commission_2 ?? 1 }}</span>%)</span>&nbsp;<b class="text-danger">*</b></label>
							<input type="number" id="commission_annual" name="commission_annual" class="form-control" value="{{ old('commission_annual', isset($sale) ? $sale->commission_annual : 0) }}" min="0" step=".01" required readonly>
							@if ($errors->has('commission_annual'))
								<em class="invalid-feedback">
									{{ $errors->first('commission_annual') }}
								</em>
							@endif
						</div>
					</div>
					<div class="col-sm-3">
						<div class="form-group {{ $errors->has('commission_hardware') ? 'has-error' : '' }}">
							<label for="commission_hardware">Comisión Hardware y Otros <span class="text-muted">(<span id="margin_p">{{ $user_seller->commission_3 ?? 1 }}</span>%)</span>&nbsp;<b class="text-danger">*</b></label>
							<input type="number" id="commission_hardware" name="commission_hardware" class="form-control" value="{{ old('commission_hardware', isset($sale) ? $sale->commission_hardware : 0) }}" min="0" step=".01" required readonly>
							@if ($errors->has('commission_hardware'))
								<em class="invalid-feedback">
									{{ $errors->first('commission_hardware') }}
								</em>
							@endif
						</div>
					</div>
					<div class="col-sm-3">
						<div class="form-group {{ $errors->has('commission_services') ? 'has-error' : '' }}">
							<label for="commission_services">Comisión Servicios <span class="text-muted">(<span id="margin_s">{{ $user_seller->commission_4 ?? 50 }}</span>%)</span>&nbsp;<b class="text-danger">*</b></label>
							<input type="number" id="commission_services" name="commission_services" class="form-control" value="{{ old('commission_services', isset($sale) ? $sale->commission_servicesices : 0) }}" min="0" step=".01" required readonly>
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
							<input type="number" id="commission_total" name="commission_total" class="form-control" value="{{ old('commission_total', isset($sale) ? $sale->commission : 0) }}" min="0" step=".01" required readonly>
							@if ($errors->has('commission_total'))
								<em class="invalid-feedback">
									{{ $errors->first('commission_total') }}
								</em>
							@endif
						</div>
					</div>
				</div>
				{{-- Dólares/Bolivares --}}
				<div class="row {{ $sale->payment_method == 'bolivares' ? 'd-none' : '' }}" id="payment_currency">
					<div class="col-12">
						<hr>
					</div>
					<div class="col-md-4">
						<br>
						<div class="custom-control custom-radio">
							<input type="radio" id="payment_usd_total" name="payment_currency" class="custom-control-input" value="usd" onchange="calculateValues()" {{ $sale->payment_currency == 'usd' ? 'checked' : '' }}>
							<label class="custom-control-label" for="payment_usd_total">Pago total en dólares</label>
						</div>
						<div class="custom-control custom-radio">
							<input type="radio" id="payment_usd_mixed" name="payment_currency" class="custom-control-input" value="mix" onchange="calculateValues()" {{ $sale->payment_currency == 'mix' ? 'checked' : '' }}>
							<label class="custom-control-label" for="payment_usd_mixed">Pago en moneda combinada</label>
						</div>
					</div>
					<div class="col-md-4">
						<div class="form-group">
							<label for="payment_amount_usd">Monto a pagar en dólares</label>
							<input type="number" id="payment_amount_usd" name="payment_amount_usd" class="form-control" value="{{ old('payment_amount_usd', isset($sale) ? $sale->payment_amount_usd : 0) }}" min="0" step=".01" {{ $sale->payment_currency == 'usd' ? 'readonly' : '' }} onchange="calculateValues(true)">
						</div>
					</div>
					<div class="col-md-4">
						<div class="form-group">
							<label for="payment_amount_bsf">Monto a pagar en bolívares (expresado en USD)</label>
							<input type="number" id="payment_amount_bsf" name="payment_amount_bsf" class="form-control" value="{{ old('payment_amount_bsf', isset($sale) ? $sale->payment_amount_bsf : 0) }}" min="0" step=".01" readonly>
						</div>
					</div>
					<div class="col-12">
						<hr>
					</div>
				</div>
				{{-- Notas --}}
				<div class="form-group {{ $errors->has('notes') ? 'has-error' : '' }}">
                    <label for="notes">Anotaciones</label>
                    <textarea id="notes" name="notes" rows="1" class="form-control notes" maxlength="300">{{ old('notes', isset($sale) ? $sale->notes : '') }}</textarea>
                    @if ($errors->has('notes'))
                        <em class="invalid-feedback">
                            {{ $errors->first('notes') }}
                        </em>
                    @endif
                </div>
				{{-- Envío --}}
                <div class="text-center text-md-right mt-4">
					<hr>
					<a class="btn btn-dark pull-left" href="{{ url()->previous() }}">
						Regresar
					</a>
                    <input class="btn btn-success" type="submit" value="Guardar">
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

			var costs = costs_perpetual = costs_annual = costs_hardware = subtotal = subtotal_perpetual = subtotal_annual = subtotal_hardware = subtotal_products = subtotal_services = commission_perpetual = commission_annual = commission_hardware = commission_services = commission_total = iva = igtf = cityhall = total = profit = payment_amount_usd = payment_amount_bsf = seller_margin_services_total = 0;

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
				var parent = $(this).parents('.item');
				var support = parent.find('.support').is(':checked');
				var seller_fee = support ? seller_margin_services : seller_margin_services / 2;
				subtotal_services += value;
				seller_margin_services_total += value * seller_fee;
				parent.find('.support').attr('title', 'Comisión: ' + (value * seller_fee).toFixed(2));
			});

			subtotal_products = subtotal_perpetual + subtotal_annual + subtotal_hardware;
			subtotal = subtotal_products + subtotal_services;
			costs = costs_perpetual + costs_annual + costs_hardware;

			if (type == 'factura')
			{
				cityhall = subtotal * 0.07;
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
				// commission_services = subtotal_services * seller_margin_services;
				commission_services = seller_margin_services_total;
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
			// setTimeout(() => {
			// 	calculateValues();
			// }, 250);

			function productHandler(action)
			{
				return function() {
					var item = arguments[0];
					var data = this.options[item].data;
					if (action == 'ADD') {
						$('#products-list').append('<tr class="item" id="prod-' + data.id + '"><td><b>' + data.title + '</b><input type="hidden" name="products[' + item + '][id]" value="' + data.id + '"><input type="hidden" name="products[' + item + '][group]" value="' + data.group + '"></td><td><i>' + data.code + '</i></td><td><span class="badge badge-secondary">' + data.type + '</span></td><td class="text-right cost">' + data.cost + '</td><td class="text-right price"><input type="hidden" name="products[' + item + '][price]" value="' + data.price + '">' + data.price + '</td><td><input type="number" name="products[' + item + '][quantity]" min="1" value="1" class="form-control p-0 quantity quantity_prod"></td><td><input type="number" min="0" step=".01" class="form-control p-0 text-right subtotal" value="' + data.price + '" readonly></td><td><div class="form-check"><input type="checkbox" name="products[' + item + '][ml]" class="form-check-input fee_mercadolibre mt-0 ml-0" title="Comisión: 0.00" style="top: -6px;"></div></td><td><input type="number" name="products[' + item + '][discount]" min="0" step=".01" class="form-control p-0 text-right discount" value="0"></td><td><input type="number" min="0" step=".01" class="form-control p-0 text-right provider" rel="' + data.group + '" value="' + data.cost + '" readonly></td><td><input type="number" name="products[' + item + '][total]" min="0" step=".01" class="form-control p-0 text-right total product" rel="' + data.group + '" value="' + data.price + '" readonly></td></tr>');
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
						$('#services-list').append('<tr class="item" id="serv-' + data.id + '"><td><b>' + data.title + '</b><input type="hidden" name="services[' + item + '][id]" value="' + data.id + '"></td><td><i>' + data.code + '</i></td><td class="price text-right"><input type="hidden" name="services[' + item + '][price]" value="' + data.price + '">' + data.price + '</td><td><input type="number" name="services[' + item + '][quantity]" min="1" value="1" class="form-control p-0 quantity quantity_serv"></td><td><div class="form-check"><input type="checkbox" name="services[' + item + '][support]" class="form-check-input support mt-0 ml-0" style="top: -6px;"></div></td><td><input type="number" min="0" step=".01" class="form-control p-0 text-right subtotal" value="' + data.price + '" readonly></td><td><input type="number" name="services[' + item + '][discount]" min="0" step=".01" class="form-control p-0 text-right discount" value="0"></td><td><input type="number" name="services[' + item + '][total]" min="0" step=".01" class="form-control p-0 text-right total service" value="' + data.price + '" readonly></td></tr>');
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
						// var data = item.split(' :: ');
						// var code = data[0].trim();
						// var name = data[1].trim();
						var client = '<div class="row new_client"><div class="col-sm-6"><div class="form-group"><label for="title">Razón Social&nbsp;<b class="text-danger">*</b></label><input type="text" id="title" name="cli_title" class="form-control" value="' + name + '" required></div></div><div class="col-sm-2"><div class="form-group"><label for="document">Identificación&nbsp;<b class="text-danger">*</b></label><input type="text" id="document" name="cli_document" class="form-control" required></div></div><div class="col-sm-2"><div class="form-group"><label for="email">Email&nbsp;<b class="text-danger">*</b></label><input type="email" id="email" name="cli_email" class="form-control" required></div></div><div class="col-sm-2"><div class="form-group"><label for="phone">Télefono&nbsp;<b class="text-danger">*</b></label><input type="phone" id="phone" name="cli_phone" class="form-control" required></div></div></div>';
						$('#cliente').after(client);
					} else {
						console.log('Cliente nuevo eliminado');
						$('.new_client').remove();
					}
				};
			}

			$('body').on('input', '.discount', function() {
				var discount = $(this).val();
				var parent = $(this).parents('.item');
				var subtotal = parent.find('.subtotal').val();
				var total = parseFloat(subtotal) - parseFloat(discount);
				parent.find('.total').val(total);
				calculateValues();
			});

			$('body').on('change', '.quantity_prod', function() {
				var quantity = $(this).val();
				var parent = $(this).parents('.item');
				var cost = parent.find('.cost').text();
				var price = parent.find('.price').text();
				var discount = parent.find('.discount').val();
				var mercadolibre = $('#fee_mercadolibre').val();
				var total = parseFloat(price) * parseInt(quantity);
				var provider = parseFloat(cost) * parseInt(quantity);
				var fee_ml = parent.find('.fee_mercadolibre').is(':checked') ? total * (mercadolibre / 100) : 0;
				total = total - parseFloat(discount);
				provider += fee_ml;
				parent.find('.subtotal').val(total);
				parent.find('.provider').val(provider.toFixed(0));
				parent.find('.product').val(total);
				calculateValues();
			});

			$('body').on('change', '.fee_mercadolibre', function() {
				var parent = $(this).parents('.item');
				var cost = parent.find('.cost').html();
				var discount = parent.find('.discount').val();
				var subtotal = parent.find('.subtotal').val();
				var mercadolibre = $('#fee_mercadolibre').val();
				var quantity = parent.find('.quantity_prod').val();
				var provider = parseFloat(cost) * parseInt(quantity);
				var fee_ml = $(this).is(':checked') ? subtotal * (mercadolibre / 100) : 0;
				var total = parseFloat(subtotal) - parseFloat(discount);
				$(this).attr({'title': 'Comisión: ' + fee_ml.toFixed(2), 'data-previous-fee': fee_ml});
				provider += fee_ml;
				parent.find('.provider').val(provider.toFixed(0));
				parent.find('.total').val(total);
				calculateValues();
			});

			$('body').on('change', '.support', function() {
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

			// $('body').on('change', '#invoice_number', function() {
			// 	var invoice_number = $(this).val();
			// 	$.ajax({
			// 		url: "{{ route('admin.sales.exists') }}",
			// 		type: 'GET',
			// 		data: { invoice_number: invoice_number },
			// 		success: function(data) {
			// 			if (data) {
			// 				$('#invoice_number').addClass('is-invalid');
			// 				$('#invoice_number').focus();
			// 				Swal.fire({
			// 					type: 'error',
			// 					title: 'Error',
			// 					text: 'Ya existe una venta registrada con ese número, que quizá incluso, pertenece a otro vendedor. Por favor, verifique.',
			// 				});
			// 			} else {
			// 				$('#invoice_number').removeClass('is-invalid');
			// 			}
			// 		}
			// 	});
			// });

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

			$('body').on('click', '.new-item', function(e) {
				e.preventDefault();
				var item = $(this).attr('rel');
				var elem = item == 'producto' ? 'products' : 'services';
				var url = item == 'producto' ? "{{ route('admin.products.add', 'modal') }}" : "{{ route('admin.services.add', 'modal') }}";
				$.ajax({
					url: url,
					type: 'GET',
					success: function(data) {
						$('#new-item .modal-footer #save-item').attr('rel', elem);
						$('#new-item .modal-body').html(data);
						$('#new-item').modal('show');
					}
				});
			});

			$('body').on('click', '#save-item', function() {
				var form = $('#new-item .modal-body').find('form');
				if (form[0].checkValidity() === false) {
					form.find('.form-control, select').addClass('is-invalid');
					return false;
				}
				var formdata = form.serialize();
				var url = form.attr('action');
				var elem = $(this).attr('rel');
				$.ajax({
					url: url,
					type: 'POST',
					data: formdata,
					success: function(data) {
						if (data.status == 'success') {
							var item = JSON.parse(data.response);
							var title = item.title + ' (' + item.code + ')';
							$('.selectize-' + elem)[0].selectize.addOption({value: item.id, text: title, data: item });
							$('.selectize-' + elem)[0].selectize.addItem(item.id);
							$('#new-item .modal-header .close').click();
							Swal.fire({
								type: 'success',
								title: 'Registro creado',
								text: data.message,
								showCancelButton: false,
								showConfirmButton: false,
								timer: 2000,
							});
						} else {
							Swal.fire({
								type: 'error',
								title: 'Error',
								text: 'Ocurrió un error al registrar. Intente de nuevo.',
								showCancelButton: false,
								showConfirmButton: true,
							});
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
