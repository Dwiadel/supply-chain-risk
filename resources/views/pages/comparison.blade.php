@extends('layouts.app')
@section('title', 'Perbandingan Negara')
@section('content')

<h5 style="color:var(--text-main);margin-bottom:20px;">
    <i class="bi bi-bar-chart-steps"></i> Country Comparison Engine
</h5>

<div class="row g-3 mb-4">
    {{-- Negara A --}}
    <div class="col-md-5">
        <div class="dash-card">
            <div class="dash-card-title">Negara A</div>
            <div style="position:relative;margin-top:10px;">
                <input type="text" id="search-a" placeholder="Ketik nama negara A..."
                       autocomplete="off"
                       style="width:100%;background:var(--dark-bg);border:1px solid var(--card-border);
                              color:var(--text-main);border-radius:8px;padding:9px 14px;font-size:14px;">
                <div id="results-a" style="display:none;position:absolute;top:calc(100% + 4px);left:0;right:0;
                     background:var(--card-bg);border:1px solid var(--card-border);border-radius:8px;
                     z-index:999;max-height:220px;overflow-y:auto;box-shadow:0 8px 24px rgba(31,35,51,0.12);"></div>
            </div>
            <div id="data-a" style="margin-top:15px;"></div>
        </div>
    </div>

    {{-- VS --}}
    <div class="col-md-2 d-flex align-items-center justify-content-center">
        <div style="text-align:center;">
            <div style="width:50px;height:50px;background:rgba(253,126,20,0.15);border:1px solid rgba(253,126,20,0.3);
                        border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto;">
                <i class="bi bi-arrow-left-right" style="color:var(--primary);font-size:18px;"></i>
            </div>
            <div style="color:var(--text-muted-custom);font-size:13px;font-weight:700;margin-top:8px;">VS</div>
        </div>
    </div>

    {{-- Negara B --}}
    <div class="col-md-5">
        <div class="dash-card">
            <div class="dash-card-title">Negara B</div>
            <div style="position:relative;margin-top:10px;">
                <input type="text" id="search-b" placeholder="Ketik nama negara B..."
                       autocomplete="off"
                       style="width:100%;background:var(--dark-bg);border:1px solid var(--card-border);
                              color:var(--text-main);border-radius:8px;padding:9px 14px;font-size:14px;">
                <div id="results-b" style="display:none;position:absolute;top:calc(100% + 4px);left:0;right:0;
                     background:var(--card-bg);border:1px solid var(--card-border);border-radius:8px;
                     z-index:999;max-height:220px;overflow-y:auto;box-shadow:0 8px 24px rgba(31,35,51,0.12);"></div>
            </div>
            <div id="data-b" style="margin-top:15px;"></div>
        </div>
    </div>
</div>

{{-- Grafik Perbandingan --}}
<div class="row g-3" id="chart-section" style="display:none!important;">
    <div class="col-md-6">
        <div class="dash-card">
            <div class="dash-card-title"><i class="bi bi-bar-chart-fill"></i> Perbandingan Risk Score</div>
            <div class="chart-container mt-3"><canvas id="chart-risk-compare"></canvas></div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="dash-card">
            <div class="dash-card-title"><i class="bi bi-graph-up"></i> Perbandingan GDP</div>
            <div class="chart-container mt-3"><canvas id="chart-gdp-compare"></canvas></div>
        </div>
    </div>
    <div class="col-md-12">
        <div class="dash-card">
            <div class="dash-card-title"><i class="bi bi-table"></i> Tabel Perbandingan Lengkap</div>
            <div id="compare-table" style="margin-top:15px;overflow-x:auto;"></div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
let countryA = null, countryB = null;
let riskA    = null, riskB    = null;
let chartRisk = null, chartGdp = null;

// Warna tim: Negara A pakai warna primary (brand), Negara B pakai biru sebagai pembeda
const COLOR_A = '#fd7e14';
const COLOR_B = '#0d6efd';
const COLOR_A_SOFT = 'rgba(253,126,20,0.7)';
const COLOR_B_SOFT = 'rgba(13,110,253,0.7)';

