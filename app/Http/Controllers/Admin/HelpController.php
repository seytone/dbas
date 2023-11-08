<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HelpController extends Controller
{
    /**
	 * Display the help page.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		return view('admin.help.index');
	}
}
