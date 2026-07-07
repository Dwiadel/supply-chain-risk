<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Global Supply Chain Risk Intelligence')</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Leaflet CSS -->
    <link href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" rel="stylesheet">

    <style>
        :root {
            --sidebar-width: 260px;
            --topbar-height: 60px;
            --primary: #0d6efd;
            --dark-bg: #0f1117;
            --card-bg: #1a1d27;
            --card-border: #2a2d3e;
            --text-muted-custom: #8b8fa8;
        }

        body {
            background-color: var(--dark-bg);
            color: #e2e5f1;
            font-family: 'Segoe UI', sans-serif;
            overflow-x: hidden;
        }

        /* ===== SIDEBAR ===== */
        #sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            top: 0; left: 0;
            background: var(--card-bg);
            border-right: 1px solid var(--card-border);
            z-index: 1000;
            overflow-y: auto;
            transition: transform 0.3s ease;
        }

        .sidebar-brand {
            padding: 20px 20px 15px;
            border-bottom: 1px solid var(--card-border);
        }

        .sidebar-brand h6 {
            color: #fff;
            font-weight: 700;
            font-size: 13px;
            letter-spacing: 0.5px;
            margin: 0;
        }

        .sidebar-brand span {
            font-size: 11px;
            color: var(--text-muted-custom);
        }

        .sidebar-nav { padding: 15px 0; }

        .sidebar-section-label {
            padding: 10px 20px 5px;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--text-muted-custom);
        }

        .sidebar-nav a {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 20px;
            color: #b0b3c8;
            text-decoration: none;
            font-size: 14px;
            transition: all 0.2s;
            border-left: 3px solid transparent;
        }

        .sidebar-nav a:hover,
        .sidebar-nav a.active {
            background: rgba(13, 110, 253, 0.1);
            color: #fff;
            border-left-color: var(--primary);
        }

        .sidebar-nav a i {
            font-size: 16px;
            width: 20px;
            text-align: center;
        }

        .sidebar-nav a.admin-link:hover,
        .sidebar-nav a.admin-link.active {
            background: rgba(220, 53, 69, 0.1);
            color: #ff6b7a;
            border-left-color: #dc3545;
        }

        /* ===== TOPBAR ===== */
        #topbar {
            height: var(--topbar-height);
            position: fixed;
            top: 0;
            left: var(--sidebar-width);
            right: 0;
            background: var(--card-bg);
            border-bottom: 1px solid var(--card-border);
            z-index: 999;
            display: flex;
            align-items: center;
            padding: 0 25px;
            gap: 15px;
        }

        /* ===== SEARCH BOX ===== */
        #country-search-wrapper {
            position: relative;
            flex: 1;
            max-width: 450px;
        }

        #country-search {
            background: var(--dark-bg);
            border: 1px solid var(--card-border);
            color: #fff;
            border-radius: 8px;
            padding: 8px 15px 8px 38px;
            width: 100%;
            font-size: 14px;
        }

        #country-search:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(13,110,253,0.15);
        }

        #country-search::placeholder { color: var(--text-muted-custom); }

        .search-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted-custom);
            font-size: 14px;
        }

        #search-results {
            position: absolute;
            top: calc(100% + 5px);
            left: 0; right: 0;
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 8px;
            z-index: 9999;
            max-height: 300px;
            overflow-y: auto;
            box-shadow: 0 10px 30px rgba(0,0,0,0.4);
        }

        .search-result-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 15px;
            cursor: pointer;
            border-bottom: 1px solid var(--card-border);
            transition: background 0.15s;
        }

        .search-result-item:hover { background: rgba(13,110,253,0.1); }
        .search-result-item:last-child { border-bottom: none; }

        .search-result-item img {
            width: 28px;
            height: 18px;
            object-fit: cover;
            border-radius: 2px;
        }

        .search-result-item .country-name {
            font-size: 14px;
            color: #fff;
            font-weight: 500;
        }

        .search-result-item .country-meta {
            font-size: 11px;
            color: var(--text-muted-custom);
        }

        /* ===== MAIN CONTENT ===== */
        #main-content {
            margin-left: var(--sidebar-width);
            margin-top: var(--topbar-height);
            padding: 25px;
            min-height: calc(100vh - var(--topbar-height));
        }

        /* ===== CARDS ===== */
        .dash-card {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 12px;
            padding: 20px;
            height: 100%;
        }

        .dash-card-title {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: var(--text-muted-custom);
            margin-bottom: 8px;
        }

        .dash-card-value {
            font-size: 28px;
            font-weight: 700;
            color: #fff;
            line-height: 1.2;
        }

        .dash-card-sub {
            font-size: 12px;
            color: var(--text-muted-custom);
            margin-top: 4px;
        }

        /* ===== RISK BADGE ===== */
        .risk-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
        }

        .risk-low    { background: rgba(25,135,84,0.2);  color: #25b574; border: 1px solid rgba(25,135,84,0.3); }
        .risk-medium { background: rgba(255,193,7,0.2);  color: #ffc107; border: 1px solid rgba(255,193,7,0.3); }
        .risk-high   { background: rgba(220,53,69,0.2);  color: #ff6b7a; border: 1px solid rgba(220,53,69,0.3); }

        /* ===== SENTIMENT BARS ===== */
        .sentiment-bar {
            height: 8px;
            border-radius: 4px;
            overflow: hidden;
            background: var(--dark-bg);
            display: flex;
            gap: 2px;
        }

        /* ===== LOADING OVERLAY ===== */
        #loading-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(15,17,23,0.85);
            z-index: 9998;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 15px;
        }

        #loading-overlay.show { display: flex; }

        .loading-spinner {
            width: 48px;
            height: 48px;
            border: 4px solid var(--card-border);
            border-top-color: var(--primary);
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin { to { transform: rotate(360deg); } }

        /* ===== COUNTRY FLAG HEADER ===== */
        #country-header {
            display: none;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
            padding: 15px 20px;
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 12px;
        }

        #country-header img {
            height: 36px;
            border-radius: 4px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.3);
        }

        #country-header.show { display: flex; }

        /* ===== CHART CONTAINER ===== */
        .chart-container {
            position: relative;
            height: 220px;
        }

        /* ===== SCROLLBAR ===== */
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: var(--dark-bg); }
        ::-webkit-scrollbar-thumb { background: var(--card-border); border-radius: 3px; }
    </style>

    @stack('styles')
