<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Config;

class ConfigController extends Controller
{
    // public function index()
	// {
	// 	$config = Config::all();
	// 	return view('admin.config.index', compact('config'));
	// }

	// public function update(Request $request)
	// {
	// 	$config = Config::all();
	// 	foreach ($config as $item) {
	// 		$item->value = $request->input($item->key);
	// 		$item->save();
	// 	}
	// 	return redirect()->route('admin.config.index')->with('success', 'Configuración actualizada correctamente');
	// }

	public function updateConfig(Request $request)
	{
		$config = Config::firstOrNew(['key' => $request->key]);
		$config->value = $request->value;
		$config->save();

		return back()->with('success', 'Configuración actualizada correctamente');
	}
}
