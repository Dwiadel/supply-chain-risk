<?php

namespace App\Services;

use App\Models\NewsArticle;

/**
 * Sentiment Analysis berbasis Lexicon (Kamus Kata).
 *
 * METODOLOGI:
 * Setiap kata dalam teks berita dicek apakah masuk ke kamus kata
 * positif atau negatif. Jumlah kata positif vs negatif dibandingkan
 * untuk menentukan sentimen keseluruhan artikel.
 *
 * Pendekatan ini disebut "Lexicon-Based Sentiment Analysis" —
 * sederhana, transparan, dan mudah dijelaskan ke dosen karena
 * logikanya langsung bisa ditelusuri kata per kata.
 *
 * Kamus kata dirancang khusus untuk konteks berita ekonomi,
 * logistik, perdagangan, dan geopolitik (sesuai use case project).
 */
class SentimentAnalysisService
{
    /**
     * Kamus kata POSITIF — kata-kata yang mencerminkan kondisi
     * baik untuk rantai pasok & ekonomi suatu negara.
     * 100+ kata mencakup konteks: ekonomi, perdagangan, logistik,
     * geopolitik, dan kondisi bisnis umum.
     */
    protected array $positiveWords = [
        // Pertumbuhan & Perkembangan Ekonomi
        'growth', 'grow', 'growing', 'grew', 'expansion', 'expand',
        'increase', 'increased', 'increasing', 'surge', 'surged', 'surging',
        'rise', 'risen', 'rising', 'boost', 'boosted', 'boosting',
        'gain', 'gained', 'gains', 'improvement', 'improve', 'improved',
        'recovery', 'recover', 'recovered', 'rebound', 'rebounded',
        'upturn', 'upswing', 'acceleration', 'accelerate',

        // Kinerja & Hasil Positif
        'profit', 'profitable', 'profitability', 'revenue', 'surplus',
        'success', 'successful', 'achievement', 'achieve', 'achieved',
        'record', 'peak', 'high', 'strong', 'strength', 'robust',
        'excellent', 'outstanding', 'remarkable', 'impressive',
        'beat', 'exceeded', 'outperform', 'outperformed',

        // Stabilitas & Keamanan
        'stable', 'stability', 'stabilize', 'stabilized', 'steady',
        'secure', 'security', 'safe', 'safety', 'reliable', 'reliability',
        'consistent', 'consistency', 'resilient', 'resilience',
        'confidence', 'confident', 'trust', 'trusted',

        // Perdagangan & Investasi
        'trade', 'export', 'exports', 'import', 'imports', 'deal',
        'agreement', 'partnership', 'cooperation', 'collaboration',
        'investment', 'invest', 'invested', 'investor', 'fund',
        'opportunity', 'opportunities', 'potential', 'promising',
        'demand', 'supply', 'open', 'opening', 'liberalization',

        // Logistik & Infrastruktur
        'efficient', 'efficiency', 'optimize', 'optimized', 'streamline',
        'upgrade', 'upgraded', 'modernize', 'modernized', 'innovation',
        'innovative', 'technology', 'digital', 'connected', 'connectivity',
        'capacity', 'infrastructure', 'development', 'develop',

        // Kebijakan & Regulasi Positif
        'reform', 'reformed', 'deregulation', 'liberalize', 'facilitate',
        'support', 'supported', 'aid', 'assistance', 'incentive',
        'approve', 'approved', 'ratify', 'ratified', 'sign', 'signed',
        'resolve', 'resolved', 'solution', 'solved',

        // Kondisi Umum Positif
        'peace', 'peaceful', 'calm', 'positive', 'optimistic', 'optimism',
        'benefit', 'beneficial', 'advantage', 'favorable', 'good',
        'better', 'best', 'healthy', 'thriving', 'flourishing',
        'boom', 'booming', 'prosper', 'prosperity', 'prosperous',
    ];

