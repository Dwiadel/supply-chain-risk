@extends('layouts.app')

@section('title', 'Dashboard — Supply Chain Risk')

@section('content')

{{-- Welcome State --}}
<div id="welcome-state">
    <div style="text-align:center;padding:80px 20px;">
        <div style="width:80px;height:80px;background:rgba(253,126,20,0.12);border-radius:50%;
                    display:flex;align-items:center;justify-content:center;margin:0 auto 20px;">
            <i class="bi bi-globe2" style="font-size:36px;color:#fd7e14;"></i>
        </div>
        <h4 style="color:var(--text-main);margin-bottom:10px;">Selamat Datang di Supply Chain Risk Intelligence</h4>
        <p style="color:var(--text-muted-custom);max-width:500px;margin:0 auto 30px;">
            Ketik nama negara mana saja di kotak pencarian di atas untuk memulai monitoring
            risiko rantai pasok secara real-time.
        </p>
        <div style="display:flex;gap:10px;justify-content:center;flex-wrap:wrap;">
            @foreach(['Germany','Indonesia','China','Australia','Japan','Singapore'] as $c)
            @php $code = \App\Models\Country::where('name',$c)->value('cca2') ?? ''; @endphp
            <button onclick="quickSelect('{{ $code }}','{{ $c }}')"
                    class="btn btn-sm"
                    style="background:var(--card-bg);border:1px solid var(--card-border);color:var(--text-main);">
                {{ $c }}
            </button>
            @endforeach
        </div>
    </div>
</div>

