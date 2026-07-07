<?php

namespace Database\Seeders;

use App\Models\Port;
use Illuminate\Database\Seeder;

class PortSeeder extends Seeder
{
    public function run(): void
    {
        $ports = [
            ['name' => 'Port of Shanghai', 'country_name' => 'China', 'cca2' => 'CN', 'latitude' => 31.2304, 'longitude' => 121.4737, 'size_category' => 'Large'],
            ['name' => 'Port of Singapore', 'country_name' => 'Singapore', 'cca2' => 'SG', 'latitude' => 1.2655, 'longitude' => 103.8201, 'size_category' => 'Large'],
            ['name' => 'Port of Ningbo-Zhoushan', 'country_name' => 'China', 'cca2' => 'CN', 'latitude' => 29.8683, 'longitude' => 121.5440, 'size_category' => 'Large'],
            ['name' => 'Port of Shenzhen', 'country_name' => 'China', 'cca2' => 'CN', 'latitude' => 22.5431, 'longitude' => 114.0579, 'size_category' => 'Large'],
            ['name' => 'Port of Guangzhou', 'country_name' => 'China', 'cca2' => 'CN', 'latitude' => 23.1167, 'longitude' => 113.2500, 'size_category' => 'Large'],
            ['name' => 'Port of Busan', 'country_name' => 'South Korea', 'cca2' => 'KR', 'latitude' => 35.1796, 'longitude' => 129.0756, 'size_category' => 'Large'],
            ['name' => 'Port of Hong Kong', 'country_name' => 'Hong Kong', 'cca2' => 'HK', 'latitude' => 22.3193, 'longitude' => 114.1694, 'size_category' => 'Large'],
            ['name' => 'Port of Qingdao', 'country_name' => 'China', 'cca2' => 'CN', 'latitude' => 36.0671, 'longitude' => 120.3826, 'size_category' => 'Large'],
            ['name' => 'Port of Tianjin', 'country_name' => 'China', 'cca2' => 'CN', 'latitude' => 39.1042, 'longitude' => 117.2000, 'size_category' => 'Large'],
            ['name' => 'Port of Rotterdam', 'country_name' => 'Netherlands', 'cca2' => 'NL', 'latitude' => 51.9225, 'longitude' => 4.4792, 'size_category' => 'Large'],
            ['name' => 'Port of Antwerp', 'country_name' => 'Belgium', 'cca2' => 'BE', 'latitude' => 51.2194, 'longitude' => 4.4025, 'size_category' => 'Large'],
            ['name' => 'Port of Hamburg', 'country_name' => 'Germany', 'cca2' => 'DE', 'latitude' => 53.5511, 'longitude' => 9.9937, 'size_category' => 'Medium'],
            ['name' => 'Port of Bremerhaven', 'country_name' => 'Germany', 'cca2' => 'DE', 'latitude' => 53.5396, 'longitude' => 8.5810, 'size_category' => 'Medium'],
            ['name' => 'Port of Valencia', 'country_name' => 'Spain', 'cca2' => 'ES', 'latitude' => 39.4699, 'longitude' => -0.3763, 'size_category' => 'Medium'],
            ['name' => 'Port of Algeciras', 'country_name' => 'Spain', 'cca2' => 'ES', 'latitude' => 36.1408, 'longitude' => -5.4565, 'size_category' => 'Medium'],
            ['name' => 'Port of Piraeus', 'country_name' => 'Greece', 'cca2' => 'GR', 'latitude' => 37.9475, 'longitude' => 23.6364, 'size_category' => 'Medium'],
            ['name' => 'Port of Felixstowe', 'country_name' => 'United Kingdom', 'cca2' => 'GB', 'latitude' => 51.9540, 'longitude' => 1.3510, 'size_category' => 'Medium'],
            ['name' => 'Port of Le Havre', 'country_name' => 'France', 'cca2' => 'FR', 'latitude' => 49.4944, 'longitude' => 0.1079, 'size_category' => 'Medium'],
            ['name' => 'Port of Gioia Tauro', 'country_name' => 'Italy', 'cca2' => 'IT', 'latitude' => 38.4244, 'longitude' => 15.8997, 'size_category' => 'Medium'],
            ['name' => 'Port of Los Angeles', 'country_name' => 'United States', 'cca2' => 'US', 'latitude' => 33.7395, 'longitude' => -118.2610, 'size_category' => 'Large'],
            ['name' => 'Port of Long Beach', 'country_name' => 'United States', 'cca2' => 'US', 'latitude' => 33.7544, 'longitude' => -118.2165, 'size_category' => 'Large'],
            ['name' => 'Port of New York and New Jersey', 'country_name' => 'United States', 'cca2' => 'US', 'latitude' => 40.6700, 'longitude' => -74.1100, 'size_category' => 'Large'],
            ['name' => 'Port of Savannah', 'country_name' => 'United States', 'cca2' => 'US', 'latitude' => 32.0809, 'longitude' => -81.0912, 'size_category' => 'Medium'],
            ['name' => 'Port of Houston', 'country_name' => 'United States', 'cca2' => 'US', 'latitude' => 29.7355, 'longitude' => -95.2700, 'size_category' => 'Medium'],
            ['name' => 'Port of Vancouver', 'country_name' => 'Canada', 'cca2' => 'CA', 'latitude' => 49.2880, 'longitude' => -123.1110, 'size_category' => 'Medium'],
            ['name' => 'Port of Santos', 'country_name' => 'Brazil', 'cca2' => 'BR', 'latitude' => -23.9608, 'longitude' => -46.3339, 'size_category' => 'Medium'],
            ['name' => 'Port of Manzanillo', 'country_name' => 'Mexico', 'cca2' => 'MX', 'latitude' => 19.0531, 'longitude' => -104.3158, 'size_category' => 'Medium'],
            ['name' => 'Port of Jawaharlal Nehru', 'country_name' => 'India', 'cca2' => 'IN', 'latitude' => 18.9490, 'longitude' => 72.9525, 'size_category' => 'Medium'],
            ['name' => 'Port of Mundra', 'country_name' => 'India', 'cca2' => 'IN', 'latitude' => 22.8390, 'longitude' => 69.7220, 'size_category' => 'Medium'],
            ['name' => 'Port of Colombo', 'country_name' => 'Sri Lanka', 'cca2' => 'LK', 'latitude' => 6.9344, 'longitude' => 79.8428, 'size_category' => 'Medium'],
            ['name' => 'Port of Jakarta (Tanjung Priok)', 'country_name' => 'Indonesia', 'cca2' => 'ID', 'latitude' => -6.1045, 'longitude' => 106.8800, 'size_category' => 'Large'],
            ['name' => 'Port of Surabaya (Tanjung Perak)', 'country_name' => 'Indonesia', 'cca2' => 'ID', 'latitude' => -7.2010, 'longitude' => 112.7330, 'size_category' => 'Medium'],
            ['name' => 'Port of Belawan', 'country_name' => 'Indonesia', 'cca2' => 'ID', 'latitude' => 3.7820, 'longitude' => 98.6920, 'size_category' => 'Medium'],
            ['name' => 'Port Klang', 'country_name' => 'Malaysia', 'cca2' => 'MY', 'latitude' => 3.0000, 'longitude' => 101.3927, 'size_category' => 'Large'],
            ['name' => 'Port of Tanjung Pelepas', 'country_name' => 'Malaysia', 'cca2' => 'MY', 'latitude' => 1.3622, 'longitude' => 103.5500, 'size_category' => 'Large'],
            ['name' => 'Port of Laem Chabang', 'country_name' => 'Thailand', 'cca2' => 'TH', 'latitude' => 13.0827, 'longitude' => 100.8830, 'size_category' => 'Medium'],
            ['name' => 'Port of Manila', 'country_name' => 'Philippines', 'cca2' => 'PH', 'latitude' => 14.5832, 'longitude' => 120.9602, 'size_category' => 'Medium'],
            ['name' => 'Port of Sydney (Botany Bay)', 'country_name' => 'Australia', 'cca2' => 'AU', 'latitude' => -33.9700, 'longitude' => 151.2200, 'size_category' => 'Medium'],
            ['name' => 'Port of Melbourne', 'country_name' => 'Australia', 'cca2' => 'AU', 'latitude' => -37.8200, 'longitude' => 144.9300, 'size_category' => 'Medium'],
            ['name' => 'Port of Durban', 'country_name' => 'South Africa', 'cca2' => 'ZA', 'latitude' => -29.8579, 'longitude' => 31.0292, 'size_category' => 'Medium'],
            ['name' => 'Port of Jeddah (Islamic Port)', 'country_name' => 'Saudi Arabia', 'cca2' => 'SA', 'latitude' => 21.4858, 'longitude' => 39.1925, 'size_category' => 'Medium'],
            ['name' => 'Port of Jebel Ali', 'country_name' => 'United Arab Emirates', 'cca2' => 'AE', 'latitude' => 25.0118, 'longitude' => 55.0617, 'size_category' => 'Large'],
        ];

        foreach ($ports as $port) {
            Port::create($port);
        }
    }
}