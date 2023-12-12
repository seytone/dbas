@extends('layouts.admin')
@section('styles')
	<style>
		#myChart0 {
			max-height: 350px;
		}
		#myChart1 {
			max-height: 450px;
		}

		/* .print-hidden {
			display: none;
		} */

		@media print {
			#print-options,
			.print-hidden {
				display: none !important;
			}
			#filters {
				display: block !important;
			}
		}
	</style>
@endsection
@section('content')
	<div class="content" id="printable-content">
		<div class="row filters mb-2">
			<div class="col-lg-3">
				<h1>
					Dashboard
					<a class="btn btn-sm btn-dark text-light pull-right d-block d-lg-none mt-2 show-filters print-hidden" rel="filters">FILTROS</a>
				</h1>
			</div>
			<div class="col-lg-9">
				<form method="POST" action="{{ route('admin.dashboard') }}" class="d-none d-lg-flex pt-2 filters-form">
					@csrf
					<div class="col">
						<div class="input-group">
							<label class="input-group-text" for="start_date">Desde</label>
							<input type="date" id="start_date" name="start_date" class="form-control" value="{{ old('start_date', date('Y-m-d', strtotime($start_date))) }}">
							<label class="input-group-text" for="final_date">Hasta</label>
							<input type="date" id="final_date" name="final_date" class="form-control" value="{{ old('final_date', date('Y-m-d', strtotime($final_date))) }}">
							@if ($user->hasRole('Superadmin'))
								<label class="input-group-text" for="seller">Vendedor</label>
								<select class="form-select" id="seller" name="seller">
									<option value="all">Todos</option>
									@foreach ($sellers as $seller)
										<option value="{{ $seller->id }}" {{ $seller->id == $vendedor ? 'selected' : '' }}>{{ $seller->user->getFullname() }}</option>
									@endforeach
								</select>
							@elseif ($user->hasRole('Vendedor'))
								<input type="hidden" id="seller" name="seller" value="{{ $user->seller->id }}">
							@endif
							<button class="btn btn-default" type="submit"><b>Filtrar</b></button>
						</div>
					</div>
				</form>
				<form method="POST" action="{{ route('admin.dashboard') }}" class="d-none pt-2" id="filters">
					@csrf
					<label class="input-group-text" for="start_date">Desde</label>
					<input type="date" id="start_date" name="start_date" class="form-control" value="{{ old('start_date', date('Y-m-d', strtotime($start_date))) }}">
					<label class="input-group-text" for="final_date">Hasta</label>
					<input type="date" id="final_date" name="final_date" class="form-control" value="{{ old('final_date', date('Y-m-d', strtotime($final_date))) }}">
					@if ($user->hasRole('Superadmin'))
						<label class="input-group-text" for="seller">Vendedor</label>
						<select class="form-control" id="seller" name="seller">
							<option value="all">Todos</option>
							@foreach ($sellers as $seller)
								<option value="{{ $seller->id }}" {{ $seller->id == $vendedor ? 'selected' : '' }}>{{ $seller->user->getFullname() }}</option>
							@endforeach
						</select>
					@elseif ($user->hasRole('Vendedor'))
						<input type="hidden" id="seller" name="seller" value="{{ $user->seller->id }}">
					@endif
					<div class="col text-right mt-3 print-hidden">
						<button class="btn btn-default" type="submit"><b>Filtrar</b></button>
					</div>
				</form>
			</div>
			<div class="col-12"><hr></div>
		</div>
		<div class="row stats">
			<div class="col-12 col-sm-4 col-md-3 col-lg-3 col-xl-3" id="stats-ventas">
				<div class="callout callout-secondary">
				<h3 class="text-muted mb-0">
					<span class="d-none d-lg-block">Ventas</span>
					<span class="d-block d-lg-none">Número de Ventas</span>
				</h3><br>
				<strong class="h4">{{ $sales }}</strong>
				</div>
			</div>
			<div class="col-6 col-sm-4 col-md-3 col-lg-3" id="stats-facturacion">
				<div class="callout callout-dark">
					<h3 class="text-muted mb-0">Facturación</h3><br>
					<strong class="h4">${{ number_format($total_amount, 2, ',', '.') }}</strong>
				</div>
			</div>
			@if ($user->hasRole('Superadmin'))
				<div class="col-6 col-sm-4 col-md-3 col-lg-3" id="stats-ganancia">
					<div class="callout callout-success">
					<h3 class="text-muted mb-0">Ganancia</h3><br>
					<strong class="h4">${{ number_format($total_profit, 2, ',', '.') }}</strong>
					</div>
				</div>
			@endif
			<div class="col-6 col-sm-4 col-md-3 col-lg-3" id="stats-comision">
				<div class="callout callout-warning">
				<h3 class="text-muted mb-0">Comisión</h3><br>
				<strong class="h4">${{ number_format($total_commission, 2, ',', '.') }}</strong>
				</div>
			</div>
			<div class="col-6 col-sm-4 col-md-3 col-lg-3 col-xl-3" id="stats-servicios">
				<div class="callout callout-danger">
				<h3 class="text-muted mb-0">Servicios</h3><br>
				<strong class="h4">{{ $total_services }}</strong>
				</div>
			</div>
			<div class="col-6 col-sm-4 col-md-3 col-lg-3 col-xl-3 d-none d-lg-block" id="stats-productos">
				<div class="callout callout-info">
				<h3 class="text-muted mb-0">Productos</h3><br>
				<strong class="h4">{{ $total_products }}</strong>
				</div>
			</div>
			<div class="col-6 col-sm-4 col-md-3 col-lg-3 col-xl-3" id="stats-hardware">
				<div class="callout callout-primary">
				<h3 class="text-muted mb-0">Hardware</h3><br>
				<strong class="h4">{{ $total_hardware }}</strong>
				</div>
			</div>
			<div class="col-6 col-sm-4 col-md-3 col-lg-3 col-xl-3" id="stats-software">
				<div class="callout callout-primary">
				<h3 class="text-muted mb-0">Software</h3><br>
				<strong class="h4">{{ $total_software }}</strong>
				</div>
			</div>
			<div class="col-12"><hr></div>
		</div>
		<div class="row charts">
			<div class="col-md-12" id="stats-grafica-1">
				<br>
				<canvas id="myChart0"></canvas>
				<br>
			</div>
			<div class="col-md-9" id="stats-grafica-2">
				<br>
				<canvas id="myChart1"></canvas>
				<br>
			</div>
			<div class="col-md-3" id="stats-grafica-3">
				<br>
				<canvas id="myChart2"></canvas>
				<br>
			</div>
		</div>
	</div>
	@if ($user->hasRole('Superadmin'))
		<div class="row mb-3" id="print-options">
			<div class="col-12">
				<hr>
			</div>
			<div class="col-md-12 text-center text-xl-right">
				<div class="custom-control custom-checkbox custom-control-inline mt-2">
					<input type="checkbox" id="ventas" class="custom-control-input printable" rel="stats-ventas" checked>
					<label class="custom-control-label" for="ventas">Ventas</label>
				</div>
				<div class="custom-control custom-checkbox custom-control-inline mt-2">
					<input type="checkbox" id="facturacion" class="custom-control-input printable" rel="stats-facturacion" checked>
					<label class="custom-control-label" for="facturacion">Facturación</label>
				</div>
				<div class="custom-control custom-checkbox custom-control-inline mt-2">
					<input type="checkbox" id="ganancia" class="custom-control-input printable" rel="stats-ganancia" checked>
					<label class="custom-control-label" for="ganancia">Ganancia</label>
				</div>
				<div class="custom-control custom-checkbox custom-control-inline mt-2">
					<input type="checkbox" id="comision" class="custom-control-input printable" rel="stats-comision" checked>
					<label class="custom-control-label" for="comision">Comisión</label>
				</div>
				<div class="custom-control custom-checkbox custom-control-inline mt-2">
					<input type="checkbox" id="servicios" class="custom-control-input printable" rel="stats-servicios" checked>
					<label class="custom-control-label" for="servicios">Servicios</label>
				</div>
				<div class="custom-control custom-checkbox custom-control-inline mt-2">
					<input type="checkbox" id="productos" class="custom-control-input printable" rel="stats-productos" checked>
					<label class="custom-control-label" for="productos">Productos</label>
				</div>
				<div class="custom-control custom-checkbox custom-control-inline mt-2">
					<input type="checkbox" id="hardware" class="custom-control-input printable" rel="stats-hardware" checked>
					<label class="custom-control-label" for="hardware">Hardware</label>
				</div>
				<div class="custom-control custom-checkbox custom-control-inline mt-2">
					<input type="checkbox" id="software" class="custom-control-input printable" rel="stats-software" checked>
					<label class="custom-control-label" for="software">Software</label>
				</div>
				<div class="custom-control custom-checkbox custom-control-inline mt-2">
					<input type="checkbox" id="grafica-1" class="custom-control-input printable" rel="stats-grafica-1" checked>
					<label class="custom-control-label" for="grafica-1">Gráfica 1</label>
				</div>
				<div class="custom-control custom-checkbox custom-control-inline mt-2">
					<input type="checkbox" id="grafica-2" class="custom-control-input printable" rel="stats-grafica-2" checked>
					<label class="custom-control-label" for="grafica-2">Gráfica 2</label>
				</div>
				<div class="custom-control custom-checkbox custom-control-inline mt-2">
					<input type="checkbox" id="grafica-3" class="custom-control-input printable" rel="stats-grafica-3" checked>
					<label class="custom-control-label" for="grafica-3">Gráfica 3</label>
				</div>
				<div class="d-block d-xl-none">
					<br>
				</div>
				<a class="btn btn-default" id="print"><i class="fa fa-print"></i> Imprimir</a>
			</div>
		</div>
	@endif