function setupSearch(inputId, resultsId, slot) {
    const input   = document.getElementById(inputId);
    const results = document.getElementById(resultsId);
    let timer     = null;

    input.addEventListener('input', function () {
        clearTimeout(timer);
        const q = this.value.trim();
        if (q.length < 2) { results.style.display = 'none'; return; }
        timer = setTimeout(() => doSearch(q, results, slot), 350);
    });

    document.addEventListener('click', function (e) {
        if (!e.target.closest('#' + inputId) && !e.target.closest('#' + resultsId)) {
            results.style.display = 'none';
        }
    });
}

async function doSearch(query, resultsEl, slot) {
    try {
        const res  = await fetch('/api/countries/search?q=' + encodeURIComponent(query));
        const data = await res.json();

        if (!data.success || !data.data.length) {
            resultsEl.innerHTML = '<div style="padding:12px;color:var(--text-muted-custom);font-size:13px;">Tidak ada hasil</div>';
            resultsEl.style.display = 'block';
            return;
        }

        resultsEl.innerHTML = data.data.map(function(c) {
            const flag = c.flag_url
                ? '<img src="' + c.flag_url + '" style="width:26px;height:17px;object-fit:cover;border-radius:2px;">'
                : '<i class="bi bi-flag"></i>';
            return '<div style="display:flex;align-items:center;gap:10px;padding:10px 14px;cursor:pointer;' +
                   'border-bottom:1px solid var(--card-border);" ' +
                   'onmouseenter="this.style.background=\'rgba(253,126,20,0.1)\'" ' +
                   'onmouseleave="this.style.background=\'\'" ' +
                   'onclick="selectForSlot(\'' + c.cca2 + '\', \'' + c.name.replace(/'/g, "\\'") + '\', \'' + slot + '\')">' +
                   flag +
                   '<div>' +
                       '<div style="color:var(--text-main);font-size:13px;font-weight:500;">' + c.name + '</div>' +
                       '<div style="color:var(--text-muted-custom);font-size:11px;">' + (c.region || '') + ' · ' + (c.currency_code || '') + '</div>' +
                   '</div></div>';
        }).join('');

        resultsEl.style.display = 'block';
    } catch(e) { console.error(e); }
}

async function selectForSlot(cca2, name, slot) {
    document.getElementById('results-' + slot).style.display = 'none';
    document.getElementById('search-' + slot).value = name;
    document.getElementById('data-' + slot).innerHTML =
        '<div style="color:var(--text-muted-custom);font-size:13px;padding:15px 0;">' +
        '<div class="loading-spinner" style="width:24px;height:24px;margin:0 auto 8px;"></div>Mengambil data...</div>';

    try {
        const res  = await fetch('/api/countries/' + cca2 + '/fetch-all', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
        });
        const data = await res.json();
        if (!data.success) throw new Error(data.message);

        const country = data.data;

        // Fetch risk breakdown
        const riskRes  = await fetch('/api/risk/' + cca2 + '/breakdown');
        const riskData = await riskRes.json();
        const risk     = riskData.success ? riskData.data : null;

        if (slot === 'a') { countryA = country; riskA = risk; }
        else              { countryB = country; riskB = risk; }

        renderSlotData(slot, country, risk);
        tryRenderComparison();

    } catch(e) {
        document.getElementById('data-' + slot).innerHTML =
            '<div style="color:#dc3545;font-size:13px;padding:10px 0;">Gagal: ' + e.message + '</div>';
    }
}

