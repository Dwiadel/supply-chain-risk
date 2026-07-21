@extends('layouts.app')
@section('title', 'Favorite Monitoring List')
@section('content')

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
    <div>
        <h5 style="color:var(--text-main);margin:0;font-weight:700;">
            <i class="bi bi-bookmark-star-fill" style="color:#ffc107;"></i>
            Favorite Monitoring List
        </h5>
        <small style="color:var(--text-muted-custom);">
            Daftar negara yang Anda pantau untuk monitoring risiko rantai pasok
        </small>
    </div>
    <div id="watchlist-count-badge"
         style="display:none;background:rgba(255,193,7,0.15);border:1px solid rgba(255,193,7,0.3);
                color:#ffc107;padding:6px 16px;border-radius:20px;font-size:13px;font-weight:600;">
        <i class="bi bi-bookmark-fill"></i>
        <span id="watchlist-count">0</span> Negara Dipantau
    </div>
</div>

{{-- Loading State --}}
<div id="watchlist-loading" style="text-align:center;padding:60px 20px;">
    <div class="loading-spinner" style="width:32px;height:32px;margin:0 auto 15px;"></div>
    <div style="color:var(--text-muted-custom);font-size:13px;">Memuat watchlist...</div>
</div>

{{-- Empty State --}}
<div id="watchlist-empty" style="display:none;text-align:center;padding:80px 20px;">
    <div style="width:80px;height:80px;background:rgba(255,193,7,0.08);border-radius:50%;
                display:flex;align-items:center;justify-content:center;margin:0 auto 20px;">
        <i class="bi bi-bookmark-plus" style="font-size:36px;color:#ffc107;opacity:0.6;"></i>
    </div>
    <h5 style="color:var(--text-main);margin-bottom:10px;">Watchlist Masih Kosong</h5>
    <p style="color:var(--text-muted-custom);max-width:400px;margin:0 auto 25px;font-size:14px;">
        Belum ada negara yang dipantau. Cari negara dari search box di atas,
        lalu klik tombol <b style="color:#ffc107;">Watchlist</b> untuk menambahkan ke daftar pantauan.
    </p>
    <div style="display:flex;gap:10px;justify-content:center;flex-wrap:wrap;">
        @foreach(['Germany','Indonesia','China','Australia','Japan'] as $c)
        @php $code = \App\Models\Country::where('name',$c)->value('cca2') ?? ''; @endphp
        @if($code)
        <button onclick="selectCountry('{{ $code }}','{{ $c }}')"
                style="background:var(--card-bg);border:1px solid var(--card-border);
                       color:var(--text-main);padding:7px 16px;border-radius:8px;cursor:pointer;font-size:13px;
                       transition:border-color 0.2s;"
                onmouseenter="this.style.borderColor='#ffc107'"
                onmouseleave="this.style.borderColor='var(--card-border)'">
            <i class="bi bi-globe2" style="color:#ffc107;"></i> {{ $c }}
        </button>
        @endif
        @endforeach
    </div>
</div>

{{-- Watchlist Content --}}
<div id="watchlist-content" style="display:none;">

    {{-- Summary Stats --}}
    <div class="row g-3 mb-4" id="watchlist-stats">
        <div class="col-md-3 col-6">
            <div class="dash-card" style="text-align:center;border-color:rgba(255,193,7,0.2);">
                <div class="dash-card-title"><i class="bi bi-bookmark-fill"></i> Total Dipantau</div>
                <div class="dash-card-value" id="stat-total" style="color:#ffc107;">0</div>
                <div class="dash-card-sub">negara</div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="dash-card" style="text-align:center;border-color:rgba(25,135,84,0.2);">
                <div class="dash-card-title"><i class="bi bi-shield-check"></i> Low Risk</div>
                <div class="dash-card-value" id="stat-low" style="color:#25b574;">0</div>
                <div class="dash-card-sub">negara</div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="dash-card" style="text-align:center;border-color:rgba(255,193,7,0.2);">
                <div class="dash-card-title"><i class="bi bi-shield-exclamation"></i> Medium Risk</div>
                <div class="dash-card-value" id="stat-medium" style="color:#ffc107;">0</div>
                <div class="dash-card-sub">negara</div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="dash-card" style="text-align:center;border-color:rgba(220,53,69,0.2);">
                <div class="dash-card-title"><i class="bi bi-shield-x"></i> High Risk</div>
                <div class="dash-card-value" id="stat-high" style="color:#ff6b7a;">0</div>
                <div class="dash-card-sub">negara</div>
            </div>
        </div>
    </div>

    {{-- Country Cards Grid --}}
    <div id="watchlist-grid" class="row g-3"></div>