@endsection
@section('scripts')
	@parent
	<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/chart.js"></script>
	<script>
		var ctx0 = document.getElementById('myChart0').getContext('2d');
		new Chart(ctx0, {
			type: 'bar',
			data: {
				labels: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
				datasets: [
					// {
					// 	type: 'line',
					// 	label: 'Total Ventas',
					// 	data: [
					// 		@foreach ($months as $month)
					// 			{{ $month['total'] }},
					// 		@endforeach
					// 	],
					// 	backgroundColor: '#20a8d8',
					// 	borderColor: '#20a8d8',
					// 	lineTension: 0.3,
					// 	borderWidth: 1,
					// 	order: 1
					// },
					{
						type: 'line',
						label: 'Total Ganancias',
						data: [
							@foreach ($months as $month)
								{{ $month['profit'] }},
							@endforeach
						],
						backgroundColor: '#4dbd74',
						borderColor: '#4dbd74',
						borderWidth: 1,
						lineTension: 0.3,
						order: 2
					},
					{
						type: 'line',
						label: 'Total Comisiones',
						data: [
							@foreach ($months as $month)
								{{ $month['commission'] }},
							@endforeach
						],
						backgroundColor: '#f86c6b',
						borderColor: '#f86c6b',
						borderWidth: 1,
						lineTension: 0.3,
						order: 2
					},
				]
			}
		});
		var ctx1 = document.getElementById('myChart1').getContext('2d');
		new Chart(ctx1, {
			type: 'bar',
			data: {
				labels: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
				datasets: [{
					label: 'Ventas Registradas',
					backgroundColor: '#ffc107',
					borderColor: '#ffc107',
					data: [
						@foreach ($months as $month)
							{{ $month['sales'] }},
						@endforeach
					],
					lineTension: 0.3,
					borderWidth: 1,
				}]
			},
			options: {
				scales: {
					yAxes: [{
						stacked: true
					}]
				},
			}
		});
		var ctx2 = document.getElementById('myChart2').getContext('2d');
		new Chart(ctx2, {
			type: 'pie',
			data: {
				labels: ['Hardware', 'Software', 'Servicios'],
				datasets: [{
					label: ['Ventas Desglosadas'],
					data: [{{ $total_hardware }}, {{ $total_software }}, {{ $total_services }}],
					backgroundColor: [
						'#20a8d8',
						'#4dbd74',
						'#f86c6b'
					],
					borderColor: [
						'#20a8d8',
						'#4dbd74',
						'#f86c6b'
					],
					borderWidth: 1
				}]
			},
			options: {
				scales: {
					yAxes: [{
						ticks: {
							beginAtZero: true
						}
					}]
				}
			}
		});

		$('.show-filters').click(function() {
			$('#filters').toggleClass('d-none');
		});

		$('.printable').click(function() {
			var rel = $(this).attr('rel');
			$('#' + rel).toggleClass('print-hidden');
		});

		$('#print').click(function() {
			print('#printable-content');
		});
	</script>
@endsection