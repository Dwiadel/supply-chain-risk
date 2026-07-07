<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WeatherCache extends Model
{
    use HasFactory;

    protected $fillable = [
        'country_id', 'temperature', 'precipitation', 'wind_speed',
        'weather_code', 'weather_description', 'storm_risk_score', 'fetched_at',
    ];

    protected $casts = [
        'fetched_at' => 'datetime',
    ];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function isStale(): bool
    {
        return $this->fetched_at->diffInMinutes(now()) > 60;
    }
}