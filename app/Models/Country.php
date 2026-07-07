<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Country extends Model
{
    use HasFactory;

    protected $fillable = [
        'cca2', 'cca3', 'name', 'official_name', 'region', 'subregion',
        'capital', 'currency_code', 'currency_name', 'latitude', 'longitude', 'flag_url',
    ];

    public function indicators(): HasMany
    {
        return $this->hasMany(CountryIndicator::class);
    }

    public function weatherCaches(): HasMany
    {
        return $this->hasMany(WeatherCache::class);
    }

    public function riskScores(): HasMany
    {
        return $this->hasMany(RiskScore::class);
    }

    public function newsArticles(): HasMany
    {
        return $this->hasMany(NewsArticle::class);
    }

    public function watchlists(): HasMany
    {
        return $this->hasMany(Watchlist::class);
    }

    public function latestIndicator()
    {
        return $this->indicators()->orderBy('year', 'desc')->first();
    }

    public function latestWeather()
    {
        return $this->weatherCaches()->orderBy('fetched_at', 'desc')->first();
    }

    public function latestRiskScore()
    {
        return $this->riskScores()->orderBy('calculated_at', 'desc')->first();
    }
}