@extends('layouts.admin')
@section('content')
	<div class="row mb-4 filters">
        <div class="col-12">
            <h1>Horas Extras</h1>
		</div>
		<div class="col-12"><hr></div>
	</div>
    {{-- @can('create_employees') --}}
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-sm-6">
			<form class="form-inline" action="{{ route('admin.hours.upload') }}" method="post" enctype="multipart/form-data">
				@csrf
				<div class="input-group">
					<span class="input-group-addon bg-light p-2" id="basic-file"><i class="fa fa-fw fa-plus mr-2 d-inline"></i>Importar Registros</span>
					<input type="text" class="form-control" placeholder="Archivo exel..." id="placetext" aria-describedby="basic-addon2" disabled='true'>
					<input type="file" class="d-none" name="excel" id="excel"/>
				</div>
				<button type="submit" class="btn btn-success d-none ml-2 my-1" id="submit"><i class="fa fa-fw fa-upload mr-2 d-inline"></i>Cargar</button>
			</form>
        </div>
    </div>
    {{-- @endcan --}}
    <div class="card">
        <div class="card-header">
            Registro de Asistencia
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover datatable datatable-employees">
                    <thead>
                        <tr>
                            <th width="10"></th>
                            <th>ID</th>
                            <th>Empleado</th>
                            <th>Departamento</th>
                            <th>Cargo</th>
                            <th width="120">&nbsp;</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- @foreach ($employees as $key => $employee)
                            <tr data-entry-id="{{ $employee->id }}">
                                <td></td>
                                <td>{{ $employee->number }}</td>
                                <td>{{ $employee->name . ' ' . $employee->lastname }}</td>
                                <td>{{ $employee->department }}</td>
                                <td>{{ $employee->position }}</td>
                                <td class="text-center">
                                    <a class="btn btn-sm btn-primary m-1" href="{{ route('admin.employees.show', $employee->id) }}" title="VER">
                                        <i class="fa fa-fw fa-eye" aria-hidden="true"></i>
                                    </a>
                                    <a class="btn btn-sm btn-warning m-1" href="{{ route('admin.employees.edit', $employee->id) }}" title="EDITAR">
                                        <i class="fa fa-fw fa-wrench" aria-hidden="true"></i>
                                    </a>
                                    <form action="{{ route('admin.employees.destroy', $employee->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
                                        <input type="hidden" name="_method" value="delete">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <button type="submit" class="btn btn-sm btn-danger m-1" title="ELIMINAR">
                                            <i class="fa fa-fw fa-trash" aria-hidden="true"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach --}}
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
                url: "{{ route('admin.employees.mass_destroy') }}",
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
            $('.datatable-employees:not(.ajaxTable)').DataTable({
                buttons: dtButtons
            })
            $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
                $($.fn.dataTable.tables(true)).DataTable()
                    .columns.adjust();
            });

			$('#basic-file').on('click', function(event) {
                event.preventDefault();
                $('#excel').click();
            });

            $('#excel').on('change', function() {
                $('#placetext').val($(this)[0].files[0].name);
				$('#submit').removeClass('d-none');
            });
        })
        $('[data-toggle="tooltip"]').tooltip()
    </script>
@endsection
