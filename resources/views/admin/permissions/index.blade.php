@extends('layouts.admin')
@section('content')
	<div class="row mb-4 filters">
        <div class="col-12">
            <h1>Permisos</h1>
		</div>
		<div class="col-12"><hr></div>
	</div>
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route('admin.permissions.create') }}">
                <i class="fa fa-fw fa-plus mr-2 d-inline"></i>{{ trans('global.add') }} {{ trans('cruds.permission.title_singular') }}
            </a>
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            {{ trans('cruds.permission.title_singular') }} {{ trans('global.list') }}
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class=" table table-bordered table-striped table-hover datatable datatable-Permission">
                    <thead>
                        <tr>
                            <th width="10"></th>
                            <th>
                                {{ trans('cruds.permission.fields.id') }}
                            </th>
                            <th>
                                {{ trans('cruds.permission.fields.title') }}
                            </th>
                            <th width="100">&nbsp;</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($permissions as $key => $permission)
                            <tr data-entry-id="{{ $permission->id }}">
                                <td></td>
                                <td>
                                    {{ $permission->id ?? '' }}
                                </td>
                                <td>
                                    {{ $permission->name ?? '' }}
                                </td>
								<td class="text-center">
                                    <a class="btn btn-sm btn-primary" href="{{ route('admin.permissions.show', $permission->id) }}" title="VER">
                                        <i class="fa fa-fw fa-eye" aria-hidden="true"></i>
                                    </a>
                                    <a class="btn btn-sm btn-warning" href="{{ route('admin.permissions.edit', $permission->id) }}" title="EDITAR">
                                        <i class="fa fa-fw fa-wrench" aria-hidden="true"></i>
                                    </a>
                                    <form action="{{ route('admin.permissions.destroy', $permission->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
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
            let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
            let deleteButton = {
                text: deleteButtonTrans,
                url: "{{ route('admin.permissions.mass_destroy') }}",
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
            $('.datatable-Permission:not(.ajaxTable)').DataTable({
                buttons: dtButtons
            })
            $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
                $($.fn.dataTable.tables(true)).DataTable()
                    .columns.adjust();
            });
        })
    </script>
@endsection
