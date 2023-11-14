@extends('layouts.admin')

@section('content')
<div class="content">
    <div class="row mb-4 filters">
        <div class="col-md-6">
            <h1>Panel de Control</h1>
		</div>
		<div class="col-md-6 pt-2">
			<form method="POST" action="{{ route('admin.dashboard') }}" class="d-flex">
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
		</div>
		<div class="col-12"><hr></div>
	</div>
	<div class="row stats">
		<div class="col-6 col-sm-4 col-md-3 col-lg-2 col-xl-1">
			<div class="callout callout-secondary">
			<h3 class="text-muted mb-0">Ventas</h3><br>
			<strong class="h4">{{ $sales }}</strong>
			</div>
		</div>
		<div class="col-6 col-sm-4 col-md-3 col-lg-2">
			<div class="callout callout-dark">
			<h3 class="text-muted mb-0">Total Facturación</h3><br>
			<strong class="h4">${{ number_format($total_amount, 2, ',', '.') }} USD</strong>
			</div>
		</div>
		<div class="col-6 col-sm-4 col-md-3 col-lg-2">
			<div class="callout callout-success">
			<h3 class="text-muted mb-0">Total Ganancia</h3><br>
			<strong class="h4">${{ number_format($total_profit, 2, ',', '.') }} USD</strong>
			</div>
		</div>
		<div class="col-6 col-sm-4 col-md-3 col-lg-2">
			<div class="callout callout-warning">
			<h3 class="text-muted mb-0">Total Comisiones</h3><br>
			<strong class="h4">${{ number_format($total_commission, 2, ',', '.') }} USD</strong>
			</div>
		</div>
		<div class="col-6 col-sm-4 col-md-3 col-lg-2 col-xl-1">
			<div class="callout callout-danger">
			<h3 class="text-muted mb-0">Servicios</h3><br>
			<strong class="h4">{{ $total_services }}</strong>
			</div>
		</div>
		<div class="col-6 col-sm-4 col-md-3 col-lg-2 col-xl-1">
			<div class="callout callout-primary">
			<h3 class="text-muted mb-0">Productos</h3><br>
			<strong class="h4">{{ $total_products }}</strong>
			</div>
		</div>
		<div class="col-6 col-sm-4 col-md-3 col-lg-2 col-xl-1">
			<div class="callout callout-info">
			<h3 class="text-muted mb-0">Hardware</h3><br>
			<strong class="h4">{{ $total_hardware }}</strong>
			</div>
		</div>
		<div class="col-6 col-sm-4 col-md-3 col-lg-2 col-xl-1">
			<div class="callout callout-info">
			<h3 class="text-muted mb-0">Software</h3><br>
			<strong class="h4">{{ $total_software }}</strong>
			</div>
		</div>
	</div>
	<div class="row charts">
		<div class="col-md-12">
			<br>
			<canvas id="myChart0" height="50px"></canvas>
			<br>
		</div>
		<div class="col-md-9">
			<br>
			<canvas id="myChart1" height="100px"></canvas>
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
			datasets: [{
				type: 'line',
				label: 'Total Comisiones',
				data: [500, 1000, 1000, 1500, 1500, 2000, 2000, 2500, 3000, 3500, 4000, 4500],
				backgroundColor: 'rgba(238, 228, 35, 0.5)',
				borderColor: 'rgba(35, 40, 41, 0.5)',
				borderWidth: 2,
				order: 1
			}, {
				type: 'line',
				label: 'Total Ventas',
				data: [2000, 10000, 5000, 2000, 7000, 13000, 4000, 18000, 6000, 4000, 8000, 10000],
				backgroundColor: 'rgba(54, 162, 235, 0.5)',
				borderColor: 'rgba(54, 162, 235, 1)',
				borderWidth: 2,
				lineTension: 0.3,
				order: 2
			}]
		}
	});
	var ctx1 = document.getElementById('myChart1').getContext('2d');
	new Chart(ctx1, {
		type: 'bar',
		data: {
			labels: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
			datasets: [{
				label: 'Ventas Registradas',
				backgroundColor: 'rgba(100, 100, 100, 0.5)',
				borderColor: 'rgba(35, 40, 41, 0.5)',
				data: [2, 10, 5, 2, 7, 13, 4, 18, 6, 4, 8, 10],
				lineTension: 0.3,
				borderWidth: 2,
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
			labels: ['Productos', 'Servicios', 'Licencias'],
			datasets: [{
				label: ['Ventas Desglosadas'],
				data: [15, 8, 20],
				backgroundColor: [
					'rgba(32, 168, 216, 0.5)',
					'rgba(100, 200, 50, 0.5)',
					'rgba(255, 153, 112, 0.5)'
				],
				borderColor: [
					'rgba(32, 168, 216, 1)',
					'rgba(100, 200, 50, 1)',
					'rgba(255, 153, 112, 1)'
				],
				borderWidth: 2
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
</script>
@endsection