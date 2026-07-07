<?php

namespace App\Services;

use App\Models\Country;
use App\Models\CurrencyRate;
use App\Models\RiskScore;

/**
 * Risk Scoring Engine — algoritma weighted scoring dengan normalisasi.
 *
 * METODOLOGI:
 * Setiap komponen dinormalisasi ke skala 0-100 (semakin tinggi = semakin
 * berisiko), baru digabung dengan bobot (weighted sum). Pendekatan ini
 * mirip prinsip SAW (Simple Additive Weighting) dari mata kuliah SPK.
 *
 * Formula:
 *   Total = (Weather x 0.30) + (Inflation x 0.25) + (Currency x 0.20) + (News x 0.25)
 *
 * Bobot:
 * - Cuaca (30%)   : dampak paling langsung terhadap pengiriman fisik
 * - Inflasi (25%) : stabilitas ekonomi makro jangka menengah
 * - Berita (25%)  : risiko geopolitik & sentimen pasar terkini
 * - Kurs (20%)    : volatilitas finansial, biasanya paling mudah di-hedge
 *
 * Risk Level:
 * - Low    : 0  - 33
 * - Medium : 34 - 66
 * - High   : 67 - 100
 */
class RiskScoringService
{
    protected const WEIGHT_WEATHER   = 0.30;
    protected const WEIGHT_INFLATION = 0.25;
    protected const WEIGHT_CURRENCY  = 0.20;
    protected const WEIGHT_NEWS      = 0.25;

    /** Inflasi "ideal" di kisaran 2% (target umum bank sentral dunia) */
    protected const IDEAL_INFLATION  = 2.0;

    /**
     * Hitung & simpan risk score lengkap untuk satu negara.
     * Setiap kalkulasi disimpan sebagai baris BARU (bukan update),
     * supaya history risk score bisa dibuat grafik tren dari waktu ke waktu.
     */
    public function calculateForCountry(Country $country): RiskScore
    {
        $weatherScore   = $this->calculateWeatherScore($country);
        $inflationScore = $this->calculateInflationScore($country);
        $currencyScore  = $this->calculateCurrencyScore($country);
        $newsScore      = $this->calculateNewsScore($country);

        $totalScore = round(
            ($weatherScore   * self::WEIGHT_WEATHER)   +
            ($inflationScore * self::WEIGHT_INFLATION)  +
            ($currencyScore  * self::WEIGHT_CURRENCY)   +
            ($newsScore      * self::WEIGHT_NEWS),
            2
        );

        return RiskScore::create([
            'country_id'           => $country->id,
            'weather_score'        => $weatherScore,
            'inflation_score'      => $inflationScore,
            'currency_score'       => $currencyScore,
            'news_sentiment_score' => $newsScore,
            'total_score'          => $totalScore,
            'risk_level'           => $this->determineRiskLevel($totalScore),
            'calculated_at'        => now(),
        ]);
    }

    /**
     * Breakdown detail semua komponen tanpa menyimpan ke database.
     * Dipakai untuk keperluan tampilan & penjelasan di frontend dashboard.
     */
    public function getBreakdown(Country $country): array
    {
        $weatherScore   = $this->calculateWeatherScore($country);
        $inflationScore = $this->calculateInflationScore($country);
        $currencyScore  = $this->calculateCurrencyScore($country);
        $newsScore      = $this->calculateNewsScore($country);

        $totalScore = round(
            ($weatherScore   * self::WEIGHT_WEATHER)   +
            ($inflationScore * self::WEIGHT_INFLATION)  +
            ($currencyScore  * self::WEIGHT_CURRENCY)   +
            ($newsScore      * self::WEIGHT_NEWS),
            2
        );

        return [
            'components' => [
                'weather' => [
                    'raw_score'    => $weatherScore,
                    'weight'       => self::WEIGHT_WEATHER,
                    'contribution' => round($weatherScore * self::WEIGHT_WEATHER, 2),
                    'label'        => 'Risiko Cuaca & Badai',
                ],
                'inflation' => [
                    'raw_score'    => $inflationScore,
                    'weight'       => self::WEIGHT_INFLATION,
                    'contribution' => round($inflationScore * self::WEIGHT_INFLATION, 2),
                    'label'        => 'Risiko Inflasi',
                ],
                'currency' => [
                    'raw_score'    => $currencyScore,
                    'weight'       => self::WEIGHT_CURRENCY,
                    'contribution' => round($currencyScore * self::WEIGHT_CURRENCY, 2),
                    'label'        => 'Volatilitas Kurs',
                ],
                'news' => [
                    'raw_score'    => $newsScore,
                    'weight'       => self::WEIGHT_NEWS,
                    'contribution' => round($newsScore * self::WEIGHT_NEWS, 2),
                    'label'        => 'Sentimen Berita',
                ],
            ],
            'total_score' => $totalScore,
            'risk_level'  => $this->determineRiskLevel($totalScore),
            'formula'     => 'Total = (Weather×0.30) + (Inflation×0.25) + (Currency×0.20) + (News×0.25)',
        ];
    }

