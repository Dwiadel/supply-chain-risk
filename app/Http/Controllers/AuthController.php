<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // ===== USER LOGIN =====
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    // ===== ADMIN LOGIN =====
    public function showAdminLogin()
    {
        // Kalau sudah login sebagai admin, langsung ke admin panel
        if (Auth::check() && Auth::user()->role === 'admin') {
            return redirect()->route('admin.index');
        }
        // Kalau sudah login tapi bukan admin, logout dulu
        if (Auth::check()) {
            Auth::logout();
        }
        return view('auth.admin-login');
    }

    public function adminLogin(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            // Cek apakah user ini benar-benar admin
            if (Auth::user()->role !== 'admin') {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Akun ini tidak memiliki akses admin.',
                ])->onlyInput('email');
            }

            $request->session()->regenerate();
            return redirect()->route('admin.index');
        }

        return back()->withErrors([
            'email' => 'Email atau password admin salah.',
        ])->onlyInput('email');
    }

    public function adminLogout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admin.login');
    }
}