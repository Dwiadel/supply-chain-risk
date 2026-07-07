<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Services\RiskScoringService;
use Illuminate\Http\JsonResponse;

class RiskController extends Controller
{
    public function __construct(
        protected RiskScoringService $riskService
    ) {}

    /**
     * GET /api/risk/{cca2}
     * Hitung & simpan risk score terbaru untuk negara tertentu.
     */
    public function calculate(string $cca2): JsonResponse
    {
        $country = Country::where('cca2', strtoupper($cca2))->first();

        if (! $country) {
            return response()->json([
                'success' => false,
                'message' => "Negara '{$cca2}' belum ada di database. Jalankan fetch-all terlebih dahulu.",
            ], 404);
        }

        $riskScore = $this->riskService->calculateForCountry($country);

        return response()->json([
            'success' => true,
            'data' => [
                'country'    => $country->name,
                'cca2'       => $country->cca2,
                'total_score'=> $riskScore->total_score,
                'risk_level' => $riskScore->risk_level,
                'components' => [
                    'weather'   => $riskScore->weather_score,
                    'inflation' => $riskScore->inflation_score,
                    'currency'  => $riskScore->currency_score,
                    'news'      => $riskScore->news_sentiment_score,
                ],
                'calculated_at' => $riskScore->calculated_at,
            ],
        ]);
    }

    /**
     * GET /api/risk/{cca2}/breakdown
     * Tampilkan breakdown detail tiap komponen (untuk dashboard penjelasan).
     */
    public function breakdown(string $cca2): JsonResponse
    {
        $country = Country::where('cca2', strtoupper($cca2))->first();

        if (! $country) {
            return response()->json([
                'success' => false,
                'message' => "Negara '{$cca2}' belum ada di database.",
            ], 404);
        }

        $breakdown = $this->riskService->getBreakdown($country);

        return response()->json([
            'success' => true,
            'country' => $country->name,
            'data'    => $breakdown,
        ]);
    }

    /**
     * GET /api/risk/{cca2}/history
     * Ambil history risk score untuk grafik tren.
     */
    public function history(string $cca2): JsonResponse
    {
        $country = Country::where('cca2', strtoupper($cca2))->first();

        if (! $country) {
            return response()->json([
                'success' => false,
                'message' => "Negara '{$cca2}' belum ada di database.",
            ], 404);
        }

        $history = $country->riskScores()
            ->orderBy('calculated_at', 'desc')
            ->limit(30)
            ->get(['total_score', 'risk_level', 'weather_score',
                   'inflation_score', 'currency_score', 'news_sentiment_score', 'calculated_at']);

        return response()->json([
            'success' => true,
            'country' => $country->name,
            'data'    => $history,
        ]);
    }
}