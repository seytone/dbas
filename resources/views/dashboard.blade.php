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
			@can('manage_sales')
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
			@endcan
			<div class="col-12"><hr></div>
		</div>
		@can('manage_sales')
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
						<div class="d-flex align-items-center justify-content-between flex-wrap" style="gap: 6px;">
							<strong class="h4 m-0">${{ number_format($total_commission, 2, ',', '.') }}</strong>
							@if($paymentMode === 'single' && $paymentRecord)
								<div class="d-flex align-items-center flex-wrap" style="gap: 4px;">
									@if($paymentRecord->payment === 'completed')
										<span class="badge badge-success" style="font-size: 10px;">PAGADO EL {{ $paymentRecord->payment_date->format('d/m/Y') }}</span>
										<button type="button" class="btn btn-sm btn-dark" data-toggle="modal" data-target="#viewEvidenceModal" data-evidence="{{ $paymentRecord->payment_evidence }}" data-payment-id="{{ $paymentRecord->id }}" title="Ver comprobante">
											<i class="fa fa-file"></i>
										</button>
									@else
										<span class="badge badge-danger" style="font-size: 10px;">PENDIENTE</span>
										<button type="button" class="btn btn-sm btn-success btn-pay-commission" data-payment-id="{{ $paymentRecord->id }}" data-period-label="{{ $paymentPeriodLabel }}" title="Marcar como pagada">
											<i class="fa fa-check"></i>
										</button>
									@endif
								</div>
							@elseif($paymentMode === 'multi')
								<button type="button" class="btn btn-sm btn-info" id="btn-open-breakdown" data-seller-id="{{ $resolvedSellerId }}" data-start-date="{{ date('Y-m-d', strtotime($start_date)) }}" data-final-date="{{ date('Y-m-d', strtotime($final_date)) }}" title="Ver estado por mes">
									<i class="fa fa-calendar mr-1"></i> Por mes
								</button>
							@endif
						</div>
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
		@endcan
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

	{{-- ============================================================
		 COMMISSION PAYMENT MODALS
		 ============================================================ --}}

	{{-- View payment evidence (single + multi share this) --}}
	<div class="modal fade" id="viewEvidenceModal" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title"><i class="fa fa-file mr-2"></i>Comprobante de pago</h5>
					<button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
				</div>
				<div class="modal-body text-center" id="viewEvidenceBody">
					<p class="text-muted">Cargando...</p>
				</div>
				<div class="modal-footer">
					<form id="unpayForm" method="POST" class="d-inline">
						@csrf
						@method('DELETE')
						<button type="button" class="btn btn-danger" id="btn-unpay-commission" onclick="return confirm('¿Deseas revertir este pago? El registro volverá al estado pendiente.');">
							<i class="fa fa-undo mr-1"></i> Revertir pago
						</button>
					</form>
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
				</div>
			</div>
		</div>
	</div>

	{{-- Multi-month breakdown --}}
	<div class="modal fade" id="breakdownModal" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog modal-xl" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title"><i class="fa fa-calendar mr-2"></i>Estado de comisiones por mes</h5>
					<button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
				</div>
				<div class="modal-body">
					<div id="breakdownLoading" class="text-center text-muted py-4">
						<i class="fa fa-spinner fa-spin"></i> Cargando...
					</div>
					<div class="table-responsive d-none" id="breakdownTableContainer">
						<table class="table table-bordered table-sm table-hover">
							<thead class="bg-light">
								<tr>
									<th>Período</th>
									<th class="text-right">Comisión</th>
									<th>Estado</th>
									<th>Fecha pago</th>
									<th class="text-center">Acciones</th>
								</tr>
							</thead>
							<tbody id="breakdownTableBody"></tbody>
						</table>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
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

		// ============================================================
		// COMMISSION PAYMENT — single-month flow (SweetAlert2)
		// ============================================================
		$('body').on('click', '.btn-pay-commission', function() {
			var $btn = $(this);
			var paymentId = $btn.data('payment-id');
			var periodLabel = $btn.data('period-label') || 'este mes';
			var inBreakdown = $('#breakdownModal').hasClass('show');

			Swal.fire({
				title: 'Marcar comisión como pagada',
				html: '<p>Vas a registrar el pago de la comisión correspondiente a <b>' + periodLabel + '</b>.</p><p class="text-muted small">Adjunta el comprobante (imagen o PDF).</p>',
				input: 'file',
				inputAttributes: {
					'accept': 'image/*,application/pdf',
					'aria-label': 'Subir comprobante'
				},
				showCancelButton: true,
				confirmButtonText: 'Registrar pago',
				cancelButtonText: 'Cancelar',
				confirmButtonColor: '#4dbd74',
				inputValidator: function(file) {
					if (!file) return 'Debes seleccionar un archivo de comprobante.';
				},
				preConfirm: function(file) {
					var formData = new FormData();
					formData.append('id', paymentId);
					formData.append('evidence', file);
					formData.append('_token', '{{ csrf_token() }}');

					return fetch('{{ route("admin.commission.pay") }}', {
						method: 'POST',
						body: formData,
						headers: { 'Accept': 'application/json' }
					}).then(function(r) {
						if (!r.ok) return r.json().then(function(j) { throw new Error(j.message || 'Error al registrar el pago.'); });
						return r.json();
					}).catch(function(err) {
						Swal.showValidationMessage(err.message);
					});
				}
			}).then(function(result) {
				if (!(result.isConfirmed && result.value && result.value.success)) return;

				var resp = result.value;
				Swal.fire({ icon: 'success', title: 'Pago registrado', timer: 1200, showConfirmButton: false });

				if (inBreakdown) {
					// Refresh the breakdown table in place.
					$('#btn-open-breakdown').trigger('click');
				} else {
					// Replace the pending badge + green button with paid badge + view button.
					var $container = $btn.parent();
					$container.html(
						'<span class="badge badge-success" style="font-size: 10px;">PAGADO EL ' + resp.payment_date + '</span>' +
						'<button type="button" class="btn btn-sm btn-dark" data-toggle="modal" data-target="#viewEvidenceModal" ' +
						'data-evidence="' + resp.payment_evidence + '" data-payment-id="' + paymentId + '" title="Ver comprobante">' +
						'<i class="fa fa-file"></i></button>'
					);
				}
			});
		});

		// ============================================================
		// View evidence modal — handles stacked modal z-index when opened
		// from inside the breakdown modal so it appears on top.
		// ============================================================
		$('#viewEvidenceModal').on('show.bs.modal', function(e) {
			// Stacked-modals fix: bump z-index above any currently visible modal.
			var openModals = $('.modal:visible').length;
			if (openModals > 0) {
				var zIndex = 1050 + (10 * (openModals + 1));
				$(this).css('z-index', zIndex);
				setTimeout(function() {
					$('.modal-backdrop').not('.modal-stack')
						.css('z-index', zIndex - 1)
						.addClass('modal-stack');
				}, 0);
			}

			var btn = $(e.relatedTarget);
			var evidence = btn.data('evidence');
			var paymentId = btn.data('payment-id');

			var body = $('#viewEvidenceBody');
			body.empty();
			if (evidence && /\.(jpg|jpeg|png|gif|webp)$/i.test(evidence)) {
				body.html('<img src="{{ asset("storage/payments") }}/' + evidence + '" class="img-fluid" alt="Comprobante">');
			} else if (evidence) {
				body.html('<a href="{{ asset("storage/payments") }}/' + evidence + '" target="_blank" class="btn btn-primary"><i class="fa fa-external-link mr-2"></i>Abrir comprobante</a>');
			} else {
				body.html('<p class="text-muted">Sin comprobante.</p>');
			}

			$('#btn-unpay-commission').data('payment-id', paymentId);
		});

		// When the evidence modal closes, keep the body class if breakdown is still open.
		$('#viewEvidenceModal').on('hidden.bs.modal', function() {
			if ($('.modal:visible').length) {
				$('body').addClass('modal-open');
			}
		});

		// Revert payment (unpay)
		$('#btn-unpay-commission').on('click', function() {
			var paymentId = $(this).data('payment-id');
			if (!paymentId) return;
			if (!confirm('¿Deseas revertir este pago? El registro volverá al estado pendiente.')) return;

			$.ajax({
				url: '{{ url("admin/commission_unpay") }}/' + paymentId,
				method: 'POST',
				data: { _method: 'DELETE', _token: '{{ csrf_token() }}' },
				success: function() {
					$('#viewEvidenceModal').modal('hide');
					// If breakdown modal is open, refresh it; otherwise reload page
					if ($('#breakdownModal').hasClass('show')) {
						$('#btn-open-breakdown').trigger('click');
					} else {
						location.reload();
					}
				},
				error: function() {
					alert('Error al revertir el pago.');
				}
			});
		});

		// ============================================================
		// COMMISSION PAYMENT — multi-month breakdown modal
		// ============================================================
		$('#btn-open-breakdown').on('click', function() {
			var btn = $(this);
			$('#breakdownLoading').removeClass('d-none');
			$('#breakdownTableContainer').addClass('d-none');
			$('#breakdownTableBody').empty();
			$('#breakdownModal').modal('show');

			$.ajax({
				url: '{{ route("admin.commission.breakdown") }}',
				method: 'GET',
				data: {
					seller_id: btn.data('seller-id'),
					start_date: btn.data('start-date'),
					final_date: btn.data('final-date')
				},
				success: function(res) {
					$('#breakdownLoading').addClass('d-none');
					$('#breakdownTableContainer').removeClass('d-none');
					if (!res.success || !res.rows.length) {
						$('#breakdownTableBody').html('<tr><td colspan="5" class="text-center text-muted">Sin comisiones en este rango.</td></tr>');
						return;
					}
					var html = '';
					res.rows.forEach(function(r) {
						var statusBadge = r.payment === 'completed'
							? '<span class="badge badge-success">PAGADO</span>'
							: '<span class="badge badge-danger">PENDIENTE</span>';
						var actions = '';
						if (r.payment === 'completed') {
							actions = '<button class="btn btn-sm btn-dark mr-1" data-toggle="modal" data-target="#viewEvidenceModal" data-evidence="' + r.payment_evidence + '" data-payment-id="' + r.id + '" title="Ver comprobante"><i class="fa fa-file"></i></button>';
						} else {
							actions = '<button class="btn btn-sm btn-success btn-pay-commission" data-payment-id="' + r.id + '" data-period-label="' + r.period_label + '" title="Marcar como pagada"><i class="fa fa-check"></i></button>';
						}
						html += '<tr>'
							+ '<td>' + r.period_label.charAt(0).toUpperCase() + r.period_label.slice(1) + '</td>'
							+ '<td class="text-right">$' + r.total_commission + '</td>'
							+ '<td>' + statusBadge + '</td>'
							+ '<td>' + (r.payment_date || '-') + '</td>'
							+ '<td class="text-center">' + actions + '</td>'
							+ '</tr>';
					});
					$('#breakdownTableBody').html(html);
				},
				error: function() {
					$('#breakdownLoading').addClass('d-none');
					$('#breakdownTableBody').html('<tr><td colspan="5" class="text-center text-danger">Error al cargar los datos.</td></tr>');
					$('#breakdownTableContainer').removeClass('d-none');
				}
			});
		});
	</script>
@endsection