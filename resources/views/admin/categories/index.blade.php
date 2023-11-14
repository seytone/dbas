@extends('layouts.admin')
@section('content')
	<div class="row mb-4 filters">
        <div class="col-12">
            <h1>Categorías</h1>
		</div>
		<div class="col-12"><hr></div>
	</div>
    {{-- @can('create_categories') --}}
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route('admin.categories.create') }}">
                <i class="fa fa-fw fa-plus mr-2 d-inline"></i>Nuevo Categoría
            </a>
        </div>
    </div>
    {{-- @endcan --}}
    <div class="card">
        <div class="card-header">
            Gestión de Categorías
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover datatable datatable-categories">
                    <thead>
                        <tr>
                            <th width="10"></th>
                            <th>Titulo</th>
                            <th>Descripción</th>
                            <th width="100">&nbsp;</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($categories as $key => $category)
                            <tr data-entry-id="{{ $category->id }}">
                                <td></td>
                                <td>{{ $category->title }}</td>
                                <td>{{ $category->description }}</td>
                                <td class="text-center">
                                    <a class="btn btn-sm btn-primary" href="{{ route('admin.categories.show', $category->id) }}" title="VER">
                                        <i class="fa fa-fw fa-eye" aria-hidden="true"></i>
                                    </a>
                                    <a class="btn btn-sm btn-warning" href="{{ route('admin.categories.edit', $category->id) }}" title="EDITAR">
                                        <i class="fa fa-fw fa-wrench" aria-hidden="true"></i>
                                    </a>
                                    <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
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
                url: "{{ route('admin.categories.mass_destroy') }}",
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
            $('.datatable-categories:not(.ajaxTable)').DataTable({
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
