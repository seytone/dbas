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
use App\Models\SellerCommissionPayment;

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
			if ($user->hasRole('Vendedor'))
			{
				$query->where('seller_id', $user->seller->id);
				$where = ['seller_id' => $user->seller->id];
			}
			else if ($request->has('seller') && $request->seller != 'all')
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
				DB::raw("DATE_FORMAT(registered_at,'%m') as month"),
				DB::raw('count(id) sales'),
				DB::raw('count(total) total'),
				DB::raw('sum(profit) profit'),
				DB::raw('sum(commission) commission'),
		)->whereYear('registered_at', Carbon::now()->year)->where($where)->groupBy('month')->get();

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

		// ============================================================
		// COMMISSION PAYMENT STATE — single-seller, full-month basis
		// ============================================================
		$resolvedSellerId = null;
		if ($user->hasRole('Vendedor')) {
			$resolvedSellerId = $user->seller->id;
		} elseif ($request->has('seller') && $request->seller != 'all' && $request->seller != '') {
			$resolvedSellerId = (int) $request->seller;
		}

		$paymentMode = 'none';   // none | single | multi
		$paymentRecord = null;   // for single
		$paymentRecords = [];    // for multi
		$paymentPeriodLabel = null;

		if ($resolvedSellerId && $total_commission > 0)
		{
			$startMonth = Carbon::parse($start_date)->startOfMonth();
			$endMonth = Carbon::parse($final_date)->startOfMonth();
			$monthsInRange = $startMonth->diffInMonths($endMonth) + 1;

			$periods = collect();
			$cursor = $startMonth->copy();
			for ($i = 0; $i < $monthsInRange; $i++) {
				$periods->push(['year' => (int) $cursor->year, 'month' => (int) $cursor->month]);
				$cursor->addMonth();
			}

			// Sync pending payment records with current commission sums for each
			// period. Paid records keep their snapshot untouched.
			foreach ($periods as $p)
			{
				$sums = Sale::where('seller_id', $resolvedSellerId)
					->whereYear('registered_at', $p['year'])
					->whereMonth('registered_at', $p['month'])
					->selectRaw('
						COALESCE(SUM(commission), 0) AS total,
						COALESCE(SUM(commission_perpetual), 0) AS perp,
						COALESCE(SUM(commission_annual), 0) AS annual,
						COALESCE(SUM(commission_hardware), 0) AS hardware,
						COALESCE(SUM(commission_services), 0) AS services
					')->first();

				$existing = SellerCommissionPayment::where('seller_id', $resolvedSellerId)
					->where('year', $p['year'])
					->where('month', $p['month'])
					->first();

				if ($existing) {
					if ($existing->payment === 'pending') {
						$existing->update([
							'total_commission' => $sums->total,
							'commission_perpetual' => $sums->perp,
							'commission_annual' => $sums->annual,
							'commission_hardware' => $sums->hardware,
							'commission_services' => $sums->services,
						]);
					}
				} elseif ($sums->total > 0) {
					$existing = SellerCommissionPayment::create([
						'seller_id' => $resolvedSellerId,
						'year' => $p['year'],
						'month' => $p['month'],
						'total_commission' => $sums->total,
						'commission_perpetual' => $sums->perp,
						'commission_annual' => $sums->annual,
						'commission_hardware' => $sums->hardware,
						'commission_services' => $sums->services,
					]);
				}
			}

			// Build the records used by the view (only those with amount > 0).
			$paymentRecords = SellerCommissionPayment::where('seller_id', $resolvedSellerId)
				->where(function ($q) use ($periods) {
					foreach ($periods as $p) {
						$q->orWhere(function ($sub) use ($p) {
							$sub->where('year', $p['year'])->where('month', $p['month']);
						});
					}
				})
				->where('total_commission', '>', 0)
				->orderBy('year', 'asc')->orderBy('month', 'asc')
				->get();

			if ($monthsInRange === 1 && $paymentRecords->count() === 1) {
				$paymentMode = 'single';
				$paymentRecord = $paymentRecords->first();
				$paymentPeriodLabel = Carbon::create($paymentRecord->year, $paymentRecord->month, 1)
					->locale('es')->isoFormat('MMMM YYYY');
			} elseif ($paymentRecords->count() > 0) {
				$paymentMode = 'multi';
			}
		}

		return view('dashboard', compact('user', 'sales', 'sellers', 'total_amount', 'total_profit', 'total_commission', 'total_services', 'total_products', 'total_hardware', 'total_software', 'start_date', 'final_date', 'vendedor', 'months', 'paymentMode', 'paymentRecord', 'paymentRecords', 'paymentPeriodLabel', 'resolvedSellerId'));
    }

	/**
	 * Mark a single SellerCommissionPayment as paid (uploads evidence image).
	 */
	public function payCommission(Request $request)
	{
		$request->validate([
			'id' => 'required|integer|exists:seller_commission_payments,id',
			'evidence' => 'required|file|mimes:jpg,jpeg,png,gif,webp,pdf|max:5120',
		]);

		$payment = SellerCommissionPayment::findOrFail($request->id);

		if ($payment->payment === 'completed') {
			return response()->json(['success' => false, 'message' => 'Esta comisión ya está marcada como pagada.'], 422);
		}

		$file = $request->file('evidence');
		$filename = uniqid('com_') . '.' . $file->getClientOriginalExtension();
		$file->storeAs('public/payments', $filename);

		$payment->update([
			'payment' => 'completed',
			'payment_date' => Carbon::now()->format('Y-m-d'),
			'payment_evidence' => $filename,
		]);

		return response()->json([
			'success' => true,
			'message' => 'Pago registrado correctamente.',
			'payment_date' => $payment->payment_date->format('d/m/Y'),
			'payment_evidence' => $filename,
		]);
	}

	/**
	 * Reset a commission payment back to pending (deletes the proof association,
	 * not the file itself in case auditing is needed).
	 */
	public function unpayCommission(Request $request, $id)
	{
		$payment = SellerCommissionPayment::findOrFail($id);

		$payment->update([
			'payment' => 'pending',
			'payment_date' => null,
			'payment_evidence' => null,
		]);

		return response()->json(['success' => true, 'message' => 'Pago revertido.']);
	}

	/**
	 * Return the monthly breakdown for the modal when the date range spans
	 * more than one month. Used as JSON via AJAX.
	 */
	public function commissionBreakdown(Request $request)
	{
		$sellerId = (int) $request->seller_id;
		$startDate = $request->start_date;
		$finalDate = $request->final_date;

		if (! $sellerId || ! $startDate || ! $finalDate) {
			return response()->json(['success' => false, 'message' => 'Parámetros incompletos.'], 422);
		}

		$startMonth = Carbon::parse($startDate)->startOfMonth();
		$endMonth = Carbon::parse($finalDate)->startOfMonth();
		$monthsInRange = $startMonth->diffInMonths($endMonth) + 1;

		$rows = [];
		$cursor = $startMonth->copy();
		for ($i = 0; $i < $monthsInRange; $i++)
		{
			$payment = SellerCommissionPayment::where('seller_id', $sellerId)
				->where('year', $cursor->year)
				->where('month', $cursor->month)
				->first();

			if ($payment && $payment->total_commission > 0) {
				$rows[] = [
					'id' => $payment->id,
					'period_label' => $cursor->copy()->locale('es')->isoFormat('MMMM YYYY'),
					'total_commission' => number_format($payment->total_commission, 2, ',', '.'),
					'payment' => $payment->payment,
					'payment_date' => $payment->payment_date ? $payment->payment_date->format('d/m/Y') : null,
					'payment_evidence' => $payment->payment_evidence,
				];
			}
			$cursor->addMonth();
		}

		return response()->json(['success' => true, 'rows' => $rows]);
	}
}