function renderSlotData(slot, country, risk) {
    const ind   = country.country_indicators && country.country_indicators.length
        ? country.country_indicators[country.country_indicators.length - 1] : null;
    const w     = country.weather_caches && country.weather_caches.length
        ? country.weather_caches[country.weather_caches.length - 1] : null;
    const level = risk ? risk.risk_level : '—';
    const score = risk ? risk.total_score : '—';
    const flag  = country.flag_url
        ? '<img src="' + country.flag_url + '" style="width:36px;border-radius:3px;">' : '';

    document.getElementById('data-' + slot).innerHTML =
        '<div style="display:flex;align-items:center;gap:10px;margin-bottom:14px;">' +
            flag +
            '<div>' +
                '<div style="color:var(--text-main);font-weight:700;font-size:15px;">' + country.name + '</div>' +
                '<div style="color:var(--text-muted-custom);font-size:11px;">' + (country.capital || '') + ' · ' + (country.region || '') + '</div>' +
            '</div>' +
        '</div>' +
        statRow('Risk Score', score + ' <span class="risk-badge risk-' + (level ? level.toLowerCase() : 'low') + '" style="font-size:10px;padding:2px 8px;">' + level + '</span>') +
        statRow('GDP', ind && ind.gdp ? 'USD ' + formatNumber(ind.gdp) : 'N/A') +
        statRow('Inflasi', ind && ind.inflation_rate !== null ? ind.inflation_rate + '%' : 'N/A') +
        statRow('Populasi', ind && ind.population ? formatNumber(ind.population) : 'N/A') +
        statRow('Suhu', w ? w.temperature + '°C — ' + (w.weather_description || '') : 'N/A') +
        statRow('Mata Uang', country.currency_code || '—');
}

function statRow(label, value) {
    return '<div style="display:flex;justify-content:space-between;align-items:center;' +
           'padding:8px 0;border-bottom:1px solid var(--card-border);">' +
               '<span style="color:var(--text-muted-custom);font-size:13px;">' + label + '</span>' +
               '<span style="color:var(--text-main);font-size:13px;font-weight:500;">' + value + '</span>' +
           '</div>';
}