</head>
<body>

{{-- Loading Overlay --}}
<div id="loading-overlay">
    <div class="loading-spinner"></div>
    <div style="color: #b0b3c8; font-size: 14px;" id="loading-text">Mengambil data negara...</div>
</div>

{{-- Sidebar --}}
<div id="sidebar">
    <div class="sidebar-brand">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:5px;">
            <div style="width:32px;height:32px;background:var(--primary);border-radius:8px;display:flex;align-items:center;justify-content:center;">
                <i class="bi bi-globe2" style="color:#fff;font-size:16px;"></i>
            </div>
            <div>
                <h6>Supply Chain Risk</h6>
                <span>Intelligence Platform</span>
            </div>
        </div>
    </div>

    <nav class="sidebar-nav">
        <div class="sidebar-section-label">Main</div>
        <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="bi bi-grid-1x2-fill"></i> Dashboard
        </a>
        <a href="{{ route('map') }}" class="{{ request()->routeIs('map') ? 'active' : '' }}">
            <i class="bi bi-map-fill"></i> Peta Global
        </a>
        <a href="{{ route('comparison') }}" class="{{ request()->routeIs('comparison') ? 'active' : '' }}">
            <i class="bi bi-bar-chart-steps"></i> Perbandingan
        </a>

        <div class="sidebar-section-label" style="margin-top:10px;">Analysis</div>
        <a href="{{ route('currency') }}" class="{{ request()->routeIs('currency') ? 'active' : '' }}">
            <i class="bi bi-currency-exchange"></i> Kurs Mata Uang
        </a>
        <a href="{{ route('news') }}" class="{{ request()->routeIs('news') ? 'active' : '' }}">
            <i class="bi bi-newspaper"></i> Berita & Sentimen
        </a>
        <a href="{{ route('ports') }}" class="{{ request()->routeIs('ports') ? 'active' : '' }}">
            <i class="bi bi-anchor"></i> Pelabuhan
        </a>

        <div class="sidebar-section-label" style="margin-top:10px;">Personal</div>
        <a href="{{ route('watchlist') }}" class="{{ request()->routeIs('watchlist') ? 'active' : '' }}">
            <i class="bi bi-bookmark-star-fill"></i> Watchlist
        </a>

        <div class="sidebar-section-label" style="margin-top:10px;">System</div>
        <a href="{{ route('admin.index') }}"
           class="admin-link {{ request()->routeIs('admin.*') ? 'active' : '' }}"
           style="color:#ff6b7a;">
            <i class="bi bi-shield-lock-fill" style="color:#dc3545;"></i> Admin Panel
        </a>
    </nav>
</div>

