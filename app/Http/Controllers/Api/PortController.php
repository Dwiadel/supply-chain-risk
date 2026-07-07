<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Port;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PortController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => Port::orderBy('size_category')->get(),
        ]);
    }

    public function search(Request $request): JsonResponse
    {
        $q = $request->query('q', '');
        $ports = Port::where('name', 'like', "%{$q}%")
            ->orWhere('country_name', 'like', "%{$q}%")
            ->get();

        return response()->json(['success' => true, 'data' => $ports]);
    }
}