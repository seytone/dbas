@extends('layouts.admin')
@section('styles')
	<style>
		#myChart0 {
			max-height: 350px;
		}
		#myChart1 {
			max-height: 450px;
		}
	</style>
@endsection
@section('content')
	<div class="content">
		<div class="row filters mb-2">
			<div class="col-md-6">
				<h1>
					Dashboard
					<a class="btn btn-sm btn-dark text-light pull-right d-block d-md-none mt-2 show-filters" rel="filters">FILTROS</a>
				</h1>
			</div>
			<div class="col-md-6">
				<form method="POST" action="{{ route('admin.dashboard') }}" class="d-none d-md-flex pt-2">
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
					@endif
					<div class="col text-right mt-3">
						<button class="btn btn-default" type="submit"><b>Filtrar</b></button>
					</div>
				</form>
			</div>
			<div class="col-12"><hr></div>
		</div>
		<div class="row stats">
			<div class="col-12 col-sm-4 col-md-3 col-lg-3 col-xl-3">
				<div class="callout callout-secondary">
				<h3 class="text-muted mb-0">
					<span class="d-none d-md-block">Ventas</span>
					<span class="d-block d-md-none">Número de Ventas</span>
				</h3><br>
				<strong class="h4">{{ $sales }}</strong>
				</div>
			</div>
			<div class="col-6 col-sm-4 col-md-3 col-lg-3">
				<div class="callout callout-dark">
					<h3 class="text-muted mb-0">Facturación</h3><br>
					<strong class="h4">${{ number_format($total_amount, 2, ',', '.') }}</strong>
				</div>
			</div>
			@if ($user->hasRole('Superadmin'))
				<div class="col-6 col-sm-4 col-md-3 col-lg-3">
					<div class="callout callout-success">
					<h3 class="text-muted mb-0">Ganancia</h3><br>
					<strong class="h4">${{ number_format($total_profit, 2, ',', '.') }}</strong>
					</div>
				</div>
			@endif
			<div class="col-6 col-sm-4 col-md-3 col-lg-3">
				<div class="callout callout-warning">
				<h3 class="text-muted mb-0">Comisión</h3><br>
				<strong class="h4">${{ number_format($total_commission, 2, ',', '.') }}</strong>
				</div>
			</div>
			<div class="col-6 col-sm-4 col-md-3 col-lg-3 col-xl-3">
				<div class="callout callout-danger">
				<h3 class="text-muted mb-0">Servicios</h3><br>
				<strong class="h4">{{ $total_services }}</strong>
				</div>
			</div>
			<div class="col-6 col-sm-4 col-md-3 col-lg-3 col-xl-3 d-none d-md-block">
				<div class="callout callout-info">
				<h3 class="text-muted mb-0">Productos</h3><br>
				<strong class="h4">{{ $total_products }}</strong>
				</div>
			</div>
			<div class="col-6 col-sm-4 col-md-3 col-lg-3 col-xl-3">
				<div class="callout callout-primary">
				<h3 class="text-muted mb-0">Hardware</h3><br>
				<strong class="h4">{{ $total_hardware }}</strong>
				</div>
			</div>
			<div class="col-6 col-sm-4 col-md-3 col-lg-3 col-xl-3">
				<div class="callout callout-primary">
				<h3 class="text-muted mb-0">Software</h3><br>
				<strong class="h4">{{ $total_software }}</strong>
				</div>
			</div>
			<div class="col-12"><hr></div>
		</div>
		<div class="row charts">
			<div class="col-md-12">
				<br>
				<canvas id="myChart0"></canvas>
				<br>
			</div>
			<div class="col-md-9">
				<br>
				<canvas id="myChart1"></canvas>
				<br>
			</div>
			<div class="col-md-3">
				<br>
				<canvas id="myChart2"></canvas>
				<br>
			</div>
		</div>
	</div>
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
					{
						type: 'line',
						label: 'Total Ventas',
						data: [
							@foreach ($months as $month)
								{{ $month['total'] }},
							@endforeach
						],
						backgroundColor: '#20a8d8',
						borderColor: '#20a8d8',
						lineTension: 0.3,
						borderWidth: 1,
						order: 1
					},
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
	</script>
@endsection