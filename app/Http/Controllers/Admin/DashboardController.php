<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;

use App\User;
use App\Models\Sale;
use App\Models\Seller;
use App\Models\SaleProduct;
use App\Models\SaleService;

use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
		if (! Gate::allows('view_dashboard')) {
            return redirect()->route('admin.help');
        }
		
		$where = [];
		$user = User::find(Auth::user()->id);
		$query = Sale::where('deleted_at', null);

		if ($request->isMethod('get'))
		{
			$query->whereMonth('registered_at', Carbon::now()->month);

			if ($user->hasRole('Vendedor'))
			{
				$query->where('seller_id', $user->seller->id);
				$where = ['seller_id' => $user->seller->id];
			}
		}

		if ($request->isMethod('post'))
		{
			if ($request->has('seller') && $request->seller != 'all')
			{
				$query->where('seller_id', $request->seller);
				$where = ['seller_id' => $request->seller];
			}

			if ($request->has('start_date'))
				$query->whereDate('registered_at', '>=', $request->start_date);

			if ($request->has('final_date'))
				$query->whereDate('registered_at', '<=', $request->final_date);
		}

		$orders = Sale::select(
				DB::raw("DATE_FORMAT(created_at,'%m') as month"),
				DB::raw('count(id) sales'),
				DB::raw('count(total) total'),
				DB::raw('sum(profit) profit'),
				DB::raw('sum(commission) commission'),
		)->whereYear('created_at', Carbon::now()->year)->where($where)->groupBy('month')->get();

		$ventas = [];
		foreach ($orders as $key => $value) {
			$ventas[intval($value->month)] = [
				'sales' => $value->sales,
				'total' => $value->total,
				'profit' => $value->profit,
				'commission' => $value->commission,
			];
		}

		$months = [];
		for ($i=1; $i <= 12; $i++) {
			$months[$i] = [
				'sales' => isset($ventas[$i]) ? $ventas[$i]['sales'] : 0,
				'total' => isset($ventas[$i]) ? $ventas[$i]['total'] : 0,
				'profit' => isset($ventas[$i]) ? $ventas[$i]['profit'] : 0,
				'commission' => isset($ventas[$i]) ? $ventas[$i]['commission'] : 0,
			];
		}

		$sales = $query->count();							// ventas
		$total_amount = $query->sum('total');				// facturación
		$total_profit = $query->sum('profit');				// ganancia
		$total_commission = $query->sum('commission');		// comisión

		$services = SaleService::join('sales', 'sales.id', '=', 'sales_services.sale_id')->where('sales.deleted_at', null);
		$products = SaleProduct::join('sales', 'sales.id', '=', 'sales_products.sale_id')->where('sales.deleted_at', null);
		$hardware = SaleProduct::join('sales', 'sales.id', '=', 'sales_products.sale_id')->join('products', 'products.id', '=', 'sales_products.product_id')->where('sales.deleted_at', null)->where('products.type', 'hardware');
		$software = SaleProduct::join('sales', 'sales.id', '=', 'sales_products.sale_id')->join('products', 'products.id', '=', 'sales_products.product_id')->where('sales.deleted_at', null)->where('products.type', 'software');
		
		if ($user->hasRole('Vendedor') || ($request->has('seller') && $request->seller != 'all'))
		{
			$seller_id = $request->seller ?? $user->seller->id;
			$services->where('sales.seller_id', $seller_id);
			$products->where('sales.seller_id', $seller_id);
			$hardware->where('sales.seller_id', $seller_id);
			$software->where('sales.seller_id', $seller_id);
		}

		$total_services = $services->sum('quantity');
		$total_products = $products->sum('quantity');
		$total_hardware = $hardware->sum('quantity');
		$total_software = $software->sum('quantity');

		$sellers = Seller::all();
		$vendedor = $request->seller ?? 'all';
		$start_date = $request->start_date ?? Carbon::now()->startOfMonth();
		$final_date = $request->final_date ?? Carbon::now();

		return view('dashboard', compact('user', 'sales', 'sellers', 'total_amount', 'total_profit', 'total_commission', 'total_services', 'total_products', 'total_hardware', 'total_software', 'start_date', 'final_date', 'vendedor', 'months'));
    }
}