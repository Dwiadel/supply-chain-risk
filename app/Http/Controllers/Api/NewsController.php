<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\NewsArticle;
use App\Services\SentimentAnalysisService;
use Illuminate\Http\JsonResponse;

class NewsController extends Controller
{
    public function __construct(
        protected SentimentAnalysisService $sentiment
    ) {}

    /**
     * GET /api/news/{cca2}
     * Ambil daftar berita terbaru untuk satu negara.
     */
    public function index(string $cca2): JsonResponse
    {
        $country = Country::where('cca2', strtoupper($cca2))->first();

        if (! $country) {
            return response()->json([
                'success' => false,
                'message' => "Negara '{$cca2}' belum ada di database.",
            ], 404);
        }

        $articles = NewsArticle::where('country_id', $country->id)
            ->orderBy('published_at', 'desc')
            ->limit(20)
            ->get(['title', 'description', 'source', 'url',
                   'sentiment', 'positive_score', 'negative_score', 'published_at']);

        return response()->json([
            'success' => true,
            'country' => $country->name,
            'total'   => $articles->count(),
            'data'    => $articles,
        ]);
    }

    /**
     * GET /api/news/{cca2}/sentiment
     * Ambil ringkasan statistik sentiment untuk dashboard.
     */
    public function sentiment(string $cca2): JsonResponse
    {
        $country = Country::where('cca2', strtoupper($cca2))->first();

        if (! $country) {
            return response()->json([
                'success' => false,
                'message' => "Negara '{$cca2}' belum ada di database.",
            ], 404);
        }

        $summary = $this->sentiment->getSummaryForCountry($country->id);

        return response()->json([
            'success' => true,
            'country' => $country->name,
            'data'    => $summary,
        ]);
    }
}