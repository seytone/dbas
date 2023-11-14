@extends('layouts.admin')
@section('content')
	<div class="row mb-4 filters">
        <div class="col-12">
            <h1>Servicios</h1>
		</div>
		<div class="col-12"><hr></div>
	</div>
    {{-- @can('create_services') --}}
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route('admin.services.create') }}">
                <i class="fa fa-fw fa-plus mr-2 d-inline"></i>Nuevo Servicio
            </a>
        </div>
    </div>
    {{-- @endcan --}}
    <div class="card">
        <div class="card-header">
            Gestión de Servicios
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover datatable datatable-services">
                    <thead>
                        <tr>
                            <th width="10"></th>
                            <th>Código</th>
                            <th>Servicio</th>
                            <th>Precio</th>
                            <th width="100">&nbsp;</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($services as $key => $service)
                            <tr data-entry-id="{{ $service->id }}">
                                <td></td>
                                <td>{{ $service->code }}</td>
                                <td>{{ $service->title }}</td>
                                <td>{{ $service->price }}</td>
                                <td class="text-center">
                                    <a class="btn btn-sm btn-primary" href="{{ route('admin.services.show', $service->id) }}" title="VER">
                                        <i class="fa fa-fw fa-eye" aria-hidden="true"></i>
                                    </a>
                                    <a class="btn btn-sm btn-warning" href="{{ route('admin.services.edit', $service->id) }}" title="EDITAR">
                                        <i class="fa fa-fw fa-wrench" aria-hidden="true"></i>
                                    </a>
                                    <form action="{{ route('admin.services.destroy', $service->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
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
        $(function() {
            let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
            let deleteButtonTrans = 'Eliminar seleccionados'
            let deleteButton = {
                text: deleteButtonTrans,
                url: "{{ route('admin.services.mass_destroy') }}",
                className: 'btn-danger',
                action: function(e, dt, node, config) {
                    var ids = $.map(dt.rows({
                        selected: true
                    }).nodes(), function(entry) {
                        return $(entry).data('entry-id')
                    });

                    if (ids.length === 0) {
                        alert('{{ trans('global.datatables.zero_selected') }}')
                        return
                    }

                    if (confirm('{{ trans('global.areYouSure') }}')) {
                        $.ajax({
                                headers: {
                                    'x-csrf-token': _token
                                },
                                method: 'POST',
                                url: config.url,
                                data: {
                                    ids: ids,
                                    _method: 'DELETE'
                                }
                            })
                            .done(function() {
                                location.reload()
                            })
                    }
                }
            }
            dtButtons.push(deleteButton)

            $.extend(true, $.fn.dataTable.defaults, {
                order: [
                    [1, 'desc']
                ],
                pageLength: 100,
            });
            $('.datatable-services:not(.ajaxTable)').DataTable({
                buttons: dtButtons
            })
            $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
                $($.fn.dataTable.tables(true)).DataTable()
                    .columns.adjust();
            });
        })
        $('[data-toggle="tooltip"]').tooltip()
    </script>
@endsection
