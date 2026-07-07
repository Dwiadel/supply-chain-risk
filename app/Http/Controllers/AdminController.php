<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Country;
use App\Models\Port;
use App\Models\RiskScore;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function index(): View
    {
        $stats = [
            'total_users'     => User::count(),
            'total_countries' => Country::count(),
            'total_ports'     => Port::count(),
            'total_articles'  => Article::count(),
            'total_risks'     => RiskScore::count(),
        ];

        $recentUsers    = User::latest()->limit(5)->get();
        $recentRisks    = RiskScore::with('country')->latest('calculated_at')->limit(5)->get();

        return view('admin.index', compact('stats', 'recentUsers', 'recentRisks'));
    }

    // ===== USER MANAGEMENT =====
    public function users(): View
    {
        $users = User::latest()->paginate(15);
        return view('admin.users', compact('users'));
    }

    public function updateUserRole(Request $request, User $user)
    {
        $request->validate(['role' => 'required|in:admin,user']);
        $user->update(['role' => $request->role]);
        return back()->with('success', "Role {$user->name} berhasil diubah ke {$request->role}.");
    }

    public function deleteUser(User $user)
    {
        $user->delete();
        return back()->with('success', 'User berhasil dihapus.');
    }

    // ===== PORT MANAGEMENT =====
    public function ports(): View
    {
        $ports = Port::orderBy('country_name')->paginate(20);
        return view('admin.ports', compact('ports'));
    }

    public function storePort(Request $request)
    {
        $request->validate([
            'name'          => 'required|string|max:255',
            'country_name'  => 'required|string|max:255',
            'cca2'          => 'nullable|string|max:2',
            'latitude'      => 'required|numeric',
            'longitude'     => 'required|numeric',
            'size_category' => 'required|in:Large,Medium,Small',
        ]);

        Port::create($request->all());
        return back()->with('success', 'Pelabuhan berhasil ditambahkan.');
    }

    public function deletePort(Port $port)
    {
        $port->delete();
        return back()->with('success', 'Pelabuhan berhasil dihapus.');
    }

    // ===== ARTICLE MANAGEMENT =====
    public function articles(): View
    {
        $articles = Article::with('user')->latest()->paginate(15);
        return view('admin.articles', compact('articles'));
    }

    public function storeArticle(Request $request)
    {
        $request->validate([
            'title'    => 'required|string|max:255',
            'content'  => 'required|string',
            'category' => 'required|string',
        ]);

        Article::create([
            'title'    => $request->title,
            'content'  => $request->content,
            'category' => $request->category,
            'user_id'  => 1,
        ]);

        return back()->with('success', 'Artikel berhasil ditambahkan.');
    }

    public function deleteArticle(Article $article)
    {
        $article->delete();
        return back()->with('success', 'Artikel berhasil dihapus.');
    }
}