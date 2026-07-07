<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CountryIndicator extends Model
{
    use HasFactory;

    protected $fillable = [
        'country_id', 'year', 'gdp', 'inflation_rate', 'population', 'exports_value', 'imports_value',
    ];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }
}