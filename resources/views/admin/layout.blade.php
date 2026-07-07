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
        :root {
            --sidebar-width: 240px;
            --dark-bg: #0f1117;
            --card-bg: #1a1d27;
            --card-border: #2a2d3e;
            --text-muted-custom: #8b8fa8;
        }
        body { background: var(--dark-bg); color: #e2e5f1; font-family: 'Segoe UI', sans-serif; }

        #admin-sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            top: 0; left: 0;
            background: var(--card-bg);
            border-right: 1px solid var(--card-border);
            z-index: 100;
            overflow-y: auto;
        }

        .admin-brand {
            padding: 20px;
            border-bottom: 1px solid var(--card-border);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .admin-brand-icon {
            width: 36px; height: 36px;
            background: #dc3545;
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
        }

        .admin-nav a {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 20px;
            color: #b0b3c8;
            text-decoration: none;
            font-size: 14px;
            border-left: 3px solid transparent;
            transition: all 0.2s;
        }

        .admin-nav a:hover,
        .admin-nav a.active {
            background: rgba(220,53,69,0.1);
            color: #fff;
            border-left-color: #dc3545;
        }

        #admin-content {
            margin-left: var(--sidebar-width);
            padding: 25px;
            min-height: 100vh;
        }

        .admin-topbar {
            background: var(--card-bg);
            border-bottom: 1px solid var(--card-border);
            padding: 15px 25px;
            margin: -25px -25px 25px -25px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .stat-card {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
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
            color: var(--text-muted-custom);
            text-transform: uppercase;
            letter-spacing: 0.8px;
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
            color: var(--text-muted-custom);
            border-bottom: 2px solid var(--card-border);
            text-align: left;
        }

        .admin-table td {
            padding: 12px 14px;
            font-size: 13px;
            border-bottom: 1px solid var(--card-border);
            color: #e2e5f1;
            vertical-align: middle;
        }

        .admin-table tr:hover td { background: rgba(255,255,255,0.02); }

        .admin-card {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
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
            border-bottom: 1px solid var(--card-border);
        }

        .form-control, .form-select {
            background: var(--dark-bg) !important;
            border: 1px solid var(--card-border) !important;
            color: #fff !important;
            font-size: 13px;
        }

        .form-control:focus, .form-select:focus {
            border-color: #dc3545 !important;
            box-shadow: 0 0 0 3px rgba(220,53,69,0.15) !important;
        }

        .form-control::placeholder { color: var(--text-muted-custom) !important; }

        .btn-admin-primary {
            background: #dc3545;
            border: none;
            color: #fff;
            padding: 8px 18px;
            border-radius: 6px;
            font-size: 13px;
            cursor: pointer;
        }

        .btn-admin-primary:hover { background: #bb2d3b; color: #fff; }

        .badge-admin { padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; }
        .badge-admin-red  { background: rgba(220,53,69,0.2);  color: #ff6b7a; border: 1px solid rgba(220,53,69,0.3); }
        .badge-admin-blue { background: rgba(13,110,253,0.2); color: #6ea8fe; border: 1px solid rgba(13,110,253,0.3); }

        ::-webkit-scrollbar { width: 4px; }
        ::-webkit-scrollbar-track { background: var(--dark-bg); }
        ::-webkit-scrollbar-thumb { background: var(--card-border); border-radius: 2px; }
    </style>
</head>
<body>

<div id="admin-sidebar">
    <div class="admin-brand">
        <div class="admin-brand-icon">
            <i class="bi bi-shield-lock-fill" style="color:#fff;font-size:16px;"></i>
        </div>
        <div>
            <div style="color:#fff;font-weight:700;font-size:13px;">Admin Panel</div>
            <div style="color:var(--text-muted-custom);font-size:11px;">Supply Chain Risk</div>
        </div>
    </div>

    <nav class="admin-nav" style="padding:15px 0;">
        <div style="padding:8px 20px;font-size:10px;text-transform:uppercase;letter-spacing:1px;color:var(--text-muted-custom);">Overview</div>
        <a href="{{ route('admin.index') }}" class="{{ request()->routeIs('admin.index') ? 'active' : '' }}">
            <i class="bi bi-grid-1x2-fill"></i> Dashboard
        </a>

        <div style="padding:8px 20px;margin-top:8px;font-size:10px;text-transform:uppercase;letter-spacing:1px;color:var(--text-muted-custom);">Kelola</div>
        <a href="{{ route('admin.users') }}" class="{{ request()->routeIs('admin.users') ? 'active' : '' }}">
            <i class="bi bi-people-fill"></i> Manajemen User
        </a>
        <a href="{{ route('admin.ports') }}" class="{{ request()->routeIs('admin.ports') ? 'active' : '' }}">
            <i class="bi bi-anchor"></i> Dataset Pelabuhan
        </a>
        <a href="{{ route('admin.articles') }}" class="{{ request()->routeIs('admin.articles') ? 'active' : '' }}">
            <i class="bi bi-file-text-fill"></i> Artikel Analisis
        </a>

        <div style="padding:8px 20px;margin-top:8px;font-size:10px;text-transform:uppercase;letter-spacing:1px;color:var(--text-muted-custom);">Navigation</div>
        <a href="{{ route('dashboard') }}">
            <i class="bi bi-arrow-left-circle"></i> Kembali ke App
        </a>
    </nav>
</div>

<div id="admin-content">
    <div class="admin-topbar">
        <div>
            <h6 style="color:#fff;margin:0;font-weight:700;">@yield('title', 'Admin Dashboard')</h6>
            <small style="color:var(--text-muted-custom);">Global Supply Chain Risk Intelligence Platform</small>
        </div>
        <div style="display:flex;align-items:center;gap:10px;">
            <span class="badge-admin badge-admin-red">
                <i class="bi bi-shield-lock-fill"></i> Admin Mode
            </span>
        </div>
    </div>

    @if(session('success'))
        <div class="alert" style="background:rgba(25,135,84,0.2);border:1px solid rgba(25,135,84,0.3);color:#25b574;border-radius:8px;padding:12px 16px;margin-bottom:20px;font-size:13px;">
            <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
        </div>
    @endif

    @yield('content')
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>