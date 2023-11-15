@extends('layouts.admin')
@section('content')
	<div class="row mb-4 filters">
        <div class="col-md-6">
            <h1>Ventas</h1>
		</div>
		<div class="col-md-6 pt-2">
			<form method="POST" action="{{ route('admin.sales.index') }}" class="d-none d-md-flex">
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
			<form method="POST" action="{{ route('admin.sales.index') }}" class="d-block d-md-none">
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
	{{-- @can('create_sales') --}}
		<div style="margin-bottom: 10px;" class="row">
			<div class="col-lg-12">
				<a class="btn btn-success" href="{{ route("admin.sales.create") }}">
					<i class="fa fa-fw fa-plus mr-2 d-inline"></i>Registrar Venta
				</a>
			</div>
		</div>
	{{-- @endcan --}}
	<div class="card">
		<div class="card-header">
			Gestión de Ventas
		</div>
		<div class="card-body">
			<div class="table-responsive">
				<table class="table table-bordered table-striped table-hover datatable datatable-sales">
					<thead>
						<tr>
							<th width="10"></th>
							<th>Fecha</th>
							<th>Tipo</th>
							<th>Código</th>
							<th>Cliente</th>
							<th>Vendedor</th>
							<th>Forma Pago</th>
							<th>Total</th>
							<th>Comisión</th>
							<th width="130">&nbsp;</th>
						</tr>
					</thead>
					<tbody>
						@foreach($sales as $key => $sale)
							<tr data-entry-id="{{ $sale->id }}">
								<td></td>
								<td>{{ date('d/m/Y', strtotime($sale->registered_at)) }}</td>
								<td><span class="badge badge-{{ $sale->invoice_type == 'factura' ? 'warning' : 'info' }}">{{ mb_strtoupper($sale->invoice_type) }}</span></td>
								<td>{{ $sale->invoice_number }}</td>
								<td>{{ $sale->client->title }}</td>
								<td>{{ $sale->seller->user->getFullname() }}</td>
								<td>{{ $sale->payment_method }}</td>
								<td>${{ number_format($sale->total, 2, ',', '.') }} USD</td>
								<td>${{ number_format($sale->commission, 2, ',', '.') }} USD</td>
								<td class="text-center">
									<a class="btn btn-sm btn-primary" href="{{ route('admin.sales.show', $sale->id) }}" title="VER">
										<i class="fa fa-fw fa-eye" aria-hidden="true"></i>
									</a>
									<a class="btn btn-sm btn-warning" href="{{ route('admin.sales.edit', $sale->id) }}" title="EDITAR">
										<i class="fa fa-fw fa-wrench" aria-hidden="true"></i>
									</a>
									<a class="btn btn-sm btn-dark" href="{{ $sale->trello }}" target="_blank" title="TRELLO">
										<i class="fa fa-fw fa-link" aria-hidden="true"></i>
									</a>
									<form action="{{ route('admin.sales.destroy', $sale->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
										<input type="hidden" name="_method" value="delete">
										<input type="hidden" name="_token" value="{{ csrf_token() }}">
										<button type="submit" class="btn btn-sm btn-danger" title="ELIMINAR">
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
        let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
        let deleteButtonTrans = 'Eliminar seleccionados'
        let deleteButton = {
            text: deleteButtonTrans,
            url: "{{ route('admin.sales.mass_destroy') }}",
            className: 'btn-danger',
            action: function (e, dt, node, config) {
            var ids = $.map(dt.rows({ selected: true }).nodes(), function (entry) {
                return $(entry).data('entry-id')
            });

            if (ids.length === 0) {
                alert('{{ trans('global.datatables.zero_selected') }}')
                return
            }

            if (confirm('{{ trans('global.areYouSure') }}')) {
                $.ajax({
                    headers: {'x-csrf-token': _token},
                    method: 'POST',
                    url: config.url,
                    data: { ids: ids, _method: 'DELETE' }})
                    .done(function () { location.reload() })
                }
            }
        }
        dtButtons.push(deleteButton)

        $.extend(true, $.fn.dataTable.defaults, {
            order: [[ 1, 'desc' ]],
            pageLength: 100,
        });
        $('.datatable-sales:not(.ajaxTable)').DataTable({ buttons: dtButtons })
            $('a[data-toggle="tab"]').on('shown.bs.tab', function(e){
                $($.fn.dataTable.tables(true)).DataTable()
                    .columns.adjust();
            });
        })
        $('[data-toggle="tooltip"]').tooltip()
</script>
@endsection