@extends('layouts.app')
@section('title', 'Risk Scoring Engine')
@section('content')

<h5 style="color:var(--text-main);margin-bottom:5px;">
    <i class="bi bi-speedometer2"></i> Risk Scoring Engine
</h5>
<p style="color:var(--text-muted-custom);font-size:13px;margin-bottom:20px;">
    Algoritma weighted scoring dengan normalisasi data. Formula:
    <code style="background:var(--dark-bg);padding:2px 8px;border-radius:4px;color:#e8590c;">
        Total = (Cuaca×0.30) + (Inflasi×0.25) + (Kurs×0.20) + (Berita×0.25)
    </code>
</p>

<div id="risk-welcome" style="text-align:center;padding:40px 20px;">
    <i class="bi bi-speedometer2" style="font-size:48px;color:#fd7e14;opacity:0.5;display:block;margin-bottom:15px;"></i>
    <p style="color:var(--text-muted-custom);">Pilih negara dari search box di atas untuk menghitung risk score</p>
    <div style="display:flex;gap:10px;justify-content:center;flex-wrap:wrap;margin-top:15px;">
        @foreach(['Germany','Indonesia','China','Australia','Japan'] as $c)
        @php $code = \App\Models\Country::where('name',$c)->value('cca2') ?? ''; @endphp
        @if($code)
        <button onclick="selectCountry('{{ $code }}','{{ $c }}')"
                style="background:var(--card-bg);border:1px solid var(--card-border);color:var(--text-main);
                       padding:6px 14px;border-radius:6px;cursor:pointer;font-size:13px;">
            {{ $c }}
        </button>
        @endif
        @endforeach
    </div>
</div>

<div id="risk-content" style="display:none;">

    {{-- Row 1: Skor Utama + Breakdown --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="dash-card" style="text-align:center;">
                <div class="dash-card-title"><i class="bi bi-speedometer2"></i> Total Risk Score</div>
                <div id="r-country-name" style="color:var(--text-muted-custom);font-size:13px;margin-top:5px;"></div>
                <div id="r-total" style="font-size:80px;font-weight:900;color:var(--text-main);line-height:1;margin:15px 0;">—</div>
                <div id="r-level-badge" class="risk-badge risk-low"
                     style="margin:0 auto;width:fit-content;font-size:15px;padding:8px 24px;">
                    <i class="bi bi-shield-check"></i>
                    <span id="r-level">—</span>
                </div>
                <canvas id="gauge-chart" height="100" style="margin-top:10px;max-width:220px;"></canvas>
                <small style="color:var(--text-muted-custom);display:block;margin-top:5px;">
                    Skala 0–100 · Semakin tinggi = semakin berisiko
                </small>
            </div>
        </div>

        <div class="col-md-8">
            <div class="dash-card">
                <div class="dash-card-title"><i class="bi bi-list-check"></i> Breakdown Per Komponen</div>
                <div id="r-breakdown" style="margin-top:15px;">
                    <div style="color:var(--text-muted-custom);text-align:center;padding:20px;">Memuat...</div>
                </div>
                <div style="margin-top:15px;padding:12px;background:var(--dark-bg);border-radius:8px;">
                    <div style="color:var(--text-muted-custom);font-size:10px;text-transform:uppercase;letter-spacing:0.8px;margin-bottom:6px;">
                        Formula Perhitungan
                    </div>
                    <div id="r-formula" style="color:#e8590c;font-size:12px;font-family:monospace;"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Row 2: Radar + Kontribusi --}}
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="dash-card">
                <div class="dash-card-title"><i class="bi bi-diagram-3-fill"></i> Radar Chart Komponen Risiko</div>
                <div style="height:280px;position:relative;margin-top:10px;">
                    <canvas id="radar-chart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="dash-card">
                <div class="dash-card-title"><i class="bi bi-bar-chart-fill"></i> Kontribusi Per Komponen</div>
                <div style="height:280px;position:relative;margin-top:10px;">
                    <canvas id="contrib-chart"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Row 3: History Chart + Interpretasi --}}
    <div class="row g-3 mb-4">
        <div class="col-md-8">
            <div class="dash-card">
                <div class="dash-card-title"><i class="bi bi-activity"></i> Riwayat Risk Score</div>
                <div style="height:220px;position:relative;margin-top:10px;">
                    <canvas id="history-chart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="dash-card">
                <div class="dash-card-title"><i class="bi bi-info-circle-fill"></i> Interpretasi Risiko</div>
                <div style="margin-top:15px;display:flex;flex-direction:column;gap:10px;">
                    <div style="padding:12px;background:rgba(25,135,84,0.1);border:1px solid rgba(25,135,84,0.3);border-radius:8px;">
                        <div style="color:#198754;font-weight:700;font-size:13px;">
                            <i class="bi bi-shield-check"></i> Low Risk (0–33)
                        </div>
                        <div style="color:var(--text-muted-custom);font-size:12px;margin-top:4px;">
                            Kondisi rantai pasok stabil. Risiko pengiriman minimal.
                        </div>
                    </div>
                    <div style="padding:12px;background:rgba(255,193,7,0.12);border:1px solid rgba(255,193,7,0.3);border-radius:8px;">
                        <div style="color:#c98a00;font-weight:700;font-size:13px;">
                            <i class="bi bi-shield-exclamation"></i> Medium Risk (34–66)
                        </div>
                        <div style="color:var(--text-muted-custom);font-size:12px;margin-top:4px;">
                            Perlu pemantauan. Beberapa faktor risiko perlu diperhatikan.
                        </div>
                    </div>
                    <div style="padding:12px;background:rgba(220,53,69,0.1);border:1px solid rgba(220,53,69,0.3);border-radius:8px;">
                        <div style="color:#dc3545;font-weight:700;font-size:13px;">
                            <i class="bi bi-shield-x"></i> High Risk (67–100)
                        </div>
                        <div style="color:var(--text-muted-custom);font-size:12px;margin-top:4px;">
                            Risiko tinggi. Pertimbangkan rute alternatif atau tunda pengiriman.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabel History --}}
    <div class="row g-3">
        <div class="col-12">
            <div class="dash-card">
                <div class="dash-card-title"><i class="bi bi-table"></i> Tabel Riwayat Kalkulasi</div>
                <div id="r-history-table" style="margin-top:15px;max-height:300px;overflow-y:auto;"></div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