</div>

@endsection

@push('scripts')
<script>
// Load watchlist saat halaman dibuka
document.addEventListener('DOMContentLoaded', function() {
    loadWatchlist();
});

// Refresh watchlist kalau negara baru ditambahkan
window.addEventListener('countrySelected', function() {
    setTimeout(loadWatchlist, 500);
});

async function loadWatchlist() {
    document.getElementById('watchlist-loading').style.display  = 'block';
    document.getElementById('watchlist-empty').style.display    = 'none';
    document.getElementById('watchlist-content').style.display  = 'none';

    try {
        var res  = await fetch('/api/watchlist');
        var data = await res.json();

        document.getElementById('watchlist-loading').style.display = 'none';

        if (!data.success || !data.data || data.data.length === 0) {
            document.getElementById('watchlist-empty').style.display = 'block';
            return;
        }

        document.getElementById('watchlist-content').style.display = 'block';
        document.getElementById('watchlist-count-badge').style.display = 'inline-flex';
        document.getElementById('watchlist-count').textContent = data.data.length;
        document.getElementById('stat-total').textContent = data.data.length;

        renderWatchlist(data.data);

    } catch(e) {
        console.error('Watchlist error:', e);
        document.getElementById('watchlist-loading').style.display = 'none';
        document.getElementById('watchlist-empty').style.display   = 'block';
    }
}

