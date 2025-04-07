@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        Detalle de Venta
    </div>

    <div class="card-body">
        <div class="table-responsive mb-2">
            <table class="table table-bordered table-striped">
                <tbody>
					<tr>
                        <th>
                            Fecha
                        </th>
                        <td>
                            {{ date('d/m/Y', strtotime($sale->registered_at)) }}
                        </td>
                    </tr>
					<tr>
                        <th>
                            Código
                        </th>
                        <td>
                            {{ $sale->invoice_number }}
                        </td>
                    </tr>
					<tr>
                        <th>
                            Tipo
                        </th>
                        <td>
                            {{ mb_strtoupper($sale->invoice_type) }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Cliente
                        </th>
                        <td>
                            {{ $sale->client->title }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Vendedor
                        </th>
                        <td>
                            {{ $sale->seller->user->getFullname() }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Forma de pago
                        </th>
                        <td>
                            {{ $sale->payment_method }}
                        </td>
                    </tr>
					<tr>
						<th>
							Productos
						</th>
						<td>
							{{ count($products) }}
							@if (count($products) > 0)
								<a class="btn btn-sm btn-dark text-light pull-right show-items" rel="products">VER</a>
							@endif
						</td>
					</tr>
					@if (count($products) > 0)
						<tr class="d-none" id="products">
							<td colspan="2">
								<table class="table">
									<thead>
										<tr>
											<th width="500">
												Producto
											</th>
											<th>
												Costo
											</th>
											<th>
												Precio
											</th>
											<th>
												Cantidad
											</th>
											<th>
												Descuento
											</th>
											<th>
												Mercado Libre
											</th>
											<th>
												Total
											</th>
										</tr>
									</thead>
									<tbody>
										@php
											$total_prods = 0;
										@endphp
										@foreach ($products as $prod)
											@isset ($prod->product)
												<tr>
													<td>
														{{ $prod->product->title }}
													</td>
													<td>
														${{ number_format($prod->cost, 2, ',', '.') }} USD
													</td>
													<td>
														${{ number_format($prod->price, 2, ',', '.') }} USD
													</td>
													<td>
														{{ $prod->quantity }}
													</td>
													<td>
														${{ number_format($prod->discount, 2, ',', '.') }} USD
													</td>
													<td>
														{{ $prod->mercadolibre ? 'Sí' : 'No' }}
													</td>
													<td>
														${{ number_format($prod->total, 2, ',', '.') }} USD
													</td>
												</tr>
												@php
													$total_prods += $prod->total;
												@endphp
											@endisset
										@endforeach
									</tbody>
									<tfoot>
										<tr>
											<th colspan="6"></th>
											<th>
												${{ number_format($total_prods, 2, ',', '.') }} USD
											</th>
										</tr>
									</tfoot>
								</table>
							</td>
						</tr>
					@endif
					<tr>
						<th>
							Servicios
						</th>
						<td>
							{{ count($services) }}
							@if (count($services) > 0)
								<a class="btn btn-sm btn-dark text-light pull-right show-items" rel="services">VER</a>
							@endif
						</td>
					</tr>
					@if (count($services) > 0)
						<tr class="d-none" id="services">
							<td colspan="2">
								<table class="table">
									<thead>
										<tr>
											<th width="500">
												Servicio
											</th>
											<th>
												Precio
											</th>
											<th>
												Cantidad
											</th>
											<th>
												Descuento
											</th>
											<th>
												Soporte
											</th>
											<th>
												Total
											</th>
										</tr>
									</thead>
									<tbody>
										@php
											$total_servs = 0;
										@endphp
										@foreach ($services as $serv)
											@isset ($serv->service)
												<tr>
													<td>
														{{ $serv->service->title }}
													</td>
													<td>
														${{ number_format($serv->price, 2, ',', '.') }} USD
													</td>
													<td>
														{{ $serv->quantity }}
													</td>
													<td>
														${{ number_format($serv->discount, 2, ',', '.') }} USD
													</td>
													<td>
														{{ $serv->support ? 'Sí' : 'No' }}
													</td>
													<td>
														${{ number_format($serv->total, 2, ',', '.') }} USD
													</td>
												</tr>
												@php
													$total_servs += $serv->total;
												@endphp
											@endisset
										@endforeach
									</tbody>
									<tfoot>
										<tr>
											<th colspan="5"></th>
											<th>
												${{ number_format($total_servs, 2, ',', '.') }} USD
											</th>
										</tr>
									</tfoot>
								</table>
							</td>
						</tr>
					@endif
                    <tr>
                        <th>
                            Subtotal
                        </th>
                        <td>
                            ${{ number_format($sale->subtotal, 2, ',', '.') }} USD
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Alcaldía
                        </th>
                        <td>
                            ${{ number_format($sale->cityhall, 2, ',', '.') }} USD
                        </td>
                    </tr>
                    <tr>
                        <th>
                            IGTF
                        </th>
                        <td>
                            ${{ number_format($sale->igtf, 2, ',', '.') }} USD
                        </td>
                    </tr>
                    <tr>
                        <th>
                            IVA
                        </th>
                        <td>
                            ${{ number_format($sale->iva, 2, ',', '.') }} USD
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Total
                        </th>
                        <td>
                            ${{ number_format($sale->total, 2, ',', '.') }} USD
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Pagado con
                        </th>
                        <td>
                            {{ $sale->currency == 'usd' ? 'Dólares' : 'Dólares + Bolívares' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Pago en USD
                        </th>
                        <td>
                            ${{ number_format($sale->payment_amount_usd, 2, ',', '.') }} USD
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Pago en Bs. (expresado en USD)
                        </th>
                        <td>
                            ${{ number_format($sale->payment_amount_bsf, 2, ',', '.') }} USD
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Costo
                        </th>
                        <td>
                            ${{ number_format($sale->provider, 2, ',', '.') }} USD
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Ganancia
                        </th>
                        <td>
                            ${{ number_format($sale->profit, 2, ',', '.') }} USD
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Comisión Licencias Perpetuas ({{ $sale->seller->commission_1 }}%)
                        </th>
                        <td>
                            ${{ number_format($sale->commission_perpetual, 2, ',', '.') }} USD
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Comisión Suscripciones Anuales ({{ $sale->seller->commission_2 }}%)
                        </th>
                        <td>
                            ${{ number_format($sale->commission_annual, 2, ',', '.') }} USD
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Comisión Hardware y Otros ({{ $sale->seller->commission_3 }}%)
                        </th>
                        <td>
                            ${{ number_format($sale->commission_hardware, 2, ',', '.') }} USD
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Comisión sobre servicios ({{ $sale->seller->commission_4 }}%)
                        </th>
                        <td>
                            ${{ number_format($sale->commission_services, 2, ',', '.') }} USD
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Comisión sobre el total
                        </th>
                        <td>
                            ${{ number_format($sale->commission, 2, ',', '.') }} USD
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Trello
                        </th>
                        <td>
                            <a href="{{ $sale->trello }}" target="_blank">{{ $sale->trello }}</a>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Notas
                        </th>
                        <td>
                            {{ $sale->notes }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <a style="margin-top:20px;" class="btn btn-dark" href="{{ url()->previous() }}">
                Regresar
            </a>
			<a style="margin-top:20px;" class="btn btn-warning pull-right" href="{{ route('admin.sales.edit', $sale->id) }}">
				Modificar
			</a>
        </div>
    </div>
</div>
@endsection

@section('scripts')
	<script>
		$(function()
		{
			$('.show-items').on('click', function()
			{
				var id = $(this).attr('rel');
				$('#' + id).toggleClass('d-none');
			});
		});
	</script>
@endsection