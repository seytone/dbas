<?php

namespace App\Imports;

use App\Models\Config;
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
	protected $extraTimeIni;
	protected $missingTimeIni;

	public function __construct()
	{
		$this->extraTimeIni = intval(Config::where('key', 'extra_time_ini')->first()->value ?? 20);
		$this->missingTimeIni = intval(Config::where('key', 'missing_time_ini')->first()->value ?? 0);
	}

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
			throw $e;
		}
    }

	public function extractAttendanceData($records, $register)
	{
		try {
			$employeeData = [];
			$attendanceData = false;
			$maxDays = $register->daysInMonth;

			foreach ($records as $index => $record)
			{
				if ($record[0] == '1') // Skip first row related to month days
					continue;

				if ($record[0] == 'ID :') // Extract employee data
				{
					$employeeData[] = [
						'id' => intval($record[2]),
						'name' => ucwords(mb_strtolower($record[9])),
						'dept' => $record[17],
					];
					$attendanceData = true;
					continue;
				}

				if ($attendanceData) // If the row contains attendance data
				{
					$last = count($employeeData) - 1;
					$latest = $employeeData[$last];
					$latest['logs'] = [];

					foreach ($record as $key => $hours) // Iterate over daily hours logs
					{
						if ($key >= $maxDays) // Stop when the month days finish to avoid extra rows
							break;

						$logs = explode("\n", trim($hours));																										// Extract daily hours logs
						$date = $register->copy()->addDays($key);																									// Calculate date based on register date and day number
						$fecha = $date->format('Y-m-d');
						$ini = $logs != [''] ? Carbon::createFromFormat('Y-m-d H:i', $fecha . ' ' . $logs[0]) : false;												// Entry time
						$end = $logs != [''] ? Carbon::createFromFormat('Y-m-d H:i', $fecha . ' ' . end($logs)) : false;											// Exit time
						$begin = Carbon::createFromFormat('Y-m-d H:i', $fecha . ' 08:00');																			// Journey start at
						$final = Carbon::createFromFormat('Y-m-d H:i', $fecha . ' 17:00');																			// Journey end at
						$start = $ini ? $ini->format('H:i') : null;																									// Entry hour:minutes
						$exit = count($logs) > 1 ? $end->format('H:i') : null;																						// Exit hour:minutes
						$quantity = count($logs) > 1 ? $ini->diff($end)->format('%H:%I') : '00:00';																	// Journey hours
						$hours = floatval(str_replace(':', '.', $quantity)) - 1;																					// Worked hours (minus 1 hour for lunch)
						$extraMinutes = $end && $end > $final ? $final->diffInMinutes($end) : 0;																	// Total extra minutes
						$extra = $extraMinutes > $this->extraTimeIni ? $extraMinutes : 0;																			// Extra minutes that apply (more than 20 min)
						$missingMinutes = ($ini && $ini > $begin ? $begin->diffInMinutes($ini) : 0) + ($end && $end < $final ? $end->diffInMinutes($final) : 0);	// Total missing minutes
						$missing = 0;																																// Missing minutes that apply (more than 0 min)
						// $missing = $missingMinutes > $this->missingTimeIni ? $missingMinutes : 0;
						$latest['logs'][$key] = [
							'date' => $date->format('Y-m-d'),																										// Date
							'entry' => $start,																														// Entry time
							'exit' => $exit,																														// Exit time
							'day' => $date->locale('es_ES')->dayName, 																								// Day of the week
							'hours' => $hours > 0 ? $hours : 0,																										// Worked hours
							'extra' => $extra,																														// Extra time (min) when apply
							'extra_time' => $extraMinutes,																											// Extra time (min) less than 20 min
							'missing' => $missing,																													// Missing time (min) when apply
							'missing_time' => $missingMinutes,																										// Missing time (min) less than 20 min
							'total' => 0
						];
					}
					$employeeData[$last] = $latest;
					$attendanceData = false;
				}
			}

			return $employeeData;

		} catch (Exception $e) {
			Log::error('Error al extraer tiempos en', [$e, $records]);
			throw $e;
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
					'missing' => array_sum(array_column($employee['logs'], 'missing')),
					'total' => array_sum(array_column($employee['logs'], 'total')),
					'manual_fix' => 0,
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
						'extra_time' => $log['extra_time'],
						'extra_apply' => $log['extra'] > $this->extraTimeIni ? 1 : 0,
						'missing' => $log['missing'],
						'missing_time' => $log['missing_time'],
						'missing_apply' => $log['missing'] > $this->missingTimeIni ? 1 : 0,
					]);
				}
			}
		} catch (Exception $e) {
			Log::error('Error al registrar tiempos en', [$e, $data]);
			throw $e;
		}
	}
}