{{-- Dashboard Content --}}
<div id="dashboard-content" style="display:none;">

    {{-- Country Header --}}
    <div id="country-header" class="mb-4" style="display:none;align-items:center;gap:15px;
         padding:15px 20px;background:var(--card-bg);border:1px solid var(--card-border);border-radius:12px;">
        <img id="ch-flag" src="" alt="flag" style="height:36px;border-radius:4px;box-shadow:0 2px 8px rgba(31,35,51,0.15);">
        <div>
            <h5 id="ch-name" style="color:var(--text-main);margin:0;font-weight:700;"></h5>
            <small id="ch-meta" style="color:var(--text-muted-custom);"></small>
        </div>
        <div style="margin-left:auto;display:flex;align-items:center;gap:10px;">
            <span id="ch-risk-badge" class="risk-badge risk-low">
                <i class="bi bi-shield-check"></i>
                <span id="ch-risk-label">Low Risk</span>
            </span>
            <button onclick="addToWatchlist()" class="btn btn-sm"
                    style="background:rgba(253,126,20,0.12);border:1px solid rgba(253,126,20,0.4);color:#e8590c;">
                <i class="bi bi-bookmark-plus"></i> Watchlist
            </button>
        </div>
    </div>

    {{-- Row 1: KPI Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="dash-card">
                <div class="dash-card-title"><i class="bi bi-graph-up-arrow"></i> GDP</div>
                <div class="dash-card-value" id="kpi-gdp">—</div>
                <div class="dash-card-sub" id="kpi-gdp-year">—</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="dash-card">
                <div class="dash-card-title"><i class="bi bi-percent"></i> Inflasi</div>
                <div class="dash-card-value" id="kpi-inflation">—</div>
                <div class="dash-card-sub">Annual %</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="dash-card">
                <div class="dash-card-title"><i class="bi bi-thermometer-half"></i> Suhu</div>
                <div class="dash-card-value" id="kpi-temp">—</div>
                <div class="dash-card-sub" id="kpi-weather-desc">—</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="dash-card">
                <div class="dash-card-title"><i class="bi bi-currency-exchange"></i> Kurs (vs USD)</div>
                <div class="dash-card-value" id="kpi-currency">—</div>
                <div class="dash-card-sub" id="kpi-currency-change">—</div>
            </div>
        </div>
    </div>

    {{-- Row 2: Risk Score + Breakdown + Weather --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="dash-card">
                <div class="dash-card-title"><i class="bi bi-speedometer2"></i> Risk Score</div>
                <div style="text-align:center;padding:10px 0;">
                    <canvas id="risk-gauge" width="200" height="120"
                            style="max-width:200px;margin:0 auto;display:block;"></canvas>
                    <div id="risk-total" style="font-size:42px;font-weight:800;color:var(--text-main);margin-top:-10px;">—</div>
                    <div id="risk-level-badge" class="risk-badge risk-low"
                         style="margin:8px auto;width:fit-content;">
                        <i class="bi bi-shield-check"></i>
                        <span id="risk-level-text">—</span>
                    </div>
                    <small style="color:var(--text-muted-custom);">Skala 0–100 | Semakin tinggi = semakin berisiko</small>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="dash-card">
                <div class="dash-card-title"><i class="bi bi-pie-chart-fill"></i> Breakdown Risiko</div>
                <div id="risk-breakdown-content" style="margin-top:15px;">
                    <div style="color:var(--text-muted-custom);text-align:center;padding:30px 0;">
                        Pilih negara untuk melihat breakdown
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="dash-card">
                <div class="dash-card-title"><i class="bi bi-cloud-sun-fill"></i> Detail Cuaca</div>
                <div id="weather-detail" style="margin-top:15px;">
                    <div style="color:var(--text-muted-custom);text-align:center;padding:30px 0;">
                        Memuat data cuaca...
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Row 3: Grafik GDP + Risk Trend --}}
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="dash-card">
                <div class="dash-card-title"><i class="bi bi-graph-up"></i> Trend GDP</div>
                <div class="chart-container mt-3">
                    <canvas id="chart-gdp"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="dash-card">
                <div class="dash-card-title"><i class="bi bi-activity"></i> Trend Risk Score</div>
                <div class="chart-container mt-3">
                    <canvas id="chart-risk"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Row 4: Berita + Sentiment --}}
    <div class="row g-3 mb-4">
        <div class="col-md-8">
            <div class="dash-card">
                <div class="dash-card-title"><i class="bi bi-newspaper"></i> Berita Terkini</div>
                <div id="news-list" style="margin-top:15px;max-height:320px;overflow-y:auto;">
                    <div style="color:var(--text-muted-custom);text-align:center;padding:30px 0;">
                        Memuat berita...
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="dash-card">
                <div class="dash-card-title"><i class="bi bi-chat-square-text-fill"></i> Analisis Sentimen</div>
                <div id="sentiment-content" style="margin-top:15px;">
                    <div style="color:var(--text-muted-custom);text-align:center;padding:20px 0;font-size:13px;">
                        Memuat sentimen...
                    </div>
                </div>
                <canvas id="chart-sentiment" height="180" style="margin-top:15px;"></canvas>
            </div>
        </div>
    </div>

    {{-- Row 5: Populasi + Ekspor + Impor --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="dash-card">
                <div class="dash-card-title"><i class="bi bi-people-fill"></i> Populasi</div>
                <div class="dash-card-value" id="kpi-population">—</div>
                <div class="dash-card-sub" id="kpi-population-year">—</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="dash-card">
                <div class="dash-card-title"><i class="bi bi-box-arrow-up-right"></i> Nilai Ekspor</div>
                <div class="dash-card-value" id="kpi-exports">—</div>
                <div class="dash-card-sub">Current USD</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="dash-card">
                <div class="dash-card-title"><i class="bi bi-box-arrow-in-down-left"></i> Nilai Impor</div>
                <div class="dash-card-value" id="kpi-imports">—</div>
                <div class="dash-card-sub">Current USD</div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
let gdpChart = null, riskChart = null, sentimentChart = null;

// Quick select dari tombol negara di welcome state
function quickSelect(cca2, name) {
    if (!cca2) {
        document.getElementById('country-search').value = name;
        document.getElementById('country-search').dispatchEvent(new Event('input'));
        return;
    }
    selectCountry(cca2, name);
}

window.addEventListener('countrySelected', async function (e) {
    const { cca2, country } = e.detail;

    document.getElementById('welcome-state').style.display     = 'none';
    document.getElementById('dashboard-content').style.display = 'block';
    document.getElementById('country-header').style.display    = 'flex';

    // Update country header
    document.getElementById('ch-flag').src = country.flag_url || '';
    document.getElementById('ch-name').textContent = country.name;
    document.getElementById('ch-meta').textContent =
        (country.capital || '—') + ' · ' + (country.region || '—') + ' · ' + (country.currency_code || '—');

    updateKPIs(country);
    await loadRiskScore(cca2, country);
    await loadNews(cca2);
    await loadSentiment(cca2);
    await loadRiskHistory(cca2);
    loadGdpChart(country);
});

function updateKPIs(country) {
    // Ambil indikator terbaru dari array
    const indicators = country.country_indicators || [];
    const ind = indicators.length > 0 ? indicators[indicators.length - 1] : null;

    // Cuaca terbaru
    const weathers = country.weather_caches || [];
    const w = weathers.length > 0 ? weathers[weathers.length - 1] : null;

    // GDP
    if (ind && ind.gdp) {
        document.getElementById('kpi-gdp').textContent = 'USD ' + formatNumber(parseFloat(ind.gdp));
    } else {
        document.getElementById('kpi-gdp').textContent = 'N/A';
    }
    document.getElementById('kpi-gdp-year').textContent = ind && ind.year ? 'Data tahun ' + ind.year : '—';

    // Inflasi
    const infEl = document.getElementById('kpi-inflation');
    if (ind && ind.inflation_rate !== null && ind.inflation_rate !== undefined) {
        const inf = parseFloat(ind.inflation_rate);
        infEl.textContent = inf.toFixed(2) + '%';
        infEl.style.color = inf > 5 ? '#dc3545' : inf < 0 ? '#e6a700' : '#198754';
    } else {
        infEl.textContent = 'N/A';
        infEl.style.color = 'var(--text-main)';
    }

    // Populasi
    document.getElementById('kpi-population').textContent =
        ind && ind.population ? formatNumber(parseInt(ind.population)) : 'N/A';
    document.getElementById('kpi-population-year').textContent =
        ind && ind.year ? 'Data tahun ' + ind.year : '—';

    // Ekspor & Impor
    document.getElementById('kpi-exports').textContent =
        ind && ind.exports_value ? 'USD ' + formatNumber(parseFloat(ind.exports_value)) : 'N/A';
    document.getElementById('kpi-imports').textContent =
        ind && ind.imports_value ? 'USD ' + formatNumber(parseFloat(ind.imports_value)) : 'N/A';

    // Cuaca
    document.getElementById('kpi-temp').textContent =
        w && w.temperature ? parseFloat(w.temperature).toFixed(1) + '°C' : 'N/A';
    document.getElementById('kpi-weather-desc').textContent =
        w && w.weather_description ? w.weather_description : '—';

    // Kurs — fetch dari API terpisah
    if (country.currency_code) {
        fetch('/api/currency/' + country.currency_code + '/history')
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data.success && data.data && data.data.length > 0) {
                    const latest = data.data[0];
                    document.getElementById('kpi-currency').textContent =
                        parseFloat(latest.rate).toLocaleString(undefined, { maximumFractionDigits: 4 });
                    const chg = parseFloat(latest.change_percent || 0);
                    const chgEl = document.getElementById('kpi-currency-change');
                    chgEl.textContent = (chg >= 0 ? '▲ +' : '▼ ') + chg.toFixed(4) + '%';
                    chgEl.style.color = chg >= 0 ? '#198754' : '#dc3545';
                } else {
                    document.getElementById('kpi-currency').textContent =
                        country.currency_code + ' (belum ada data)';
                    document.getElementById('kpi-currency-change').textContent =
                        'Daftar ExchangeRate API';
                }
            }).catch(function() {
                document.getElementById('kpi-currency').textContent = country.currency_code || '—';
            });
    }
}

