<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdministrativeDocument;
use App\Models\Client;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdministrativeDocumentsController extends Controller
{
    /**
     * Validate that the URL type segment is a real document kind. Called at
     * the top of every action; keeps a bad URL from reaching the template
     * dispatcher.
     */
    protected function assertType(string $type): void
    {
        abort_unless(array_key_exists($type, AdministrativeDocument::$labels), 404);
    }

    /**
     * Convert a type slug to the folder that holds its form/pdf blades.
     * All template lookups go through this helper.
     */
    protected function viewFor(string $type, string $view): string
    {
        return "admin.admin_docs.$type.$view";
    }

    // ---------- List ----------

    public function index(string $type)
    {
        $this->assertType($type);

        $documents = AdministrativeDocument::where('type', $type)
            ->orderByDesc('created_at')
            ->paginate(30);

        return view('admin.admin_docs.index', [
            'type' => $type,
            'label' => AdministrativeDocument::$labels[$type],
            'documents' => $documents,
        ]);
    }

    // ---------- Create ----------

    public function create(string $type)
    {
        $this->assertType($type);

        // Client list feeds the searchable selector at the top of every
        // admin doc form — lets the user pick an existing client and
        // auto-fill the rest of the fields instead of retyping.
        $clients = Client::orderBy('title')->get();

        $extra = [];
        if ($type === AdministrativeDocument::TYPE_CREDIT_NOTE) {
            // Nota de Crédito needs a parent Invoice to reference.
            $extra['invoices'] = AdministrativeDocument::where('type', AdministrativeDocument::TYPE_INVOICE)
                ->orderByDesc('number')
                ->get();
        }

        return view($this->viewFor($type, 'form'), array_merge([
            'type' => $type,
            'label' => AdministrativeDocument::$labels[$type],
            'clients' => $clients,
        ], $extra));
    }

    // ---------- Store ----------

    public function store(Request $request, string $type)
    {
        $this->assertType($type);

        $rules = $this->validationRulesFor($type);
        $validated = $request->validate($rules);

        $company = $validated['company'] ?? 've';
        $parentId = $validated['parent_document_id'] ?? null;

        // Any field not part of the document envelope goes into the JSON
        // payload. Keeps the schema stable while each type stores its own
        // shape (items list, free text, etc.).
        $payload = collect($validated)
            ->except(['company', 'parent_document_id'])
            ->toArray();

        $document = DB::transaction(function () use ($type, $company, $parentId, $payload) {
            return AdministrativeDocument::create([
                'type' => $type,
                'number' => AdministrativeDocument::nextNumber($type),
                'company' => $company,
                'parent_document_id' => $parentId,
                'data' => $payload,
                'created_by' => Auth::id(),
            ]);
        });

        return redirect()
            ->route('admin.admin_docs.show', [$type, $document->id])
            ->with('message', AdministrativeDocument::$labels[$type] . ' ' . $document->formatted_number . ' generado.');
    }

    // ---------- Show ----------

    public function show(string $type, int $documentId)
    {
        $this->assertType($type);
        $document = AdministrativeDocument::where('type', $type)->findOrFail($documentId);

        return view('admin.admin_docs.show', [
            'type' => $type,
            'label' => AdministrativeDocument::$labels[$type],
            'document' => $document,
        ]);
    }

    // ---------- PDF ----------

    public function pdf(string $type, int $documentId)
    {
        $this->assertType($type);
        $document = AdministrativeDocument::where('type', $type)->findOrFail($documentId);

        $pdf = Pdf::loadView($this->viewFor($type, 'pdf'), [
            'document' => $document,
            'company' => config('companies.' . $document->company),
        ])->setOptions([
            'isRemoteEnabled' => true,
            'defaultFont' => 'DejaVu Sans',
            'isHtml5ParserEnabled' => true,
            'dpi' => 96,
        ]);

        $filename = strtolower(AdministrativeDocument::$prefixes[$type]) . '-' . str_pad($document->number, 4, '0', STR_PAD_LEFT) . '.pdf';
        return $pdf->download($filename);
    }

    // ---------- Destroy ----------

    public function destroy(string $type, int $documentId)
    {
        $this->assertType($type);
        $document = AdministrativeDocument::where('type', $type)->findOrFail($documentId);
        $document->delete();

        return redirect()
            ->route('admin.admin_docs.index', $type)
            ->with('message', 'Documento eliminado.');
    }

    // ---------- Per-type validation ----------

    protected function validationRulesFor(string $type): array
    {
        $base = [
            'company' => 'required|string|in:ve,us',
            'client_name' => 'required|string|max:255',
            'client_document' => 'nullable|string|max:50',
            'client_phone' => 'nullable|string|max:50',
            'client_address' => 'nullable|string|max:500',
        ];

        switch ($type) {
            case AdministrativeDocument::TYPE_INVOICE:
                return $base + [
                    'ship_address' => 'nullable|string|max:500',
                    'items' => 'required|array|min:1',
                    'items.*.code' => 'nullable|string|max:100',
                    'items.*.description' => 'required|string|max:2000',
                    'items.*.quantity' => 'required|numeric|min:0',
                    'items.*.price' => 'required|numeric',
                ];

            case AdministrativeDocument::TYPE_CREDIT_NOTE:
                return $base + [
                    'parent_document_id' => 'required|exists:administrative_documents,id',
                    'reason' => 'nullable|string|max:255',
                    'items' => 'required|array|min:1',
                    'items.*.code' => 'nullable|string|max:100',
                    'items.*.description' => 'required|string|max:2000',
                    'items.*.quantity' => 'required|numeric|min:0',
                    'items.*.price' => 'required|numeric',
                ];

            case AdministrativeDocument::TYPE_DELIVERY_ORDER:
                return $base + [
                    'items' => 'required|array|min:1',
                    'items.*.quantity' => 'required|numeric|min:0',
                    'items.*.description' => 'required|string|max:2000',
                    'items.*.serial' => 'nullable|string|max:100',
                ];

            case AdministrativeDocument::TYPE_TERMS:
                return $base + [
                    'delivery_order_ref' => 'nullable|string|max:50',
                    'payment_method' => 'required|string|max:100',
                    'sign_city' => 'required|string|max:100',
                    'sign_state' => 'required|string|max:100',
                ];

            case AdministrativeDocument::TYPE_EXIT_ORDER:
                return $base + [
                    'invoice_ref' => 'nullable|string|max:50',
                    'seller_name' => 'required|string|max:150',
                    'items' => 'required|array|min:1',
                    'items.*.quantity' => 'required|numeric|min:0',
                    'items.*.sku' => 'nullable|string|max:100',
                    'items.*.description' => 'required|string|max:2000',
                    'observations' => 'nullable|string|max:1000',
                ];
        }

        return $base;
    }
}
