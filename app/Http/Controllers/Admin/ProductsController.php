<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Http\Request;

use App\Models\Brand;
use App\Models\Product;
use App\Models\Category;

class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = Product::all();

        return view('admin.products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
		$brands = Brand::all();
		$categories = Category::all();

        return view('admin.products.create', compact('brands', 'categories'));
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
            'code' => 'required|string|unique:products|max:20',
            'category_id' => 'required|string|max:20',
            'brand_id' => 'required|string|max:20',
            'type' => 'required|string',
            'title' => 'required|string|max:100',
            'description' => 'required|string|max:140',
            'cost' => 'required|integer',
            'price' => 'required|integer',
        ]);

        Product::create($validatedData);

        return redirect()->route('admin.products.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        return view('admin.products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
		$brands = Brand::all();
		$categories = Category::all();
		
        return view('admin.products.edit', compact('product', 'brands', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        $validatedData = $request->validate([
            'code' => 'required|string|max:20',
			'category_id' => 'required|string|max:20',
			'brand_id' => 'required|string|max:20',
			'type' => 'required|string',
            'title' => 'required|string|max:100',
            'description' => 'required|string|max:140',
            'cost' => 'required|integer',
            'price' => 'required|integer',
        ]);

        $product->update($validatedData);

        return redirect()->route('admin.products.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('admin.products.index');
    }

    /**
     * Delete all selected Product at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        Product::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }

    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function list(Request $request)
    {
        $products = $request->limit ? Product::orderBy('created_at', 'desc')->skip(0)->take($request->limit)->get() : Product::paginate(20);

        return response()->json(
            $products
        );
    }

    /**
        * Display the specified resource.
        *
        * @param  \App\Models\Product  $product
        * @return \Illuminate\Http\Response
        */
    public function details(Product $product)
    {
        return response()->json($product);
    }
}