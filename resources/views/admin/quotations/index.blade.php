@extends('layouts.admin')
@section('content')
	<div class="row mb-2 filters">
		<div class="col-md-6">
			<h1>
				Cotizaciones
				<a class="btn btn-sm btn-dark text-light pull-right d-block d-md-none mt-2 show-filters" rel="filters">FILTROS</a>
			</h1>
		</div>
		<div class="col-md-6">
			<form method="POST" action="{{ route('admin.quotations_filter') }}" class="d-none d-md-flex pt-2 filters-form">
				@csrf
				<div class="col">
					<div class="input-group">
						<label class="input-group-text" for="start_date">Desde</label>
						<input type="date" id="start_date" name="start_date" class="form-control" value="{{ old('start_date', date('Y-m-d', strtotime($start_date))) }}">
						<label class="input-group-text" for="final_date">Hasta</label>
						<input type="date" id="final_date" name="final_date" class="form-control" value="{{ old('final_date', date('Y-m-d', strtotime($final_date))) }}">
						<label class="input-group-text" for="status">Estado</label>
						<select class="form-select" id="status" name="status">
							<option value="all">Todos</option>
							<option value="draft">Borrador</option>
							<option value="sent">Enviada</option>
							<option value="accepted">Aceptada</option>
							<option value="rejected">Rechazada</option>
						</select>
						<button class="btn btn-default" type="submit"><b>Filtrar</b></button>
					</div>
				</div>
			</form>
			<form method="POST" action="{{ route('admin.quotations_filter') }}" class="d-none pt-2" id="filters">
				@csrf
				<label class="input-group-text" for="start_date">Desde</label>
				<input type="date" id="start_date" name="start_date" class="form-control" value="{{ old('start_date', date('Y-m-d', strtotime($start_date))) }}">
				<label class="input-group-text" for="final_date">Hasta</label>
				<input type="date" id="final_date" name="final_date" class="form-control" value="{{ old('final_date', date('Y-m-d', strtotime($final_date))) }}">
				<label class="input-group-text" for="status">Estado</label>
				<select class="form-control" id="status" name="status">
					<option value="all">Todos</option>
					<option value="draft">Borrador</option>
					<option value="sent">Enviada</option>
					<option value="accepted">Aceptada</option>
					<option value="rejected">Rechazada</option>
				</select>
				<div class="col text-right mt-3">
					<button class="btn btn-default" type="submit"><b>Filtrar</b></button>
				</div>
			</form>
		</div>
		<div class="col-12"><hr></div>
	</div>
	<div style="margin-bottom: 10px;" class="row">
		<div class="col-lg-12">
			<a class="btn btn-success" href="{{ route('admin.quotations.create') }}">
				<i class="fa fa-fw fa-plus mr-2 d-inline"></i>Nueva Cotización
			</a>
		</div>
	</div>
	<div class="card">
		<div class="card-header">
			Gestión de Cotizaciones
		</div>
		<div class="card-body">
			<div class="table-responsive">
				<table class="table table-bordered table-striped table-hover datatable datatable-quotations">
					<thead>
						<tr>
							<th width="10"></th>
							<th>Nro.</th>
							<th>Fecha</th>
							<th>Vencimiento</th>
							<th>Cliente</th>
							<th>Total</th>
							<th>Estado</th>
							<th width="220">&nbsp;</th>
						</tr>
					</thead>
					<tbody>
						@foreach($quotations as $quotation)
							<tr data-entry-id="{{ $quotation->id }}">
								<td></td>
								<td>{{ $quotation->formatted_number }}</td>
								<td>{{ date('d/m/Y', strtotime($quotation->emission_date)) }}</td>
								<td>{{ date('d/m/Y', strtotime($quotation->expiration_date)) }}</td>
								<td>{{ $quotation->client->title }}</td>
								<td>${{ number_format($quotation->total, 2, ',', '.') }} {{ $quotation->currency }}</td>
								<td>
									@switch($quotation->status)
										@case('draft')
											<span class="badge badge-secondary">Borrador</span>
											@break
										@case('sent')
											<span class="badge badge-primary">Enviada</span>
											@break
										@case('accepted')
											<span class="badge badge-success">Aceptada</span>
											@break
										@case('rejected')
											<span class="badge badge-danger">Rechazada</span>
											@break
									@endswitch
								</td>
								<td class="text-center">
									<a class="btn btn-sm btn-primary m-1" href="{{ route('admin.quotations.show', $quotation->id) }}" title="VER">
										<i class="fa fa-fw fa-eye" aria-hidden="true"></i>
									</a>
									@if($quotation->status !== 'accepted')
										<a class="btn btn-sm btn-warning m-1" href="{{ route('admin.quotations.edit', $quotation->id) }}" title="EDITAR">
											<i class="fa fa-fw fa-wrench" aria-hidden="true"></i>
										</a>
									@else
										<button type="button" class="btn btn-sm btn-secondary m-1" disabled title="Cotización aceptada — no editable">
											<i class="fa fa-fw fa-lock" aria-hidden="true"></i>
										</button>
									@endif
									<a class="btn btn-sm btn-info m-1" href="{{ route('admin.quotations.duplicate', $quotation->id) }}" title="DUPLICAR" onclick="return confirm('¿Desea duplicar esta cotización?');">
										<i class="fa fa-fw fa-copy" aria-hidden="true"></i>
									</a>
									<a class="btn btn-sm btn-dark m-1" href="{{ route('admin.quotations.pdf', $quotation->id) }}" target="_blank" title="PDF">
										<i class="fa fa-fw fa-file-pdf-o" aria-hidden="true"></i>
									</a>
									<form action="{{ route('admin.quotations.destroy', $quotation->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
										<input type="hidden" name="_method" value="delete">
										<input type="hidden" name="_token" value="{{ csrf_token() }}">
										<button type="submit" class="btn btn-sm btn-danger m-1" title="ELIMINAR">
											<i class="fa fa-fw fa-trash" aria-hidden="true"></i>
										</button>
									</form>
								</td>
							</tr>
						@endforeach
					</tbody>
				</table>
			</div>
		</div>
	</div>
