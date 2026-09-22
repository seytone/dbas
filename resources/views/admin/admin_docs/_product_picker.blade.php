{{-- Product picker shared by the item-based admin_docs forms
     (invoice, delivery_order, exit_order, service_order). Renders the
     Selectize search + "Producto Libre" button. Also publishes
     window.__productsBySku so each form can wire up an SKU lookup on
     the row's code input.

     $scope (opcional): sufijo para poder incluir el picker más de una vez
     en la misma página (la Orden de Servicio tiene dos tablas de items).
     Sin $scope los IDs/clases quedan como siempre — no rompe los forms
     que ya lo usaban. --}}
@php $scope = $scope ?? ''; @endphp

<div class="row mb-3">
	<div class="col-md-8">
		<select id="product-selector{{ $scope }}" class="selectize-products{{ $scope }}" placeholder="Buscar producto por SKU o nombre...">
			<option value="">Buscar producto por SKU o nombre...</option>
			@foreach ($categories as $category)
				@if ($category->products->count() > 0)
					<optgroup label="{{ mb_strtoupper($category->title) }}">
						@foreach ($category->products as $product)
							<option value="{{ $product->id }}" data-data='@json($product)'>
								{{ $product->code }} — {{ $product->title }}
							</option>
						@endforeach
					</optgroup>
				@endif
			@endforeach
		</select>
	</div>
	<div class="col-md-4">
		<button type="button" class="btn btn-outline-success btn-block" id="btn-add-free{{ $scope }}">
			<i class="fa fa-plus mr-1"></i> Producto Libre
		</button>
	</div>
</div>

@once
<script>
{{-- Look-up table so the form can resolve a SKU typed in the row's code
     column to the full product record without hitting the server. Keys
     are uppercased/trimmed for case-insensitive matches. Pure vanilla
     JS so it can run at parse time (before jQuery is loaded). --}}
@php
	$productsBySku = [];
	foreach ($categories as $category) {
		foreach ($category->products as $p) {
			if (!empty($p->code)) {
				$productsBySku[mb_strtoupper(trim($p->code))] = [
					'id'          => $p->id,
					'code'        => $p->code,
					'title'       => $p->title,
					'description' => $p->description,
					'price'       => $p->price,
				];
			}
		}
	}
@endphp
window.__productsBySku = @json($productsBySku);
window.__lookupProductBySku = function(sku) {
	if (!sku) return null;
	return window.__productsBySku[String(sku).trim().toUpperCase()] || null;
};
</script>
@endonce
