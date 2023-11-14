<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

use App\User;
use App\Models\Sale;
use App\Models\Seller;
use App\Models\Client;
use App\Models\Product;
use App\Models\Service;
use App\Models\Category;
use App\Models\SaleProduct;
use App\Models\SaleService;

use Carbon\Carbon;

class SalesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
		$user = User::find(Auth::user()->id);
		$query = Sale::where('deleted_at', null);

		if ($request->isMethod('get'))
		{
			$query->whereMonth('registered_at', Carbon::now()->month);

			if ($user->hasRole('Vendedor'))
				$query->where('seller_id', $user->seller->id);
		}

		if ($request->isMethod('post'))
		{
			if ($request->has('seller') && $request->seller != 'all')
				$query->where('seller_id', $request->seller);

			if ($request->has('client') && $request->client != 'all')
				$query->where('client_id', $request->client);

			if ($request->has('invoice_type') && $request->invoice_type != 'all')
				$query->where('invoice_type', $request->invoice_type);

			if ($request->has('payment_method') && $request->payment_method != 'all')
				$query->where('payment_method', $request->payment_method);

			if ($request->has('start_date'))
				$query->whereDate('registered_at', '>=', $request->start_date);

			if ($request->has('final_date'))
				$query->whereDate('registered_at', '<=', $request->final_date);

			if ($request->has('search'))
				$query->where('id', 'like', '%' . $request->search . '%')
					->orWhere('trello', 'like', '%' . $request->search . '%')
					->orWhere('invoice_number', 'like', '%' . $request->search . '%');
		}

		$query->orderBy('registered_at', 'desc');

		$sales = $query->get();
		$sellers = Seller::all();
		$vendedor = $request->seller ?? 'all';
		$start_date = $request->start_date ?? Carbon::now()->startOfMonth();
		$final_date = $request->final_date ?? Carbon::now();

        return view('admin.sales.index', compact('user', 'sales', 'sellers', 'vendedor', 'start_date', 'final_date'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
		$user = User::find(Auth::user()->id);
        $sellers = Seller::all();
        $clients = Client::all();
        $services = Service::all();
        $categories = Category::with('products')->get();

        return view('admin.sales.create', compact('user', 'sellers', 'clients', 'services', 'categories'));
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
			'registered_at' => 'required|date',
			'client_id' => 'required|integer|exists:clients,id',
			'seller_id' => 'required|integer|exists:sellers,id',
			'invoice_type' => 'required|string|max:10',
			'invoice_number' => 'required|string|max:20',
			'payment_method' => 'required|string|max:20',
			'payment_currency' => 'required|string|max:3',
			'payment_amount_usd' => 'required|numeric',
			'payment_amount_bsf' => 'required|numeric',
			'subtotal' => 'required|numeric',
			'iva' => 'required|numeric',
			'igtf' => 'required|numeric',
			'cityhall' => 'required|numeric',
			'total' => 'required|numeric',
			'provider' => 'required|numeric',
			'profit' => 'required|numeric',
			'commission_total' => 'required|numeric',
			'commission_prod' => 'required|numeric',
			'commission_serv' => 'required|numeric',
			'trello' => 'required|url',
			'notes' => 'nullable|max:300',
			'products' => 'sometimes|required|array',
			'products.*.id' => 'required_if:products,[]|integer|exists:products,id',
			'products.*.price' => 'required_if:products,[]|numeric',
			'products.*.quantity' => 'required_if:products,[]|integer',
			'products.*.discount' => 'required_if:products,[]|numeric',
			'products.*.total' => 'required_if:products,[]|numeric',
			'services' => 'sometimes|required|array',
			'services.*.id' => 'required_if:services,[]|integer|exists:services,id',
			'services.*.price' => 'required_if:services,[]|numeric',
			'services.*.quantity' => 'required_if:services,[]|integer',
			'services.*.discount' => 'required_if:services,[]|numeric',
			'services.*.total' => 'required_if:services,[]|numeric',
		]);
		$validatedData['commission'] = $validatedData['commission_total'];
		$validatedData['commission_products'] = $validatedData['commission_prod'];
		$validatedData['commission_services'] = $validatedData['commission_serv'];

		// Store the Sale
		$sale = Sale::create($validatedData);

		// Store the Products included in the Sale
		if (isset($validatedData['products']))
		{
			foreach ($validatedData['products'] as $product)
			{
				$saleProduct = new SaleProduct();
				$saleProduct->sale_id = $sale->id;
				$saleProduct->product_id = $product['id'];
				$saleProduct->quantity = $product['quantity'];
				$saleProduct->price = $product['price'];
				$saleProduct->discount = $product['discount'];
				$saleProduct->total = $product['total'];
				$saleProduct->save();
			}
		}

		// Store the Services included in the Sale
		if (isset($validatedData['services']))
		{
			foreach ($validatedData['services'] as $service)
			{
				$saleService = new SaleService();
				$saleService->sale_id = $sale->id;
				$saleService->service_id = $service['id'];
				$saleService->quantity = $service['quantity'];
				$saleService->price = $service['price'];
				$saleService->discount = $service['discount'];
				$saleService->total = $service['total'];
				$saleService->save();
			}
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
		$products = $sale->products;
		$services = $sale->services;

        return view('admin.sales.show', compact('sale', 'products', 'services'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Sale  $sale
     * @return \Illuminate\Http\Response
     */
    public function edit(Sale $sale)
    {
        $user = User::find(Auth::user()->id);
        $sellers = Seller::all();
        $clients = Client::all();
        $services = Service::all();
        $categories = Category::with('products')->get();
		$sale_products = $sale->products->pluck('id')->toArray();
		$sale_services = $sale->services->pluck('id')->toArray();

        return view('admin.sales.edit', compact('sale', 'user', 'sellers', 'clients', 'services', 'categories', 'sale_products', 'sale_services'));
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
			'registered_at' => 'required|date',
			'client_id' => 'required|integer|exists:clients,id',
			'seller_id' => 'required|integer|exists:sellers,id',
			'invoice_type' => 'required|string|max:10',
			'invoice_number' => 'required|string|max:20',
			'payment_method' => 'required|string|max:20',
			'payment_currency' => 'required|string|max:3',
			'payment_amount_usd' => 'required|numeric',
			'payment_amount_bsf' => 'required|numeric',
			'subtotal' => 'required|numeric',
			'iva' => 'required|numeric',
			'igtf' => 'required|numeric',
			'cityhall' => 'required|numeric',
			'total' => 'required|numeric',
			'provider' => 'required|numeric',
			'profit' => 'required|numeric',
			'commission_total' => 'required|numeric',
			'commission_prod' => 'required|numeric',
			'commission_serv' => 'required|numeric',
			'trello' => 'required|url',
			'notes' => 'nullable|max:300',
			'products' => 'sometimes|required|array',
			'products.*.id' => 'required_if:products,[]|integer|exists:products,id',
			'products.*.price' => 'required_if:products,[]|numeric',
			'products.*.quantity' => 'required_if:products,[]|integer',
			'products.*.discount' => 'required_if:products,[]|numeric',
			'products.*.total' => 'required_if:products,[]|numeric',
			'services' => 'sometimes|required|array',
			'services.*.id' => 'required_if:services,[]|integer|exists:services,id',
			'services.*.price' => 'required_if:services,[]|numeric',
			'services.*.quantity' => 'required_if:services,[]|integer',
			'services.*.discount' => 'required_if:services,[]|numeric',
			'services.*.total' => 'required_if:services,[]|numeric',
		]);
		$validatedData['commission'] = $validatedData['commission_total'];
		$validatedData['commission_products'] = $validatedData['commission_prod'];
		$validatedData['commission_services'] = $validatedData['commission_serv'];

		// Update the Sale data
		$sale->update($validatedData);

		// Refresh the Products included in the Sale
		if (isset($validatedData['products']))
		{
			SaleProduct::where('sale_id', $sale->id)->delete();
			foreach ($validatedData['products'] as $product)
			{
				$saleProduct = new SaleProduct();
				$saleProduct->sale_id = $sale->id;
				$saleProduct->product_id = $product['id'];
				$saleProduct->quantity = $product['quantity'];
				$saleProduct->price = $product['price'];
				$saleProduct->discount = $product['discount'];
				$saleProduct->total = $product['total'];
				$saleProduct->save();
			}
		}

		// Refresh the Services included in the Sale
		if (isset($validatedData['services']))
		{
			SaleService::where('sale_id', $sale->id)->delete();
			foreach ($validatedData['services'] as $service)
			{
				$saleService = new SaleService();
				$saleService->sale_id = $sale->id;
				$saleService->service_id = $service['id'];
				$saleService->quantity = $service['quantity'];
				$saleService->price = $service['price'];
				$saleService->discount = $service['discount'];
				$saleService->total = $service['total'];
				$saleService->save();
			}
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
        $sale = $request->limit ? Sale::ordregister'created_at', 'desc')->skip(0)->take($request->limit)->get() : Sale::paginate(20);

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
