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
    public function create($layout = 'admin')
    {
		$brands = Brand::all();
		$categories = Category::all();

        return view('admin.products.create', compact('layout', 'brands', 'categories'));
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
            'code' => 'required|string|max:20|unique:products,deleted_at,NULL',
            'category_id' => 'required|string|max:30',
            'brand_id' => 'required|string|max:30',
            'group' => 'required|string',
            'type' => 'required|string',
            'title' => 'required|string|max:100',
            'description' => 'required|string|max:140',
            'cost' => 'required|numeric|between:0,999999.99',
            'price' => 'required|numeric|between:0,999999.99',
        ]);

		if (!is_numeric($request->category_id))
		{
			$category = Category::create([
				'title' => $request->category_id,
				'description' => $request->category_id,
			]);
			$validatedData['category_id'] = $category->id;
		} else {
			$validatedData['category_id'] = $request->category_id;
		}

		if (!is_numeric($request->brand_id))
		{
			$brand = Brand::create([
				'title' => $request->brand_id,
				'description' => $request->brand_id,
			]);
			$validatedData['brand_id'] = $brand->id;
		} else {
			$validatedData['brand_id'] = $request->brand_id;
		}

		$product = Product::create($validatedData);

		if ($request->layout == 'modal')
		{
			return response()->json([
				'status' => 'success',
				'message' => 'El producto ha sido registrado exitosamente.',
				'response' => $product->toJson(),
			]);
		}

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
            'code' => 'required|string|max:20|unique:products,code,' . $product->id . ',id,deleted_at,NULL',
			'category_id' => 'required|string|max:30',
			'brand_id' => 'required|string|max:30',
            'group' => 'required|string',
            'type' => 'required|string',
            'title' => 'required|string|max:100',
            'description' => 'required|string|max:140',
            'cost' => 'required|numeric|between:0,999999.99',
            'price' => 'required|numeric|between:0,999999.99',
        ]);

		if (!is_numeric($request->category_id))
		{
			$category = Category::create([
				'title' => $request->category_id,
				'description' => $request->category_id,
			]);
			$validatedData['category_id'] = $category->id;
		} else {
			$validatedData['category_id'] = $request->category_id;
		}

		if (!is_numeric($request->brand_id))
		{
			$brand = Brand::create([
				'title' => $request->brand_id,
				'description' => $request->brand_id,
			]);
			$validatedData['brand_id'] = $brand->id;
		} else {
			$validatedData['brand_id'] = $request->brand_id;
		}

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

	/**
	 * Check if the specified resource exists.
	 * 
	 * @param  \App\Models\Product  $product
	 * @return \Illuminate\Http\Response
	 */
	public function exists(Request $request)
	{
		$product = Product::where('code', $request->code)->first();
		$exists = $product ? true : false;

		return response()->json($exists);
	}
}