function tryRenderComparison() {
    if (!countryA || !countryB) return;

    document.getElementById('chart-section').style.removeProperty('display');

    // Chart Risk Score
    const riskScoreA = riskA ? riskA.total_score : 0;
    const riskScoreB = riskB ? riskB.total_score : 0;

    if (chartRisk) chartRisk.destroy();
    chartRisk = new Chart(document.getElementById('chart-risk-compare'), {
        type: 'bar',
        data: {
            labels: ['Weather Risk', 'Inflation Risk', 'Currency Risk', 'News Risk', 'Total Score'],
            datasets: [
                {
                    label: countryA.name,
                    data: riskA ? [
                        riskA.components.weather.raw_score,
                        riskA.components.inflation.raw_score,
                        riskA.components.currency.raw_score,
                        riskA.components.news.raw_score,
                        riskA.total_score,
                    ] : [0,0,0,0,0],
                    backgroundColor: COLOR_A_SOFT,
                    borderColor: COLOR_A, borderWidth: 1,
                },
                {
                    label: countryB.name,
                    data: riskB ? [
                        riskB.components.weather.raw_score,
                        riskB.components.inflation.raw_score,
                        riskB.components.currency.raw_score,
                        riskB.components.news.raw_score,
                        riskB.total_score,
                    ] : [0,0,0,0,0],
                    backgroundColor: COLOR_B_SOFT,
                    borderColor: COLOR_B, borderWidth: 1,
                }
            ]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { labels: { color: '#6b7088' } } },
            scales: {
                x: { ticks: { color: '#6b7088' }, grid: { color: 'rgba(31,35,51,0.06)' } },
                y: { ticks: { color: '#6b7088' }, grid: { color: 'rgba(31,35,51,0.06)' }, max: 100 }
            }
        }
    });

    // Chart GDP
    const indA = countryA.country_indicators && countryA.country_indicators.length
        ? countryA.country_indicators[countryA.country_indicators.length - 1] : null;
    const indB = countryB.country_indicators && countryB.country_indicators.length
        ? countryB.country_indicators[countryB.country_indicators.length - 1] : null;

    if (chartGdp) chartGdp.destroy();
    chartGdp = new Chart(document.getElementById('chart-gdp-compare'), {
        type: 'bar',
        data: {
            labels: ['GDP (Triliun USD)', 'Inflasi (%)', 'Populasi (Juta)'],
            datasets: [
                {
                    label: countryA.name,
                    data: [
                        indA && indA.gdp ? (indA.gdp / 1e12).toFixed(2) : 0,
                        indA && indA.inflation_rate ? indA.inflation_rate : 0,
                        indA && indA.population ? (indA.population / 1e6).toFixed(1) : 0,
                    ],
                    backgroundColor: 'rgba(25,135,84,0.7)',
                    borderColor: '#198754', borderWidth: 1,
                },
                {
                    label: countryB.name,
                    data: [
                        indB && indB.gdp ? (indB.gdp / 1e12).toFixed(2) : 0,
                        indB && indB.inflation_rate ? indB.inflation_rate : 0,
                        indB && indB.population ? (indB.population / 1e6).toFixed(1) : 0,
                    ],
                    backgroundColor: 'rgba(255,193,7,0.7)',
                    borderColor: '#ffc107', borderWidth: 1,
                }
            ]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { labels: { color: '#6b7088' } } },
            scales: {
                x: { ticks: { color: '#6b7088' }, grid: { color: 'rgba(31,35,51,0.06)' } },
                y: { ticks: { color: '#6b7088' }, grid: { color: 'rgba(31,35,51,0.06)' } }
            }
        }
    });

    // Tabel perbandingan lengkap
    const indAs = countryA.country_indicators && countryA.country_indicators.length
        ? countryA.country_indicators[countryA.country_indicators.length - 1] : {};
    const indBs = countryB.country_indicators && countryB.country_indicators.length
        ? countryB.country_indicators[countryB.country_indicators.length - 1] : {};

    const rows = [
        ['Risk Score',   riskA ? riskA.total_score : '—',                       riskB ? riskB.total_score : '—',                       'lower'],
        ['Risk Level',   riskA ? riskA.risk_level : '—',                         riskB ? riskB.risk_level : '—',                         null],
        ['GDP (USD)',     indAs.gdp ? 'USD ' + formatNumber(indAs.gdp) : '—',    indBs.gdp ? 'USD ' + formatNumber(indBs.gdp) : '—',    'higher'],
        ['Inflasi (%)',  indAs.inflation_rate || '—',                             indBs.inflation_rate || '—',                             'lower'],
        ['Populasi',     indAs.population ? formatNumber(indAs.population) : '—', indBs.population ? formatNumber(indBs.population) : '—', 'higher'],
        ['Wilayah',      countryA.region || '—',                                  countryB.region || '—',                                  null],
        ['Mata Uang',    countryA.currency_code || '—',                           countryB.currency_code || '—',                           null],
        ['Ibu Kota',     countryA.capital || '—',                                 countryB.capital || '—',                                 null],
    ];

    const flagA = countryA.flag_url ? '<img src="' + countryA.flag_url + '" style="width:20px;border-radius:2px;margin-right:6px;">' : '';
    const flagB = countryB.flag_url ? '<img src="' + countryB.flag_url + '" style="width:20px;border-radius:2px;margin-right:6px;">' : '';

    document.getElementById('compare-table').innerHTML =
        '<table style="width:100%;border-collapse:collapse;">' +
            '<thead>' +
                '<tr style="border-bottom:2px solid var(--card-border);">' +
                    '<th style="padding:10px;color:var(--text-muted-custom);font-size:12px;text-align:left;width:30%;">Indikator</th>' +
                    '<th style="padding:10px;color:' + COLOR_A + ';font-size:13px;text-align:center;">' + flagA + countryA.name + '</th>' +
                    '<th style="padding:10px;color:' + COLOR_B + ';font-size:13px;text-align:center;">' + flagB + countryB.name + '</th>' +
                '</tr>' +
            '</thead>' +
            '<tbody>' +
                rows.map(function(r) {
                    return '<tr style="border-bottom:1px solid var(--card-border);">' +
                        '<td style="padding:10px;color:var(--text-muted-custom);font-size:13px;">' + r[0] + '</td>' +
                        '<td style="padding:10px;color:var(--text-main);font-size:13px;text-align:center;font-weight:500;">' + r[1] + '</td>' +
                        '<td style="padding:10px;color:var(--text-main);font-size:13px;text-align:center;font-weight:500;">' + r[2] + '</td>' +
                    '</tr>';
                }).join('') +
            '</tbody>' +
        '</table>';
}

setupSearch('search-a', 'results-a', 'a');
setupSearch('search-b', 'results-b', 'b');
</script>
@endpush