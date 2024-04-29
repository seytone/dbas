<?php

namespace App\Imports;

use App\Models\Attendance;
use App\Models\AttendanceRecord;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Carbon\Carbon;

class AttendanceRecordsImport implements ToCollection
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
		// Extract date range
		$date = explode(' ~ ', $collection[2][2]);
		$register = Carbon::createFromFormat('Y-m-d', $date[0]);
		$year = $register->format('Y');
		$month = $register->format('M');

		// Extract records
		$records = $collection->slice(3, $collection->count() - 3);

		// Extract employees attendance data
		$attendance = $this->extractAttendanceData($records, $register);

		// Store attendance data
		$this->storeAttendanceData($year, $month, $attendance);

		return true;
    }

	public function extractAttendanceData($records, $register)
	{
		$employeeData = [];
		$attendanceData = false;

		foreach ($records as $key => $record)
		{
			if ($record[0] == '1')
				continue;

			if ($record[0] == 'ID :')
			{
				$employeeData[] = [
					'id' => $record[2],
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
					$start = $ini ? $ini->format('H:i') : null;
					$exit = count($logs) > 1 ? $end->format('H:i') : null;
					$quantity = count($logs) > 1 ? $ini->diff($end)->format('%H:%I') : '00:00';
					$hours = floatval(str_replace(':', '.', $quantity)) - 1; // Rest 1 hour for lunch
					$extra = floatval(number_format($hours > floatval(8) ? $hours - floatval(8) : 0, 2));
					$latest['logs'][$key] = [
						'date' => $date->format('Y-m-d'),	// Date
						'entry' => $start,					// Entry time
						'exit' => $exit,					// Exit time
						'day' => $date->format('D'),		// Day of the week
						'hours' => $hours > 0 ? $hours : 0,	// Worked hours
						'extra' => $extra > 0 ? $extra : 0,	// Extra hours
					];
				}
				$employeeData[$last] = $latest;
				$attendanceData = false;
			}
		}

		return $employeeData;
	}

	public function storeAttendanceData($year, $month, $data)
	{
		foreach ($data as $employee)
		{
			$attendance = Attendance::updateOrCreate([
				'employee_id' => $employee['id'], // TODO: Find employee by number to get the id
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
	}
}
