<?php

namespace App\Services;

use App\Models\Country;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RestCountriesService
{
    protected string $baseUrl = 'https://api.restcountries.com/countries/v5';

    protected string $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.restcountries.key', '');
    }

    public function search(string $query): array
    {
        try {
            $response = Http::timeout(10)
                ->withHeaders($this->authHeader())
                ->get($this->baseUrl.'/name', ['q' => $query, 'limit' => 10]);

            if (! $response->successful()) {
                Log::warning('RestCountriesService::search status gagal', [
                    'query' => $query,
                    'status' => $response->status(),
                    'body' => $response->json('errors'),
                ]);

                return [];
            }

            $objects = $response->json('data.objects', []);

            if (! is_array($objects)) {
                return [];
            }

            return collect($objects)
                ->map(fn ($country) => $this->simplify($country))
                ->filter()
                ->values()
                ->all();
        } catch (\Exception $e) {
            Log::warning('RestCountriesService::search gagal', ['query' => $query, 'error' => $e->getMessage()]);

            return [];
        }
    }

    public function getByCode(string $cca2): ?array
    {
        try {
            $response = Http::timeout(10)
                ->withHeaders($this->authHeader())
                ->get($this->baseUrl.'/codes.alpha_2/'.$cca2);

            if (! $response->successful()) {
                Log::warning('RestCountriesService::getByCode status gagal', [
                    'cca2' => $cca2,
                    'status' => $response->status(),
                    'body' => $response->json('errors'),
                ]);

                return null;
            }

            $objects = $response->json('data.objects', []);
            $country = $objects[0] ?? null;

            if (! $country) {
                return null;
            }

            return $this->simplify($country);
        } catch (\Exception $e) {
            Log::warning('RestCountriesService::getByCode gagal', ['cca2' => $cca2, 'error' => $e->getMessage()]);

            return null;
        }
    }

    public function saveToDatabase(array $simplifiedData): Country
    {
        return Country::updateOrCreate(
            ['cca2' => $simplifiedData['cca2']],
            $simplifiedData
        );
    }

    protected function authHeader(): array
    {
        if (empty($this->apiKey)) {
            Log::warning('RestCountriesService: REST_COUNTRIES_API_KEY belum diset di .env');
        }

        return ['Authorization' => 'Bearer '.$this->apiKey];
    }

    protected function simplify(array $country): ?array
    {
        $cca2 = $country['codes']['alpha_2'] ?? null;

        if (! $cca2) {
            return null;
        }

        $currencies = $country['currencies'] ?? [];
        $firstCurrency = $currencies[0] ?? null;
        $firstCurrencyCode = $firstCurrency['code'] ?? null;
        $firstCurrencyName = $firstCurrency['name'] ?? null;

        $capitals = $country['capitals'] ?? [];
        $capitalName = $capitals[0]['name'] ?? null;

        return [
            'cca2' => $cca2,
            'cca3' => $country['codes']['alpha_3'] ?? null,
            'name' => $country['names']['common'] ?? 'Unknown',
            'official_name' => $country['names']['official'] ?? null,
            'region' => $country['region'] ?? null,
            'subregion' => $country['subregion'] ?? null,
            'capital' => $capitalName,
            'currency_code' => $firstCurrencyCode,
            'currency_name' => $firstCurrencyName,
            'latitude' => $country['coordinates']['lat'] ?? null,
            'longitude' => $country['coordinates']['lng'] ?? null,
            'flag_url' => $country['flag']['url_png'] ?? null,
        ];
    }
}