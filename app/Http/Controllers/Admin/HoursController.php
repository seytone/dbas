<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Controller;
use App\Imports\AttendanceImport;
use App\Models\Attendance;
use App\Models\Employee;
use Carbon\Carbon;
use Exception;

class HoursController extends Controller
{
    /**
	 * Display the help page.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index(Request $request)
	{
		$now = Carbon::now();
		$period = $request->period ?? $now->format('Y-m');
		$periods = Attendance::selectRaw("CONCAT(year, '-', month) AS period")->distinct()->orderBy('period', 'desc')->get();
		$periodYear = explode('-', $period)[0];
		$periodMonth = explode('-', $period)[1];

		$employees = Employee::with(['attendances' => function ($attendances) use ($periodYear, $periodMonth) {
			$attendances->where('year', $periodYear)->where('month', $periodMonth)->first();
		}])->get();

		return view('admin.hours.index', compact('now','period','periods','employees','periodYear','periodMonth'));
	}

	public function upload(Request $request)
	{
		$request->validate([
			'excel' => 'required|file|mimes:xlsx,xls|max:2048'
		]);

		$file = $request->file('excel');
		$file->storeAs('public', $file->getClientOriginalName());
		$excel = storage_path('app/public/' . $file->getClientOriginalName());

		try {
			Excel::import(new AttendanceImport, $excel);
			@unlink($excel);
		} catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
			$errors = $e->failures();
			foreach ($errors as $error) {
				$errorRow = $error->row();
				$errorColumn = $error->attribute();
				$errorValue = $error->errors()[0];
				Log::error("Error importing row $errorRow, column $errorColumn: $errorValue");
			}
			dd($e);
		} catch (Exception $e) {
			Log::error('Error importing excel', [$e]);
			dd($e);
		}

		return redirect()->route('admin.hours.index', ['success' => 'Archivo importado correctamente']);
	}
}