    /**
     * KOMPONEN 1: Skor Cuaca (0-100)
     *
     * Langsung memakai storm_risk_score yang sudah dihitung OpenMeteoService
     * dari kombinasi kecepatan angin & curah hujan.
     * Default 50 (netral) kalau data tidak tersedia.
     */
    protected function calculateWeatherScore(Country $country): float
    {
        $weather = $country->latestWeather();
        return $weather ? (float) $weather->storm_risk_score : 50.0;
    }

    /**
     * KOMPONEN 2: Skor Inflasi (0-100)
     *
     * Inflasi sehat ada di kisaran 2% (IDEAL_INFLATION).
     * Semakin JAUH dari angka ideal — baik karena terlalu tinggi
     * (hiperinflasi) MAUPUN terlalu rendah/negatif (deflasi/resesi)
     * — sama-sama berisiko bagi rantai pasok.
     *
     * Jarak 15 poin persen dari ideal dianggap risiko maksimum (100).
     * Contoh: inflasi 17% → jarak 15 → skor 100 (High Risk)
     *         inflasi 2%  → jarak 0  → skor 0   (Low Risk)
     *         inflasi -5% → jarak 7  → skor 47  (Medium Risk)
     */
    protected function calculateInflationScore(Country $country): float
    {
        $indicator = $country->latestIndicator();

        if (! $indicator || $indicator->inflation_rate === null) {
            return 50.0;
        }

        $distance = abs((float) $indicator->inflation_rate - self::IDEAL_INFLATION);
        $score    = ($distance / 15) * 100;

        return round(min(max($score, 0), 100), 2);
    }

    /**
     * KOMPONEN 3: Skor Kurs/Currency (0-100)
     *
     * Yang dinilai BUKAN nilai tukarnya sendiri, melainkan seberapa
     * BESAR perubahan (volatilitas) hariannya (change_percent).
     * Perubahan besar dalam waktu singkat = ketidakstabilan finansial
     * yang menyulitkan perencanaan biaya impor/ekspor.
     *
     * Perubahan harian >= 5% dianggap risiko maksimum (100).
     * Pergerakan normal major currency biasanya < 1% per hari.
     */
    protected function calculateCurrencyScore(Country $country): float
    {
        if (! $country->currency_code) {
            return 50.0;
        }

        $rate = CurrencyRate::where('base_currency', 'USD')
            ->where('target_currency', $country->currency_code)
            ->orderBy('rate_date', 'desc')
            ->first();

        if (! $rate || $rate->change_percent === null) {
            return 50.0;
        }

        $absoluteChange = abs((float) $rate->change_percent);
        $score          = ($absoluteChange / 5) * 100;

        return round(min(max($score, 0), 100), 2);
    }

    /**
     * KOMPONEN 4: Skor Sentiment Berita (0-100)
     *
     * Dihitung dari rasio berita bersentimen NEGATIF terhadap total
     * berita yang dianalisis (lexicon-based sentiment dari Tahap 4).
     * Semakin besar proporsi berita negatif = semakin tinggi risiko.
     *
     * Contoh: 15 dari 20 berita negatif → skor 75 (High Risk)
     *         5 dari 20 berita negatif  → skor 25 (Low Risk)
     *
     * Default 50 (netral) kalau belum ada berita yang di-cache
     * (GNews API belum dikonfigurasi atau Tahap 4 belum dijalankan).
     */
    protected function calculateNewsScore(Country $country): float
    {
        $articles = $country->newsArticles()
            ->latest('published_at')
            ->limit(20)
            ->get();

        if ($articles->isEmpty()) {
            return 50.0;
        }

        $negativeCount = $articles->where('sentiment', 'Negative')->count();
        $score         = ($negativeCount / $articles->count()) * 100;

        return round($score, 2);
    }

    /**
     * Tentukan kategori risiko dari skor total (0-100).
     * Dibagi rata 3 kategori untuk kemudahan interpretasi.
     */
    protected function determineRiskLevel(float $totalScore): string
    {
        return match (true) {
            $totalScore <= 33 => 'Low',
            $totalScore <= 66 => 'Medium',
            default           => 'High',
        };
    }
}