{{-- Topbar --}}
<div id="topbar">
    <div id="country-search-wrapper">
        <i class="bi bi-search search-icon"></i>
        <input type="text" id="country-search"
               placeholder="Cari negara mana saja di dunia..."
               autocomplete="off">
        <div id="search-results" style="display:none;"></div>
    </div>

    <div style="margin-left:auto;display:flex;align-items:center;gap:15px;">
        <div id="selected-country-badge" style="display:none;" class="risk-badge risk-low">
            <i class="bi bi-globe2"></i>
            <span id="selected-country-name">-</span>
        </div>
        <a href="{{ route('admin.index') }}"
           style="background:rgba(220,53,69,0.15);border:1px solid rgba(220,53,69,0.3);
                  color:#ff6b7a;padding:5px 12px;border-radius:6px;font-size:12px;
                  text-decoration:none;display:flex;align-items:center;gap:5px;">
            <i class="bi bi-shield-lock-fill"></i> Admin
        </a>
        <a href="{{ route('watchlist') }}"
           style="background:var(--card-border);color:#fff;border:none;
                  padding:5px 12px;border-radius:6px;font-size:12px;
                  text-decoration:none;display:flex;align-items:center;gap:5px;">
            <i class="bi bi-bookmark-plus"></i>
        </a>
    </div>
</div>

{{-- Main Content --}}
<div id="main-content">
    @yield('content')
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
window.AppState = {
    currentCountry: null,
    currentRisk: null,
};

const searchInput = document.getElementById('country-search');
const searchBox   = document.getElementById('search-results');
let searchTimeout = null;

searchInput.addEventListener('input', function () {
    clearTimeout(searchTimeout);
    const q = this.value.trim();
    if (q.length < 2) { searchBox.style.display = 'none'; return; }
    searchTimeout = setTimeout(() => searchCountries(q), 350);
});

searchInput.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') searchBox.style.display = 'none';
});

document.addEventListener('click', function (e) {
    if (!e.target.closest('#country-search-wrapper')) searchBox.style.display = 'none';
});

async function searchCountries(query) {
    try {
        const res  = await fetch('/api/countries/search?q=' + encodeURIComponent(query));
        const data = await res.json();

        if (!data.success || !data.data.length) {
            searchBox.innerHTML = '<div class="search-result-item" style="color:var(--text-muted-custom);">Tidak ada hasil untuk "' + query + '"</div>';
            searchBox.style.display = 'block';
            return;
        }

        searchBox.innerHTML = data.data.map(function(country) {
            const flag = country.flag_url
                ? '<img src="' + country.flag_url + '" alt="' + country.name + '" onerror="this.style.display=\'none\'">'
                : '<i class="bi bi-flag" style="width:28px;text-align:center;color:var(--text-muted-custom);"></i>';
            return '<div class="search-result-item" onclick="selectCountry(\'' + country.cca2 + '\', \'' + country.name.replace(/'/g, "\\'") + '\')">' +
                flag +
                '<div>' +
                    '<div class="country-name">' + country.name + '</div>' +
                    '<div class="country-meta">' + (country.region || '') + ' · ' + (country.currency_code || '') + '</div>' +
                '</div>' +
            '</div>';
        }).join('');

        searchBox.style.display = 'block';
    } catch (err) {
        console.error('Search error:', err);
    }
}

async function selectCountry(cca2, name) {
    searchBox.style.display = 'none';
    searchInput.value = name;

    showLoading('Mengambil data ' + name + '...');

    try {
        const res  = await fetch('/api/countries/' + cca2 + '/fetch-all', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
        });
        const data = await res.json();

        if (!data.success) throw new Error(data.message);

        window.AppState.currentCountry = data.data;

        document.getElementById('selected-country-name').textContent = name;
        document.getElementById('selected-country-badge').style.display = 'flex';

        window.dispatchEvent(new CustomEvent('countrySelected', {
            detail: { cca2: cca2, country: data.data }
        }));

    } catch (err) {
        alert('Gagal mengambil data: ' + err.message);
    } finally {
        hideLoading();
    }
}

function showLoading(text) {
    document.getElementById('loading-text').textContent = text || 'Memuat...';
    document.getElementById('loading-overlay').classList.add('show');
}

function hideLoading() {
    document.getElementById('loading-overlay').classList.remove('show');
}

function formatNumber(num) {
    if (!num) return 'N/A';
    if (num >= 1e12) return (num / 1e12).toFixed(2) + ' T';
    if (num >= 1e9)  return (num / 1e9).toFixed(2) + ' B';
    if (num >= 1e6)  return (num / 1e6).toFixed(2) + ' M';
    return Number(num).toLocaleString();
}
</script>

@stack('scripts')
</body>
</html>