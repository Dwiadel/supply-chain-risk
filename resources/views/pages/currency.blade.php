@extends('layouts.app')
@section('title', 'Kurs Mata Uang')
@section('content')

<h5 style="color:var(--text-main);margin-bottom:20px;">
    <i class="bi bi-currency-exchange"></i> Currency Impact Dashboard
</h5>

<div id="currency-welcome" style="text-align:center;padding:60px 20px;">
    <i class="bi bi-currency-exchange" style="font-size:48px;color:var(--primary);opacity:0.5;display:block;margin-bottom:15px;"></i>
    <p style="color:var(--text-muted-custom);">Pilih negara dari search box di atas untuk melihat data kurs</p>
</div>

<div id="currency-content" style="display:none;">
    {{-- KPI Kurs --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="dash-card">
                <div class="dash-card-title"><i class="bi bi-currency-dollar"></i> Base Currency</div>
                <div class="dash-card-value">USD</div>
                <div class="dash-card-sub">US Dollar (Referensi)</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="dash-card">
                <div class="dash-card-title"><i class="bi bi-translate"></i> Target Currency</div>
                <div class="dash-card-value" id="cur-code">—</div>
                <div class="dash-card-sub" id="cur-name">—</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="dash-card">
                <div class="dash-card-title"><i class="bi bi-arrow-left-right"></i> Kurs Saat Ini</div>
                <div class="dash-card-value" id="cur-rate">—</div>
                <div class="dash-card-sub">per 1 USD</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="dash-card">
                <div class="dash-card-title"><i class="bi bi-activity"></i> Perubahan Harian</div>
                <div class="dash-card-value" id="cur-change">—</div>
                <div class="dash-card-sub" id="cur-change-label">vs hari sebelumnya</div>
            </div>
        </div>
    </div>

    {{-- Grafik --}}
    <div class="row g-3 mb-4">
        <div class="col-md-8">
            <div class="dash-card">
                <div class="dash-card-title"><i class="bi bi-graph-up"></i> Trend Kurs (30 Hari Terakhir)</div>
                <div class="chart-container mt-3">
                    <canvas id="chart-currency-trend"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="dash-card">
                <div class="dash-card-title"><i class="bi bi-info-circle"></i> Info Mata Uang</div>
                <div id="currency-info" style="margin-top:15px;"></div>
            </div>
        </div>
    </div>

    {{-- Tabel History --}}
    <div class="row g-3">
        <div class="col-12">
            <div class="dash-card">
                <div class="dash-card-title"><i class="bi bi-table"></i> Riwayat Kurs</div>
                <div id="currency-history-table" style="margin-top:15px;max-height:300px;overflow-y:auto;"></div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
let currencyChart = null;

window.addEventListener('countrySelected', async function(e) {
    const country = e.detail.country;
    if (!country.currency_code) return;

    document.getElementById('currency-welcome').style.display = 'none';
    document.getElementById('currency-content').style.display = 'block';

    const currency = country.currency_code;

    document.getElementById('cur-code').textContent = currency;
    document.getElementById('cur-name').textContent = country.currency_name || '—';

    document.getElementById('currency-info').innerHTML =
        '<div style="display:flex;flex-direction:column;gap:10px;">' +
            infoItem('bi-flag-fill',         'Negara',        country.name) +
            infoItem('bi-translate',          'Kode Mata Uang', currency) +
            infoItem('bi-cash-coin',          'Nama',          country.currency_name || '—') +
            infoItem('bi-globe2',             'Region',        country.region || '—') +
        '</div>';

    try {
        const res  = await fetch('/api/currency/' + currency + '/history');
        const data = await res.json();

        if (!data.success || !data.data.length) {
            document.getElementById('currency-content').innerHTML +=
                '<div style="color:var(--text-muted-custom);text-align:center;padding:20px;">Belum ada data kurs. Konfigurasi EXCHANGE_RATE_API_KEY untuk mengaktifkan fitur ini.</div>';
            return;
        }

        const rates  = data.data.slice().reverse(); // urutkan dari lama ke baru
        const latest = data.data[0]; // data terbaru

        // Update KPI
        document.getElementById('cur-rate').textContent =
            parseFloat(latest.rate).toLocaleString(undefined, { maximumFractionDigits: 4 });

        const chg    = parseFloat(latest.change_percent || 0);
        const chgEl  = document.getElementById('cur-change');
        chgEl.textContent = (chg >= 0 ? '▲ +' : '▼ ') + chg.toFixed(4) + '%';
        chgEl.style.color = chg >= 0 ? '#198754' : '#dc3545';

        // Grafik trend
        const labels = rates.map(r => r.rate_date);
        const values = rates.map(r => parseFloat(r.rate));

        if (currencyChart) currencyChart.destroy();
        currencyChart = new Chart(
            document.getElementById('chart-currency-trend').getContext('2d'), {
            type: 'line',
            data: {
                labels,
                datasets: [{
                    label: '1 USD → ' + currency,
                    data: values,
                    borderColor: '#fd7e14',
                    backgroundColor: 'rgba(253,126,20,0.1)',
                    fill: true,
                    tension: 0.4,
                    pointRadius: 3,
                    pointBackgroundColor: '#fd7e14',
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { labels: { color: '#6b7088' } } },
                scales: {
                    x: { ticks: { color: '#6b7088', maxTicksLimit: 10 }, grid: { color: 'rgba(31,35,51,0.06)' } },
                    y: { ticks: { color: '#6b7088' }, grid: { color: 'rgba(31,35,51,0.06)' } }
                }
            }
        });

        // Tabel history
        document.getElementById('currency-history-table').innerHTML =
            '<table style="width:100%;border-collapse:collapse;">' +
            '<thead><tr style="border-bottom:2px solid var(--card-border);">' +
                '<th style="padding:8px;color:var(--text-muted-custom);font-size:12px;text-align:left;">Tanggal</th>' +
                '<th style="padding:8px;color:var(--text-muted-custom);font-size:12px;text-align:right;">Kurs (1 USD)</th>' +
                '<th style="padding:8px;color:var(--text-muted-custom);font-size:12px;text-align:right;">Perubahan</th>' +
            '</tr></thead><tbody>' +
            data.data.map(function(r) {
                const c = parseFloat(r.change_percent || 0);
                return '<tr style="border-bottom:1px solid var(--card-border);">' +
                    '<td style="padding:8px;color:var(--text-muted-custom);font-size:13px;">' + r.rate_date + '</td>' +
                    '<td style="padding:8px;color:var(--text-main);font-size:13px;text-align:right;font-weight:600;">' +
                        parseFloat(r.rate).toLocaleString(undefined, { maximumFractionDigits: 4 }) +
                    '</td>' +
                    '<td style="padding:8px;font-size:13px;text-align:right;color:' + (c >= 0 ? '#198754' : '#dc3545') + ';">' +
                        (c >= 0 ? '▲ +' : '▼ ') + c.toFixed(4) + '%' +
                    '</td>' +
                '</tr>';
            }).join('') +
            '</tbody></table>';

    } catch(e) { console.error('Currency error:', e); }
});

function infoItem(icon, label, value) {
    return '<div style="display:flex;align-items:center;gap:10px;padding:8px;background:var(--dark-bg);border-radius:8px;">' +
        '<i class="bi ' + icon + '" style="color:var(--primary);font-size:16px;width:20px;text-align:center;"></i>' +
        '<div>' +
            '<div style="color:var(--text-muted-custom);font-size:10px;">' + label + '</div>' +
            '<div style="color:var(--text-main);font-size:13px;font-weight:500;">' + value + '</div>' +
        '</div>' +
    '</div>';
}
</script>
@endpush