<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ExchangeRateService
{
	/** Cache lifetime in seconds (10 minutes) for SUCCESSFUL fetches.
	 *  We intentionally never cache failures (null) so transient errors
	 *  do not block retries for the full TTL. */
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
		$cached = Cache::get('quotation_rate_bcv');
		if (is_numeric($cached)) {
			return (float) $cached;
		}

		try {
			$response = Http::withoutVerifying()
				->timeout(self::TIMEOUT)
				->get('https://ve.dolarapi.com/v1/dolares/oficial');

			if ($response->successful() && $response->json('promedio')) {
				$rate = round((float) $response->json('promedio'), 4);
				Cache::put('quotation_rate_bcv', $rate, self::CACHE_TTL);
				return $rate;
			}

			Log::warning('ExchangeRateService BCV fetch returned no rate', [
				'status' => $response->status(),
				'body' => mb_substr($response->body(), 0, 500),
			]);
		} catch (\Throwable $e) {
			Log::warning('ExchangeRateService BCV fetch failed: ' . $e->getMessage(), [
				'class' => get_class($e),
			]);
		}

		// Do not cache null: allow the next call to retry immediately.
		return null;
	}

	/**
	 * Fetch the Binance P2P USDT/VES rate from CriptoYa (uses the ask price).
	 */
	public function fetchBinance(): ?float
	{
		$cached = Cache::get('quotation_rate_binance');
		if (is_numeric($cached)) {
			return (float) $cached;
		}

		try {
			$response = Http::withoutVerifying()
				->timeout(self::TIMEOUT)
				->get('https://criptoya.com/api/binancep2p/USDT/VES/1');

			if ($response->successful() && $response->json('ask')) {
				$rate = round((float) $response->json('ask'), 4);
				Cache::put('quotation_rate_binance', $rate, self::CACHE_TTL);
				return $rate;
			}

			Log::warning('ExchangeRateService Binance fetch returned no rate', [
				'status' => $response->status(),
				'body' => mb_substr($response->body(), 0, 500),
			]);
		} catch (\Throwable $e) {
			Log::warning('ExchangeRateService Binance fetch failed: ' . $e->getMessage(), [
				'class' => get_class($e),
			]);
		}

		// Do not cache null: allow the next call to retry immediately.
		return null;
	}
}
