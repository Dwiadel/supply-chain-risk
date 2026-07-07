<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CurrencyRate;
use Illuminate\Http\JsonResponse;

class CurrencyController extends Controller
{
    public function history(string $currency): JsonResponse
    {
        $rates = CurrencyRate::where('base_currency', 'USD')
            ->where('target_currency', strtoupper($currency))
            ->orderBy('rate_date', 'desc')
            ->limit(30)
            ->get(['rate', 'change_percent', 'rate_date']);

        return response()->json([
            'success' => true,
            'currency' => strtoupper($currency),
            'data' => $rates,
        ]);
    }
}