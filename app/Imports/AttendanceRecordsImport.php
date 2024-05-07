<?php

namespace App\Imports;

use App\Models\Employee;
use App\Models\Attendance;
use App\Models\AttendanceRecord;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Carbon\Carbon;
use Exception;

class AttendanceRecordsImport implements ToCollection
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
		try {
			// Check if the file is valid
			if (empty($collection) || $collection->count() < 3) {
				throw new Exception('Error en registros del archivo');
				return false;
			}

			// Extract date range
			$date = explode(' ~ ', $collection[2][2]);
			$register = Carbon::createFromFormat('Y-m-d', $date[0]);
			$year = $register->format('Y');
			$month = $register->format('n');

			// Extract records
			$records = $collection->slice(3, $collection->count() - 3);

			// Extract employees attendance data
			$attendance = $this->extractAttendanceData($records, $register);

			// Store attendance data
			$this->storeAttendanceData($year, $month, $attendance);

			return true;
		} catch (Exception $e) {
			Log::error('Error al importar registros de asistencia', [$e]);
			return false;
		}
    }

	public function extractAttendanceData($records, $register)
	{
		try {
			$employeeData = [];
			$attendanceData = false;

			foreach ($records as $key => $record)
			{
				if ($record[0] == '1')
					continue;

				if ($record[0] == 'ID :')
				{
					$employeeData[] = [
						'id' => intval($record[2]),
						'name' => ucwords(mb_strtolower($record[9])),
						'dept' => $record[17],
					];
					$attendanceData = true;
					continue;
				}

				if ($attendanceData)
				{
					$last = count($employeeData) - 1;
					$latest = $employeeData[$last];
					$latest['logs'] = [];
					foreach ($record as $key => $hours)
					{
						
						$logs = explode("\n", trim($hours));
						$date = $register->copy()->addDays($key);
						$fecha = $date->format('Y-m-d');
						$ini = $logs != [''] ? Carbon::createFromFormat('Y-m-d H:i', $fecha . ' ' . $logs[0]) : false;
						$end = $logs != [''] ? Carbon::createFromFormat('Y-m-d H:i', $fecha . ' ' . end($logs)) : false;
						$final = Carbon::createFromFormat('Y-m-d H:i', $fecha . ' 17:00');
						$start = $ini ? $ini->format('H:i') : null;
						$exit = count($logs) > 1 ? $end->format('H:i') : null;
						$quantity = count($logs) > 1 ? $ini->diff($end)->format('%H:%I') : '00:00';
						$hours = floatval(str_replace(':', '.', $quantity)) - 1; // Rest 1 hour for lunch
						// $extras = floatval($hours > 8 ? $hours - 8 : 0);
						// $base = floor($extras);
						// $decimals = floatval(number_format($extras - $base, 2));
						// $extra = $decimals >= 0.20 ? $base + 1 : $base;
						// TODO: Calculate extra minutes instead of hours. The extra time is considered from 17:21 onwards, no matter the entry time.
						$extraMinutes = $end && $end > $final ? $final->diffInMinutes($end) : 0;
						$extra = $extraMinutes > 20 ? $extraMinutes : 0;
						$latest['logs'][$key] = [
							'date' => $date->format('Y-m-d'),			// Date
							'entry' => $start,							// Entry time
							'exit' => $exit,							// Exit time
							'day' => $date->locale('es_ES')->dayName, 	// Day of the week
							'hours' => $hours > 0 ? $hours : 0,			// Worked hours
							'extra' => $extra,							// Extra time (min)
							// 'extra' => $extra > 0 ? $extra : 0,		// Extra time (hours)
						];
					}
					$employeeData[$last] = $latest;
					$attendanceData = false;
				}
			}

			return $employeeData;

		} catch (Exception $e) {
			Log::error('Error al extraer tiempos en', [$records, $e]);
			return false;
		}
	}

	public function storeAttendanceData($year, $month, $data)
	{
		try {
			foreach ($data as $employee)
			{
				$fullname = explode(' ', $employee['name']);
				$name = ucwords(mb_strtolower($fullname[0]));
				$lastname = ucwords(mb_strtolower($fullname[1] ?? $name));
				$employeeID = Employee::firstOrCreate(['number' => $employee['id']], [
					'name' => $name,
					'lastname' => $lastname,
					'department' => $employee['dept'],
				])->id;
				$attendance = Attendance::updateOrCreate([
					'employee_id' => $employeeID,
					'year' => $year,
					'month' => $month,
				], [
					'hours' => array_sum(array_column($employee['logs'], 'hours')),
					'extra' => array_sum(array_column($employee['logs'], 'extra')),
				]);

				foreach ($employee['logs'] as $log)
				{
					AttendanceRecord::updateOrCreate([
						'attendance_id' => $attendance->id,
						'date' => $log['date'],
					], [
						'day' => $log['day'],
						'entry' => $log['entry'],
						'exit' => $log['exit'],
						'hours' => $log['hours'],
						'extra' => $log['extra'],
					]);
				}
			}
		} catch (Exception $e) {
			Log::error('Error al registrar tiempos en', [$data, $e]);
			return false;
		}
	}
}
