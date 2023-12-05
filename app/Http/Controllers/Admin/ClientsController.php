<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

use App\Models\Client;

class ClientsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $clients = Client::all();

        return view('admin.clients.index', compact('clients'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.clients.create');
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
            'title' => 'required|string|max:100',
            'document' => 'required|string|max:20|unique:clients',
            'email' => 'required|email',
            'phone' => 'required|numeric',
            'address' => 'required|string|max:140',
        ]);
		$validatedData['code'] = 'CLI-' . time();

        Client::create($validatedData);

        return redirect()->route('admin.clients.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Client  $client
     * @return \Illuminate\Http\Response
     */
    public function show(Client $client)
    {
        return view('admin.clients.show', compact('client'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Client  $client
     * @return \Illuminate\Http\Response
     */
    public function edit(Client $client)
    {
        return view('admin.clients.edit', compact('client'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Client  $client
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Client $client)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:100',
            'document' => 'required|string|max:20|unique:clients,document,' . $client->id,
            'email' => 'required|email',
            'phone' => 'required|numeric',
            'address' => 'required|string|max:140',
        ]);

        $client->update($validatedData);

        return redirect()->route('admin.clients.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Client  $client
     * @return \Illuminate\Http\Response
     */
    public function destroy(Client $client)
    {
        $client->delete();

        return redirect()->route('admin.clients.index');
    }

    /**
     * Delete all selected Client at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        Client::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }

    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function list(Request $request)
    {
        $clients = $request->limit ? Client::orderBy('created_at', 'desc')->skip(0)->take($request->limit)->get() : Client::paginate(20);

        return response()->json(
            $clients
        );
    }

    /**
        * Display the specified resource.
        *
        * @param  \App\Models\Client  $client
        * @return \Illuminate\Http\Response
        */
    public function details(Client $client)
    {
        return response()->json($client);
    }

	/**
	 * Check if the specified resource exists.
	 *
	 * @param  \App\Models\Client  $client
	 * @return \Illuminate\Http\Response
	 */
	public function exists(Request $request)
	{
		$client = Client::where('document', $request->document)->first();
		$exists = $client ? true : false;

		return response()->json($exists);
	}
}