let gaugeChart = null, radarChart = null, contribChart = null, historyChart = null;

// ===== LOAD DATA SAAT HALAMAN DIBUKA =====
// Kalau ada negara yang sudah dipilih sebelumnya (dari AppState),
// langsung load tanpa perlu pilih ulang
document.addEventListener('DOMContentLoaded', function() {
    var currentCountry = window.AppState && window.AppState.currentCountry;
    if (currentCountry && currentCountry.cca2) {
        loadRiskData(currentCountry.cca2, currentCountry);
    }
});

// ===== EVENT LISTENER untuk negara baru dipilih =====
window.addEventListener('countrySelected', function(e) {
    var cca2    = e.detail.cca2;
    var country = e.detail.country;
    loadRiskData(cca2, country);
});

// ===== FUNGSI UTAMA LOAD RISK DATA =====
async function loadRiskData(cca2, country) {
    document.getElementById('risk-welcome').style.display = 'none';
    document.getElementById('risk-content').style.display = 'block';

    if (country && country.name) {
        document.getElementById('r-country-name').textContent = country.name;
    }

    // Load breakdown
    try {
        var res  = await fetch('/api/risk/' + cca2 + '/breakdown');
        var data = await res.json();
        if (!data.success) return;

        var score  = data.data.total_score;
        var level  = data.data.risk_level;
        var comps  = data.data.components;
        var formula = data.data.formula;

        // Update score & level
        document.getElementById('r-total').textContent = score;
        document.getElementById('r-level').textContent = level + ' Risk';
        document.getElementById('r-level-badge').className = 'risk-badge risk-' + level.toLowerCase();
        document.getElementById('r-level-badge').style.cssText = 'margin:0 auto;width:fit-content;font-size:15px;padding:8px 24px;';
        document.getElementById('r-formula').textContent = formula || 'Total = (Cuaca×0.30) + (Inflasi×0.25) + (Kurs×0.20) + (Berita×0.25)';

        // Warna total score
        document.getElementById('r-total').style.color = getColor(score);

        // Breakdown bars
        var html = '';
        var labels = [], rawScores = [], contributions = [];

        Object.entries(comps).forEach(function(entry) {
            var key = entry[0], c = entry[1];
            labels.push(c.label);
            rawScores.push(c.raw_score);
            contributions.push(c.contribution);

            html += '<div style="margin-bottom:18px;">' +
                '<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px;">' +
                    '<div>' +
                        '<span style="color:var(--text-main);font-weight:600;font-size:13px;">' + c.label + '</span>' +
                        '<span style="color:var(--text-muted-custom);font-size:11px;margin-left:8px;">Bobot ' + (c.weight*100).toFixed(0) + '%</span>' +
                    '</div>' +
                    '<div style="text-align:right;">' +
                        '<span style="color:' + getColor(c.raw_score) + ';font-weight:700;font-size:16px;">' + c.raw_score.toFixed(1) + '</span>' +
                        '<span style="color:var(--text-muted-custom);font-size:11px;"> / 100</span>' +
                    '</div>' +
                '</div>' +
                '<div style="height:12px;background:var(--dark-bg);border-radius:6px;overflow:hidden;margin-bottom:4px;">' +
                    '<div style="height:100%;width:' + c.raw_score + '%;background:' + getColor(c.raw_score) + ';' +
                    'border-radius:6px;transition:width 1s ease;"></div>' +
                '</div>' +
                '<div style="display:flex;justify-content:space-between;">' +
                    '<small style="color:var(--text-muted-custom);">Kontribusi ke total: <b style="color:var(--text-main);">' + c.contribution.toFixed(2) + '</b></small>' +
                    '<small style="color:' + getColor(c.raw_score) + ';font-weight:600;">' + getRiskLabel(c.raw_score) + '</small>' +
                '</div>' +
            '</div>';
        });
        document.getElementById('r-breakdown').innerHTML = html;

        // Gauge chart
        if (gaugeChart) gaugeChart.destroy();
        gaugeChart = new Chart(document.getElementById('gauge-chart'), {
            type: 'doughnut',
            data: {
                datasets: [{
                    data: [score, 100 - score],
                    backgroundColor: [getColor(score), 'rgba(31,35,51,0.08)'],
                    borderWidth: 0,
                    circumference: 180,
                    rotation: 270,
                }]
            },
            options: { plugins: { legend: { display: false } }, cutout: '75%' }
        });

        // Radar chart
        if (radarChart) radarChart.destroy();
        radarChart = new Chart(document.getElementById('radar-chart'), {
            type: 'radar',
            data: {
                labels: labels,
                datasets: [{
                    label: country ? country.name : cca2,
                    data: rawScores,
                    borderColor: '#fd7e14',
                    backgroundColor: 'rgba(253,126,20,0.18)',
                    pointBackgroundColor: '#fd7e14',
                    pointRadius: 5,
                }]
            },
            options: {
                scales: {
                    r: {
                        min: 0, max: 100,
                        ticks: { color: '#6b7088', font: { size: 10 }, stepSize: 25 },
                        grid: { color: 'rgba(31,35,51,0.08)' },
                        pointLabels: { color: '#4b4f66', font: { size: 11 } },
                        angleLines: { color: 'rgba(31,35,51,0.12)' },
                    }
                },
                plugins: { legend: { labels: { color: '#4b4f66' } } }
            }
        });

        // Contribution bar chart
        if (contribChart) contribChart.destroy();
        contribChart = new Chart(document.getElementById('contrib-chart'), {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Kontribusi ke Total Score',
                    data: contributions,
                    backgroundColor: ['rgba(253,126,20,0.75)', 'rgba(37,181,116,0.7)', 'rgba(255,193,7,0.7)', 'rgba(220,53,69,0.7)'],
                    borderWidth: 0,
                    borderRadius: 4,
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false, indexAxis: 'y',
                plugins: { legend: { display: false } },
                scales: {
                    x: { ticks: { color: '#6b7088' }, grid: { color: 'rgba(31,35,51,0.06)' } },
                    y: { ticks: { color: '#6b7088' }, grid: { color: 'rgba(31,35,51,0.06)' } }
                }
            }
        });

    } catch(e) { console.error('Risk breakdown error:', e); }

    // Load history
    try {
        var res2  = await fetch('/api/risk/' + cca2 + '/history');
        var data2 = await res2.json();
        if (!data2.success || !data2.data.length) return;

        var items   = data2.data.slice().reverse();
        var hlabels = items.map(function(i) {
            return new Date(i.calculated_at).toLocaleString('id-ID', { day:'2-digit', month:'short', hour:'2-digit', minute:'2-digit' });
        });
        var hscores = items.map(function(i) { return i.total_score; });

        if (historyChart) historyChart.destroy();
        historyChart = new Chart(document.getElementById('history-chart'), {
            type: 'line',
            data: {
                labels: hlabels,
                datasets: [{
                    label: 'Risk Score',
                    data: hscores,
                    borderColor: '#fd7e14',
                    backgroundColor: 'rgba(253,126,20,0.12)',
                    fill: true, tension: 0.4,
                    pointBackgroundColor: hscores.map(function(s) { return getColor(s); }),
                    pointRadius: 5,
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { ticks: { color: '#6b7088', maxTicksLimit: 8, font: { size: 10 } }, grid: { color: 'rgba(31,35,51,0.06)' } },
                    y: { min: 0, max: 100, ticks: { color: '#6b7088' }, grid: { color: 'rgba(31,35,51,0.06)' } }
                }
            }
        });

        // Tabel history
        document.getElementById('r-history-table').innerHTML =
            '<table style="width:100%;border-collapse:collapse;">' +
            '<thead><tr style="border-bottom:2px solid var(--card-border);">' +
                '<th style="padding:8px;color:var(--text-muted-custom);font-size:11px;text-align:left;">Waktu Kalkulasi</th>' +
                '<th style="padding:8px;color:var(--text-muted-custom);font-size:11px;text-align:center;">Cuaca</th>' +
                '<th style="padding:8px;color:var(--text-muted-custom);font-size:11px;text-align:center;">Inflasi</th>' +
                '<th style="padding:8px;color:var(--text-muted-custom);font-size:11px;text-align:center;">Kurs</th>' +
                '<th style="padding:8px;color:var(--text-muted-custom);font-size:11px;text-align:center;">Berita</th>' +
                '<th style="padding:8px;color:var(--text-muted-custom);font-size:11px;text-align:center;">Total</th>' +
                '<th style="padding:8px;color:var(--text-muted-custom);font-size:11px;text-align:center;">Level</th>' +
            '</tr></thead><tbody>' +
            data2.data.map(function(r) {
                return '<tr style="border-bottom:1px solid var(--card-border);">' +
                    '<td style="padding:8px;color:#4b4f66;font-size:12px;">' + new Date(r.calculated_at).toLocaleString('id-ID') + '</td>' +
                    '<td style="padding:8px;text-align:center;font-size:12px;color:var(--text-main);">' + r.weather_score + '</td>' +
                    '<td style="padding:8px;text-align:center;font-size:12px;color:var(--text-main);">' + r.inflation_score + '</td>' +
                    '<td style="padding:8px;text-align:center;font-size:12px;color:var(--text-main);">' + r.currency_score + '</td>' +
                    '<td style="padding:8px;text-align:center;font-size:12px;color:var(--text-main);">' + r.news_sentiment_score + '</td>' +
                    '<td style="padding:8px;text-align:center;font-weight:700;color:' + getColor(r.total_score) + ';font-size:14px;">' + r.total_score + '</td>' +
                    '<td style="padding:8px;text-align:center;">' +
                        '<span class="risk-badge risk-' + r.risk_level.toLowerCase() + '" style="font-size:10px;padding:2px 8px;">' + r.risk_level + '</span>' +
                    '</td>' +
                '</tr>';
            }).join('') +
            '</tbody></table>';

    } catch(e) { console.error('Risk history error:', e); }
}

function getColor(score) {
    if (score <= 33) return '#198754';
    if (score <= 66) return '#c98a00';
    return '#dc3545';
}

function getRiskLabel(score) {
    if (score <= 33) return 'Low';
    if (score <= 66) return 'Medium';
    return 'High';
}
</script>
@endpush