async function renderWatchlist(items) {
    var grid = document.getElementById('watchlist-grid');
    grid.innerHTML = '';

    var lowCount = 0, medCount = 0, highCount = 0;

    for (var i = 0; i < items.length; i++) {
        var item    = items[i];
        var country = item.country;
        if (!country) continue;

        // Fetch risk score untuk negara ini
        var riskLevel = 'Low', riskScore = '—', riskColor = '#25b574', riskBorder = 'rgba(25,135,84,0.2)';
        try {
            var rRes  = await fetch('/api/risk/' + country.cca2 + '/breakdown');
            var rData = await rRes.json();
            if (rData.success) {
                riskScore = rData.data.total_score;
                riskLevel = rData.data.risk_level;
                riskColor  = riskLevel === 'High' ? '#ff6b7a' : riskLevel === 'Medium' ? '#ffc107' : '#25b574';
                riskBorder = riskLevel === 'High' ? 'rgba(220,53,69,0.2)' : riskLevel === 'Medium' ? 'rgba(255,193,7,0.2)' : 'rgba(25,135,84,0.2)';

                if (riskLevel === 'Low')    lowCount++;
                else if (riskLevel === 'Medium') medCount++;
                else highCount++;
            }
        } catch(e) {}

        var watchlistId = item.id;
        var flagImg = country.flag_url
            ? '<img src="' + country.flag_url + '" style="width:48px;border-radius:4px;box-shadow:0 2px 8px rgba(31,35,51,0.15);">'
            : '<i class="bi bi-flag" style="font-size:32px;color:var(--text-muted-custom);"></i>';

        var card = document.createElement('div');
        card.className = 'col-md-4 col-sm-6';
        card.id = 'watchlist-card-' + watchlistId;
        card.innerHTML =
            '<div class="dash-card" style="border-color:' + riskBorder + ';' +
                'transition:transform 0.2s,box-shadow 0.2s;cursor:pointer;"' +
                ' onmouseenter="this.style.transform=\'translateY(-2px)\';this.style.boxShadow=\'0 8px 25px rgba(31,35,51,0.15)\'"' +
                ' onmouseleave="this.style.transform=\'\';this.style.boxShadow=\'\'">' +

                '<div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:15px;">' +
                    '<div style="display:flex;align-items:center;gap:12px;">' +
                        flagImg +
                        '<div>' +
                            '<div style="color:var(--text-main);font-weight:700;font-size:15px;">' + country.name + '</div>' +
                            '<div style="color:var(--text-muted-custom);font-size:12px;">' +
                                (country.capital || '—') + ' · ' + (country.region || '—') +
                            '</div>' +
                        '</div>' +
                    '</div>' +
                    '<button onclick="removeFromWatchlist(' + watchlistId + ')" ' +
                            'style="background:rgba(220,53,69,0.1);border:1px solid rgba(220,53,69,0.2);' +
                                   'color:#ff6b7a;width:28px;height:28px;border-radius:6px;cursor:pointer;' +
                                   'display:flex;align-items:center;justify-content:center;font-size:12px;' +
                                   'flex-shrink:0;transition:background 0.2s;"' +
                            'onmouseenter="this.style.background=\'rgba(220,53,69,0.25)\'"' +
                            'onmouseleave="this.style.background=\'rgba(220,53,69,0.1)\'"' +
                            'title="Hapus dari watchlist">' +
                        '<i class="bi bi-x-lg"></i>' +
                    '</button>' +
                '</div>' +

                '<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:15px;">' +
                    '<span class="risk-badge risk-' + riskLevel.toLowerCase() + '" style="font-size:12px;">' +
                        '<i class="bi bi-shield-' + (riskLevel === 'Low' ? 'check' : riskLevel === 'Medium' ? 'exclamation' : 'x') + '"></i>' +
                        riskLevel + ' Risk' +
                    '</span>' +
                    '<span style="font-size:28px;font-weight:800;color:' + riskColor + ';">' + riskScore + '</span>' +
                '</div>' +

                '<div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:15px;">' +
                    miniStat('bi-translate', 'Mata Uang', country.currency_code || '—') +
                    miniStat('bi-geo-alt', 'Subregion', country.subregion || '—') +
                '</div>' +

                '<div style="display:flex;gap:8px;">' +
                    '<button onclick="selectCountry(\'' + country.cca2 + '\',\'' + country.name.replace(/'/g, "\\'") + '\')" ' +
                            'style="flex:1;background:rgba(253,126,20,0.10);border:1px solid rgba(253,126,20,0.2);' +
                                   'color:var(--primary);padding:7px;border-radius:8px;cursor:pointer;font-size:12px;' +
                                   'transition:background 0.2s;"' +
                            'onmouseenter="this.style.background=\'rgba(253,126,20,0.2)\'"' +
                            'onmouseleave="this.style.background=\'rgba(253,126,20,0.10)\'">' +
                        '<i class="bi bi-graph-up"></i> Lihat Dashboard' +
                    '</button>' +
                    '<button onclick="goToRisk(\'' + country.cca2 + '\',\'' + country.name.replace(/'/g, "\\'") + '\')" ' +
                            'style="background:rgba(255,193,7,0.1);border:1px solid rgba(255,193,7,0.2);' +
                                   'color:#ffc107;padding:7px 12px;border-radius:8px;cursor:pointer;font-size:12px;' +
                                   'transition:background 0.2s;"' +
                            'onmouseenter="this.style.background=\'rgba(255,193,7,0.2)\'"' +
                            'onmouseleave="this.style.background=\'rgba(255,193,7,0.1)\'">' +
                        '<i class="bi bi-speedometer2"></i>' +
                    '</button>' +
                '</div>' +
            '</div>';

        grid.appendChild(card);
    }

    // Update stats
    document.getElementById('stat-low').textContent    = lowCount;
    document.getElementById('stat-medium').textContent = medCount;
    document.getElementById('stat-high').textContent   = highCount;
}

function miniStat(icon, label, value) {
    return '<div style="background:var(--dark-bg);border-radius:8px;padding:8px;text-align:center;">' +
        '<i class="bi ' + icon + '" style="color:var(--primary);font-size:13px;"></i>' +
        '<div style="color:var(--text-main);font-size:12px;font-weight:600;margin-top:3px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">' + value + '</div>' +
        '<div style="color:var(--text-muted-custom);font-size:10px;">' + label + '</div>' +
    '</div>';
}

async function removeFromWatchlist(id) {
    if (!confirm('Hapus negara ini dari watchlist?')) return;

    try {
        var res  = await fetch('/api/watchlist/' + id, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
        });
        var data = await res.json();

        if (data.success) {
            var card = document.getElementById('watchlist-card-' + id);
            if (card) {
                card.style.transition = 'opacity 0.3s, transform 0.3s';
                card.style.opacity    = '0';
                card.style.transform  = 'scale(0.95)';
                setTimeout(function() {
                    card.remove();
                    loadWatchlist(); // reload untuk update stats
                }, 300);
            }
        }
    } catch(e) { alert('Gagal menghapus dari watchlist.'); }
}

function goToRisk(cca2, name) {
    selectCountry(cca2, name);
    setTimeout(function() {
        window.location.href = '/risk-engine';
    }, 500);
}
</script>
@endpush