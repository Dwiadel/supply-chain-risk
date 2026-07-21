@extends('layouts.app')
@section('title', 'Data Visualization Dashboard')
@section('content')

<h5 style="color:var(--text-main);margin-bottom:5px;">
    <i class="bi bi-graph-up"></i> Data Visualization Dashboard
</h5>
<p style="color:var(--text-muted-custom);font-size:13px;margin-bottom:20px;">
    Grafik trend GDP, Inflasi, Kurs, dan Risk Score dari waktu ke waktu.
</p>

<div id="viz-welcome" style="text-align:center;padding:40px 20px;">
    <i class="bi bi-bar-chart-line" style="font-size:48px;color:#fd7e14;opacity:0.5;display:block;margin-bottom:15px;"></i>
    <p style="color:var(--text-muted-custom);">Pilih negara dari search box di atas untuk melihat visualisasi data</p>
</div>

<div id="viz-content" style="display:none;">

    {{-- Row 1: GDP + Inflasi Trend --}}
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="dash-card">
                <div class="dash-card-title"><i class="bi bi-graph-up-arrow"></i> GDP Trend (Miliar USD)</div>
                <div style="height:250px;position:relative;margin-top:10px;">
                    <canvas id="v-gdp-chart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="dash-card">
                <div class="dash-card-title"><i class="bi bi-percent"></i> Inflasi Trend (%)</div>
                <div style="height:250px;position:relative;margin-top:10px;">
                    <canvas id="v-inflation-chart"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Row 2: Currency + Risk Trend --}}
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="dash-card">
                <div class="dash-card-title"><i class="bi bi-currency-exchange"></i> Currency Trend (vs USD)</div>
                <div style="height:250px;position:relative;margin-top:10px;">
                    <canvas id="v-currency-chart"></canvas>
                </div>
                <div id="v-currency-note" style="margin-top:10px;font-size:12px;color:var(--text-muted-custom);text-align:center;"></div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="dash-card">
                <div class="dash-card-title"><i class="bi bi-activity"></i> Risk Score Trend</div>
                <div style="height:250px;position:relative;margin-top:10px;">
                    <canvas id="v-risk-chart"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Row 3: Summary Stats --}}
    <div class="row g-3">
        <div class="col-md-3 col-6">
            <div class="dash-card" style="text-align:center;">
                <div class="dash-card-title">GDP Tertinggi</div>
                <div class="dash-card-value" id="v-gdp-max" style="font-size:20px;">—</div>
                <div class="dash-card-sub" id="v-gdp-max-year">—</div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="dash-card" style="text-align:center;">
                <div class="dash-card-title">Inflasi Rata-rata</div>
                <div class="dash-card-value" id="v-inf-avg" style="font-size:20px;">—</div>
                <div class="dash-card-sub">Per tahun</div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="dash-card" style="text-align:center;">
                <div class="dash-card-title">Risk Score Rata-rata</div>
                <div class="dash-card-value" id="v-risk-avg" style="font-size:20px;">—</div>
                <div class="dash-card-sub">Dari semua kalkulasi</div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="dash-card" style="text-align:center;">
                <div class="dash-card-title">Total Data Poin</div>
                <div class="dash-card-value" id="v-data-points" style="font-size:20px;">—</div>
                <div class="dash-card-sub">Semua indikator</div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
let vGdpChart = null, vInflChart = null, vCurChart = null, vRiskChart = null;

