<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

// ===== USER AUTH =====
Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout',[AuthController::class, 'logout'])->name('logout');

// ===== ADMIN AUTH =====
Route::get('/admin/login',  [AuthController::class, 'showAdminLogin'])->name('admin.login');
Route::post('/admin/login', [AuthController::class, 'adminLogin'])->name('admin.login.post');
Route::post('/admin/logout',[AuthController::class, 'adminLogout'])->name('admin.logout');

// ===== USER ROUTES (harus login sebagai user/admin) =====
Route::middleware('auth')->group(function () {
    Route::get('/',              [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/map',           [DashboardController::class, 'map'])->name('map');
    Route::get('/comparison',    [DashboardController::class, 'comparison'])->name('comparison');
    Route::get('/currency',      [DashboardController::class, 'currency'])->name('currency');
    Route::get('/news',          [DashboardController::class, 'news'])->name('news');
    Route::get('/ports',         [DashboardController::class, 'ports'])->name('ports');
    Route::get('/watchlist',     [DashboardController::class, 'watchlist'])->name('watchlist');
    Route::get('/risk-engine',   [DashboardController::class, 'riskEngine'])->name('risk-engine');
    Route::get('/visualization', [DashboardController::class, 'visualization'])->name('visualization');
});

// ===== ADMIN ROUTES (harus login sebagai admin) =====
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/',                      [AdminController::class, 'index'])->name('index');
    Route::get('/users',                 [AdminController::class, 'users'])->name('users');
    Route::patch('/users/{user}/role',   [AdminController::class, 'updateUserRole'])->name('users.role');
    Route::delete('/users/{user}',       [AdminController::class, 'deleteUser'])->name('users.delete');
    Route::get('/ports',                 [AdminController::class, 'ports'])->name('ports');
    Route::post('/ports',                [AdminController::class, 'storePort'])->name('ports.store');
    Route::delete('/ports/{port}',       [AdminController::class, 'deletePort'])->name('ports.delete');
    Route::get('/articles',              [AdminController::class, 'articles'])->name('articles');
    Route::post('/articles',             [AdminController::class, 'storeArticle'])->name('articles.store');
    Route::delete('/articles/{article}', [AdminController::class, 'deleteArticle'])->name('articles.delete');
});