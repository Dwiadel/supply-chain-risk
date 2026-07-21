<?php

namespace App\Services;

use App\Models\Country;
use App\Models\NewsArticle;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GNewsService
{
    protected string $baseUrl = 'https://gnews.io/api/v4';
    protected string $apiKey;

    /**
     * Kategori berita sesuai panduan dosen:
     * Logistics, Trade, Shipping, Economy
     */
    protected array $categories = [
        'logistics' => [
            'label' => 'Logistics',
            'icon'  => 'bi-truck',
            'color' => '#0d6efd',
            'query' => 'logistics OR warehouse OR freight OR cargo OR delivery OR supply chain',
        ],
        'trade' => [
            'label' => 'Trade',
            'icon'  => 'bi-arrow-left-right',
            'color' => '#25b574',
            'query' => 'trade OR export OR import OR tariff OR FTA OR bilateral trade',
        ],
        'shipping' => [
            'label' => 'Shipping',
            'icon'  => 'bi-ship',
            'color' => '#0dcaf0',
            'query' => 'shipping OR port OR vessel OR maritime OR container ship OR ocean freight',
        ],
        'economy' => [
            'label' => 'Economy',
            'icon'  => 'bi-graph-up-arrow',
            'color' => '#ffc107',
            'query' => 'economy OR GDP OR inflation OR investment OR economic growth OR recession',
        ],
    ];

    public function __construct()
    {
        $this->apiKey = config('services.gnews.key', '');
    }

    public function getCategories(): array
    {
        return $this->categories;
    }

    /**
     * Fetch berita untuk semua kategori (Logistics, Trade, Shipping, Economy)
     * berdasarkan negara yang dipilih.
     */
    public function fetchAndCacheForCountry(
        Country $country,
        SentimentAnalysisService $sentiment
    ): array {
        if (empty($this->apiKey)) {
            Log::warning('GNewsService: GNEWS_API_KEY belum diset di .env');
            return [];
        }

        $saved = [];

        foreach ($this->categories as $categoryKey => $cat) {
            // Query spesifik: nama negara + topik kategori
            $query = '"' . $country->name . '" AND (' . $cat['query'] . ')';

            try {
                $response = Http::timeout(15)->get("{$this->baseUrl}/search", [
                    'q'      => $query,
                    'lang'   => 'en',
                    'max'    => 5, // 5 artikel per kategori = 20 total
                    'apikey' => $this->apiKey,
                    'sortby' => 'publishedAt',
                ]);

                if (! $response->successful()) {
                    Log::warning('GNewsService: response gagal', [
                        'country'  => $country->name,
                        'category' => $categoryKey,
                        'status'   => $response->status(),
                    ]);
                    continue;
                }

                $articles = $response->json('articles', []);

                foreach ($articles as $article) {
                    if (empty($article['title'])) continue;

                    // Skip duplikat berdasarkan URL
                    $exists = NewsArticle::where('url', $article['url'] ?? '')->exists();
                    if ($exists) continue;

                    $newsArticle = NewsArticle::create([
                        'country_id'     => $country->id,
                        'title'          => $article['title'],
                        'description'    => $article['description'] ?? null,
                        'source'         => $article['source']['name'] ?? null,
                        'url'            => $article['url'] ?? null,
                        'published_at'   => isset($article['publishedAt'])
                            ? \Carbon\Carbon::parse($article['publishedAt'])
                            : null,
                        'positive_score' => 0,
                        'negative_score' => 0,
                        'sentiment'      => 'Neutral',
                        // Simpan kategori di field source sebagai prefix
                        // supaya tidak perlu alter table
                    ]);

                    // Analisis sentimen
                    $result  = $sentiment->analyze($newsArticle);

                    // Update source dengan category tag
                    $newsArticle->update([
                        'source' => '[' . strtoupper($categoryKey) . '] ' . ($article['source']['name'] ?? '—'),
                    ]);

                    $saved[] = array_merge(
                        ['title' => $newsArticle->title, 'category' => $categoryKey],
                        $result
                    );
                }

                // Delay kecil antar request supaya tidak kena rate limit GNews
                usleep(300000); // 0.3 detik

            } catch (\Exception $e) {
                Log::warning('GNewsService::fetchAndCacheForCountry gagal', [
                    'country'  => $country->name,
                    'category' => $categoryKey,
                    'error'    => $e->getMessage(),
                ]);
            }
        }

        return $saved;
    }
}