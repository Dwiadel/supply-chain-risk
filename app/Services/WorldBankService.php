<?php

namespace App\Services;

use App\Models\Country;
use App\Models\CountryIndicator;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WorldBankService
{
    protected string $baseUrl = 'https://api.worldbank.org/v2';

    protected array $indicators = [
        'gdp' => 'NY.GDP.MKTP.CD',
        'inflation_rate' => 'FP.CPI.TOTL.ZG',
        'population' => 'SP.POP.TOTL',
        'exports_value' => 'NE.EXP.GNFS.CD',
        'imports_value' => 'NE.IMP.GNFS.CD',
    ];

    public function fetchAndCache(Country $country): ?CountryIndicator
    {
        if (! $country->cca3) {
            Log::warning('WorldBankService: negara tidak punya kode cca3', ['country' => $country->name]);

            return $country->latestIndicator();
        }

        $values = [];
        foreach ($this->indicators as $key => $code) {
            $result = $this->fetchIndicator($country->cca3, $code);
            if ($result !== null) {
                $values[$key] = $result['value'];
                $values['year'] = $result['year'];
            }
        }

        if (empty($values) || ! isset($values['year'])) {
            return $country->latestIndicator();
        }

        return CountryIndicator::updateOrCreate(
            ['country_id' => $country->id, 'year' => $values['year']],
            array_merge(['country_id' => $country->id], $values)
        );
    }

    protected function fetchIndicator(string $cca3, string $indicatorCode): ?array
    {
        try {
            $response = Http::timeout(10)->get(
                "{$this->baseUrl}/country/{$cca3}/indicator/{$indicatorCode}",
                ['format' => 'json', 'per_page' => 10, 'mrnev' => 5]
            );

            if (! $response->successful()) {
                return null;
            }

            $data = $response->json();
            $entries = $data[1] ?? null;

            if (! is_array($entries)) {
                return null;
            }

            foreach ($entries as $entry) {
                if (isset($entry['value']) && $entry['value'] !== null) {
                    return [
                        'year' => (int) $entry['date'],
                        'value' => (float) $entry['value'],
                    ];
                }
            }

            return null;
        } catch (\Exception $e) {
            Log::warning('WorldBankService::fetchIndicator gagal', [
                'cca3' => $cca3,
                'indicator' => $indicatorCode,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }
}