function loadGdpChart(country) {
    const indicators = country.country_indicators || [];
    if (indicators.length < 2) return;

    const sorted = indicators.slice().sort(function(a, b) { return a.year - b.year; });
    const labels = sorted.map(function(i) { return i.year; });
    const values = sorted.map(function(i) { return i.gdp ? parseFloat(i.gdp) / 1e9 : null; });

    if (gdpChart) gdpChart.destroy();
    gdpChart = new Chart(document.getElementById('chart-gdp').getContext('2d'), {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'GDP (Miliar USD)',
                data: values,
                borderColor: '#198754',
                backgroundColor: 'rgba(25,135,84,0.12)',
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#198754',
                pointRadius: 4,
                spanGaps: true,
            }]
        },
        options: chartOptions('GDP (Miliar USD)')
    });
}

async function loadRiskScore(cca2, country) {
    try {
        const res  = await fetch('/api/risk/' + cca2 + '/breakdown');
        const data = await res.json();
        if (!data.success) return;

        const score = data.data.total_score;
        const level = data.data.risk_level;
        const comps = data.data.components;

        document.getElementById('risk-total').textContent = score;
        document.getElementById('risk-level-text').textContent = level + ' Risk';
        document.getElementById('risk-level-badge').className = 'risk-badge risk-' + level.toLowerCase();
        document.getElementById('ch-risk-badge').className    = 'risk-badge risk-' + level.toLowerCase();
        document.getElementById('ch-risk-label').textContent  = level + ' Risk';

        // Breakdown bars
        var bHtml = '';
        Object.entries(comps).forEach(function(entry) {
            var key = entry[0], c = entry[1];
            bHtml += '<div style="margin-bottom:12px;">' +
                '<div style="display:flex;justify-content:space-between;margin-bottom:4px;">' +
                    '<small style="color:#6b7088;">' + c.label + '</small>' +
                    '<small style="color:var(--text-main);font-weight:600;">' + c.raw_score.toFixed(1) + '</small>' +
                '</div>' +
                '<div style="height:6px;background:var(--card-border);border-radius:3px;overflow:hidden;">' +
                    '<div style="height:100%;width:' + c.raw_score + '%;background:' + getRiskColor(c.raw_score) + ';border-radius:3px;transition:width 0.8s ease;"></div>' +
                '</div>' +
                '<small style="color:var(--text-muted-custom);">Bobot ' + (c.weight*100).toFixed(0) + '% · Kontribusi ' + c.contribution.toFixed(2) + '</small>' +
            '</div>';
        });
        document.getElementById('risk-breakdown-content').innerHTML = bHtml;

        // Weather detail
        var weathers = country.weather_caches || [];
        var latestW = weathers.length > 0 ? weathers[weathers.length - 1] : null;
        if (latestW) {
            document.getElementById('weather-detail').innerHTML =
                '<div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">' +
                    weatherStat('bi-thermometer-half', 'Suhu', parseFloat(latestW.temperature).toFixed(1) + '°C') +
                    weatherStat('bi-wind', 'Angin', latestW.wind_speed + ' km/h') +
                    weatherStat('bi-cloud-rain', 'Curah Hujan', latestW.precipitation + ' mm') +
                    weatherStat('bi-exclamation-triangle', 'Storm Risk', latestW.storm_risk_score) +
                '</div>' +
                '<div style="margin-top:12px;padding:10px;background:var(--dark-bg);border-radius:8px;text-align:center;">' +
                    '<span style="color:#6b7088;font-size:13px;">' + latestW.weather_description + '</span>' +
                '</div>';
        }

        drawGauge(score, level);
    } catch (err) { console.error('loadRiskScore error:', err); }
}

