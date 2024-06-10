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
		$missingTimeIni = Config::where('key', 'missing_time_ini')->first()->value ?? 20;

		return view('admin.hours.index', compact('now','period','periods','employees','periodYear','periodMonth','request','extraTimeIni','missingTimeIni'));
	}

	public function upload(Request $request)
	{
		$request->validate([
			'excel' => 'required|file|mimes:xlsx|max:2048'
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

	public function applyExtra(Request $request)
	{
		$record = AttendanceRecord::find($request->id);
		$record->extra = $request->extra;
		$record->extra_apply = $request->apply;
		$record->save();

		$attendance = Attendance::find($record->attendance_id);
		$attendance->extra = $attendance->records()->sum('extra');
		$attendance->save();

		return response()->json(['success' => 'Registro actualizado correctamente']);
	}

	public function applyMissing(Request $request)
	{
		$record = AttendanceRecord::find($request->id);
		$record->missing = $request->missing;
		$record->missing_apply = $request->apply;
		$record->save();

		$attendance = Attendance::find($record->attendance_id);
		$attendance->missing = $attendance->records()->sum('missing');
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
		// upload payment evidence
		$file = $request->file('evidence');
		$extension = $file->getClientOriginalExtension();
		$filename = uniqid() . '.' . $extension;
		$file->storeAs('public/payments/', $filename);

		$attendance = Attendance::find($request->id);
		$attendance->payment = 'completed';
		$attendance->payment_date = Carbon::now()->format('Y-m-d');
		$attendance->payment_evidence = $filename;
		$attendance->save();

		return response()->json(['success' => 'Pago registrado correctamente']);
	}

	public function payment_delete($id)
	{
		$attendance = Attendance::find($id);
		$attendance->payment = 'pending';
		$attendance->payment_date = null;
		$attendance->payment_evidence = null;
		$attendance->save();

		return redirect()->route('admin.hours.index');
	}
}
