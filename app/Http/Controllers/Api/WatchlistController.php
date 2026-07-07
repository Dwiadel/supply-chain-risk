<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Watchlist;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WatchlistController extends Controller
{
    public function index(): JsonResponse
    {
        // Untuk sekarang pakai user_id = 1 (user demo)
        // Nanti bisa diganti Auth::id() setelah auth diimplementasikan
        $items = Watchlist::with('country')
            ->where('user_id', 1)
            ->get();

        return response()->json(['success' => true, 'data' => $items]);
    }

    public function store(Request $request): JsonResponse
    {
        $country = Country::where('cca2', strtoupper($request->cca2))->first();

        if (! $country) {
            return response()->json(['success' => false, 'message' => 'Negara tidak ditemukan.'], 404);
        }

        $exists = Watchlist::where('user_id', 1)
            ->where('country_id', $country->id)->exists();

        if ($exists) {
            return response()->json(['success' => false, 'message' => 'Negara sudah ada di watchlist.']);
        }

        Watchlist::create(['user_id' => 1, 'country_id' => $country->id]);

        return response()->json(['success' => true, 'message' => "{$country->name} ditambahkan ke watchlist."]);
    }

    public function destroy(int $id): JsonResponse
    {
        Watchlist::where('id', $id)->where('user_id', 1)->delete();

        return response()->json(['success' => true, 'message' => 'Dihapus dari watchlist.']);
    }
}