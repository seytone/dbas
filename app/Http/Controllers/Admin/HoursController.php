<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Controller;
use App\Imports\AttendanceImport;
use App\Models\AttendanceRecord;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Config;
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
		$period = ($request->period && $request->period != 'all') ? $request->period : $now->subMonth()->format('Y-n');
		$periods = Attendance::selectRaw("CONCAT(year, '-', month) AS period")->distinct()->orderBy('period', 'desc')->get();
		$periodYear = explode('-', $period)[0];
		$periodMonth = explode('-', $period)[1];
		$employees = Employee::orderBy('number', 'asc')->get();
		$extraTimeIni = Config::where('key', 'extra_time_ini')->first()->value ?? 20;

		return view('admin.hours.index', compact('now','period','periods','employees','periodYear','periodMonth','request','extraTimeIni'));
	}

	public function upload(Request $request)
	{
		$request->validate([
			'excel' => 'required|file|mimes:xlsx,xls|max:2048'
		]);

		$file = $request->file('excel');
		$file->storeAs('public', $file->getClientOriginalName());
		$excel = storage_path('app/public/' . $file->getClientOriginalName());
		$result = 'success';
		$message = 'Archivo importado correctamente';

		try {
			Excel::import(new AttendanceImport, $excel);
		} catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
			$errors = $e->failures();
			foreach ($errors as $error) {
				$errorRow = $error->row();
				$errorColumn = $error->attribute();
				$errorValue = $error->errors()[0];
				Log::error("Error processing row $errorRow, column $errorColumn: $errorValue");
			}
			$result = 'error';
			$message = 'Error al procesar el archivo';
		} catch (Exception $e) {
			Log::error('Error importing excel', [$e]);
			$result = 'error';
			$message = 'Error al importar el archivo';
		}
		
		@unlink($excel);

		return redirect()->route('admin.hours.index')->with($result, $message);
	}

	public function apply(Request $request)
	{
		$record = AttendanceRecord::find($request->id);
		$record->apply = $request->apply;
		$record->extra = $request->extra;
		$record->save();

		$attendance = Attendance::find($record->attendance_id);
		$attendance->extra = $attendance->records()->sum('extra');
		$attendance->save();

		return response()->json(['success' => 'Registro actualizado correctamente']);
	}

	public function comment(Request $request)
	{
		$record = AttendanceRecord::find($request->id);
		$record->comments = $request->comment;
		$record->save();

		return response()->json(['success' => 'Comentario guardado correctamente']);
	}

	public function pay(Request $request)
	{
		$attendance = Attendance::find($request->id);
		$attendance->payment = 'completed';
		$attendance->payment_date = Carbon::now()->format('Y-m-d');
		$attendance->save();

		return response()->json(['success' => 'Pago guardado correctamente']);
	}
}
