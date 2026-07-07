<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    public function run(): void
    {
        $countries = [
            ['cca2' => 'DE', 'cca3' => 'DEU', 'name' => 'Germany', 'official_name' => 'Federal Republic of Germany', 'region' => 'Europe', 'subregion' => 'Western Europe', 'capital' => 'Berlin', 'currency_code' => 'EUR', 'currency_name' => 'Euro', 'latitude' => 51.1657, 'longitude' => 10.4515],
            ['cca2' => 'CN', 'cca3' => 'CHN', 'name' => 'China', 'official_name' => "People's Republic of China", 'region' => 'Asia', 'subregion' => 'Eastern Asia', 'capital' => 'Beijing', 'currency_code' => 'CNY', 'currency_name' => 'Chinese Yuan', 'latitude' => 35.8617, 'longitude' => 104.1954],
            ['cca2' => 'ID', 'cca3' => 'IDN', 'name' => 'Indonesia', 'official_name' => 'Republic of Indonesia', 'region' => 'Asia', 'subregion' => 'South-Eastern Asia', 'capital' => 'Jakarta', 'currency_code' => 'IDR', 'currency_name' => 'Indonesian Rupiah', 'latitude' => -0.7893, 'longitude' => 113.9213],
            ['cca2' => 'AU', 'cca3' => 'AUS', 'name' => 'Australia', 'official_name' => 'Commonwealth of Australia', 'region' => 'Oceania', 'subregion' => 'Australia and New Zealand', 'capital' => 'Canberra', 'currency_code' => 'AUD', 'currency_name' => 'Australian Dollar', 'latitude' => -25.2744, 'longitude' => 133.7751],
            ['cca2' => 'US', 'cca3' => 'USA', 'name' => 'United States', 'official_name' => 'United States of America', 'region' => 'Americas', 'subregion' => 'North America', 'capital' => 'Washington, D.C.', 'currency_code' => 'USD', 'currency_name' => 'United States Dollar', 'latitude' => 37.0902, 'longitude' => -95.7129],
            ['cca2' => 'JP', 'cca3' => 'JPN', 'name' => 'Japan', 'official_name' => 'Japan', 'region' => 'Asia', 'subregion' => 'Eastern Asia', 'capital' => 'Tokyo', 'currency_code' => 'JPY', 'currency_name' => 'Japanese Yen', 'latitude' => 36.2048, 'longitude' => 138.2529],
            ['cca2' => 'SG', 'cca3' => 'SGP', 'name' => 'Singapore', 'official_name' => 'Republic of Singapore', 'region' => 'Asia', 'subregion' => 'South-Eastern Asia', 'capital' => 'Singapore', 'currency_code' => 'SGD', 'currency_name' => 'Singapore Dollar', 'latitude' => 1.3521, 'longitude' => 103.8198],
            ['cca2' => 'GB', 'cca3' => 'GBR', 'name' => 'United Kingdom', 'official_name' => 'United Kingdom of Great Britain and Northern Ireland', 'region' => 'Europe', 'subregion' => 'Northern Europe', 'capital' => 'London', 'currency_code' => 'GBP', 'currency_name' => 'British Pound', 'latitude' => 55.3781, 'longitude' => -3.4360],
            ['cca2' => 'BR', 'cca3' => 'BRA', 'name' => 'Brazil', 'official_name' => 'Federative Republic of Brazil', 'region' => 'Americas', 'subregion' => 'South America', 'capital' => 'Brasília', 'currency_code' => 'BRL', 'currency_name' => 'Brazilian Real', 'latitude' => -14.2350, 'longitude' => -51.9253],
            ['cca2' => 'IN', 'cca3' => 'IND', 'name' => 'India', 'official_name' => 'Republic of India', 'region' => 'Asia', 'subregion' => 'Southern Asia', 'capital' => 'New Delhi', 'currency_code' => 'INR', 'currency_name' => 'Indian Rupee', 'latitude' => 20.5937, 'longitude' => 78.9629],
        ];

        foreach ($countries as $country) {
            Country::updateOrCreate(['cca2' => $country['cca2']], $country);
        }
    }
}