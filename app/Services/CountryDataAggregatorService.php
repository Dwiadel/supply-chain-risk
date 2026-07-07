<?php

namespace App\Services;

use App\Models\Country;
use Illuminate\Support\Facades\Log;

class CountryDataAggregatorService
{
    public function __construct(
        protected RestCountriesService    $restCountries,
        protected OpenMeteoService        $weather,
        protected WorldBankService        $worldBank,
        protected ExchangeRateService     $exchangeRate,
        protected GNewsService            $gNews,
        protected SentimentAnalysisService $sentiment,
    ) {}

    public function searchCountries(string $query): array
    {
        return $this->restCountries->search($query);
    }

    public function fetchAllDataForCountry(string $cca2): array
    {
        $errors = [];
        $cca2   = strtoupper($cca2);

        // Langkah 1: pastikan negara ada di tabel lokal
        $country = Country::where('cca2', $cca2)->first();

        if (! $country) {
            $countryData = $this->restCountries->getByCode($cca2);

            if (! $countryData) {
                throw new \RuntimeException("Negara dengan kode '{$cca2}' tidak ditemukan.");
            }

            $country = $this->restCountries->saveToDatabase($countryData);
        }

        // Langkah 2: fetch cuaca
        try {
            $this->weather->fetchAndCache($country);
        } catch (\Exception $e) {
            $errors[] = 'Gagal mengambil data cuaca: ' . $e->getMessage();
            Log::warning('Aggregator: weather fetch gagal', ['country' => $cca2]);
        }

        // Langkah 3: fetch data ekonomi
        try {
            $this->worldBank->fetchAndCache($country);
        } catch (\Exception $e) {
            $errors[] = 'Gagal mengambil data ekonomi: ' . $e->getMessage();
            Log::warning('Aggregator: worldbank fetch gagal', ['country' => $cca2]);
        }

        // Langkah 4: fetch kurs
        try {
            $this->exchangeRate->fetchAndCache($country);
        } catch (\Exception $e) {
            $errors[] = 'Gagal mengambil data kurs: ' . $e->getMessage();
            Log::warning('Aggregator: exchangerate fetch gagal', ['country' => $cca2]);
        }

        // Langkah 5: fetch berita + analisis sentimen
        try {
            $this->gNews->fetchAndCacheForCountry($country, $this->sentiment);
        } catch (\Exception $e) {
            $errors[] = 'Gagal mengambil berita: ' . $e->getMessage();
            Log::warning('Aggregator: gnews fetch gagal', ['country' => $cca2]);
        }

        $country->refresh();
        $country->load(['indicators', 'weatherCaches', 'riskScores', 'newsArticles']);

        return [
            'country' => $country,
            'errors'  => $errors,
        ];
    }
}