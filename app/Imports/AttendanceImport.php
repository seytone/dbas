<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class AttendanceImport implements WithMultipleSheets
{
	public function sheets(): array
	{
		return [
			'Registro asistencia' => new AttendanceRecordsImport(),
		];
	}
}
