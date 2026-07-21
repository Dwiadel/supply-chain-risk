<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin — @yield('title', 'Supply Chain Risk')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        /* ===== VARIABEL KHUSUS ADMIN — tidak terpengaruh tema utama ===== */
        :root {
            --adm-bg:       #0a0c10;
            --adm-card:     #12141c;
            --adm-border:   #1e2030;
            --adm-muted:    #6c757d;
            --adm-text:     #e2e5f1;
            --adm-red:      #dc3545;
            --adm-red-soft: rgba(220,53,69,0.15);
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            background: var(--adm-bg) !important;
            color: var(--adm-text) !important;
            font-family: 'Segoe UI', sans-serif;
        }

        /* ===== SIDEBAR ===== */
        #admin-sidebar {
            width: 240px;
            height: 100vh;
            position: fixed;
            top: 0; left: 0;
            background: var(--adm-card);
            border-right: 1px solid var(--adm-border);
            z-index: 100;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
        }

        .admin-brand {
            padding: 18px 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            background: linear-gradient(135deg, #dc3545, #a71d2a);
            flex-shrink: 0;
        }

        .admin-brand-icon {
            width: 40px; height: 40px;
            background: rgba(255,255,255,0.2);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }

        .admin-brand h6 {
            color: #fff;
            font-weight: 700;
            font-size: 13px;
            margin: 0 0 2px;
        }

        .admin-brand span {
            color: rgba(255,255,255,0.8);
            font-size: 11px;
        }

        .admin-status {
            padding: 8px 20px;
            background: rgba(220,53,69,0.08);
            border-bottom: 1px solid var(--adm-border);
            display: flex;
            align-items: center;
            gap: 8px;
            flex-shrink: 0;
        }

        .admin-status-dot {
            width: 7px; height: 7px;
            background: #dc3545;
            border-radius: 50%;
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse { 0%,100%{opacity:1} 50%{opacity:0.3} }

        .admin-status span {
            color: #ff6b7a;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .admin-nav {
            padding: 12px 0;
            flex: 1;
        }

        .nav-section-label {
            padding: 10px 20px 4px;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #3a3d4e;
            font-weight: 700;
        }

        .admin-nav a {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 20px;
            color: #6c757d;
            text-decoration: none;
            font-size: 13px;
            border-left: 3px solid transparent;
            transition: all 0.2s;
        }

        .admin-nav a i {
            font-size: 15px;
            width: 18px;
            text-align: center;
            flex-shrink: 0;
        }

        .admin-nav a:hover,
        .admin-nav a.active {
            background: rgba(220,53,69,0.1);
            color: #fff;
            border-left-color: #dc3545;
        }

        .admin-nav a.nav-back { color: #3a3d4e; }
        .admin-nav a.nav-back:hover {
            background: rgba(255,255,255,0.03);
            color: #6c757d;
            border-left-color: #3a3d4e;
        }

        .admin-user-info {
            padding: 14px 20px;
            border-top: 1px solid var(--adm-border);
            display: flex;
            align-items: center;
            gap: 10px;
            flex-shrink: 0;
        }

        .admin-avatar {
            width: 34px; height: 34px;
            background: linear-gradient(135deg, #dc3545, #a71d2a);
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
            font-size: 14px;
            color: #fff;
            font-weight: 700;
        }

        .admin-user-name {
            color: #e2e5f1;
            font-size: 12px;
            font-weight: 600;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .admin-user-role {
            color: #ff6b7a;
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* ===== TOPBAR ===== */
        #admin-content { margin-left: 240px; min-height: 100vh; }

        .admin-topbar {
            background: var(--adm-card);
            border-bottom: 1px solid var(--adm-border);
            padding: 0 25px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 99;
        }

        .admin-topbar-left h6 {
            color: #fff;
            margin: 0;
            font-weight: 700;
            font-size: 15px;
        }

        .admin-topbar-left small { color: var(--adm-muted); font-size: 12px; }

        .admin-topbar-right {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .admin-content-body { padding: 25px; }

        /* ===== KOMPONEN ===== */
        .stat-card {
            background: var(--adm-card);
            border: 1px solid var(--adm-border);
            border-radius: 12px;
            padding: 20px;
        }

        .stat-card-value {
            font-size: 32px;
            font-weight: 800;
            color: #fff;
        }

        .stat-card-label {
            font-size: 12px;
            color: var(--adm-muted);
            text-transform: uppercase;
            letter-spacing: 0.8px;
        }

        .admin-card {
            background: var(--adm-card);
            border: 1px solid var(--adm-border);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .admin-card-title {
            font-size: 13px;
            font-weight: 600;
            color: #fff;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid var(--adm-border);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .admin-table {
            width: 100%;
            border-collapse: collapse;
        }

        .admin-table th {
            padding: 10px 14px;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: var(--adm-muted);
            border-bottom: 2px solid var(--adm-border);
            text-align: left;
        }

        .admin-table td {
            padding: 12px 14px;
            font-size: 13px;
            border-bottom: 1px solid var(--adm-border);
            color: var(--adm-text);
            vertical-align: middle;
        }

        .admin-table tr:hover td {
            background: rgba(220,53,69,0.03);
        }

        .form-control, .form-select {
            background: var(--adm-bg) !important;
            border: 1px solid var(--adm-border) !important;
            color: #fff !important;
            font-size: 13px;
            border-radius: 8px;
        }

        .form-control:focus, .form-select:focus {
            border-color: #dc3545 !important;
            box-shadow: 0 0 0 3px rgba(220,53,69,0.15) !important;
        }

        .form-control::placeholder { color: #3a3d4e !important; }

        .btn-admin-primary {
            background: linear-gradient(135deg, #dc3545, #a71d2a);
            border: none;
            color: #fff;
            padding: 9px 20px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            box-shadow: 0 3px 10px rgba(220,53,69,0.3);
        }

        .btn-admin-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 5px 15px rgba(220,53,69,0.4);
            color: #fff;
        }

        .badge-admin {
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
        }

        .badge-admin-red {
            background: rgba(220,53,69,0.15);
            color: #ff6b7a;
            border: 1px solid rgba(220,53,69,0.3);
        }

        .badge-admin-blue {
            background: rgba(13,110,253,0.15);
            color: #6ea8fe;
            border: 1px solid rgba(13,110,253,0.3);
        }

        .badge-admin-green {
            background: rgba(37,181,116,0.15);
            color: #25b574;
            border: 1px solid rgba(37,181,116,0.3);
        }

        .alert-success-custom {
            background: rgba(25,135,84,0.1);
            border: 1px solid rgba(25,135,84,0.3);
            color: #25b574;
            border-radius: 8px;
            padding: 12px 16px;
            margin-bottom: 20px;
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* Pagination override */
        .pagination .page-link {
            background: var(--adm-card) !important;
            border-color: var(--adm-border) !important;
            color: var(--adm-text) !important;
        }

        .pagination .page-item.active .page-link {
            background: #dc3545 !important;
            border-color: #dc3545 !important;
        }

        ::-webkit-scrollbar { width: 4px; }
        ::-webkit-scrollbar-track { background: var(--adm-bg); }
        ::-webkit-scrollbar-thumb { background: var(--adm-border); border-radius: 2px; }
    </style>
</head>
<body>

{{-- SIDEBAR --}}
<div id="admin-sidebar">
    <div class="admin-brand">
        <div class="admin-brand-icon">
            <i class="bi bi-shield-lock-fill" style="color:#fff;font-size:18px;"></i>
        </div>
        <div>
            <h6>Admin Panel</h6>
            <span>Supply Chain Risk</span>
        </div>
    </div>

    <div class="admin-status">
        <div class="admin-status-dot"></div>
        <span>ADMIN SESSION ACTIVE</span>
    </div>

    <nav class="admin-nav">
        <div class="nav-section-label">Overview</div>
        <a href="{{ route('admin.index') }}"
           class="{{ request()->routeIs('admin.index') ? 'active' : '' }}">
            <i class="bi bi-grid-1x2-fill"></i> Dashboard
        </a>

        <div class="nav-section-label" style="margin-top:8px;">Kelola Data</div>
        <a href="{{ route('admin.users') }}"
           class="{{ request()->routeIs('admin.users') ? 'active' : '' }}">
            <i class="bi bi-people-fill"></i> Manajemen User
        </a>
        <a href="{{ route('admin.ports') }}"
           class="{{ request()->routeIs('admin.ports') ? 'active' : '' }}">
            <i class="bi bi-anchor"></i> Dataset Pelabuhan
        </a>
        <a href="{{ route('admin.articles') }}"
           class="{{ request()->routeIs('admin.articles') ? 'active' : '' }}">
            <i class="bi bi-file-text-fill"></i> Artikel Analisis
        </a>

        <div class="nav-section-label" style="margin-top:8px;">Navigasi</div>
        <a href="{{ route('dashboard') }}" class="nav-back">
            <i class="bi bi-arrow-left-circle"></i> Kembali ke App
        </a>
    </nav>

    <div class="admin-user-info">
        <div class="admin-avatar">
            {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
        </div>
        <div style="flex:1;min-width:0;">
            <div class="admin-user-name">{{ Auth::user()->name ?? 'Administrator' }}</div>
            <div class="admin-user-role">
                <i class="bi bi-shield-fill"></i> Administrator
            </div>
        </div>
    </div>
</div>

{{-- MAIN CONTENT --}}
<div id="admin-content">

    {{-- Topbar --}}
    <div class="admin-topbar">
        <div class="admin-topbar-left">
            <h6>@yield('title', 'Admin Dashboard')</h6>
            <small>Global Supply Chain Risk Intelligence Platform</small>
        </div>
        <div class="admin-topbar-right">
            <span class="badge-admin badge-admin-red">
                <i class="bi bi-shield-lock-fill"></i> Admin Mode
            </span>
            <form method="POST" action="{{ route('admin.logout') }}" style="margin:0;">
                @csrf
                <button type="submit"
                        style="background:rgba(220,53,69,0.12);border:1px solid rgba(220,53,69,0.3);
                               color:#ff6b7a;padding:6px 14px;border-radius:8px;font-size:12px;
                               cursor:pointer;display:flex;align-items:center;gap:6px;transition:all 0.2s;"
                        onmouseenter="this.style.background='rgba(220,53,69,0.25)'"
                        onmouseleave="this.style.background='rgba(220,53,69,0.12)'">
                    <i class="bi bi-box-arrow-right"></i> Logout Admin
                </button>
            </form>
        </div>
    </div>

    {{-- Body --}}
    <div class="admin-content-body">

        @if(session('success'))
        <div class="alert-success-custom">
            <i class="bi bi-check-circle-fill"></i>
            {{ session('success') }}
        </div>
        @endif

        @yield('content')
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>