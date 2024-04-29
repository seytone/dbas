<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Controller;
use App\Imports\AttendanceImport;
use Exception;

class HoursController extends Controller
{
    /**
	 * Display the help page.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		return view('admin.hours.index');
	}

	public function upload(Request $request)
	{
		$request->validate([
			'excel' => 'required|file|mimes:xlsx,xls|max:2048'
		]);

		$file = $request->file('excel');
		$file->storeAs('public', $file->getClientOriginalName());
		$excel = storage_path('app/public/' . $file->getClientOriginalName());

		ini_set('max_execution_time', '-1');
		ini_set('memory_limit', '-1');

		try {
			Excel::import(new AttendanceImport, $excel);
			@unlink($excel);
		
		} catch (Exception $e) {
			Log::error($e->getMessage());
			dd($e);
		}

		return redirect()->route('admin.hours.index', ['success' => 'Archivo importado correctamente']);
	}
}
