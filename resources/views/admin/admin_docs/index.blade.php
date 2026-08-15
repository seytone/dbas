@extends('layouts.admin')
@section('content')
	<div class="row mb-3">
		<div class="col-md-8">
			<h1>{{ $label }}</h1>
		</div>
		<div class="col-md-4 text-right">
			<a href="{{ route('admin.admin_docs.create', $type) }}" class="btn btn-primary">
				<i class="fa fa-plus mr-1"></i> Nuevo
			</a>
		</div>
	</div>

	{{-- session('message') already rendered by the layout at the top with an X button. --}}

	<div class="card">
		<div class="card-body">
			@if($documents->count() === 0)
				<p class="text-center text-muted mb-0 py-4">
					Aún no hay documentos de este tipo. Crea el primero con el botón "Nuevo".
				</p>
			@else
				<table class="table table-hover table-sm datatable datatable-admin-docs">
					<thead>
						<tr>
							<th>Número</th>
							<th>Cliente</th>
							<th>Empresa</th>
							<th>Emitido por</th>
							<th>Fecha</th>
							<th class="text-right">Acciones</th>
						</tr>
					</thead>
					<tbody>
						@foreach($documents as $doc)
							<tr>
								<td><b>{{ $doc->formatted_number }}</b></td>
								<td>{{ $doc->data['client_name'] ?? '—' }}</td>
								<td>{{ config('companies.' . $doc->company . '.label') }}</td>
								<td>{{ $doc->author->name ?? '—' }}</td>
								<td>{{ $doc->created_at->format('d/m/Y H:i') }}</td>
								<td class="text-right">
									<a href="{{ route('admin.admin_docs.show', [$type, $doc->id]) }}" class="btn btn-sm btn-info" title="Ver">
										<i class="fa fa-eye"></i>
									</a>
									<a href="{{ route('admin.admin_docs.edit', [$type, $doc->id]) }}" class="btn btn-sm btn-warning" title="Editar">
										<i class="fa fa-pencil-alt"></i>
									</a>
									<a href="{{ route('admin.admin_docs.pdf', [$type, $doc->id]) }}" class="btn btn-sm btn-secondary" title="Descargar PDF">
										<i class="fa fa-file-pdf"></i>
									</a>
									<form action="{{ route('admin.admin_docs.destroy', [$type, $doc->id]) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar este documento?');">
										@csrf @method('DELETE')
										<button type="submit" class="btn btn-sm btn-danger" title="Eliminar"><i class="fa fa-times"></i></button>
									</form>
								</td>
							</tr>
						@endforeach
					</tbody>
				</table>
			@endif
		</div>
	</div>
@endsection

@section('scripts')
<script>
$(function() {
	// DataTable init (search box + sortable columns + pagination). The
	// last column has HTML action buttons and shouldn't sort/search.
	$('.datatable-admin-docs').DataTable({
		order: [[4, 'desc']],
		columnDefs: [{ orderable: false, searchable: false, targets: -1 }],
	});
});
</script>
@endsection
