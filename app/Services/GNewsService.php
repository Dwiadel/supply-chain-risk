<?php

namespace App\Services;

use App\Models\Country;
use App\Models\NewsArticle;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Service untuk fetch berita dari GNews API.
 * Membutuhkan API key gratis dari https://gnews.io/
 * Simpan di .env sebagai GNEWS_API_KEY.
 *
 * Free tier: 100 request/hari, max 10 artikel per request.
 * Kita simpan artikel ke database (cache) supaya tidak
 * membuang quota saat page di-refresh.
 */
class GNewsService
{
    protected string $baseUrl = 'https://gnews.io/api/v4';

    protected string $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.gnews.key', '');
    }

    /**
     * Fetch berita terkait rantai pasok/ekonomi untuk satu negara,
     * simpan ke database, lalu jalankan sentiment analysis otomatis.
     *
     * Query dirancang untuk dapat berita yang relevan dengan
     * konteks supply chain: ekonomi, logistik, perdagangan.
     */
    public function fetchAndCacheForCountry(
        Country $country,
        SentimentAnalysisService $sentiment
    ): array {
        if (empty($this->apiKey)) {
            Log::warning('GNewsService: GNEWS_API_KEY belum diset di .env');
            return [];
        }

        $query = "\"{$country->name}\" AND (economy OR trade OR logistics OR export OR import OR supply)";

        try {
            $response = Http::timeout(15)->get("{$this->baseUrl}/search", [
                'q'        => $query,
                'lang'     => 'en',
                'max'      => 10,
                'apikey'   => $this->apiKey,
                'sortby'   => 'publishedAt',
            ]);

            if (! $response->successful()) {
                Log::warning('GNewsService: response gagal', [
                    'country' => $country->name,
                    'status'  => $response->status(),
                ]);
                return [];
            }

            $articles = $response->json('articles', []);
            $saved    = [];

            foreach ($articles as $article) {
                // Skip kalau judul kosong
                if (empty($article['title'])) {
                    continue;
                }

                // Hindari duplikat berdasarkan URL
                $exists = NewsArticle::where('url', $article['url'] ?? '')->exists();
                if ($exists) {
                    continue;
                }

                $newsArticle = NewsArticle::create([
                    'country_id'   => $country->id,
                    'title'        => $article['title'],
                    'description'  => $article['description'] ?? null,
                    'source'       => $article['source']['name'] ?? null,
                    'url'          => $article['url'] ?? null,
                    'published_at' => isset($article['publishedAt'])
                        ? \Carbon\Carbon::parse($article['publishedAt'])
                        : null,
                    'positive_score' => 0,
                    'negative_score' => 0,
                    'sentiment'      => 'Neutral',
                ]);

                // Langsung analisis sentimen setelah disimpan
                $result  = $sentiment->analyze($newsArticle);
                $saved[] = array_merge(
                    ['title' => $newsArticle->title],
                    $result
                );
            }

            return $saved;
        } catch (\Exception $e) {
            Log::warning('GNewsService::fetchAndCacheForCountry gagal', [
                'country' => $country->name,
                'error'   => $e->getMessage(),
            ]);
            return [];
        }
    }
}