<?php

use App\Http\Controllers\Api\CountryController;
use App\Http\Controllers\Api\RiskController;
use App\Http\Controllers\Api\NewsController;
use App\Http\Controllers\Api\PortController;
use App\Http\Controllers\Api\CurrencyController;
use App\Http\Controllers\Api\WatchlistController;
use Illuminate\Support\Facades\Route;

Route::prefix('countries')->group(function () {
    Route::get('/search', [CountryController::class, 'search']);
    Route::post('/{cca2}/fetch-all', [CountryController::class, 'fetchAll']);
});

Route::prefix('risk')->group(function () {
    Route::get('/{cca2}', [RiskController::class, 'calculate']);
    Route::get('/{cca2}/history', [RiskController::class, 'history']);
    Route::get('/{cca2}/breakdown', [RiskController::class, 'breakdown']);
});

Route::prefix('news')->group(function () {
    Route::get('/{cca2}', [NewsController::class, 'index']);
    Route::get('/{cca2}/sentiment', [NewsController::class, 'sentiment']);
});

Route::get('/ports', [PortController::class, 'index']);
Route::get('/ports/search', [PortController::class, 'search']);

Route::prefix('currency')->group(function () {
    Route::get('/{currency}/history', [CurrencyController::class, 'history']);
});

Route::prefix('watchlist')->group(function () {
    Route::get('/', [WatchlistController::class, 'index']);
    Route::post('/', [WatchlistController::class, 'store']);
    Route::delete('/{id}', [WatchlistController::class, 'destroy']);
});