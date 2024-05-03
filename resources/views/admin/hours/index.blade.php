@php
	use App\Models\Attendance;
	use App\Models\AttendanceRecord;
@endphp

@extends('layouts.admin')
@section('content')
	<div class="row mb-2 filters">
        <div class="col-md-6">
            <h1>
				Horas Extras
				<a class="btn btn-sm btn-dark text-light pull-right d-block d-md-none mt-2 show-filters" rel="filters">FILTROS</a>
			</h1>
		</div>
		<div class="col-md-6">
			<form method="POST" action="{{ route('admin.hours.index') }}" class="d-none d-md-flex pt-2 filters-form">
				@csrf
				<div class="col">
					<div class="input-group justify-content-end">
						<label class="input-group-text" for="start_date">Periodo</label>
						<select class="form-select" id="period" name="period">
							<option value="all">Todos</option>
							@foreach ($periods as $val)
								<option value="{{ $val->period }}" {{ $val->period == $period ? 'selected' : '' }}>{{ $val->period }}</option>
							@endforeach
						</select>
						<button class="btn btn-default" type="submit"><b>Filtrar</b></button>
					</div>
				</div>
			</form>
			<form method="POST" action="{{ route('admin.sales.index') }}" class="d-none pt-2" id="filters">
				@csrf
				<label class="input-group-text" for="start_date">Periodo</label>
				<select class="form-select" id="period" name="period">
					<option value="all">Todos</option>
					@foreach ($periods as $val)
						<option value="{{ $val->period }}" {{ $val->period == $period ? 'selected' : '' }}>{{ $val->period }}</option>
					@endforeach
				</select>
				<div class="col text-right mt-3">
					<button class="btn btn-default" type="submit"><b>Filtrar</b></button>
				</div>
			</form>
		</div>
		<div class="col-12"><hr></div>
	</div>
    {{-- @can('create_employees') --}}
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-sm-6">
			<form class="form-inline" action="{{ route('admin.hours.upload') }}" method="post" enctype="multipart/form-data">
				@csrf
				<div class="input-group">
					<span class="input-group-addon btn bg-light p-2" id="basic-file"><i class="fa fa-fw fa-plus mr-2 d-inline"></i>Importar Registros</span>
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
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Empleado</th>
                            <th>Departamento</th>
                            <th>Período</th>
                            {{-- <th>Horas Trabajadas</th> --}}
                            <th>Horas Extras</th>
                            <th class="text-right">Total Extras</th>
                            <th class="text-center">Pago</th>
                            <th width="120">&nbsp;</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($employees as $key => $employee)
							@php
								// $attendances = Attendance::where('employee_id', $employee->id)->where('year', $periodYear)->where('year', $periodMonth)->orderBy('id', 'desc')->first();
								$attendances = $employee->attendances()->latest()->first();
								$totalPayment = ($employee->salary * $attendances->extra) ?? 0;
							@endphp
                            <tr data-entry-id="{{ $employee->id }}">
                                <td>{{ $employee->number }}</td>
                                <td>{{ $employee->name . ' ' . $employee->lastname }}</td>
                                <td>{{ $employee->department }}</td>
                                <td>{{ $attendances->year . '-' . $attendances->month }}</td>
                                {{-- <td class="text-right">{{ $attendances->hours }}</td> --}}
                                <td>{{ $attendances->extra }}</td>
                                <td class="text-right">${{ number_format($totalPayment, 2, ',', '.') }} USD</td>
                                <td class="text-center"><span class="badge badge-{{ $attendances->payment == 'pending' ? 'warning' : 'success' }}">{{ $attendances->payment == 'pending' ? 'PENDIENTE' : 'REALIZADO' }}</span></td>
                                <td class="text-center">
									<a class="btn btn-sm btn-success m-1 show-records" href="#" data-employee="{{ $employee->id }}" title="VER ASISTENCIAS">
                                        <i class="fa fa-fw fa-calendar" aria-hidden="true"></i>
                                    </a>
                                    <a class="btn btn-sm btn-primary m-1" href="{{ route('admin.employees.show', $employee->id) }}" title="VER EMPLEADO">
                                        <i class="fa fa-fw fa-eye" aria-hidden="true"></i>
                                    </a>
                                </td>
                            </tr>
							@php
								// $records = AttendanceRecord::where('attendance_id', $attendances->id)->orderBy('id', 'asc')->get();
								$records = $attendances->records()->get();
							@endphp
							@if (count($records) > 0)
								<tr class="d-none" id="records-{{ $employee->id }}">
									<td colspan="8">
										<table class="table table-sm table-hover">
											<thead>
												<tr>
													<th>
														Fecha
													</th>
													<th>
														Día
													</th>
													<th>
														Entrada
													</th>
													<th>
														Salida
													</th>
													<th>
														Horas Registradas
													</th>
													<th>
														Horas Extras
													</th>
													<th>
														Comentarios
													</th>
													<th width="60">&nbsp;</th>
												</tr>
											</thead>
											<tbody>
												@foreach ($records as $record)
													<tr class="{{ $record->day == 'sábado' || $record->day == 'domingo' ? 'table-secondary text-muted' : '' }}">
														<td>
															{{ $record->date }}
														</td>
														<td>
															{{ ucwords($record->day) }}
														</td>
														<td>
															{{ $record->entry }}
														</td>
														<td>
															{{ $record->exit }}
														</td>
														<td>
															{{ $record->hours }}
														</td>
														<td class="{{ $record->extra > 0 ? 'font-weight-bold' : '' }}">
															{{ $record->extra }}
														</td>
														<td>
															{{ $record->comments }}
														</td>
														<td>
															<a class="btn btn-sm btn-secondary m-1" href="#" title="COMENTAR">
																<i class="fa fa-fw fa-comment" aria-hidden="true"></i>
															</a>
														</td>
													</tr>
												@endforeach
											</tbody>
										</table>
									</td>
								</tr>
							@endif
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
                    [1, 'asc']
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

			$('.show-records').on('click', function(event) {
				event.preventDefault();
				let employee = $(this).data('employee');
				$('#records-' + employee).toggleClass('d-none');
			});
        })
        $('[data-toggle="tooltip"]').tooltip()
    </script>
@endsection
