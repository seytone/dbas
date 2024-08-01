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
        <div class="col-md-9">
			<form class="form-inline" action="{{ route('admin.hours.upload') }}" method="post" enctype="multipart/form-data">
				@csrf
				<div class="input-group">
					<span class="input-group-addon btn bg-light px-2" id="basic-file"><i class="fa fa-fw fa-plus mr-2 d-inline"></i>Importar Registros</span>
					<input type="text" class="form-control" placeholder="Archivo en formato .xlsx" id="placetext" aria-describedby="basic-addon2" disabled='true'>
					<input type="file" class="d-none" name="excel" id="excel" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"/>
				</div>
				<button type="submit" class="btn btn-success d-none ml-2 my-1" id="submit"><i class="fa fa-fw fa-upload mr-2 d-inline"></i>Cargar</button>
			</form>
        </div>
		<div class="col-md-3">
			<form class="filters-form pt-2" action="{{ route('admin.hours.index') }}" method="POST" id="filters">
				@csrf
				<div class="input-group justify-content-end">
					<label class="input-group-text" for="start_date"><i class="fa fa-fw fa-calendar mr-2 d-inline"></i>Periodo</label>
					<select class="form-control text-right period" name="period" rel="{{ $period }}">
						<option value="all">Todos</option>
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
                            <th class="text-right pr-5">Tiempo Extra</th>
                            <th class="text-right pr-5">Tiempo Deducible</th>
							<th class="text-center" width="120">Ajuste</th>
                            <th class="text-right pr-5">Costo Extra</th>
                            <th class="text-right pr-5">Costo Deducible</th>
                            <th class="text-right pr-5">Pago Total</th>
                            <th class="text-center">Ejecución</th>
                            <th width="180">&nbsp;</th>
                        </tr>
                    </thead>
                    <tbody>
						@if (count($employees) == 0)
							<tr>
								<td colspan="9" class="text-center">--- No hay registros ---</td>
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
										<td class="text-right pr-5">---</td>
										<td class="text-right pr-5">---</td>
										<td class="text-right pr-5">---</td>
										<td class="text-right pr-5">---</td>
										<td class="text-right pr-5">---</td>
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
										$extraTime = $attendances->extra ?? 0;
										$missingTime = $attendances->missing ?? 0;
										$fixedTime = $attendances->manual_fix ?? 0;
										$fixedCost = $minuteRate * $fixedTime;
										$extraCost = $minuteRate * $extraTime;
										$extraCost = $fixedTime < 0 ? $extraCost : $extraCost + $fixedCost;
										$missingCost = $minuteRate * $missingTime;
										$missingCost = $fixedTime > 0 ? $missingCost : $missingCost - $fixedCost;
										$totalCost = $extraCost - $missingCost;
									@endphp
									<tr class="text-primary" data-entry-id="{{ $employee->id }}" data-rate="{{ $minuteRate }}">
										<td>{{ $employee->number }}</td>
										<td>{{ $employee->name . ' ' . $employee->lastname }}</td>
										<td>{{ $employee->department ?? '---' }}</td>
										<td>{{ $attendances->year . '-' . $attendances->month }}</td>
										<td class="text-right pr-5" id="extraTime-{{ $employee->id }}" data-time="{{ $fixedTime > 0 ? $extraTime + $fixedTime : $extraTime }}" data-time-db="{{ $extraTime }}" data-time-fixed="{{ $fixedTime > 0 ? $fixedTime : 0 }}">
											{{ $fixedTime > 0 ? $extraTime + $fixedTime : $extraTime }} min
										</td>
										<td class="text-right pr-5" id="missingTime-{{ $employee->id }}" data-time="{{ $fixedTime < 0 ? $missingTime - $fixedTime : $missingTime }}" data-time-db="{{ $missingTime }}" data-time-fixed="{{ $fixedTime < 0 ? $fixedTime : 0 }}">
											{{ $fixedTime < 0 ? $missingTime - $fixedTime : $missingTime }} min
										</td>
										<td class="text-center" title="Sumar o restar minutos de forma manual. Valor positivo para minutos extras (SUMAR). Valor negativo para minutos deducibles (RESTAR).">
											<div class="input-group">
												<input type="number" name="fix" class="form-control form-control-sm manualFIX" id="manualFIX-{{ $employee->id }}" rel="{{ $attendances->id }}" value="{{ $attendances->manual_fix ?? 0 }}" data-employee="{{ $employee->id }}" data-rate="{{ $minuteRate }}" data-payment="{{ $minuteRate * ($attendances->manual_fix ?? 0) }}">
												<div class="input-group-append">
													<button class="btn btn-sm btn-outline-secondary applyManual" type="button" id="button-addon2"><i class="fa fa-wd fa-check"></i></button>
												</div>
											</div>
										</td>
										<td class="text-right pr-5" id="extraCost-{{ $employee->id }}" data-total="{{ $extraCost }}" data-base="{{ $minuteRate * $extraTime }}">
											${{ number_format($extraCost ?? 0, 4, '.', ',') }} USD
										</td>
										<td class="text-right pr-5" id="missingCost-{{ $employee->id }}" data-total="{{ $missingCost }}" data-base="{{ $minuteRate * $missingTime }}">
											${{ number_format($missingCost ?? 0, 4, '.', ',') }} USD
										</td>
										<td class="text-right pr-5" id="totalCost-{{ $employee->id }}" data-total="{{ $totalCost }}" data-base="{{ $totalCost }}">
											${{ number_format(($totalCost) ?? 0, 4, '.', ',') }} USD
										</td>
										<td class="text-center">
											<span class="badge badge-{{ $attendances->payment == 'pending' ? 'danger' : 'success' }}">
												{{ $attendances->payment == 'pending' ? 'PENDIENTE' : 'PAGADO EL ' . $attendances->payment_date }}
											</span>
										</td>
										<td class="text-right">
											@if($attendances->payment == 'pending')
												<a class="btn btn-sm btn-success m-1 pay-employee" href="#" data-attendance="{{ $attendances->id }}" title="MARCAR COMO PAGADO">
													<i class="fa fa-fw fa-check" aria-hidden="true"></i>
												</a>
											@else
												<a class="btn btn-sm btn-dark m-1" data-toggle="modal" data-target="#imageModal{{ $attendances->id }}" title="VER COMPROBANTE DE PAGO">
													<i class="fa fa-fw fa-file" aria-hidden="true"></i>
												</a>
												<!-- Modal -->
												<div class="modal fade" id="imageModal{{ $attendances->id }}" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel" aria-hidden="true">
													<div class="modal-dialog modal-lg" role="document">
														<div class="modal-content">
															<div class="modal-header">
																<h5 class="modal-title" id="imageModalLabel">Comprobante de Pago</h5>
																<button type="button" class="close" data-dismiss="modal" aria-label="Close">
																	<span aria-hidden="true">&times;</span>
																</button>
															</div>
															<div class="modal-body">
																<img src="{{ asset('storage/payments/' . $attendances->payment_evidence) }}" class="img-fluid" alt="Comprobante de Pago">
															</div>
															@can('manage_employees')
																<div class="modal-footer">
																	<form action="{{ route('admin.hours.payment_delete', $attendances->id) }}" method="POST">
																		@csrf
																		@method('DELETE')
																		<button type="submit" class="btn btn-danger">Eliminar Pago</button>
																	</form>
																</div>
															@endcan
														</div>
													</div>
												</div>
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
											<td colspan="12">
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
																¿Corresponde Pagar?
															</th>
															<th class="text-center">
																Minutos Faltantes
															</th>
															<th class="text-center">
																¿Corresponde Descontar?
															</th>
															{{-- <th class="text-right pr-5">
																Total
															</th> --}}
															{{-- TODO: Incluir campo para añadir o restar minutos de forma manual --}}
															<th class="text-center" width="300">
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
															<tr class="{{ $record->day == 'sábado' || $record->day == 'domingo' ? 'table-secondary text-muted' : '' }}" rel="{{ $record->id }}">
																<td class="text-center">
																	{{ $record->date }}
																</td>
																<td>
																	{{ ucwords($record->day) }}
																</td>
																<td class="{{ ($entryH >= 8 && $entryM >= 1) || $entryH >= 9 ? 'text-danger' : '' }} {{ empty($record->entry) ? 'text-black-50' : '' }} text-center" rel="{{ $entryH . ':' . $entryM }}">
																	{{ !empty($record->entry) ? $record->entry : '---' }}
																</td>
																<td class="{{ $exitH < 17 ? 'text-warning' : (($exitH >= 17 && $exitM >= ($extraTimeIni + 1)) || $exitH >= 18 ? 'text-danger' : '') }} {{ empty($record->exit) ? 'text-black-50' : '' }} text-center" rel="{{ $exitH . ':' . $exitM }}">
																	{{ !empty($record->exit) ? $record->exit : '---' }}
																</td>
																<td class="{{ $record->extra_time > 0 ? 'font-weight-bold' : 'text-black-50' }} text-center">
																	{{ $record->extra_time }}
																</td>
																<td class="{{ $record->extra_time > 0 ? 'font-weight-bold' : 'text-black-50' }} text-center">
																	@if (($exitH >= 17 && $exitM >= 1) || $exitH >= 18)
																		<div class="form-check form-check-inline">
																			<label class="form-check-label" for="applyExtraNo-{{ $record->id }}">
																				<input type="radio" id="applyExtraNo-{{ $record->id }}" name="applyExtra-{{ $record->id }}" value="0" rel="{{ $record->id }}" class="form-check-input applyExtra" data-employee="{{ $employee->id }}" data-time="{{ $record->extra_time }}" data-payment="{{ $minuteRate * $record->extra_time }}" {{ $record->extra_apply ? '' : 'checked' }}>
																				<span style="position: relative; top: -2px;">NO</span>
																			</label>
																		</div>
																		<div class="form-check form-check-inline">
																			<label class="form-check-label" for="applyExtraSi-{{ $record->id }}">
																				<input type="radio" id="applyExtraSi-{{ $record->id }}" name="applyExtra-{{ $record->id }}" value="1" rel="{{ $record->id }}" class="form-check-input applyExtra" data-employee="{{ $employee->id }}" data-time="{{ $record->extra_time }}" data-payment="{{ $minuteRate * $record->extra_time }}" {{ $record->extra_apply ? 'checked' : '' }}>
																				<span style="position: relative; top: -2px;">SI</span>
																			</label>
																		</div>
																	@else
																		N/A
																	@endif
																</td>
																<td class="{{ $record->missing_time > $missingTimeIni ? 'font-weight-bold' : 'text-black-50' }} text-center">
																	{{ $record->missing_time }}
																</td>
																<td class="{{ $record->missing_time > $missingTimeIni ? 'font-weight-bold' : 'text-black-50' }} text-center">
																	@if ($record->missing_time > $missingTimeIni)
																		<div class="form-check form-check-inline">
																			<label class="form-check-label" for="applyMissingNo-{{ $record->id }}">
																				<input type="radio" id="applyMissingNo-{{ $record->id }}" name="applyMissing-{{ $record->id }}" value="0" rel="{{ $record->id }}" class="form-check-input applyMissing" data-employee="{{ $employee->id }}" data-time="{{ $record->missing_time }}" data-payment="{{ $minuteRate * $record->missing_time }}" {{ $record->missing_apply ? '' : 'checked' }}>
																				<span style="position: relative; top: -2px;">NO</span>
																			</label>
																		</div>
																		<div class="form-check form-check-inline">
																			<label class="form-check-label" for="applyMissingSi-{{ $record->id }}">
																				<input type="radio" id="applyMissingSi-{{ $record->id }}" name="applyMissing-{{ $record->id }}" value="1" rel="{{ $record->id }}" class="form-check-input applyMissing" data-employee="{{ $employee->id }}" data-time="{{ $record->missing_time }}" data-payment="{{ $minuteRate * $record->missing_time }}" {{ $record->missing_apply ? 'checked' : '' }}>
																				<span style="position: relative; top: -2px;">SI</span>
																			</label>
																		</div>
																	@else
																		N/A
																	@endif
																</td>
																{{-- @php
																	$total_extra = $minuteRate * $record->extra_time;
																	$total_missing = $minuteRate * $record->missing_time;
																	$total = $total_extra - $total_missing;
																@endphp
																<td class="{{ $record->extra_time > $extraTimeIni ? 'font-weight-bold' : 'text-black-50' }} text-right pr-5" id="payExtra-{{ $record->id }}" rel="{{ $minuteRate }}" title="{{ '= ' . number_format($minuteRate, 4, '.', ',') . ' x ' . $record->extra_time . ' - ' . number_format($minuteRate, 4, '.', ',') . ' x ' . $record->missing_time }}">
																	{{ $total != 0 ? '$' . number_format(($total) ?? 0, 4, '.', ',') . ' USD' : 'N/A' }}
																</td> --}}
																<td class="text-center text-black-50">
																	@if (($exitH >= 17 && $exitM >= 1) || $exitH >= 18 || $record->missing_time > $missingTimeIni)
																		<input type="text" name="comment" class="form-control form-control-sm commentTXT" id="commentTXT-{{ $record->id }}" rel="{{ $record->id }}" value="{{ $record->comments }}">
																	@else
																		N/A
																	@endif
																</td>
																<td>
																	@if (($exitH >= 17 && $exitM >= 1) || $exitH >= 18 || $record->missing_time > $missingTimeIni)
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
					title: 'Adjuntar comprobante de pago',
					showCancelButton: true,
					cancelButtonText: 'Cancelar',
					confirmButtonText: 'Registrar Pago',
					input: 'file',
					inputAttributes: {
						'accept': 'image/*',
						'aria-label': 'Adjuntar comprobante de pago'
					},
					onBeforeOpen: () => {
						$('.swal2-file').change(function () {
							var reader = new FileReader();
							reader.readAsDataURL(this.files[0]);
						});
					}
				}).then((result) => {
					let file = $('.swal2-file');
					if (result.value !== undefined) {
						if (file.val() != '') {
							let _token = $('meta[name="csrf-token"]').attr('content');
							let attendance = $(this).data('attendance');
							let attachment = file[0].files[0];
							let formData = new FormData();
							formData.append('id', attendance);
							formData.append('evidence', attachment);
							$.ajax({
								headers: {
									'x-csrf-token': _token
								},
								method: 'POST',
								url: "{{ route('admin.hours.pay') }}",
								data: formData,
								processData: false,
								contentType: false
							}).done(function() {
								console.log(attendance, attachment, formData);
								Swal.fire({
									type: 'success',
									title: '¡Éxito!',
									text: 'Pago registrado correctamente'
								}).then(() => {
									location.reload();
								});
							});
						} else {
							Swal.fire({
								type: 'error',
								title: '¡Error!',
								text: 'Debe adjuntar el comprobante de pago'
							});
						}
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

			$('.applyExtra').on('change', function() {
				let id = $(this).attr('rel');
				let value = parseInt($(this).val());
				let time = parseInt($(this).attr('data-time'));
				let payment = parseFloat($(this).attr('data-payment'));
				let employee = parseInt($(this).attr('data-employee'));
				let extraTime = parseInt($('#extraTime-' + employee).attr('data-time'));
				let extraTimeO = parseInt($('#extraTime-' + employee).attr('data-time-db'));
				let extraTimeF = parseInt($('#extraTime-' + employee).attr('data-time-fixed'));
				let extraCost = parseFloat($('#extraCost-' + employee).attr('data-total'));
				let missingCost = parseFloat($('#missingCost-' + employee).attr('data-total'));

				extraTime = value == 1 ? extraTime + time : extraTime - time;
				extraCost = value == 1 ? extraCost + payment : extraCost - payment;
				extraCost = extraCost < 0 ? 0 : extraCost;
				
				let totalCost = extraCost - missingCost;

				// console.log('Time: ' + time + ' ExtraTime: ' + extraTime);
				// console.log('Payment: ' + payment + ' ExtraCost: ' + extraCost + ' MissingCost: ' + missingCost);

				// Change the Extra Time Column value according to the extra time
				$('#extraTime-' + employee).text(extraTime + ' min').attr('data-time', extraTime);
				// $('#extraTime-' + employee).text(extraTime + ' min').attr({'data-time': extraTime, 'data-time-db': extraTimeO});
				// Change the Extra Cost Column value according to the extra time
				$('#extraCost-' + employee).text('$' + parseFloat(extraCost).toFixed(4) + ' USD').attr('data-total', extraCost);
				// Change the Total Cost Column value according to the extra time
				$('#totalCost-' + employee).text('$' + parseFloat(totalCost).toFixed(4) + ' USD').attr('data-total', totalCost);

				// Update record in database to apply or not the extra time, and update the total payment
				$.ajax({
					headers: {
						'x-csrf-token': _token
					},
					method: 'POST',
					url: "{{ route('admin.hours.apply_extra') }}",
					data: {
						id: id,
						apply: value,
						extra: value == 1 ? time : 0
					}
				});
			});

			$('.applyMissing').on('change', function() {
				let id = $(this).attr('rel');														// Record ID
				let value = parseInt($(this).val());												// Radio button value
				let time = parseInt($(this).attr('data-time'));										// Original missing time on radio button
				let payment = parseFloat($(this).attr('data-payment'));								// Payment for the missing time on radio button
				let employee = parseInt($(this).attr('data-employee'));								// Employee ID
				let missingTime = parseInt($('#missingTime-' + employee).attr('data-time'));		// Current missing time including manual fix
				let missingTimeO = parseInt($('#missingTime-' + employee).attr('data-time-db'));	// Original missing time from DB without manual fix
				let missingTimeF = parseInt($('#missingTime-' + employee).attr('data-time-fixed'));	// Applied missing manual fix time
				let extraCost = parseFloat($('#extraCost-' + employee).attr('data-total'));			// Total cost for the extra time
				let missingCost = parseFloat($('#missingCost-' + employee).attr('data-total'));		// Total cost for the missing time

				missingTime = value == 1 ? missingTime + time : missingTime - time;					// Calculate the missing time according to the radio button value // TODO: Funciona bien con los radio buttons, pero no con el ajuste manual
				missingCost = value == 1 ? missingCost + payment : missingCost - payment;			// Calculate the missing cost according to the radio button value
				missingCost = missingCost < 0 ? 0 : missingCost;									// If the missing cost is negative, set it to 0

				let totalCost = extraCost - missingCost;											// Calculate the total cost

				// console.log('Time: ' + time + ' MissingTime: ' + missingTime);
				// console.log('Payment: ' + payment + ' ExtraCost: ' + extraCost + ' MissingCost: ' + missingCost);

				// Change the Missing Time Column value according to the missing time
				$('#missingTime-' + employee).text(missingTime + ' min').attr('data-time', missingTime);
				// $('#missingTime-' + employee).text(missingTime + ' min').attr({'data-time': missingTime, 'data-time-db': missingTimeO});
				// Change the Missing Cost Column value according to the missing time
				$('#missingCost-' + employee).text('$' + parseFloat(missingCost).toFixed(4) + ' USD').attr('data-total', missingCost);
				// Change the Total Cost Column value according to the missing time
				$('#totalCost-' + employee).text('$' + parseFloat(totalCost).toFixed(4) + ' USD').attr('data-total', totalCost);

				// Update record in database to apply or not the missing time, and update the total payment
				$.ajax({
					headers: {
						'x-csrf-token': _token
					},
					method: 'POST',
					url: "{{ route('admin.hours.apply_missing') }}",
					data: {
						id: id,
						apply: value,
						missing: value == 1 ? time : 0
					}
				});
			});

			$('.applyManual').on('click', function() {
				// Add or substract minutes from the total cost depending on the value
				// If the value is a negative number, substract minutes from the total, so a deduction is applied
				// If the value is a positive number, add minutes to the total, so an extra time is applied
				// If the value is 0, keep the original values
				let input = $(this).parent().siblings('input');
				let fix = parseInt(input.val());
				let id = input.attr('rel');
				let rate = input.attr('data-rate');
				let payment = parseFloat(input.attr('data-payment'));
				let employee = parseInt(input.attr('data-employee'));
				let extraTime = parseInt($('#extraTime-' + employee).attr('data-time'));
				let extraTimeO = parseInt($('#extraTime-' + employee).attr('data-time-db'));
				let extraTimeF = parseInt($('#extraTime-' + employee).attr('data-time-fixed'));
				let missingTime = parseInt($('#missingTime-' + employee).attr('data-time'));
				let missingTimeO = parseInt($('#missingTime-' + employee).attr('data-time-db'));
				let missingTimeF = parseInt($('#missingTime-' + employee).attr('data-time-fixed'));
				let extraCost = parseFloat($('#extraCost-' + employee).attr('data-total'));
				let extraCostO = parseFloat($('#extraCost-' + employee).attr('data-base'));
				let missingCost = parseFloat($('#missingCost-' + employee).attr('data-total'));
				let missingCostO = parseFloat($('#missingCost-' + employee).attr('data-base'));
				let baseCost = parseFloat($('#totalCost-' + employee).attr('data-base'));
				let fixedCost = fix * rate;
				let realFix = fix >= 0 ? (extraTime - extraTimeF) + fix : (missingTime + missingTimeF) + fix;
				
				if (fix == 0) {
					extraTime = realFix;
					extraCost = extraCostO;
					missingCost = missingCostO;
					missingTime = missingTimeO;
				} else if (fix < 0) {
					missingTime = realFix;
					missingCost = missingCost + fixedCost;
					missingCost = missingCost < 0 ? 0 : missingCost;
					extraCost = extraCostO;
					extraTime = extraTimeO;
				} else if (fix > 0) {
					extraTime = realFix;
					extraCost = extraCost + fixedCost;
					extraCost = extraCost < 0 ? 0 : extraCost;
					missingCost = missingCostO;
					missingTime = missingTimeO;
				}

				let totalCost = extraCost - missingCost;

				// console.log('Fix: ' + fix + ' ExtraTime: ' + extraTime);
				// console.log('Payment: ' + payment + ' ExtraCost: ' + extraCost + ' MissingCost: ' + missingCost);

				// Change the Extra Time Column value according to the extra time
				$('#extraTime-' + employee).text(extraTime + ' min').attr({'data-time': extraTime, 'data-time-fixed': (fix > 0 ? fix : 0)});
				// Change the Missing Time Column value according to the missing time
				$('#missingTime-' + employee).text(missingTime + ' min').attr({'data-time': missingTime, 'data-time-fixed': (fix < 0 ? fix : 0)});
				// Change the Extra Cost Column value according to the extra time
				$('#extraCost-' + employee).text('$' + parseFloat(extraCost).toFixed(4) + ' USD').attr('data-total', extraCost);
				// Change the Missing Cost Column value according to the missing time
				$('#missingCost-' + employee).text('$' + parseFloat(missingCost).toFixed(4) + ' USD').attr('data-total', missingCost);
				// Change the Total Cost Column value according to the manual fix
				$('#totalCost-' + employee).text('$' + parseFloat(totalCost).toFixed(4) + ' USD').attr('data-total', totalCost);

				// Update the manual fix input total payment property
				input.attr('data-payment', fixedCost);

				// Update record in database to apply the manual fix, and update the total payment
				$.ajax({
					headers: {
						'x-csrf-token': _token
					},
					method: 'POST',
					url: "{{ route('admin.hours.manual_fix') }}",
					data: {
						id: id,
						fix: fix
					}
				}).then(function() {
					// window.location.reload();
				});
			});
        });
        $('[data-toggle="tooltip"]').tooltip()
    </script>
@endsection
