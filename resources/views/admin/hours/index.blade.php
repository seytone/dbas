@php
	use App\Models\Attendance;
	use App\Models\AttendanceRecord;
@endphp

@extends('layouts.admin')
@section('content')
	<div class="row mb-2">
        <div class="col-8">
            <h1>Horas Extras</h1>
		</div>
		<div class="col-4">
			@if (Auth::user()->hasRole('Superadmin'))
				<div class="float-right">
					<form class="form-inline" action="{{ route('admin.update_config') }}" method="post" title="NOTA: Recuerda que si actualizas este dato debes volver a importar el reporte de asistencia.">
						@csrf
						<input type="hidden" name="key" value="extra_time_ini">
						<div class="input-group">
							<span class="input-group-addon btn bg-light px-2" id="basic-addon2"><i class="fa fa-fw fa-cog mr-2 d-inline"></i>Minutos tiempo extra</span>
							<input type="text" class="form-control text-center" placeholder="20" name="value" value="{{ $extraTimeIni }}" aria-describedby="basic-addon2" min="0" required>
							<div class="input-group-append">
								<button type="submit" class="btn btn-dark btn-sm" id="basic-addon2"><i class="fa fa-fw fa-save"></i></button>
							</div>
						</div>
					</form>
				</div>
			@endif
		</div>
		<div class="col-12"><hr></div>
	</div>
    {{-- @can('create_employees') --}}
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-md-8">
			<form class="form-inline" action="{{ route('admin.hours.upload') }}" method="post" enctype="multipart/form-data">
				@csrf
				<div class="input-group">
					<span class="input-group-addon btn bg-light px-2" id="basic-file"><i class="fa fa-fw fa-plus mr-2 d-inline"></i>Importar Registros</span>
					<input type="text" class="form-control" placeholder="Archivo exel..." id="placetext" aria-describedby="basic-addon2" disabled='true'>
					<input type="file" class="d-none" name="excel" id="excel"/>
				</div>
				<button type="submit" class="btn btn-success d-none ml-2 my-1" id="submit"><i class="fa fa-fw fa-upload mr-2 d-inline"></i>Cargar</button>
			</form>
        </div>
		<div class="col-md-4">
			<form class="filters-form pt-2" action="{{ route('admin.hours.index') }}" method="POST" id="filters">
				@csrf
				<div class="input-group justify-content-end">
					<label class="input-group-text" for="start_date"><i class="fa fa-fw fa-calendar mr-2 d-inline"></i>Periodo</label>
					<select class="form-control text-right period" name="period" rel="{{ $period }}">
						@foreach ($periods as $val)
							<option value="{{ $val->period }}" {{ $val->period == $period ? 'selected' : '' }}>{{ $val->period }}</option>
						@endforeach
					</select>
				</div>
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
                            <th>Tiempo Extra</th>
                            <th class="text-right pr-5">Costo Extra</th>
                            <th class="text-center">Pago</th>
                            <th width="180">&nbsp;</th>
                        </tr>
                    </thead>
                    <tbody>
						@if (count($employees) == 0)
							<tr>
								<td colspan="8" class="text-center">--- No hay registros ---</td>
							</tr>
						@else
							@foreach ($employees as $key => $employee)
								@php
									$attendances = Attendance::where('employee_id', $employee->id)->where('year', $periodYear)->where('month', $periodMonth)->orderBy('id', 'desc')->first();
								@endphp
								@if ($attendances == null)
									<tr>
										<td>{{ $employee->number }}</td>
										<td>{{ $employee->name . ' ' . $employee->lastname }}</td>
										<td>{{ $employee->department }}</td>
										<td>---</td>
										<td>---</td>
										<td class="text-right pr-5">---</td>
										<td class="text-center">---</td>
										<td class="text-right">
											@can('manage_employees')
												<a class="btn btn-sm btn-primary m-1" href="{{ route('admin.employees.show', $employee->id) }}" title="VER EMPLEADO">
													<i class="fa fa-fw fa-eye" aria-hidden="true"></i>
												</a>
											@endcan
										</td>
									</tr>
								@else
									@php
										$hourlyRate = $employee->salary;
										$minuteRate = $hourlyRate / 60;
									@endphp
									<tr class="text-primary" data-entry-id="{{ $employee->id }}">
										<td>{{ $employee->number }}</td>
										<td>{{ $employee->name . ' ' . $employee->lastname }}</td>
										<td>{{ $employee->department ?? '---' }}</td>
										<td>{{ $attendances->year . '-' . $attendances->month }}</td>
										<td>{{ $attendances->extra }} min</td>
										<td class="text-right pr-5" id="extraCost-{{ $employee->id }}" data-total="{{ $minuteRate * $attendances->extra }}">${{ number_format(($minuteRate * $attendances->extra) ?? 0, 2, '.', ',') }} USD</td>
										<td class="text-center"><span class="badge badge-{{ $attendances->payment == 'pending' ? 'danger' : 'success' }}">{{ $attendances->payment == 'pending' ? 'PENDIENTE' : 'HECHO EL ' . $attendances->payment_date }}</span></td>
										<td class="text-right">
											@if($attendances->payment == 'pending')
												<a class="btn btn-sm btn-success m-1 pay-employee" href="#" data-attendance="{{ $attendances->id }}" title="MARCAR COMO PAGADO">
													<i class="fa fa-fw fa-check" aria-hidden="true"></i>
												</a>
											@endif
											<a class="btn btn-sm btn-warning m-1 show-records" href="#" data-employee="{{ $employee->id }}" title="VER ASISTENCIAS">
												<i class="fa fa-fw fa-calendar" aria-hidden="true"></i>
											</a>
											@can('manage_employees')
												<a class="btn btn-sm btn-primary m-1" href="{{ route('admin.employees.show', $employee->id) }}" title="VER EMPLEADO">
													<i class="fa fa-fw fa-eye" aria-hidden="true"></i>
												</a>
											@endcan
										</td>
									</tr>
									@php
										$records = AttendanceRecord::where('attendance_id', $attendances->id)->orderBy('id', 'asc')->get();
									@endphp
									@if (count($records) > 0)
										<tr class="d-none" id="records-{{ $employee->id }}">
											<td colspan="8">
												<table class="table table-sm table-hover">
													<thead>
														<tr class="bg-dark text-white">
															<th class="text-center">
																Fecha
															</th>
															<th>
																Día
															</th>
															<th class="text-center">
																Hora Entrada
															</th>
															<th class="text-center">
																Hora Salida
															</th>
															<th class="text-center">
																Minutos Adicionales
															</th>
															<th class="text-center">
																Corresponde
															</th>
															<th class="text-right pr-5">
																Pago Adicional
															</th>
															<th width="300">
																Comentarios
															</th>
															<th width="60">&nbsp;</th>
														</tr>
													</thead>
													<tbody>
														@foreach ($records as $record)
															@php
																$entryT = explode(':', $record->entry);
																$entryH = intval($entryT[0]);
																$entryM = count($entryT) > 1 ? intval($entryT[1]) : 0;
																$exitT = !empty($record->exit) ? explode(':', $record->exit) : [0,0];
																$exitH = intval($exitT[0]);
																$exitM = count($exitT) > 1 ? intval($exitT[1]) : 0;
															@endphp
															<tr class="{{ $record->day == 'sábado' || $record->day == 'domingo' ? 'table-secondary text-muted' : '' }}">
																<td class="text-center">
																	{{ $record->date }}
																</td>
																<td>
																	{{ ucwords($record->day) }}
																</td>
																<td class="{{ ($entryH >= 8 && $entryM >= 1) || $entryH >= 9 ? 'text-danger' : '' }} {{ empty($record->entry) ? 'text-black-50' : '' }} text-center" rel="{{ $entryH . ':' . $entryM }}">
																	{{ !empty($record->entry) ? $record->entry : '---' }}
																</td>
																<td class="{{ ($exitH >= 17 && $exitM >= ($extraTimeIni + 1)) || $exitH >= 18 ? 'text-danger' : '' }} {{ empty($record->exit) ? 'text-black-50' : '' }} text-center" rel="{{ $exitH . ':' . $exitM }}">
																	{{ !empty($record->exit) ? $record->exit : '---' }}
																</td>
																<td class="{{ $record->extra_time > $extraTimeIni ? 'font-weight-bold' : 'text-black-50' }} text-center">
																	{{-- {{ $record->extra_time > $extraTimeIni ? $record->extra_time : 'N/A'}} --}}
																	{{ $record->extra_time }}
																</td>
																<td class="{{ $record->extra_time > $extraTimeIni ? 'font-weight-bold' : 'text-black-50' }} text-center">
																	@if (($exitH >= 17 && $exitM >= 1) || $exitH >= 18)
																		<div class="form-check form-check-inline">
																			<label class="form-check-label" for="applyExtraNo-{{ $record->id }}">
																				<input type="radio" id="applyExtraNo-{{ $record->id }}" name="applyExtra-{{ $record->id }}" value="0" rel="{{ $record->id }}" class="form-check-input applyPayment" data-employee="{{ $employee->id }}" data-time="{{ $record->extra_time }}" data-payment="{{ $minuteRate * $record->extra_time }}" {{ $record->apply ? '' : 'checked' }}>
																				<span style="position: relative; top: -2px;">NO</span>
																			</label>
																		</div>
																		<div class="form-check form-check-inline">
																			<label class="form-check-label" for="applyExtraSi-{{ $record->id }}">
																				<input type="radio" id="applyExtraSi-{{ $record->id }}" name="applyExtra-{{ $record->id }}" value="1" rel="{{ $record->id }}" class="form-check-input applyPayment" data-employee="{{ $employee->id }}" data-time="{{ $record->extra_time }}" data-payment="{{ $minuteRate * $record->extra_time }}" {{ $record->apply ? 'checked' : '' }}>
																				<span style="position: relative; top: -2px;">SI</span>
																			</label>
																		</div>
																	@else
																		N/A
																	@endif
																</td>
																<td class="{{ $record->extra_time > $extraTimeIni ? 'font-weight-bold' : 'text-black-50' }} text-right pr-5" id="payExtra-{{ $record->id }}" rel="{{ $minuteRate }}">
																	{{-- {{ $record->extra_time > $extraTimeIni ? '$' . number_format(($minuteRate * $record->extra_time) ?? 0, 2, '.', ',') . ' USD' : 'N/A' }} --}}
																	{{ $record->extra_time > 0 ? '$' . number_format(($minuteRate * $record->extra_time) ?? 0, 2, '.', ',') . ' USD' : 'N/A' }}
																</td>
																<td class="text-center text-black-50">
																	@if (($exitH >= 17 && $exitM >= 1) || $exitH >= 18)
																		<input type="text" name="comment" class="form-control form-control-sm commentTXT" id="commentTXT-{{ $record->id }}" rel="{{ $record->id }}" value="{{ $record->comments }}">
																	@else
																		N/A
																	@endif
																</td>
																<td>
																	@if (($exitH >= 17 && $exitM >= 1) || $exitH >= 18)
																		<a href="#" title="COMENTAR" class="btn btn-sm btn-secondary m-1 commentBTN" id="commentBTN-{{ $record->id }}" rel="{{ $record->id }}">
																			<i class="fa fa-fw fa-comment" aria-hidden="true"></i>
																		</a>
																	@endif
																</td>
															</tr>
														@endforeach
													</tbody>
												</table>
											</td>
										</tr>
									@endif
								@endif
							@endforeach
						@endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    @parent
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@8"></script>
    <script>
        $(function() {
			@if ($request->session()->has('success'))
				Swal.fire({
					toast: true,
					position: 'bottom-end',
					icon: 'success',
					title: '¡Éxito!',
					text: '{{ $request->session()->get('success') }}',
					timer: 2000,
					showConfirmButton: false
				});
			@endif

			@if ($request->session()->has('error'))
				Swal.fire({
					toast: true,
					position: 'bottom-end',
					icon: 'error',
					title: 'Error!',
					text: '{{ $request->session()->get('error') }}',
					timer: 3000,
					showConfirmButton: false
				});
			@endif

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
            });

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

			$('.period').on('change', function() {
				$('#filters').submit();
			});

			$('.show-records').on('click', function(event) {
				event.preventDefault();
				let employee = $(this).data('employee');
				$('#records-' + employee).toggleClass('d-none');
			});

			$('.pay-employee').on('click', function(event) {
				event.preventDefault();
				Swal.fire({
					title: '¿Estás seguro de marcar como pagado?',
					showCancelButton: true,
					confirmButtonText: 'Sí, marcar como pagado',
					cancelButtonText: 'Cancelar'
				}).then((result) => {
					if (result.value === true) {
						let _token = $('meta[name="csrf-token"]').attr('content');
						let attendance = $(this).data('attendance');
						$.ajax({
							headers: {
								'x-csrf-token': _token
							},
							method: 'POST',
							url: "{{ route('admin.hours.pay') }}",
							data: {
								id: attendance
							}
						}).done(function() {
							location.reload();
						});
					}
				});
			});

			$('.commentTXT').on('input', function() {
				let id = $(this).attr('rel');
				let comment = $(this).val();
				if (comment.length > 0) {
					$('#commentBTN-' + id).removeClass('btn-secondary').addClass('btn-success');
				} else {
					$('#commentBTN-' + id).removeClass('btn-success').addClass('btn-secondary');
				}
			});

			$('.commentBTN').on('click', function(event) {
				event.preventDefault();
				let id = $(this).attr('rel');
				let comment = $('#commentTXT-' + id).val();
				if (comment.length > 0) {
					$.ajax({
						headers: {
							'x-csrf-token': _token
						},
						method: 'POST',
						url: "{{ route('admin.hours.comment') }}",
						data: {
							id: id,
							comment: comment
						}
					}).done(function() {
						$('#commentBTN-' + id).removeClass('btn-success').addClass('btn-secondary');
					});
				}
			});

			$('.applyPayment').on('change', function() {
				let id = $(this).attr('rel');
				let value = $(this).val();
				let time = $(this).data('time');
				let payment = $(this).data('payment');
				let employee = $(this).data('employee');
				let total = $('#extraCost-' + employee).data('total');
				
				// if (value == 1) {
				// 	total = total + payment;
				// 	$('#payExtra-' + id).text('$' + parseFloat(payment).toFixed(2) + ' USD');
				// } else {
				// 	total = total - payment;
				// 	$('#payExtra-' + id).text('$0.00 USD');
				// }

				total = value == 1 ? total + payment : total - payment;
				total = total < 0 ? 0 : total;
				
				// Change the Extra Cost Column value according to the extra time
				$('#extraCost-' + employee).text('$' + parseFloat(total).toFixed(2) + ' USD').data('total', total);

				// Update record in database to apply or not the extra time, and update the total payment
				$.ajax({
					headers: {
						'x-csrf-token': _token
					},
					method: 'POST',
					url: "{{ route('admin.hours.apply') }}",
					data: {
						id: id,
						apply: value,
						extra: value == 1 ? time : 0
					}
				});
			});
        })
        $('[data-toggle="tooltip"]').tooltip()
    </script>
@endsection