async function loadNews(cca2) {
    try {
        const res  = await fetch('/api/news/' + cca2);
        const data = await res.json();

        if (!data.success || !data.data || !data.data.length) {
            document.getElementById('news-list').innerHTML =
                '<div style="color:var(--text-muted-custom);text-align:center;padding:30px 0;">' +
                '<i class="bi bi-newspaper" style="font-size:28px;opacity:0.3;display:block;margin-bottom:8px;"></i>' +
                'Belum ada berita. Konfigurasikan GNEWS_API_KEY untuk mengaktifkan fitur ini.</div>';
            return;
        }

        document.getElementById('news-list').innerHTML = data.data.map(function(a) {
            var sentClass = a.sentiment === 'Positive' ? 'risk-low' :
                            a.sentiment === 'Negative' ? 'risk-high' : 'risk-medium';
            var date = a.published_at
                ? new Date(a.published_at).toLocaleDateString('id-ID')
                : '—';
            return '<div style="padding:12px 0;border-bottom:1px solid var(--card-border);">' +
                '<div style="display:flex;align-items:flex-start;gap:10px;">' +
                    '<span class="risk-badge ' + sentClass + '" style="padding:2px 8px;font-size:10px;white-space:nowrap;">' +
                        a.sentiment +
                    '</span>' +
                    '<div>' +
                        '<a href="' + (a.url || '#') + '" target="_blank" ' +
                           'style="color:var(--text-main);text-decoration:none;font-size:13px;font-weight:500;line-height:1.4;">' +
                            a.title +
                        '</a>' +
                        '<div style="font-size:11px;color:var(--text-muted-custom);margin-top:3px;">' +
                            (a.source || '—') + ' · ' + date +
                        '</div>' +
                    '</div>' +
                '</div>' +
            '</div>';
        }).join('');
    } catch (err) { console.error('loadNews error:', err); }
}