window.addEventListener('countrySelected', async function(e) {
    const cca2    = e.detail.cca2;
    const country = e.detail.country;

    document.getElementById('viz-welcome').style.display = 'none';
    document.getElementById('viz-content').style.display = 'block';

    const indicators = country.country_indicators || [];
    const sorted     = indicators.slice().sort(function(a, b) { return a.year - b.year; });

    // GDP Chart
    var gdpLabels = sorted.map(function(i) { return i.year; });
    var gdpValues = sorted.map(function(i) { return i.gdp ? parseFloat(i.gdp) / 1e9 : null; });

    if (vGdpChart) vGdpChart.destroy();
    vGdpChart = new Chart(document.getElementById('v-gdp-chart'), {
        type: 'line',
        data: {
            labels: gdpLabels,
            datasets: [{
                label: 'GDP (Miliar USD)',
                data: gdpValues,
                borderColor: '#25b574',
                backgroundColor: 'rgba(37,181,116,0.15)',
                fill: true, tension: 0.4,
                pointBackgroundColor: '#25b574', pointRadius: 5,
                spanGaps: true,
            }]
        },
        options: chartOpts()
    });

    // GDP summary
    var validGdp = sorted.filter(function(i) { return i.gdp; });
    if (validGdp.length) {
        var maxGdp = validGdp.reduce(function(a, b) { return parseFloat(a.gdp) > parseFloat(b.gdp) ? a : b; });
        document.getElementById('v-gdp-max').textContent = 'USD ' + (parseFloat(maxGdp.gdp) / 1e12).toFixed(2) + ' T';
        document.getElementById('v-gdp-max-year').textContent = 'Tahun ' + maxGdp.year;
    }

    // Inflation Chart
    var infLabels = sorted.map(function(i) { return i.year; });
    var infValues = sorted.map(function(i) { return i.inflation_rate ? parseFloat(i.inflation_rate) : null; });

    if (vInflChart) vInflChart.destroy();
    vInflChart = new Chart(document.getElementById('v-inflation-chart'), {
        type: 'bar',
        data: {
            labels: infLabels,
            datasets: [{
                label: 'Inflasi (%)',
                data: infValues,
                backgroundColor: infValues.map(function(v) {
                    if (!v) return 'rgba(107,112,136,0.4)';
                    return v > 5 ? 'rgba(220,53,69,0.7)' : v < 0 ? 'rgba(255,193,7,0.7)' : 'rgba(253,126,20,0.75)';
                }),
                borderRadius: 4, borderWidth: 0,
            }]
        },
        options: chartOpts()
    });

    // Inflation summary
    var validInf = infValues.filter(function(v) { return v !== null; });
    if (validInf.length) {
        var avg = validInf.reduce(function(a, b) { return a + b; }, 0) / validInf.length;
        document.getElementById('v-inf-avg').textContent = avg.toFixed(2) + '%';
    }

    // Data points
    document.getElementById('v-data-points').textContent = sorted.length;

    // Currency Chart
    if (country.currency_code) {
        try {
            var res  = await fetch('/api/currency/' + country.currency_code + '/history');
            var data = await res.json();

            if (data.success && data.data.length) {
                var curData   = data.data.slice().reverse();
                var curLabels = curData.map(function(r) { return r.rate_date; });
                var curValues = curData.map(function(r) { return parseFloat(r.rate); });

                if (vCurChart) vCurChart.destroy();
                vCurChart = new Chart(document.getElementById('v-currency-chart'), {
                    type: 'line',
                    data: {
                        labels: curLabels,
                        datasets: [{
                            label: '1 USD → ' + country.currency_code,
                            data: curValues,
                            borderColor: '#c98a00',
                            backgroundColor: 'rgba(255,193,7,0.15)',
                            fill: true, tension: 0.3,
                            pointRadius: 2, pointBackgroundColor: '#c98a00',
                        }]
                    },
                    options: chartOpts()
                });
                document.getElementById('v-currency-note').textContent =
                    'Menampilkan ' + curData.length + ' hari terakhir · 1 USD = ' + curValues[curValues.length-1].toLocaleString() + ' ' + country.currency_code;
            } else {
                document.getElementById('v-currency-chart').parentElement.innerHTML =
                    '<div style="color:var(--text-muted-custom);text-align:center;padding:60px 0;">Belum ada data kurs. Konfigurasi EXCHANGE_RATE_API_KEY.</div>';
            }
        } catch(e) {}
    }

    // Risk Chart
    try {
        var res  = await fetch('/api/risk/' + cca2 + '/history');
        var data = await res.json();

        if (data.success && data.data.length) {
            var rItems  = data.data.slice().reverse();
            var rLabels = rItems.map(function(i) { return new Date(i.calculated_at).toLocaleDateString('id-ID'); });
            var rScores = rItems.map(function(i) { return i.total_score; });

            if (vRiskChart) vRiskChart.destroy();
            vRiskChart = new Chart(document.getElementById('v-risk-chart'), {
                type: 'line',
                data: {
                    labels: rLabels,
                    datasets: [{
                        label: 'Risk Score',
                        data: rScores,
                        borderColor: '#fd7e14',
                        backgroundColor: 'rgba(253,126,20,0.12)',
                        fill: true, tension: 0.4,
                        pointBackgroundColor: rScores.map(function(s) {
                            return s <= 33 ? '#198754' : s <= 66 ? '#c98a00' : '#dc3545';
                        }),
                        pointRadius: 5,
                    }]
                },
                options: Object.assign(chartOpts(), {
                    scales: {
                        x: { ticks: { color: '#6b7088', maxTicksLimit: 8, font: { size: 10 } }, grid: { color: 'rgba(31,35,51,0.06)' } },
                        y: { min: 0, max: 100, ticks: { color: '#6b7088' }, grid: { color: 'rgba(31,35,51,0.06)' } }
                    }
                })
            });

            var avgRisk = rScores.reduce(function(a, b) { return a + b; }, 0) / rScores.length;
            document.getElementById('v-risk-avg').textContent = avgRisk.toFixed(1);
        }
    } catch(e) {}
});

function chartOpts() {
    return {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { labels: { color: '#4b4f66', font: { size: 11 } } } },
        scales: {
            x: { ticks: { color: '#6b7088', font: { size: 10 } }, grid: { color: 'rgba(31,35,51,0.06)' } },
            y: { ticks: { color: '#6b7088', font: { size: 10 } }, grid: { color: 'rgba(31,35,51,0.06)' } }
        }
    };
}
</script>
@endpush