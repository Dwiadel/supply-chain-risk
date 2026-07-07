@extends('layouts.app')
@section('title', 'Berita & Sentimen')
@section('content')

<h5 style="color:#fff;margin-bottom:20px;">
    <i class="bi bi-newspaper"></i> News Intelligence & Sentiment Analysis
</h5>

<div id="news-welcome" style="text-align:center;padding:60px 20px;">
    <i class="bi bi-newspaper" style="font-size:48px;color:#0d6efd;opacity:0.5;display:block;margin-bottom:15px;"></i>
    <p style="color:var(--text-muted-custom);">Pilih negara dari search box di atas untuk melihat berita & analisis sentimen</p>
</div>

<div id="news-content" style="display:none;">
    {{-- Sentiment Summary --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="dash-card" style="border-color:rgba(37,181,116,0.3);">
                <div class="dash-card-title" style="color:#25b574;"><i class="bi bi-emoji-smile-fill"></i> Positif</div>
                <div class="dash-card-value" id="sent-positive" style="color:#25b574;">—</div>
                <div class="dash-card-sub" id="sent-positive-pct">—%</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="dash-card" style="border-color:rgba(255,193,7,0.3);">
                <div class="dash-card-title" style="color:#ffc107;"><i class="bi bi-emoji-neutral-fill"></i> Netral</div>
                <div class="dash-card-value" id="sent-neutral" style="color:#ffc107;">—</div>
                <div class="dash-card-sub" id="sent-neutral-pct">—%</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="dash-card" style="border-color:rgba(255,107,122,0.3);">
                <div class="dash-card-title" style="color:#ff6b7a;"><i class="bi bi-emoji-frown-fill"></i> Negatif</div>
                <div class="dash-card-value" id="sent-negative" style="color:#ff6b7a;">—</div>
                <div class="dash-card-sub" id="sent-negative-pct">—%</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="dash-card">
                <div class="dash-card-title"><i class="bi bi-file-text-fill"></i> Total Artikel</div>
                <div class="dash-card-value" id="sent-total">—</div>
                <div class="dash-card-sub">artikel dianalisis</div>
            </div>
        </div>
    </div>

    {{-- Chart + Artikel --}}
    <div class="row g-3">
        <div class="col-md-4">
            <div class="dash-card">
                <div class="dash-card-title"><i class="bi bi-pie-chart-fill"></i> Distribusi Sentimen</div>
                <canvas id="chart-sentiment-page" style="margin-top:15px;"></canvas>
                <div id="sentiment-bar-wrap" style="margin-top:20px;">
                    <div style="display:flex;justify-content:space-between;margin-bottom:4px;">
                        <small style="color:#25b574;">Positif</small>
                        <small id="bar-pos-pct" style="color:#25b574;">0%</small>
                    </div>
                    <div style="height:8px;background:var(--dark-bg);border-radius:4px;overflow:hidden;margin-bottom:8px;">
                        <div id="bar-pos" style="height:100%;width:0%;background:#25b574;border-radius:4px;transition:width 0.8s;"></div>
                    </div>
                    <div style="display:flex;justify-content:space-between;margin-bottom:4px;">
                        <small style="color:#ffc107;">Netral</small>
                        <small id="bar-neu-pct" style="color:#ffc107;">0%</small>
                    </div>
                    <div style="height:8px;background:var(--dark-bg);border-radius:4px;overflow:hidden;margin-bottom:8px;">
                        <div id="bar-neu" style="height:100%;width:0%;background:#ffc107;border-radius:4px;transition:width 0.8s;"></div>
                    </div>
                    <div style="display:flex;justify-content:space-between;margin-bottom:4px;">
                        <small style="color:#ff6b7a;">Negatif</small>
                        <small id="bar-neg-pct" style="color:#ff6b7a;">0%</small>
                    </div>
                    <div style="height:8px;background:var(--dark-bg);border-radius:4px;overflow:hidden;">
                        <div id="bar-neg" style="height:100%;width:0%;background:#ff6b7a;border-radius:4px;transition:width 0.8s;"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="dash-card">
                <div class="dash-card-title"><i class="bi bi-list-ul"></i> Daftar Berita Terkini</div>
                <div id="news-article-list" style="margin-top:15px;max-height:500px;overflow-y:auto;"></div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
let sentChart = null;

window.addEventListener('countrySelected', async function(e) {
    const cca2 = e.detail.cca2;

    document.getElementById('news-welcome').style.display  = 'none';
    document.getElementById('news-content').style.display  = 'block';

    // Load sentimen
    try {
        const res  = await fetch('/api/news/' + cca2 + '/sentiment');
        const data = await res.json();

        if (data.success && data.data.total > 0) {
            const d = data.data;

            document.getElementById('sent-positive').textContent     = d.positive;
            document.getElementById('sent-positive-pct').textContent = d.positive_pct + '%';
            document.getElementById('sent-neutral').textContent      = d.neutral;
            document.getElementById('sent-neutral-pct').textContent  = d.neutral_pct + '%';
            document.getElementById('sent-negative').textContent     = d.negative;
            document.getElementById('sent-negative-pct').textContent = d.negative_pct + '%';
            document.getElementById('sent-total').textContent        = d.total;

            // Progress bars
            document.getElementById('bar-pos').style.width     = d.positive_pct + '%';
            document.getElementById('bar-pos-pct').textContent = d.positive_pct + '%';
            document.getElementById('bar-neu').style.width     = d.neutral_pct + '%';
            document.getElementById('bar-neu-pct').textContent = d.neutral_pct + '%';
            document.getElementById('bar-neg').style.width     = d.negative_pct + '%';
            document.getElementById('bar-neg-pct').textContent = d.negative_pct + '%';

            // Pie chart
            if (sentChart) sentChart.destroy();
            sentChart = new Chart(
                document.getElementById('chart-sentiment-page').getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: ['Positif', 'Netral', 'Negatif'],
                    datasets: [{
                        data: [d.positive, d.neutral, d.negative],
                        backgroundColor: ['#25b574', '#ffc107', '#ff6b7a'],
                        borderWidth: 0,
                    }]
                },
                options: {
                    plugins: { legend: { labels: { color: '#b0b3c8', font: { size: 11 } } } },
                    cutout: '60%',
                }
            });
        } else {
            document.getElementById('sent-total').textContent = '0';
            ['sent-positive','sent-neutral','sent-negative'].forEach(function(id) {
                document.getElementById(id).textContent = '0';
            });
        }
    } catch(e) { console.error('Sentiment error:', e); }

    // Load artikel
    try {
        const res  = await fetch('/api/news/' + cca2);
        const data = await res.json();

        if (!data.success || !data.data.length) {
            document.getElementById('news-article-list').innerHTML =
                '<div style="color:var(--text-muted-custom);text-align:center;padding:40px 0;">' +
                '<i class="bi bi-newspaper" style="font-size:32px;opacity:0.3;display:block;margin-bottom:10px;"></i>' +
                'Belum ada berita. Konfigurasikan GNEWS_API_KEY di .env untuk mengaktifkan fitur ini.</div>';
            return;
        }

        document.getElementById('news-article-list').innerHTML = data.data.map(function(a) {
            const sentColor = a.sentiment === 'Positive' ? '#25b574'
                            : a.sentiment === 'Negative' ? '#ff6b7a' : '#ffc107';
            const sentClass = a.sentiment === 'Positive' ? 'risk-low'
                            : a.sentiment === 'Negative' ? 'risk-high' : 'risk-medium';
            const date = a.published_at
                ? new Date(a.published_at).toLocaleDateString('id-ID', { day:'numeric', month:'short', year:'numeric' })
                : '—';

            return '<div style="padding:14px 0;border-bottom:1px solid var(--card-border);">' +
                '<div style="display:flex;align-items:flex-start;gap:12px;">' +
                    '<span class="risk-badge ' + sentClass + '" style="padding:3px 10px;font-size:10px;white-space:nowrap;margin-top:2px;">' +
                        a.sentiment +
                    '</span>' +
                    '<div style="flex:1;">' +
                        '<a href="' + (a.url || '#') + '" target="_blank" ' +
                           'style="color:#fff;text-decoration:none;font-size:13px;font-weight:500;line-height:1.5;' +
                           'display:block;margin-bottom:5px;" ' +
                           'onmouseenter="this.style.color=\'#0d6efd\'" ' +
                           'onmouseleave="this.style.color=\'#fff\'">' +
                            a.title +
                        '</a>' +
                        (a.description
                            ? '<div style="color:var(--text-muted-custom);font-size:12px;margin-bottom:6px;line-height:1.4;">' +
                              a.description.substring(0, 150) + (a.description.length > 150 ? '...' : '') +
                              '</div>'
                            : '') +
                        '<div style="display:flex;align-items:center;gap:12px;">' +
                            '<span style="color:var(--text-muted-custom);font-size:11px;">' +
                                '<i class="bi bi-newspaper"></i> ' + (a.source || '—') +
                            '</span>' +
                            '<span style="color:var(--text-muted-custom);font-size:11px;">' +
                                '<i class="bi bi-calendar3"></i> ' + date +
                            '</span>' +
                            '<span style="font-size:11px;color:' + sentColor + ';">' +
                                '<i class="bi bi-plus-circle"></i> ' + (a.positive_score || 0) + ' · ' +
                                '<i class="bi bi-dash-circle"></i> ' + (a.negative_score || 0) +
                            '</span>' +
                        '</div>' +
                    '</div>' +
                '</div>' +
            '</div>';
        }).join('');

    } catch(e) { console.error('News error:', e); }
});
</script>
@endpush