<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

use App\Models\Sale;
use App\Models\Seller;
use App\Models\Client;
use App\Models\Product;
use App\Models\Service;
use App\Models\Category;

class SalesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $sales = Sale::all();

        return view('admin.sales.index', compact('sales'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $sellers = Seller::all();
        $clients = Client::all();
        $services = Service::all();
        $categories = Category::with('products')->get();

        return view('admin.sales.create', compact('sellers', 'clients', 'services', 'categories'));
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
            'seller_id' => 'required|integer|exists:sales_sellers,id',
            'title' => 'required|string|max:50',
            'image' => 'sometimes|mimes:jpg,jpeg,png,gif',
            'resume' => 'required|string|max:140',
            'content' => 'required',
            'author' => 'required|url',
        ]);

        $sale = Sale::create($validatedData);

        if($request->hasFile('image')) {
            $fileName = strtoupper(time().'-'.Str::random(4)).'.'.$request->file('image')->getClientOriginalExtension();
            $filePath = $request->file('image')->storeAs('uploads/sales', $fileName, 'public');
            $sale->image = $filePath;
            $sale->save();
        }

        return redirect()->route('admin.sales.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Sale  $sale
     * @return \Illuminate\Http\Response
     */
    public function show(Sale $sale)
    {
        $sale->with('seller');

        return view('admin.sales.show', compact('sales'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Sale  $sale
     * @return \Illuminate\Http\Response
     */
    public function edit(Sale $sale)
    {
        $sellers = Seller::all();
        $clients = Client::all();
        $products = Product::all();
        $services = Service::all();

        return view('admin.sales.edit', compact('sales', 'sellers', 'clients', 'products', 'services'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Sale  $sale
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Sale $sale)
    {
        $validatedData = $request->validate([
            'seller_id' => 'required|integer|exists:sales_sellers,id',
            'title' => 'required|string|max:50',
            'image' => 'sometimes|mimes:jpg,jpeg,png,gif',
            'resume' => 'required|string|max:140',
            'content' => 'required',
            'author' => 'required|url',
        ]);

        $sale->update($validatedData);

        if($request->hasFile('image')) {
            $fileName = strtoupper(time().'-'.Str::random(4)).'.'.$request->file('image')->getClientOriginalExtension();
            $filePath = $request->file('image')->storeAs('uploads/sales', $fileName, 'public');
            $sale->image = $filePath;
            $sale->save();
        }

        return redirect()->route('admin.sales.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Sale  $sale
     * @return \Illuminate\Http\Response
     */
    public function destroy(Sale $sale)
    {
        $sale->delete();

        return redirect()->route('admin.sales.index');
    }

    /**
     * Delete all selected Sale at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        Sale::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }

    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function list(Request $request)
    {
        $sale = $request->limit ? Sale::orderBy('created_at', 'desc')->skip(0)->take($request->limit)->get() : Sale::paginate(20);

        return response()->json(
            $sale
        );
    }

    /**
        * Display the specified resource.
        *
        * @param  \App\Models\Sale  $sale
        * @return \Illuminate\Http\Response
        */
    public function details(Sale $sale)
    {
        return response()->json($sale);
    }
}
