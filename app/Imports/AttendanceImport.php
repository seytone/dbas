<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Illuminate\Support\Facades\Log;
use Exception;

class AttendanceImport implements WithMultipleSheets
{
	public function sheets(): array
	{
		try {
			return [
				'Registro asistencia' => new AttendanceRecordsImport(),
			];
		} catch (Exception $e) {
			Log::error('Error al procesar excel', [$e]);
			return [];
		}
	}
}
