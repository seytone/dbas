@extends('layouts.admin')

@section('content')
<div class="content">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="mb-4">Control de Ventas</h1>
            <hr>
            <div class="row">
                <div class="col-sm-2">
                    <div class="callout callout-success">
                    <h3 class="text-muted mb-0">Total Ventas</h3><br>
                    <strong class="h4">10.850 USD</strong>
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="callout callout-warning">
                    <h3 class="text-muted mb-0">Total Comisiones</h3><br>
                    <strong class="h4">1.225 USD</strong>
                    </div>
                </div>
				<div class="col-sm-2">
                    <div class="callout callout-dark">
                    <h3 class="text-muted mb-0">Total Productos</h3><br>
                    <strong class="h4">120</strong>
                    </div>
                </div>
				<div class="col-sm-2">
                    <div class="callout callout-primary">
                    <h3 class="text-muted mb-0">Hardware</h3><br>
                    <strong class="h4">85</strong>
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="callout callout-danger">
                    <h3 class="text-muted mb-0">Software</h3><br>
                    <strong class="h4">10</strong>
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="callout callout-info">
                    <h3 class="text-muted mb-0">Servicios</h3><br>
                    <strong class="h4">25</strong>
                    </div>
                </div>
            </div>
            <div class="row">
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