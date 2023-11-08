<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Http\Request;

use App\User;
use App\Models\Seller;

class SellersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $sellers = Seller::all();

        return view('admin.sellers.index', compact('sellers'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Seller  $seller
     * @return \Illuminate\Http\Response
     */
    public function show(Seller $seller)
    {
        return view('admin.sellers.show', compact('seller'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Seller  $seller
     * @return \Illuminate\Http\Response
     */
    public function edit(Seller $seller)
    {
		$users = User::all();

        return view('admin.sellers.edit', compact('users', 'seller'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Seller  $seller
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Seller $seller)
    {
        $validatedData = $request->validate([
            'code' => 'required|string|max:20',
            'title' => 'required|string|max:100',
            'description' => 'required|string|max:140',
            'price' => 'required|integer',
        ]);

        $seller->update($validatedData);

        return redirect()->route('admin.sellers.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Seller  $seller
     * @return \Illuminate\Http\Response
     */
    public function destroy(Seller $seller)
    {
        $seller->delete();

        return redirect()->route('admin.sellers.index');
    }

    /**
     * Delete all selected Seller at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        Seller::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Display a listing of the resource.
     *
	 * @return \Illuminate\Http\Response
     */
    public function list(Request $request)
    {
        $sellers = $request->limit ? Seller::orderBy('created_at', 'desc')->skip(0)->take($request->limit)->get() : Seller::paginate(20);

        return response()->json(
            $sellers
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Seller  $seller
     * @return \Illuminate\Http\Response
     */
    public function details(Seller $seller)
    {
        return response()->json($seller);
    }
}