@endsection
@section('scripts')
@parent
<script>
	$(function () {
		let deleteButton = {
			text: 'Eliminar seleccionados',
			url: "{{ route('admin.quotations.mass_destroy') }}",
			className: 'btn-danger',
			action: function (e, dt, node, config)
			{
				var ids = $.map(dt.rows({ selected: true }).nodes(), function (entry) {
					return $(entry).data('entry-id')
				});

				if (ids.length === 0) {
					alert('{{ trans('global.datatables.zero_selected') }}')
					return
				}

				if (confirm('{{ trans('global.areYouSure') }}')) {
					$.ajax({
						headers: { 'x-csrf-token': _token },
						method: 'POST',
						url: config.url,
						data: { ids: ids, _method: 'DELETE' }
					}).done(function () { location.reload() });
				}
			}
		};

		// Override global DataTable button config for quotations: only Excel + ColVis + Delete
		let customButtons = [
			{
				extend: 'excel',
				className: 'btn-default',
				text: '<i class="fa fa-file-excel-o mr-1"></i> Exportar a Excel',
				exportOptions: { columns: ':visible' }
			},
			{
				extend: 'colvis',
				className: 'btn-default',
				text: 'Visibilidad de columnas',
				exportOptions: { columns: ':visible' }
			},
			deleteButton
		];

		$.extend(true, $.fn.dataTable.defaults, {
			order: [[ 1, 'desc' ]],
			pageLength: 100,
		});

		$('.datatable-quotations:not(.ajaxTable)').DataTable({ buttons: customButtons })

		$('.show-filters').click(function() {
			$('#filters').toggleClass('d-none');
		});
	});
</script>
@endsection
