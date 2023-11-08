@extends('layouts.admin')
@section('content')
	<br><br>
    <div class="card">
        <div class="card-header">
            Gestión de Vendedores
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover datatable datatable-sellers">
                    <thead>
                        <tr>
                            <th width="10"></th>
                            <th>Vendedor</th>
                            <th>% Licencias Perpétuas</th>
                            <th>% Suscripciones Anuales</th>
                            <th>% Hardware y Otros</th>
                            <th>% Servicios</th>
                            <th width="90">&nbsp;</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($sellers as $key => $seller)
                            <tr data-entry-id="{{ $seller->id }}">
                                <td></td>
                                <td>{{ $seller->user->getFullname() }}</td>
                                <td>{{ $seller->commission_1 }}</td>
                                <td>{{ $seller->commission_2 }}</td>
                                <td>{{ $seller->commission_3 }}</td>
                                <td>{{ $seller->commission_4 }}</td>
                                <td class="text-center">
                                    <a class="btn btn-sm btn-primary" href="{{ route('admin.users.show', $seller->user_id) }}" title="VER">
                                        <i class="fa fa-eye" aria-hidden="true"></i>
                                    </a>
                                    <a class="btn btn-sm btn-warning" href="{{ route('admin.users.edit', $seller->user_id) }}" title="EDITAR">
                                        <i class="fa fa-wrench" aria-hidden="true"></i>
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
        $(function() {
            let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
            let deleteButtonTrans = 'Eliminar seleccionados'
            let deleteButton = {
                text: deleteButtonTrans,
                url: "{{ route('admin.sellers.mass_destroy') }}",
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
            // dtButtons.push(deleteButton)

            $.extend(true, $.fn.dataTable.defaults, {
                order: [
                    [1, 'desc']
                ],
                pageLength: 100,
            });
            $('.datatable-sellers:not(.ajaxTable)').DataTable({
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
