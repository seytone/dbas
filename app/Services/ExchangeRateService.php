<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ExchangeRateService
{
	/** Cache lifetime in seconds (10 minutes). Rates do not change often intraday. */
	const CACHE_TTL = 600;

	/** Request timeout in seconds. */
	const TIMEOUT = 5;

	/**
	 * Fetch both rates. Returns ['binance' => float|null, 'bcv' => float|null].
	 * A null value means that source could not be reached.
	 */
	public function fetchRates(): array
	{
		return [
			'binance' => $this->fetchBinance(),
			'bcv' => $this->fetchBcv(),
		];
	}

	/**
	 * Fetch the official BCV rate from DolarAPI.
	 */
	public function fetchBcv(): ?float
	{
		return Cache::remember('quotation_rate_bcv', self::CACHE_TTL, function () {
			try {
				$response = Http::withoutVerifying()
					->timeout(self::TIMEOUT)
					->get('https://ve.dolarapi.com/v1/dolares/oficial');

				if ($response->successful() && $response->json('promedio')) {
					return round((float) $response->json('promedio'), 4);
				}
			} catch (\Throwable $e) {
				Log::warning('ExchangeRateService BCV fetch failed: ' . $e->getMessage());
			}
			return null;
		});
	}

	/**
	 * Fetch the Binance P2P USDT/VES rate from CriptoYa (uses the ask price).
	 */
	public function fetchBinance(): ?float
	{
		return Cache::remember('quotation_rate_binance', self::CACHE_TTL, function () {
			try {
				$response = Http::withoutVerifying()
					->timeout(self::TIMEOUT)
					->get('https://criptoya.com/api/binancep2p/USDT/VES/1');

				if ($response->successful() && $response->json('ask')) {
					return round((float) $response->json('ask'), 4);
				}
			} catch (\Throwable $e) {
				Log::warning('ExchangeRateService Binance fetch failed: ' . $e->getMessage());
			}
			return null;
		});
	}
}
