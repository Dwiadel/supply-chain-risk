@extends('layouts.app')
@section('title', 'Berita & Sentimen')
@section('content')

<h5 style="color:var(--text-main);margin-bottom:5px;">
    <i class="bi bi-newspaper"></i> News Intelligence & Sentiment Analysis
</h5>
<p style="color:var(--text-muted-custom);font-size:13px;margin-bottom:20px;">
    Berita terkait Logistics, Trade, Shipping & Economy — dianalisis menggunakan
    <b style="color:var(--text-main);">GNews API</b> + <b style="color:var(--text-main);">Lexicon-Based Sentiment Analysis</b>
</p>

<div id="news-welcome" style="text-align:center;padding:60px 20px;">
    <i class="bi bi-newspaper" style="font-size:48px;color:#fd7e14;opacity:0.5;display:block;margin-bottom:15px;"></i>
    <p style="color:var(--text-muted-custom);">Pilih negara dari search box di atas untuk melihat berita & analisis sentimen</p>
</div>

<div id="news-content" style="display:none;">

    {{-- Sentiment Summary Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-6">
            <div class="dash-card" style="border-color:rgba(25,135,84,0.3);">
                <div class="dash-card-title" style="color:#198754;">
                    <i class="bi bi-emoji-smile-fill"></i> Positif
                </div>
                <div class="dash-card-value" id="sent-positive" style="color:#198754;">—</div>
                <div class="dash-card-sub" id="sent-positive-pct">—%</div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="dash-card" style="border-color:rgba(201,138,0,0.3);">
                <div class="dash-card-title" style="color:#c98a00;">
                    <i class="bi bi-emoji-neutral-fill"></i> Netral
                </div>
                <div class="dash-card-value" id="sent-neutral" style="color:#c98a00;">—</div>
                <div class="dash-card-sub" id="sent-neutral-pct">—%</div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="dash-card" style="border-color:rgba(220,53,69,0.3);">
                <div class="dash-card-title" style="color:#dc3545;">
                    <i class="bi bi-emoji-frown-fill"></i> Negatif
                </div>
                <div class="dash-card-value" id="sent-negative" style="color:#dc3545;">—</div>
                <div class="dash-card-sub" id="sent-negative-pct">—%</div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="dash-card">
                <div class="dash-card-title">
                    <i class="bi bi-file-text-fill"></i> Total Artikel
                </div>
                <div class="dash-card-value" id="sent-total">—</div>
                <div class="dash-card-sub">artikel dianalisis</div>
            </div>
        </div>
    </div>

    {{-- Category Filter Tabs --}}
    <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:20px;" id="category-tabs">
        <button onclick="filterCategory('all')" id="tab-all"
                class="tab-btn active-tab"
                style="padding:8px 18px;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;
                       border:1px solid var(--card-border);background:rgba(31,35,51,0.06);color:var(--text-main);
                       transition:all 0.2s;">
            <i class="bi bi-grid-fill"></i> Semua
        </button>
        <button onclick="filterCategory('logistics')" id="tab-logistics"
                class="tab-btn"
                style="padding:8px 18px;border-radius:8px;font-size:13px;font-weight:500;cursor:pointer;
                       border:1px solid rgba(13,110,253,0.3);background:rgba(13,110,253,0.08);color:#0d6efd;
                       transition:all 0.2s;">
            <i class="bi bi-truck"></i> Logistics
        </button>
        <button onclick="filterCategory('trade')" id="tab-trade"
                class="tab-btn"
                style="padding:8px 18px;border-radius:8px;font-size:13px;font-weight:500;cursor:pointer;
                       border:1px solid rgba(25,135,84,0.3);background:rgba(25,135,84,0.08);color:#198754;
                       transition:all 0.2s;">
            <i class="bi bi-arrow-left-right"></i> Trade
        </button>
        <button onclick="filterCategory('shipping')" id="tab-shipping"
                class="tab-btn"
                style="padding:8px 18px;border-radius:8px;font-size:13px;font-weight:500;cursor:pointer;
                       border:1px solid rgba(10,149,179,0.3);background:rgba(10,149,179,0.08);color:#0a95b3;
                       transition:all 0.2s;">
            <i class="bi bi-ship"></i> Shipping
        </button>
        <button onclick="filterCategory('economy')" id="tab-economy"
                class="tab-btn"
                style="padding:8px 18px;border-radius:8px;font-size:13px;font-weight:500;cursor:pointer;
                       border:1px solid rgba(201,138,0,0.3);background:rgba(201,138,0,0.08);color:#c98a00;
                       transition:all 0.2s;">
            <i class="bi bi-graph-up-arrow"></i> Economy
        </button>
    </div>

    {{-- Category Count Badges --}}
    <div style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:20px;" id="category-counts">
        <span style="color:var(--text-muted-custom);font-size:12px;align-self:center;">Artikel per kategori:</span>
        <span id="count-logistics" style="background:rgba(13,110,253,0.12);border:1px solid rgba(13,110,253,0.3);
              color:#0d6efd;padding:3px 10px;border-radius:12px;font-size:11px;font-weight:600;">
            <i class="bi bi-truck"></i> Logistics: <span>0</span>
        </span>
        <span id="count-trade" style="background:rgba(25,135,84,0.12);border:1px solid rgba(25,135,84,0.3);
              color:#198754;padding:3px 10px;border-radius:12px;font-size:11px;font-weight:600;">
            <i class="bi bi-arrow-left-right"></i> Trade: <span>0</span>
        </span>
        <span id="count-shipping" style="background:rgba(10,149,179,0.12);border:1px solid rgba(10,149,179,0.3);
              color:#0a95b3;padding:3px 10px;border-radius:12px;font-size:11px;font-weight:600;">
            <i class="bi bi-ship"></i> Shipping: <span>0</span>
        </span>
        <span id="count-economy" style="background:rgba(201,138,0,0.12);border:1px solid rgba(201,138,0,0.3);
              color:#c98a00;padding:3px 10px;border-radius:12px;font-size:11px;font-weight:600;">
            <i class="bi bi-graph-up-arrow"></i> Economy: <span>0</span>
        </span>
    </div>

    {{-- Main Content: Artikel + Pie Chart --}}
    <div class="row g-3">
        <div class="col-md-8">
            <div class="dash-card">
                <div class="dash-card-title">
                    <i class="bi bi-list-ul"></i>
                    <span id="articles-title">Daftar Berita Terkini</span>
                    <span id="articles-filter-label" style="margin-left:8px;font-size:11px;
                          color:var(--text-muted-custom);font-weight:400;"></span>
                </div>
                <div id="news-article-list"
                     style="margin-top:15px;max-height:600px;overflow-y:auto;"></div>
            </div>
        </div>

        <div class="col-md-4">
            {{-- Pie Chart --}}
            <div class="dash-card" style="margin-bottom:16px;">
                <div class="dash-card-title">
                    <i class="bi bi-pie-chart-fill"></i> Distribusi Sentimen
                </div>
                <canvas id="chart-sentiment-page" style="margin-top:10px;"></canvas>
                <div id="sentiment-bars" style="margin-top:20px;">
                    <div style="display:flex;justify-content:space-between;margin-bottom:4px;">
                        <small style="color:#198754;">Positif</small>
                        <small id="bar-pos-pct" style="color:#198754;">0%</small>
                    </div>
                    <div style="height:7px;background:var(--dark-bg);border-radius:4px;overflow:hidden;margin-bottom:8px;">
                        <div id="bar-pos" style="height:100%;width:0%;background:#198754;border-radius:4px;transition:width 0.8s;"></div>
                    </div>
                    <div style="display:flex;justify-content:space-between;margin-bottom:4px;">
                        <small style="color:#c98a00;">Netral</small>
                        <small id="bar-neu-pct" style="color:#c98a00;">0%</small>
                    </div>
                    <div style="height:7px;background:var(--dark-bg);border-radius:4px;overflow:hidden;margin-bottom:8px;">
                        <div id="bar-neu" style="height:100%;width:0%;background:#c98a00;border-radius:4px;transition:width 0.8s;"></div>
                    </div>
                    <div style="display:flex;justify-content:space-between;margin-bottom:4px;">
                        <small style="color:#dc3545;">Negatif</small>
                        <small id="bar-neg-pct" style="color:#dc3545;">0%</small>
                    </div>
                    <div style="height:7px;background:var(--dark-bg);border-radius:4px;overflow:hidden;">
                        <div id="bar-neg" style="height:100%;width:0%;background:#dc3545;border-radius:4px;transition:width 0.8s;"></div>
                    </div>
                </div>
            </div>

            {{-- Kategori Breakdown --}}
            <div class="dash-card">
                <div class="dash-card-title">
                    <i class="bi bi-tags-fill"></i> Breakdown Kategori
                </div>
                <div id="category-breakdown" style="margin-top:10px;">
                    <div style="color:var(--text-muted-custom);font-size:12px;text-align:center;padding:15px 0;">
                        Memuat...
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<style>
.tab-btn.active-tab {
    background: rgba(253,126,20,0.12) !important;
    color: #e8590c !important;
    border-color: rgba(253,126,20,0.35) !important;
}
.article-item { transition: background 0.15s; }
.article-item:hover { background: rgba(31,35,51,0.03); border-radius: 8px; }
</style>

@endsection

@push('scripts')
<script>
let sentChart  = null;
let allArticles = [];
let currentCca2 = null;

// Mapping kategori dari prefix source
const CATEGORIES = {
    logistics: { label: 'Logistics', icon: 'bi-truck',           color: '#0d6efd' },
    trade:     { label: 'Trade',     icon: 'bi-arrow-left-right', color: '#198754' },
    shipping:  { label: 'Shipping',  icon: 'bi-ship',             color: '#0a95b3' },
    economy:   { label: 'Economy',   icon: 'bi-graph-up-arrow',   color: '#c98a00' },
};

window.addEventListener('countrySelected', async function(e) {
    currentCca2 = e.detail.cca2;
    document.getElementById('news-welcome').style.display  = 'none';
    document.getElementById('news-content').style.display  = 'block';
    await loadNewsData(currentCca2);
});

async function loadNewsData(cca2) {
    // Reset filter ke "Semua"
    filterCategory('all', false);

    // Load sentimen
    try {
        var res  = await fetch('/api/news/' + cca2 + '/sentiment');
        var data = await res.json();

        if (data.success && data.data.total > 0) {
            var d = data.data;
            document.getElementById('sent-positive').textContent     = d.positive;
            document.getElementById('sent-positive-pct').textContent = d.positive_pct + '%';
            document.getElementById('sent-neutral').textContent      = d.neutral;
            document.getElementById('sent-neutral-pct').textContent  = d.neutral_pct + '%';
            document.getElementById('sent-negative').textContent     = d.negative;
            document.getElementById('sent-negative-pct').textContent = d.negative_pct + '%';
            document.getElementById('sent-total').textContent        = d.total;

            document.getElementById('bar-pos').style.width     = d.positive_pct + '%';
            document.getElementById('bar-pos-pct').textContent = d.positive_pct + '%';
            document.getElementById('bar-neu').style.width     = d.neutral_pct + '%';
            document.getElementById('bar-neu-pct').textContent = d.neutral_pct + '%';
            document.getElementById('bar-neg').style.width     = d.negative_pct + '%';
            document.getElementById('bar-neg-pct').textContent = d.negative_pct + '%';

            if (sentChart) sentChart.destroy();
            sentChart = new Chart(document.getElementById('chart-sentiment-page'), {
                type: 'doughnut',
                data: {
                    labels: ['Positif', 'Netral', 'Negatif'],
                    datasets: [{
                        data: [d.positive, d.neutral, d.negative],
                        backgroundColor: ['#198754', '#c98a00', '#dc3545'],
                        borderWidth: 0,
                    }]
                },
                options: {
                    plugins: { legend: { labels: { color: '#4b4f66', font: { size: 11 } } } },
                    cutout: '60%',
                }
            });
        }
    } catch(e) { console.error('Sentiment error:', e); }

    // Load artikel
    try {
        var res2  = await fetch('/api/news/' + cca2);
        var data2 = await res2.json();

        if (!data2.success || !data2.data || !data2.data.length) {
            document.getElementById('news-article-list').innerHTML =
                '<div style="color:var(--text-muted-custom);text-align:center;padding:40px 0;">' +
                '<i class="bi bi-newspaper" style="font-size:32px;opacity:0.3;display:block;margin-bottom:10px;"></i>' +
                'Belum ada berita. Konfigurasikan GNEWS_API_KEY di .env.</div>';
            return;
        }

        allArticles = data2.data;
        renderArticles(allArticles);
        updateCategoryCounts(allArticles);
        renderCategoryBreakdown(allArticles);

    } catch(e) { console.error('News error:', e); }
}

function getCategoryFromSource(source) {
    if (!source) return 'general';
    var s = source.toUpperCase();
    if (s.startsWith('[LOGISTICS]')) return 'logistics';
    if (s.startsWith('[TRADE]'))     return 'trade';
    if (s.startsWith('[SHIPPING]'))  return 'shipping';
    if (s.startsWith('[ECONOMY]'))   return 'economy';
    return 'general';
}

function getCleanSource(source) {
    if (!source) return '—';
    return source.replace(/^\[.*?\]\s*/, '');
}

function filterCategory(cat, updateTab) {
    if (updateTab !== false) {
        // Update active tab styling
        document.querySelectorAll('.tab-btn').forEach(function(btn) {
            btn.classList.remove('active-tab');
        });
        var activeTab = document.getElementById('tab-' + cat);
        if (activeTab) activeTab.classList.add('active-tab');
    }

    if (!allArticles.length) return;

    var filtered = cat === 'all'
        ? allArticles
        : allArticles.filter(function(a) {
            return getCategoryFromSource(a.source) === cat;
        });

    var catLabel = cat === 'all' ? '' : '— ' + (CATEGORIES[cat] ? CATEGORIES[cat].label : cat);
    document.getElementById('articles-filter-label').textContent = catLabel;

    renderArticles(filtered);
}

function renderArticles(articles) {
    if (!articles.length) {
        document.getElementById('news-article-list').innerHTML =
            '<div style="color:var(--text-muted-custom);text-align:center;padding:30px 0;font-size:13px;">' +
            'Tidak ada berita untuk kategori ini.</div>';
        return;
    }

    document.getElementById('news-article-list').innerHTML = articles.map(function(a) {
        var cat      = getCategoryFromSource(a.source);
        var catInfo  = CATEGORIES[cat] || { label: 'General', icon: 'bi-newspaper', color: '#6b7088' };
        var source   = getCleanSource(a.source);
        var sentClass = a.sentiment === 'Positive' ? 'risk-low' :
                        a.sentiment === 'Negative' ? 'risk-high' : 'risk-medium';
        var date = a.published_at
            ? new Date(a.published_at).toLocaleDateString('id-ID', { day:'numeric', month:'short', year:'numeric' })
            : '—';

        return '<div class="article-item" style="padding:14px 8px;border-bottom:1px solid var(--card-border);">' +
            '<div style="display:flex;align-items:flex-start;gap:10px;">' +
                // Kategori + Sentiment badges
                '<div style="display:flex;flex-direction:column;gap:4px;flex-shrink:0;">' +
                    '<span class="risk-badge ' + sentClass + '" style="padding:2px 8px;font-size:10px;white-space:nowrap;">' +
                        a.sentiment +
                    '</span>' +
                    '<span style="background:rgba(31,35,51,0.04);border:1px solid var(--card-border);' +
                          'color:' + catInfo.color + ';padding:2px 8px;border-radius:12px;font-size:10px;' +
                          'font-weight:600;white-space:nowrap;text-align:center;">' +
                        '<i class="bi ' + catInfo.icon + '"></i> ' + catInfo.label +
                    '</span>' +
                '</div>' +
                // Content
                '<div style="flex:1;min-width:0;">' +
                    '<a href="' + (a.url || '#') + '" target="_blank" ' +
                       'style="color:var(--text-main);text-decoration:none;font-size:13px;font-weight:500;' +
                       'line-height:1.5;display:block;margin-bottom:5px;"' +
                       ' onmouseenter="this.style.color=\'#fd7e14\'"' +
                       ' onmouseleave="this.style.color=\'var(--text-main)\'">' +
                        a.title +
                    '</a>' +
                    (a.description
                        ? '<div style="color:var(--text-muted-custom);font-size:12px;margin-bottom:6px;' +
                          'line-height:1.5;overflow:hidden;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;">' +
                          a.description + '</div>'
                        : '') +
                    '<div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;">' +
                        '<span style="color:var(--text-muted-custom);font-size:11px;">' +
                            '<i class="bi bi-newspaper"></i> ' + source +
                        '</span>' +
                        '<span style="color:var(--text-muted-custom);font-size:11px;">' +
                            '<i class="bi bi-calendar3"></i> ' + date +
                        '</span>' +
                        '<span style="font-size:11px;color:#198754;">' +
                            '<i class="bi bi-plus-circle"></i> ' + (a.positive_score || 0) +
                        '</span>' +
                        '<span style="font-size:11px;color:#dc3545;">' +
                            '<i class="bi bi-dash-circle"></i> ' + (a.negative_score || 0) +
                        '</span>' +
                    '</div>' +
                '</div>' +
            '</div>' +
        '</div>';
    }).join('');
}

function updateCategoryCounts(articles) {
    var counts = { logistics: 0, trade: 0, shipping: 0, economy: 0 };
    articles.forEach(function(a) {
        var cat = getCategoryFromSource(a.source);
        if (counts[cat] !== undefined) counts[cat]++;
    });

    ['logistics', 'trade', 'shipping', 'economy'].forEach(function(cat) {
        var el = document.querySelector('#count-' + cat + ' span');
        if (el) el.textContent = counts[cat];
    });
}

function renderCategoryBreakdown(articles) {
    var counts = { logistics: 0, trade: 0, shipping: 0, economy: 0 };
    articles.forEach(function(a) {
        var cat = getCategoryFromSource(a.source);
        if (counts[cat] !== undefined) counts[cat]++;
    });

    var total = articles.length || 1;
    var html  = '';

    Object.keys(CATEGORIES).forEach(function(cat) {
        var c    = CATEGORIES[cat];
        var cnt  = counts[cat];
        var pct  = Math.round((cnt / total) * 100);

        html += '<div style="margin-bottom:14px;">' +
            '<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:5px;">' +
                '<span style="color:#4b4f66;font-size:12px;display:flex;align-items:center;gap:6px;">' +
                    '<i class="bi ' + c.icon + '" style="color:' + c.color + ';"></i>' +
                    c.label +
                '</span>' +
                '<span style="color:var(--text-main);font-size:12px;font-weight:600;">' + cnt + ' artikel</span>' +
            '</div>' +
            '<div style="height:6px;background:var(--dark-bg);border-radius:3px;overflow:hidden;">' +
                '<div style="height:100%;width:' + pct + '%;background:' + c.color + ';border-radius:3px;transition:width 0.8s;"></div>' +
            '</div>' +
        '</div>';
    });

    document.getElementById('category-breakdown').innerHTML = html ||
        '<div style="color:var(--text-muted-custom);font-size:12px;text-align:center;">Tidak ada data</div>';
}
</script>
@endpush