{{-- Product picker shared by the 4 item-based admin_docs forms
     (invoice, credit_note, delivery_order, exit_order). Renders the
     Selectize search + "Producto Libre" button. Each form is expected to
     define two JS callbacks the picker will invoke:
        addProductRow(data)  -> data = {id, code, title, price, description}
        addFreeRow()
     These handlers own the specific row markup for their format. --}}

<div class="row mb-3">
	<div class="col-md-8">
		<select id="product-selector" class="selectize-products" placeholder="Buscar producto por código o nombre...">
			<option value="">Seleccione un producto para agregar...</option>
			@foreach ($categories as $category)
				@if ($category->products->count() > 0)
					<optgroup label="{{ mb_strtoupper($category->title) }}">
						@foreach ($category->products as $product)
							<option value="{{ $product->id }}" data-data='@json($product)'>
								{{ $product->title }} ({{ $product->code }})
							</option>
						@endforeach
					</optgroup>
				@endif
			@endforeach
		</select>
	</div>
	<div class="col-md-4">
		<button type="button" class="btn btn-outline-success btn-block" id="btn-add-free">
			<i class="fa fa-plus mr-1"></i> Producto Libre
		</button>
	</div>
</div>
