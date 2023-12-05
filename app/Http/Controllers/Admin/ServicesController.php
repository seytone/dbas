<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Http\Request;

use App\Models\Service;

class ServicesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $services = Service::all();

        return view('admin.services.index', compact('services'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($layout = 'admin')
    {
        return view('admin.services.create', compact('layout'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'code' => 'required|string|max:20|unique:services,deleted_at,NULL',
            'title' => 'required|string|max:100',
            'description' => 'required|string|max:140',
            'price' => 'required|numeric|between:0,999999.99',
        ]);

		$service = Service::create($validatedData);

		if ($request->layout == 'modal')
		{
			return response()->json([
				'status' => 'success',
				'message' => 'El servicio ha sido registrado exitosamente.',
				'response' => $service->toJson(),
			]);
		}

        return redirect()->route('admin.services.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Service  $service
     * @return \Illuminate\Http\Response
     */
    public function show(Service $service)
    {
        return view('admin.services.show', compact('service'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Service  $service
     * @return \Illuminate\Http\Response
     */
    public function edit(Service $service)
    {
        return view('admin.services.edit', compact('service'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Service  $service
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Service $service)
    {
        $validatedData = $request->validate([
            'code' => 'required|string|max:20|unique:services,code,' . $service->id . ',id,deleted_at,NULL',
            'title' => 'required|string|max:100',
            'description' => 'required|string|max:140',
            'price' => 'required|numeric|between:0,999999.99',
        ]);

        $service->update($validatedData);

        return redirect()->route('admin.services.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Service  $service
     * @return \Illuminate\Http\Response
     */
    public function destroy(Service $service)
    {
        $service->delete();

        return redirect()->route('admin.services.index');
    }

    /**
     * Delete all selected Service at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        Service::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Display a listing of the resource.
     *
	 * @return \Illuminate\Http\Response
     */
    public function list(Request $request)
    {
        $services = $request->limit ? Service::orderBy('created_at', 'desc')->skip(0)->take($request->limit)->get() : Service::paginate(20);

        return response()->json(
            $services
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Service  $service
     * @return \Illuminate\Http\Response
     */
    public function details(Service $service)
    {
        return response()->json($service);
    }

	/**
	 * Check if the specified resource exists.
	 * 
	 * @param  \App\Models\Service  $service
	 * @return \Illuminate\Http\Response
	 */
	public function exists(Request $request)
	{
		$service = Service::where('code', $request->code)->first();
		$exists = $service ? true : false;

		return response()->json($exists);
	}
}
