<?php

namespace App\Services;

use App\Models\Country;
use App\Models\CurrencyRate;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ExchangeRateService
{
    protected string $apiKey = '';
    protected string $baseUrl = 'https://v6.exchangerate-api.com/v6';

    public function __construct()
    {
        $this->apiKey = (string) config('services.exchangerate.key', '');
    }

    public function fetchAndCache(Country $country): ?CurrencyRate
    {
        if (! $country->currency_code) {
            return $this->latestCachedRate($country->currency_code);
        }

        if ($country->currency_code === 'USD') {
            return CurrencyRate::updateOrCreate(
                ['base_currency' => 'USD', 'target_currency' => 'USD', 'rate_date' => now()->toDateString()],
                ['rate' => 1, 'change_percent' => 0]
            );
        }

        if (empty($this->apiKey)) {
            Log::warning('ExchangeRateService: API key belum diset di .env');
            return $this->latestCachedRate($country->currency_code);
        }

        $todayCache = CurrencyRate::where('base_currency', 'USD')
            ->where('target_currency', $country->currency_code)
            ->where('rate_date', now()->toDateString())
            ->first();

        if ($todayCache) {
            return $todayCache;
        }

        try {
            $response = Http::timeout(10)->get("{$this->baseUrl}/{$this->apiKey}/pair/USD/{$country->currency_code}");

            if (! $response->successful()) {
                return $this->latestCachedRate($country->currency_code);
            }

            $data = $response->json();

            if (($data['result'] ?? null) !== 'success') {
                return $this->latestCachedRate($country->currency_code);
            }

            $rate          = $data['conversion_rate'];
            $previousRate  = $this->latestCachedRate($country->currency_code);
            $changePercent = $previousRate
                ? round((($rate - $previousRate->rate) / $previousRate->rate) * 100, 4)
                : 0;

            return CurrencyRate::updateOrCreate(
                [
                    'base_currency'   => 'USD',
                    'target_currency' => $country->currency_code,
                    'rate_date'       => now()->toDateString(),
                ],
                ['rate' => $rate, 'change_percent' => $changePercent]
            );
        } catch (\Exception $e) {
            Log::warning('ExchangeRateService::fetchAndCache gagal', [
                'currency' => $country->currency_code,
                'error'    => $e->getMessage(),
            ]);
            return $this->latestCachedRate($country->currency_code);
        }
    }

    protected function latestCachedRate(?string $currencyCode): ?CurrencyRate
    {
        if (! $currencyCode) {
            return null;
        }

        return CurrencyRate::where('base_currency', 'USD')
            ->where('target_currency', $currencyCode)
            ->orderBy('rate_date', 'desc')
            ->first();
    }
}