    /**
     * Kamus kata NEGATIF — kata-kata yang mencerminkan kondisi
     * buruk/berisiko untuk rantai pasok & ekonomi suatu negara.
     */
    protected array $negativeWords = [
        // Penurunan & Kemerosotan Ekonomi
        'decline', 'declined', 'declining', 'decrease', 'decreased',
        'fall', 'fallen', 'falling', 'drop', 'dropped', 'dropping',
        'plunge', 'plunged', 'plunging', 'collapse', 'collapsed',
        'shrink', 'shrinking', 'contraction', 'contract', 'contracted',
        'slowdown', 'slow', 'slowing', 'slowed', 'downturn', 'downfall',
        'recession', 'depression', 'stagnation', 'stagnate',

        // Krisis & Masalah Ekonomi
        'crisis', 'crises', 'crash', 'default', 'bankrupt', 'bankruptcy',
        'debt', 'deficit', 'loss', 'losses', 'deficit', 'shortfall',
        'inflation', 'hyperinflation', 'stagflation', 'deflation',
        'unemployment', 'jobless', 'layoff', 'layoffs', 'redundancy',

        // Konflik & Ketidakstabilan Politik
        'war', 'warfare', 'conflict', 'conflicts', 'tension', 'tensions',
        'dispute', 'disputed', 'clash', 'clashes', 'violence', 'violent',
        'attack', 'attacked', 'strike', 'coup', 'uprising', 'protest',
        'sanction', 'sanctions', 'embargo', 'blockade', 'restriction',
        'ban', 'banned', 'prohibited', 'imposed', 'penalty', 'penalize',

        // Gangguan Logistik & Rantai Pasok
        'delay', 'delayed', 'delays', 'disruption', 'disrupted', 'disrupt',
        'shortage', 'shortages', 'scarcity', 'bottleneck', 'backlog',
        'congestion', 'congested', 'halt', 'halted', 'suspend', 'suspended',
        'shutdown', 'closed', 'closure', 'blockage', 'blocked',
        'strike', 'strikes', 'walkout', 'stoppage',

        // Bencana & Kondisi Ekstrem
        'disaster', 'catastrophe', 'catastrophic', 'devastate', 'devastated',
        'flood', 'flooding', 'drought', 'earthquake', 'typhoon', 'hurricane',
        'storm', 'storms', 'damage', 'damaged', 'destroy', 'destroyed',
        'collapse', 'accident', 'explosion', 'fire', 'outbreak',

        // Ketidakpastian & Risiko
        'risk', 'risky', 'uncertain', 'uncertainty', 'unstable', 'instability',
        'volatile', 'volatility', 'threat', 'threatened', 'danger', 'dangerous',
        'concern', 'concerns', 'worry', 'worried', 'fear', 'feared',
        'warning', 'warn', 'alarming', 'alarmed', 'crisis',

        // Negatif Umum
        'bad', 'worse', 'worst', 'poor', 'weak', 'weakness', 'fail',
        'failed', 'failure', 'problem', 'problems', 'issue', 'issues',
        'challenge', 'difficult', 'difficulty', 'obstacle', 'barrier',
        'negative', 'pessimistic', 'pessimism', 'gloomy', 'bleak',
        'corruption', 'corrupt', 'fraud', 'scandal', 'controversy',
    ];

    /**
     * Analisis sentimen satu artikel berita dari judulnya (title)
     * dan deskripsinya (description), lalu simpan hasilnya ke database.
     *
     * @return array{positive: int, negative: int, sentiment: string}
     */
    public function analyze(NewsArticle $article): array
    {
        $text = strtolower($article->title . ' ' . ($article->description ?? ''));

        // Tokenisasi: pecah teks jadi array kata-kata individual
        // preg_split memisahkan berdasarkan apapun yang bukan huruf/angka
        $words = preg_split('/[^a-z0-9]+/', $text, -1, PREG_SPLIT_NO_EMPTY);

        $positiveScore = 0;
        $negativeScore = 0;

        foreach ($words as $word) {
            if (in_array($word, $this->positiveWords)) {
                $positiveScore++;
            }
            if (in_array($word, $this->negativeWords)) {
                $negativeScore++;
            }
        }

        // Tentukan sentimen: kalau skor sama → Neutral
        $sentiment = match (true) {
            $positiveScore > $negativeScore => 'Positive',
            $negativeScore > $positiveScore => 'Negative',
            default                         => 'Neutral',
        };

        // Simpan hasil analisis ke kolom artikel
        $article->update([
            'positive_score' => $positiveScore,
            'negative_score' => $negativeScore,
            'sentiment'      => $sentiment,
        ]);

        return [
            'positive'  => $positiveScore,
            'negative'  => $negativeScore,
            'sentiment' => $sentiment,
        ];
    }

    /**
     * Analisis batch: proses semua artikel yang belum dianalisis
     * (positive_score & negative_score masih 0 dan sentiment Neutral)
     * untuk satu negara tertentu.
     */
    public function analyzePendingForCountry(int $countryId): int
    {
        $pending = NewsArticle::where('country_id', $countryId)
            ->where('positive_score', 0)
            ->where('negative_score', 0)
            ->get();

        foreach ($pending as $article) {
            $this->analyze($article);
        }

        return $pending->count();
    }

    /**
     * Hitung ringkasan statistik sentiment untuk semua artikel
     * yang sudah dianalisis milik satu negara.
     * Dipakai untuk tampilan di dashboard dan perhitungan news score.
     */
    public function getSummaryForCountry(int $countryId): array
    {
        $articles = NewsArticle::where('country_id', $countryId)->get();

        if ($articles->isEmpty()) {
            return [
                'total'    => 0,
                'positive' => 0,
                'neutral'  => 0,
                'negative' => 0,
                'positive_pct' => 0,
                'neutral_pct'  => 0,
                'negative_pct' => 0,
            ];
        }

        $total    = $articles->count();
        $positive = $articles->where('sentiment', 'Positive')->count();
        $neutral  = $articles->where('sentiment', 'Neutral')->count();
        $negative = $articles->where('sentiment', 'Negative')->count();

        return [
            'total'        => $total,
            'positive'     => $positive,
            'neutral'      => $neutral,
            'negative'     => $negative,
            'positive_pct' => round(($positive / $total) * 100, 1),
            'neutral_pct'  => round(($neutral  / $total) * 100, 1),
            'negative_pct' => round(($negative / $total) * 100, 1),
        ];
    }
}