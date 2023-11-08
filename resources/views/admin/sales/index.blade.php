@extends('layouts.admin')
@section('content')
{{-- @can('create_sales') --}}
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route("admin.sales.create") }}">
                <i class="fa fa-plus mr-2 d-inline"></i>Registrar Venta
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
                        <th width="100">&nbsp;</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sales as $key => $sale)
                        <tr data-entry-id="{{ $sale->id }}">
                            <td></td>
                            <td>{{ $sale->registered_at }}</td>
                            <td>{{ $sale->type }}</td>
                            <td>{{ $sale->invoice_number }}</td>
                            <td>{{ $sale->client->title }}</td>
                            <td>{{ $sale->seller->user->getFullname() }}</td>
                            <td>{{ $sale->payment_method }}</td>
                            <td>{{ $sale->total }}</td>
                            <td class="text-center">
								<a class="btn btn-sm btn-primary" href="{{ route('admin.sales.show', $sale->id) }}" title="VER">
                                    <i class="fa fa-eye" aria-hidden="true"></i>
                                </a>
								<a class="btn btn-sm btn-waring" href="{{ route('admin.sales.edit', $sale->id) }}" title="EDITAR">
									<i class="fa fa-wrench" aria-hidden="true"></i>
								</a>
                                <form action="{{ route('admin.sales.destroy', $sale->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
                                    <input type="hidden" name="_method" value="delete">
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    <button type="submit" class="btn btn-sm btn-danger" title="ELIMINAR">
										<i class="fa fa-trash" aria-hidden="true"></i>
									</button>
                                </form>
								<a class="btn btn-sm btn-dark" href="{{ $sale->trello }}" title="TRELLO">
                                    <i class="fa fa-trello" aria-hidden="true"></i>
                                </a>
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