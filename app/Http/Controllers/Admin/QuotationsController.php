<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\User;
use App\Models\Client;
use App\Models\Config;
use App\Models\Category;
use App\Models\Quotation;
use App\Models\QuotationProduct;
use App\Services\ExchangeRateService;

use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class QuotationsController extends Controller
{
	/**
	 * Display a listing of quotations.
	 */
	public function index(Request $request)
	{
		$query = Quotation::where('deleted_at', null);

		if ($request->isMethod('post'))
		{
			if ($request->has('client') && $request->client != 'all')
				$query->where('client_id', $request->client);

			if ($request->has('status') && $request->status != 'all')
				$query->where('status', $request->status);

			if ($request->has('start_date'))
				$query->whereDate('emission_date', '>=', $request->start_date);

			if ($request->has('final_date'))
				$query->whereDate('emission_date', '<=', $request->final_date);

			if ($request->has('search') && $request->search)
				$query->where(function ($q) use ($request) {
					$q->where('quotation_number', 'like', '%' . $request->search . '%')
					  ->orWhereHas('client', function ($cq) use ($request) {
						  $cq->where('title', 'like', '%' . $request->search . '%');
					  });
				});
		}

		$query->orderByRaw('CAST(quotation_number AS UNSIGNED) DESC');

		$quotations = $query->get();
		$clients = Client::all();
		$start_date = $request->start_date ?? Carbon::now()->startOfMonth();
		$final_date = $request->final_date ?? Carbon::now();

		return view('admin.quotations.index', compact('quotations', 'clients', 'start_date', 'final_date'));
	}

	/**
	 * Show the form for creating a new quotation.
	 */
	public function create()
	{
		$clients = Client::all();
		$categories = Category::with('products')->get();
		$nextNumber = Quotation::nextNumber();
		$rates = $this->savedRates();

		return view('admin.quotations.create', compact('clients', 'categories', 'nextNumber', 'rates'));
	}

	/**
	 * Read the exchange rates stored in the config table.
	 */
	protected function savedRates(): array
	{
		return [
			'binance' => Config::where('key', 'binance_rate')->first()->value ?? 0,
			'bcv' => Config::where('key', 'bcv_rate')->first()->value ?? 0,
		];
	}

	/**
	 * Store a newly created quotation.
	 */
	public function store(Request $request)
	{
		$client = $this->resolveClient($request);

		$validatedData = $request->validate([
			'emission_date' => 'required|date',
			'expiration_date' => 'required|date|after_or_equal:emission_date',
			'currency' => 'required|string|max:10',
			'price_mode' => 'required|string|in:usd,bcv',
			'binance_rate' => 'nullable|numeric|min:0',
			'bcv_rate' => 'nullable|numeric|min:0',
			'iva_rate' => 'required|numeric',
			'subtotal' => 'required|numeric|min:0',
			'discount_1' => 'required|numeric|min:0|max:100',
			'discount_1_amount' => 'required|numeric|min:0',
			'discount_2' => 'required|numeric|min:0|max:100',
			'discount_2_amount' => 'required|numeric|min:0',
			'freight' => 'required|numeric|min:0',
			'tax_exempt' => 'required|numeric|min:0',
			'tax_base' => 'required|numeric|min:0',
			'iva_amount' => 'required|numeric|min:0',
			'igtf_rate' => 'required|numeric|min:0',
			'igtf_amount' => 'required|numeric|min:0',
			'total' => 'required|numeric|min:0',
			'notes' => 'nullable|string',
			'status' => 'required|string|in:draft,sent,accepted,rejected',
			'status_comment' => 'nullable|string|max:1000',
			'items' => 'required|array|min:1',
			'items.*.product_id' => 'nullable|integer|exists:products,id',
			'items.*.code' => 'required|string|max:50',
			'items.*.description' => 'required|string',
			'items.*.quantity' => 'required|integer|min:1',
			'items.*.unit_price' => 'required|numeric|min:0',
			'items.*.discount_percent' => 'required|numeric|min:0|max:100',
			'items.*.discount_amount' => 'required|numeric|min:0',
			'items.*.total' => 'required|numeric|min:0',
		], [], $this->validationAttributes());

		$validatedData['client_id'] = $client->id;
		$validatedData['created_by'] = Auth::id();
		// Snapshot the client's data on the quotation. Keeps each PDF/edit
		// view stable against later changes to the shared Client record.
		$validatedData['client_title'] = $client->title;
		$validatedData['client_document'] = $client->document;
		$validatedData['client_email'] = $client->email;
		$validatedData['client_phone'] = $client->phone;
		$validatedData['client_address'] = $client->address;

		// Store the Quotation. The correlative number is assigned here (not at
		// form load) to avoid collisions when two users create at the same time.
		// On a duplicate-number race condition we retry with the next number.
		$quotation = null;
		for ($attempt = 0; $attempt < 5; $attempt++)
		{
			try {
				$validatedData['quotation_number'] = Quotation::nextNumber();
				$quotation = Quotation::create($validatedData);
				break;
			} catch (\Illuminate\Database\QueryException $e) {
				// 1062 = MySQL duplicate entry. Retry; otherwise rethrow.
				if ($attempt === 4 || ($e->errorInfo[1] ?? null) !== 1062) {
					throw $e;
				}
				usleep(100000); // wait 100ms before retrying
			}
		}

		// Store the Products included in the Quotation
		foreach ($validatedData['items'] as $index => $item)
		{
			$qp = new QuotationProduct();
			$qp->quotation_id = $quotation->id;
			$qp->product_id = $item['product_id'] ?: null;
			$qp->code = $item['code'];
			$qp->description = $this->normalizeDescription($item['description']);
			$qp->quantity = $item['quantity'];
			$qp->unit_price = $item['unit_price'];
			$qp->discount_percent = $item['discount_percent'];
			$qp->discount_amount = $item['discount_amount'];
			$qp->total = $item['total'];
			$qp->sort_order = $index;
			$qp->save();
		}

		return redirect()->route('admin.quotations.index')->with('message', 'Cotización creada exitosamente.');
	}

	/**
	 * Display the specified quotation.
	 */
	public function show(Quotation $quotation)
	{
		$quotation->load(['client', 'items.product', 'author']);

		return view('admin.quotations.show', compact('quotation'));
	}

	/**
	 * Show the form for editing the specified quotation.
	 */
	public function edit(Quotation $quotation)
	{
		if ($quotation->status === 'accepted')
		{
			return redirect()->route('admin.quotations.show', $quotation->id);
		}

		$clients = Client::all();
		$categories = Category::with('products')->get();
		$quotation->load(['client', 'items.product']);
		$rates = $this->savedRates();

		return view('admin.quotations.edit', compact('quotation', 'clients', 'categories', 'rates'));
	}

	/**
	 * Update the specified quotation.
	 */
	public function update(Request $request, Quotation $quotation)
	{
		if ($quotation->status === 'accepted')
		{
			return redirect()->route('admin.quotations.show', $quotation->id);
		}

		$client = $this->resolveClient($request);

		$validatedData = $request->validate([
			'quotation_number' => 'required|string|max:20|unique:quotations,quotation_number,' . $quotation->id . ',id,deleted_at,NULL',
			'emission_date' => 'required|date',
			'expiration_date' => 'required|date|after_or_equal:emission_date',
			'currency' => 'required|string|max:10',
			'price_mode' => 'required|string|in:usd,bcv',
			'binance_rate' => 'nullable|numeric|min:0',
			'bcv_rate' => 'nullable|numeric|min:0',
			'iva_rate' => 'required|numeric',
			'subtotal' => 'required|numeric|min:0',
			'discount_1' => 'required|numeric|min:0|max:100',
			'discount_1_amount' => 'required|numeric|min:0',
			'discount_2' => 'required|numeric|min:0|max:100',
			'discount_2_amount' => 'required|numeric|min:0',
			'freight' => 'required|numeric|min:0',
			'tax_exempt' => 'required|numeric|min:0',
			'tax_base' => 'required|numeric|min:0',
			'iva_amount' => 'required|numeric|min:0',
			'igtf_rate' => 'required|numeric|min:0',
			'igtf_amount' => 'required|numeric|min:0',
			'total' => 'required|numeric|min:0',
			'notes' => 'nullable|string',
			'status' => 'required|string|in:draft,sent,accepted,rejected',
			'status_comment' => 'nullable|string|max:1000',
			'items' => 'required|array|min:1',
			'items.*.product_id' => 'nullable|integer|exists:products,id',
			'items.*.code' => 'required|string|max:50',
			'items.*.description' => 'required|string',
			'items.*.quantity' => 'required|integer|min:1',
			'items.*.unit_price' => 'required|numeric|min:0',
			'items.*.discount_percent' => 'required|numeric|min:0|max:100',
			'items.*.discount_amount' => 'required|numeric|min:0',
			'items.*.total' => 'required|numeric|min:0',
		], [], $this->validationAttributes());

		$validatedData['client_id'] = $client->id;
		// Snapshot the client's data on the quotation. Keeps each PDF/edit
		// view stable against later changes to the shared Client record.
		$validatedData['client_title'] = $client->title;
		$validatedData['client_document'] = $client->document;
		$validatedData['client_email'] = $client->email;
		$validatedData['client_phone'] = $client->phone;
		$validatedData['client_address'] = $client->address;

		// Once edited through the new form, the quotation uses the automatic
		// calculation, so it is no longer flagged as a manual (legacy) one.
		$quotation->manual_calculation = false;

		// Update the Quotation
		$quotation->update($validatedData);

		// Refresh the Products: delete and recreate
		QuotationProduct::where('quotation_id', $quotation->id)->delete();
		foreach ($validatedData['items'] as $index => $item)
		{
			$qp = new QuotationProduct();
			$qp->quotation_id = $quotation->id;
			$qp->product_id = $item['product_id'] ?: null;
			$qp->code = $item['code'];
			$qp->description = $this->normalizeDescription($item['description']);
			$qp->quantity = $item['quantity'];
			$qp->unit_price = $item['unit_price'];
			$qp->discount_percent = $item['discount_percent'];
			$qp->discount_amount = $item['discount_amount'];
			$qp->total = $item['total'];
			$qp->sort_order = $index;
			$qp->save();
		}

		return redirect()->route('admin.quotations.index')->with('message', 'Cotización actualizada exitosamente.');
	}

	/**
	 * Remove the specified quotation.
	 */
	public function destroy(Quotation $quotation)
	{
		$quotation->delete();

		return redirect()->route('admin.quotations.index');
	}

	/**
	 * Delete all selected quotations at once.
	 */
	public function massDestroy(Request $request)
	{
		Quotation::whereIn('id', request('ids'))->delete();

		return response(null, Response::HTTP_NO_CONTENT);
	}

	/**
	 * Duplicate a quotation.
	 */
	public function duplicate(Quotation $quotation)
	{
		$quotation->load('items');

		// Pre-load the create form with the source quotation's data via flashed
		// "old input". Nothing is persisted until the user explicitly saves —
		// cancelling out of the form discards the duplicate cleanly.
		$items = $quotation->items->map(function ($item) {
			return [
				'product_id' => $item->product_id,
				'code' => $item->code,
				'description' => $item->description,
				'quantity' => (int) $item->quantity,
				'unit_price' => $item->unit_price,
				'discount_percent' => $item->discount_percent,
			];
		})->all();

		// Client data comes from the source quotation's snapshot, not the
		// live Client record — so if the client's data has drifted since,
		// the duplicate still starts from what the original quotation shows.
		// The client_id selector is also pre-filled, since Selectize doesn't
		// fire onChange on initial render and the store() validation needs
		// the cli_* fields anyway.
		session()->flashInput([
			'client_id' => $quotation->client_id,
			'cli_title' => $quotation->client_title ?? '',
			'cli_document' => $quotation->client_document ?? '',
			'cli_email' => $quotation->client_email ?? '',
			'cli_phone' => $quotation->client_phone ?? '',
			'cli_address' => $quotation->client_address ?? '',
			'emission_date' => Carbon::now()->format('Y-m-d'),
			'expiration_date' => Carbon::now()->addDays(5)->format('Y-m-d'),
			'currency' => $quotation->currency,
			'price_mode' => $quotation->price_mode,
			'binance_rate' => $quotation->binance_rate,
			'bcv_rate' => $quotation->bcv_rate,
			'iva_rate' => $quotation->iva_rate,
			'igtf_rate' => $quotation->igtf_rate,
			'discount_1' => $quotation->discount_1,
			'discount_2' => $quotation->discount_2,
			'freight' => $quotation->freight,
			'notes' => $quotation->notes,
			'items' => $items,
		]);

		return redirect()->route('admin.quotations.create')->with('message', 'Cotización duplicada. Revísala y guárdala para confirmarla.');
	}

	/**
	 * Export quotation to PDF.
	 */
	public function exportPdf(Quotation $quotation)
	{
		$quotation->load(['client', 'items.product']);

		$pdf = Pdf::loadView('admin.quotations.pdf', compact('quotation'))
			->setOptions([
				'isRemoteEnabled' => true,
				'defaultFont' => 'DejaVu Sans',
				'isHtml5ParserEnabled' => true,
				'isFontSubsettingEnabled' => true,
				'dpi' => 96,
			]);

		return $pdf->download("cotizacion-{$quotation->quotation_number}.pdf");
	}

	/**
	 * Normalize the description text to replace smart punctuation
	 * (en-dash, em-dash, curly quotes, etc.) with their ASCII equivalents
	 * to avoid characters being lost or rendered as "?" by some fonts.
	 */
	protected function normalizeDescription(?string $text): ?string
	{
		if ($text === null) return null;
		$map = [
			"\u{2010}" => '-', "\u{2011}" => '-', "\u{2012}" => '-',
			"\u{2013}" => '-', "\u{2014}" => '-', "\u{2212}" => '-',
			"\u{2018}" => "'", "\u{2019}" => "'", "\u{201A}" => "'", "\u{201B}" => "'",
			"\u{201C}" => '"', "\u{201D}" => '"', "\u{201E}" => '"', "\u{201F}" => '"',
			"\u{2026}" => '...',
		];
		return strtr($text, $map);
	}

	/**
	 * Friendly field names for validation messages (avoids "items.0.description").
	 */
	protected function validationAttributes(): array
	{
		return [
			'emission_date' => 'fecha de emisión',
			'expiration_date' => 'fecha de vencimiento',
			'price_mode' => 'moneda',
			'cli_title' => 'razón social',
			'cli_document' => 'RIF',
			'cli_email' => 'email',
			'cli_phone' => 'teléfono',
			'cli_address' => 'dirección',
			'items' => 'productos',
			'items.*.code' => 'código del producto',
			'items.*.description' => 'descripción del producto',
			'items.*.quantity' => 'cantidad del producto',
			'items.*.unit_price' => 'precio unitario del producto',
			'items.*.discount_percent' => 'tributos del producto',
			'status_comment' => 'comentario del estado',
		];
	}

	/**
	 * Resolve the client for a quotation: update the selected one or create a new one
	 * based on the cli_* form fields. Returns the Client instance.
	 */
	protected function resolveClient(Request $request)
	{
		$clientId = $request->input('client_id');

		$clientData = $request->validate([
			'cli_title' => 'required|string|max:100',
			'cli_document' => 'required|string|max:20',
			'cli_email' => 'nullable|email|max:100',
			'cli_phone' => 'nullable|string|max:30',
			'cli_address' => 'nullable|string|max:500',
		]);

		$payload = [
			'title' => $clientData['cli_title'],
			'document' => $clientData['cli_document'],
			'email' => $clientData['cli_email'] ?? null,
			'phone' => $clientData['cli_phone'] ?? null,
			'address' => $clientData['cli_address'] ?? null,
		];

		// Selected an existing client from the dropdown.
		if ($clientId)
		{
			$client = Client::findOrFail($clientId);
			$client->update($payload);
			return $client;
		}

		// No client selected: if a client with this RIF already exists
		// (e.g. typed manually or created concurrently), link to it and
		// update its data instead of failing with a unique-constraint error.
		$existing = Client::where('document', $clientData['cli_document'])->first();
		if ($existing)
		{
			$existing->update($payload);
			return $existing;
		}

		// Truly new client.
		$payload['code'] = 'CLI-' . time();
		return Client::create($payload);
	}

	/**
	 * Lightweight endpoint to keep the session alive while a form is open.
	 * Any authenticated request refreshes the session lifetime.
	 */
	public function heartbeat()
	{
		return response()->json(['alive' => true]);
	}

	/**
	 * Fetch the current Binance and BCV rates from external APIs, persist them
	 * in config, and return them as JSON. Falls back to the saved values if a
	 * source is unreachable.
	 */
	public function fetchRates(ExchangeRateService $service)
	{
		$saved = $this->savedRates();
		$fetched = $service->fetchRates();

		$binance = $fetched['binance'] ?? null;
		$bcv = $fetched['bcv'] ?? null;

		// Persist whatever we successfully fetched; keep saved value otherwise.
		if ($binance !== null) {
			Config::firstOrNew(['key' => 'binance_rate'])->fill(['value' => $binance])->save();
		}
		if ($bcv !== null) {
			Config::firstOrNew(['key' => 'bcv_rate'])->fill(['value' => $bcv])->save();
		}

		return response()->json([
			'success' => ($binance !== null && $bcv !== null),
			'binance' => $binance ?? $saved['binance'],
			'bcv' => $bcv ?? $saved['bcv'],
			'binance_ok' => $binance !== null,
			'bcv_ok' => $bcv !== null,
		]);
	}

	/**
	 * Save manually-entered rates to config.
	 */
	public function saveRates(Request $request)
	{
		$data = $request->validate([
			'binance_rate' => 'required|numeric|min:0',
			'bcv_rate' => 'required|numeric|min:0',
		]);

		Config::firstOrNew(['key' => 'binance_rate'])->fill(['value' => $data['binance_rate']])->save();
		Config::firstOrNew(['key' => 'bcv_rate'])->fill(['value' => $data['bcv_rate']])->save();

		return response()->json(['success' => true, 'binance' => $data['binance_rate'], 'bcv' => $data['bcv_rate']]);
	}

	/**
	 * Print view for the quotation. Uses the same template as the PDF for visual parity.
	 */
	public function printView(Quotation $quotation)
	{
		$quotation->load(['client', 'items.product']);
		$autoPrint = true;

		return view('admin.quotations.pdf', compact('quotation', 'autoPrint'));
	}
}
