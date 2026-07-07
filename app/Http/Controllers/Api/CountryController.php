<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CountryDataAggregatorService;
use App\Services\RiskScoringService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    public function __construct(
        protected CountryDataAggregatorService $aggregator,
        protected RiskScoringService $riskService,
    ) {}

    public function search(Request $request): JsonResponse
    {
        $query = $request->query('q', '');

        if (strlen($query) < 2) {
            return response()->json([
                'success' => false,
                'message' => 'Ketik minimal 2 karakter untuk mencari negara.',
                'data'    => [],
            ], 422);
        }

        $results = $this->aggregator->searchCountries($query);

        return response()->json([
            'success' => true,
            'data'    => $results,
        ]);
    }

    public function fetchAll(string $cca2): JsonResponse
    {
        try {
            $result  = $this->aggregator->fetchAllDataForCountry($cca2);
            $country = $result['country'];

            // Load semua relasi
            $country->load(['indicators', 'weatherCaches', 'riskScores', 'newsArticles']);

            // Hitung risk score baru
            $this->riskService->calculateForCountry($country);

            // Reload setelah risk score dihitung
            $country->load(['indicators', 'weatherCaches', 'riskScores', 'newsArticles']);

            // Build response array dengan nama key yang konsisten untuk frontend
            $responseData = [
                'id'                  => $country->id,
                'cca2'                => $country->cca2,
                'cca3'                => $country->cca3,
                'name'                => $country->name,
                'official_name'       => $country->official_name,
                'region'              => $country->region,
                'subregion'           => $country->subregion,
                'capital'             => $country->capital,
                'currency_code'       => $country->currency_code,
                'currency_name'       => $country->currency_name,
                'latitude'            => $country->latitude,
                'longitude'           => $country->longitude,
                'flag_url'            => $country->flag_url,

                // Nama key eksplisit supaya JavaScript bisa baca dengan benar
                'country_indicators'  => $country->indicators->toArray(),
                'weather_caches'      => $country->weatherCaches->toArray(),
                'risk_scores'         => $country->riskScores->toArray(),
                'news_articles'       => $country->newsArticles->toArray(),
            ];

            return response()->json([
                'success'        => true,
                'data'           => $responseData,
                'partial_errors' => $result['errors'],
            ]);

        } catch (\RuntimeException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }
}