async function loadSentiment(cca2) {
    try {
        const res  = await fetch('/api/news/' + cca2 + '/sentiment');
        const data = await res.json();

        if (!data.success || !data.data || !data.data.total) {
            document.getElementById('sentiment-content').innerHTML =
                '<div style="color:var(--text-muted-custom);text-align:center;padding:20px 0;font-size:13px;">Belum ada data sentimen</div>';
            return;
        }

        var d = data.data;
        document.getElementById('sentiment-content').innerHTML =
            '<div style="display:flex;justify-content:space-between;margin-bottom:12px;">' +
                sentimentStat(d.positive, d.positive_pct, '#198754', 'Positif') +
                sentimentStat(d.neutral,  d.neutral_pct,  '#e6a700', 'Netral')  +
                sentimentStat(d.negative, d.negative_pct, '#dc3545', 'Negatif') +
            '</div>' +
            '<div style="font-size:11px;color:var(--text-muted-custom);text-align:center;">Total ' + d.total + ' artikel dianalisis</div>';

        if (sentimentChart) sentimentChart.destroy();
        sentimentChart = new Chart(document.getElementById('chart-sentiment').getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: ['Positif', 'Netral', 'Negatif'],
                datasets: [{
                    data: [d.positive, d.neutral, d.negative],
                    backgroundColor: ['#198754', '#e6a700', '#dc3545'],
                    borderWidth: 0,
                }]
            },
            options: {
                plugins: { legend: { labels: { color: '#4b4f66', font: { size: 11 } } } },
                cutout: '65%',
            }
        });
    } catch (err) { console.error('loadSentiment error:', err); }
}

async function loadRiskHistory(cca2) {
    try {
        const res  = await fetch('/api/risk/' + cca2 + '/history');
        const data = await res.json();
        if (!data.success || !data.data || !data.data.length) return;

        var items  = data.data.slice().reverse();
        var labels = items.map(function(i) {
            return new Date(i.calculated_at).toLocaleDateString('id-ID');
        });
        var scores = items.map(function(i) { return i.total_score; });

        if (riskChart) riskChart.destroy();
        riskChart = new Chart(document.getElementById('chart-risk').getContext('2d'), {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Risk Score',
                    data: scores,
                    borderColor: '#fd7e14',
                    backgroundColor: 'rgba(253,126,20,0.12)',
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#fd7e14',
                    pointRadius: 4,
                }]
            },
            options: chartOptions('Risk Score (0-100)')
        });
    } catch (err) { console.error('loadRiskHistory error:', err); }
}

// ===== HELPERS =====
function weatherStat(icon, label, value) {
    return '<div style="background:var(--dark-bg);border-radius:8px;padding:10px;text-align:center;">' +
        '<i class="bi ' + icon + '" style="color:#fd7e14;font-size:18px;"></i>' +
        '<div style="color:var(--text-main);font-weight:600;font-size:14px;margin-top:4px;">' + value + '</div>' +
        '<div style="color:var(--text-muted-custom);font-size:11px;">' + label + '</div>' +
    '</div>';
}

function sentimentStat(count, pct, color, label) {
    return '<div style="text-align:center;">' +
        '<div style="font-size:22px;font-weight:700;color:' + color + ';">' + count + '</div>' +
        '<div style="font-size:11px;color:var(--text-muted-custom);">' + label + ' (' + pct + '%)</div>' +
    '</div>';
}

function getRiskColor(score) {
    if (score <= 33) return '#198754';
    if (score <= 66) return '#e6a700';
    return '#dc3545';
}

function drawGauge(score, level) {
    var colors = { Low: '#198754', Medium: '#e6a700', High: '#dc3545' };
    var ctx = document.getElementById('risk-gauge').getContext('2d');
    if (window._gaugeChart) window._gaugeChart.destroy();
    window._gaugeChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            datasets: [{
                data: [score, 100 - score],
                backgroundColor: [colors[level] || '#fd7e14', 'rgba(31,35,51,0.08)'],
                borderWidth: 0,
                circumference: 180,
                rotation: 270,
            }]
        },
        options: { plugins: { legend: { display: false } }, cutout: '75%' }
    });
}

function chartOptions(yLabel) {
    return {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            x: { ticks: { color: '#6b7088', font: { size: 10 } }, grid: { color: 'rgba(31,35,51,0.06)' } },
            y: { ticks: { color: '#6b7088', font: { size: 10 } }, grid: { color: 'rgba(31,35,51,0.06)' } }
        }
    };
}

async function addToWatchlist() {
    var c = window.AppState.currentCountry;
    if (!c) return;
    try {
        var res  = await fetch('/api/watchlist', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ cca2: c.cca2 })
        });
        var data = await res.json();
        alert(data.success ? c.name + ' ditambahkan ke watchlist!' : data.message);
    } catch (err) { alert('Gagal menambahkan ke watchlist.'); }
}
</script>
@endpush