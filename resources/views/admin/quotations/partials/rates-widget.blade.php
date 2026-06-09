{{-- Exchange rates widget (Binance + BCV). Mirrors the Mercado Libre commission widget pattern. --}}
<div class="rates-widget bg-light border rounded px-3 py-2 mt-2 mt-md-0">
	<div class="d-flex align-items-center flex-wrap" style="gap: 8px;">
		<span class="text-muted" style="font-size: 12px;"><i class="fa fa-cog mr-1"></i><b>Tasas del día</b></span>
		<div class="input-group input-group-sm" style="width: 160px;">
			<div class="input-group-prepend"><span class="input-group-text">Binance</span></div>
			<input type="number" step="0.0001" min="0" id="rate_binance_input" class="form-control text-right" value="{{ old('binance_rate') ?: $rates['binance'] }}">
		</div>
		<div class="input-group input-group-sm" style="width: 140px;">
			<div class="input-group-prepend"><span class="input-group-text">BCV</span></div>
			<input type="number" step="0.0001" min="0" id="rate_bcv_input" class="form-control text-right" value="{{ old('bcv_rate') ?: $rates['bcv'] }}">
		</div>
		<button type="button" class="btn btn-sm btn-info" id="btn-fetch-rates" title="Traer tasas automáticamente">
			<i class="fa fa-refresh"></i> <span class="d-none d-lg-inline">Actualizar</span>
		</button>
		<button type="button" class="btn btn-sm btn-dark d-none" id="btn-save-rates" title="Guardar las tasas editadas manualmente">
			<i class="fa fa-save"></i>
		</button>
		<span id="rates-status" style="font-size: 11px;"></span>
	</div>
</div>
