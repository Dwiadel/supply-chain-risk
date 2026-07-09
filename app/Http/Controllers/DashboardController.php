<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('dashboard');
    }

    public function map(): View
    {
        return view('pages.map');
    }

    public function comparison(): View
    {
        return view('pages.comparison');
    }

    public function currency(): View
    {
        return view('pages.currency');
    }

    public function news(): View
    {
        return view('pages.news');
    }

    public function ports(): View
    {
        return view('pages.ports');
    }

    public function watchlist(): View
    {
        return view('pages.watchlist');
    }

    public function riskEngine(): View
    {
    return view('pages.risk');
    }

    public function visualization(): View
    {
    return view('pages.visualization');
    }
}