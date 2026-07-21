@extends('layouts.app')
@section('title', 'Peta Global')
@section('content')

<div class="row g-3">
    {{-- Panel Kiri --}}
    <div class="col-md-3">
        <div class="dash-card" style="height:650px;overflow-y:auto;">

            {{-- Info Negara Aktif --}}
            <div class="dash-card-title"><i class="bi bi-geo-alt-fill"></i> Negara Aktif</div>
            <div id="country-info-panel">
                <div style="color:var(--text-muted-custom);font-size:13px;text-align:center;padding:15px 0;">
                    Pilih negara dari search box di atas
                </div>
            </div>

            <hr style="border-color:var(--card-border);margin:15px 0;">

            {{-- Weather Panel --}}
            <div class="dash-card-title"><i class="bi bi-cloud-sun-fill"></i> Kondisi Cuaca</div>
            <div id="weather-panel">
                <div style="color:var(--text-muted-custom);font-size:12px;text-align:center;padding:10px 0;">—</div>
            </div>

            <hr style="border-color:var(--card-border);margin:15px 0;">

            {{-- Legenda --}}
            <div class="dash-card-title"><i class="bi bi-info-circle"></i> Legenda Peta</div>
            <div style="margin-top:8px;display:flex;flex-direction:column;gap:8px;">
                <div style="display:flex;align-items:center;gap:8px;">
                    <div style="width:14px;height:14px;background:#fd7e14;border-radius:50%;flex-shrink:0;"></div>
                    <span style="color:#4b4f66;font-size:12px;">Pelabuhan Besar (Large)</span>
                </div>
                <div style="display:flex;align-items:center;gap:8px;">
                    <div style="width:10px;height:10px;background:#6c757d;border-radius:50%;flex-shrink:0;"></div>
                    <span style="color:#4b4f66;font-size:12px;">Pelabuhan Medium/Small</span>
                </div>
                <div style="display:flex;align-items:center;gap:8px;">
                    <div style="width:14px;height:14px;background:#25b574;border-radius:50%;flex-shrink:0;border:2px solid #fff;box-shadow:0 0 0 1px var(--card-border);"></div>
                    <span style="color:#4b4f66;font-size:12px;">Negara dipilih (Low Risk)</span>
                </div>
                <div style="display:flex;align-items:center;gap:8px;">
                    <div style="width:14px;height:14px;background:#ffc107;border-radius:50%;flex-shrink:0;border:2px solid #fff;box-shadow:0 0 0 1px var(--card-border);"></div>
                    <span style="color:#4b4f66;font-size:12px;">Negara dipilih (Medium Risk)</span>
                </div>
                <div style="display:flex;align-items:center;gap:8px;">
                    <div style="width:14px;height:14px;background:#ff6b7a;border-radius:50%;flex-shrink:0;border:2px solid #fff;box-shadow:0 0 0 1px var(--card-border);"></div>
                    <span style="color:#4b4f66;font-size:12px;">Negara dipilih (High Risk)</span>
                </div>
                <div style="display:flex;align-items:center;gap:8px;">
                    <div style="width:20px;height:14px;background:rgba(253,126,20,0.3);border:1px solid #fd7e14;border-radius:3px;flex-shrink:0;"></div>
                    <span style="color:#4b4f66;font-size:12px;">Overlay Hujan/Badai</span>
                </div>
            </div>

            <hr style="border-color:var(--card-border);margin:15px 0;">

            {{-- Pelabuhan Terdekat --}}
            <div class="dash-card-title"><i class="bi bi-anchor"></i> Pelabuhan Terdekat</div>
            <div id="nearby-ports" style="margin-top:8px;">
                <div style="color:var(--text-muted-custom);font-size:12px;">—</div>
            </div>
        </div>
    </div>

    {{-- Peta --}}
    <div class="col-md-9">
        <div class="dash-card" style="padding:0;overflow:hidden;">
            <div id="world-map" style="height:650px;border-radius:12px;"></div>
        </div>

        {{-- Weather Stats Bar --}}
        <div id="weather-stats-bar" style="display:none;margin-top:12px;">
            <div class="row g-2">
                <div class="col-3">
                    <div class="dash-card" style="padding:12px;text-align:center;">
                        <i class="bi bi-cloud-rain-fill" style="font-size:20px;color:#fd7e14;"></i>
                        <div style="color:var(--text-main);font-weight:700;font-size:16px;margin-top:4px;" id="ws-rain">—</div>
                        <div style="color:var(--text-muted-custom);font-size:11px;">Curah Hujan (mm)</div>
                    </div>
                </div>
                <div class="col-3">
                    <div class="dash-card" style="padding:12px;text-align:center;">
                        <i class="bi bi-wind" style="font-size:20px;color:#25b574;"></i>
                        <div style="color:var(--text-main);font-weight:700;font-size:16px;margin-top:4px;" id="ws-wind">—</div>
                        <div style="color:var(--text-muted-custom);font-size:11px;">Angin (km/h)</div>
                    </div>
                </div>
                <div class="col-3">
                    <div class="dash-card" style="padding:12px;text-align:center;">
                        <i class="bi bi-exclamation-triangle-fill" style="font-size:20px;color:#e6a700;"></i>
                        <div style="color:var(--text-main);font-weight:700;font-size:16px;margin-top:4px;" id="ws-storm">—</div>
                        <div style="color:var(--text-muted-custom);font-size:11px;">Storm Risk (0-100)</div>
                    </div>
                </div>
                <div class="col-3">
                    <div class="dash-card" style="padding:12px;text-align:center;">
                        <i class="bi bi-thermometer-half" style="font-size:20px;color:#dc3545;"></i>
                        <div style="color:var(--text-main);font-weight:700;font-size:16px;margin-top:4px;" id="ws-temp">—</div>
                        <div style="color:var(--text-muted-custom);font-size:11px;">Suhu (°C)</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
const map = L.map('world-map', { center: [20, 0], zoom: 2 });

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors',
    maxZoom: 18,
}).addTo(map);

let allPorts       = [];
let countryMarker  = null;
let weatherCircle  = null;
let weatherOverlay = null;

// Load pelabuhan
fetch('/api/ports')
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (!data.success) return;
        allPorts = data.data;
        allPorts.forEach(function(port) {
            var isLarge = port.size_category === 'Large';
            L.circleMarker([port.latitude, port.longitude], {
                radius      : isLarge ? 7 : 5,
                fillColor   : isLarge ? '#fd7e14' : '#6c757d',
                color       : '#fff',
                weight      : 1.5,
                opacity     : 1,
                fillOpacity : 0.85,
            }).addTo(map)
            .bindPopup(
                '<div style="min-width:140px;">' +
                '<b style="font-size:13px;">' + port.name + '</b><br>' +
                '<span style="color:#666;">' + port.country_name + '</span><br>' +
                '<span style="background:' + (isLarge ? '#fd7e14' : '#6c757d') + ';color:#fff;padding:2px 8px;border-radius:10px;font-size:10px;">' +
                    port.size_category + ' Port' +
                '</span>' +
                '</div>'
            );
        });
    });

// Event saat negara dipilih
window.addEventListener('countrySelected', function(e) {
    var country = e.detail.country;
    if (!country.latitude || !country.longitude) return;

    var lat = parseFloat(country.latitude);
    var lng = parseFloat(country.longitude);

    // Animasi fly ke negara
    map.flyTo([lat, lng], 5, { duration: 1.5 });

    // Hapus marker & overlay lama
    if (countryMarker)  { map.removeLayer(countryMarker);  countryMarker  = null; }
    if (weatherCircle)  { map.removeLayer(weatherCircle);  weatherCircle  = null; }
    if (weatherOverlay) { map.removeLayer(weatherOverlay); weatherOverlay = null; }

    // Ambil data cuaca
    var weathers = country.weather_caches || [];
    var w = weathers.length > 0 ? weathers[weathers.length - 1] : null;

    // Tentukan warna marker berdasarkan risk score
    var risks = country.risk_scores || [];
    var latestRisk = risks.length > 0 ? risks[risks.length - 1] : null;
    var markerColor = '#25b574';
    if (latestRisk) {
        if (latestRisk.risk_level === 'High')   markerColor = '#ff6b7a';
        if (latestRisk.risk_level === 'Medium') markerColor = '#ffc107';
    }

    // Marker negara
    var flagImg = country.flag_url
        ? '<img src="' + country.flag_url + '" style="width:36px;border-radius:3px;margin-bottom:6px;"><br>'
        : '';

    countryMarker = L.circleMarker([lat, lng], {
        radius: 12, fillColor: markerColor,
        color: '#fff', weight: 2,
        fillOpacity: 0.9,
    }).addTo(map)
    .bindPopup(
        '<div style="text-align:center;min-width:160px;">' +
        flagImg +
        '<b style="font-size:14px;">' + country.name + '</b><br>' +
        '<small>' + (country.capital || '') + ' · ' + (country.region || '') + '</small>' +
        (w ? '<br><br><b style="color:' + markerColor + ';">' + (latestRisk ? latestRisk.risk_level + ' Risk' : '') + '</b>' : '') +
        '</div>'
    ).openPopup();

    // Overlay cuaca (circle radius berdasarkan intensitas cuaca)
    if (w) {
        var precipitation = parseFloat(w.precipitation || 0);
        var windSpeed     = parseFloat(w.wind_speed || 0);
        var stormRisk     = parseFloat(w.storm_risk_score || 0);

        // Warna overlay berdasarkan kondisi cuaca
        var overlayColor  = '#fd7e14'; // default oranye (hujan ringan)
        var overlayLabel  = 'Kondisi Normal';

        if (stormRisk > 66) {
            overlayColor = '#ff6b7a'; // merah = badai
            overlayLabel = 'Badai / Storm';
        } else if (windSpeed > 30) {
            overlayColor = '#ffc107'; // kuning = angin kencang
            overlayLabel = 'Angin Kencang';
        } else if (precipitation > 5) {
            overlayColor = '#fd7e14'; // oranye = hujan
            overlayLabel = 'Hujan';
        }

        // Radius circle overlay proportional ke storm risk (min 50km, max 400km)
        var radiusKm = 50 + (stormRisk / 100) * 350;

        weatherCircle = L.circle([lat, lng], {
            color       : overlayColor,
            fillColor   : overlayColor,
            fillOpacity : 0.12,
            weight      : 2,
            dashArray   : '6,4',
            radius      : radiusKm * 1000, // konversi km ke meter
        }).addTo(map)
        .bindPopup(
            '<div style="text-align:center;">' +
            '<b style="color:' + overlayColor + ';font-size:13px;">' + overlayLabel + '</b><br>' +
            '<table style="margin-top:8px;font-size:12px;">' +
            '<tr><td style="color:#666;padding:2px 6px;">🌧 Hujan:</td><td><b>' + precipitation + ' mm</b></td></tr>' +
            '<tr><td style="color:#666;padding:2px 6px;">💨 Angin:</td><td><b>' + windSpeed + ' km/h</b></td></tr>' +
            '<tr><td style="color:#666;padding:2px 6px;">⚡ Storm Risk:</td><td><b>' + stormRisk + '/100</b></td></tr>' +
            '<tr><td style="color:#666;padding:2px 6px;">🌡 Suhu:</td><td><b>' + (w.temperature || '—') + '°C</b></td></tr>' +
            '</table>' +
            '</div>'
        );

        // Update weather stats bar di bawah peta
        document.getElementById('weather-stats-bar').style.display = 'block';
        document.getElementById('ws-rain').textContent  = precipitation;
        document.getElementById('ws-wind').textContent  = windSpeed;
        document.getElementById('ws-storm').textContent = stormRisk;
        document.getElementById('ws-temp').textContent  = (w.temperature || '—') + '°C';

        // Warnai stats berdasarkan intensitas
        document.getElementById('ws-rain').style.color  = precipitation > 10 ? '#dc3545' : precipitation > 5 ? '#e6a700' : '#198754';
        document.getElementById('ws-wind').style.color  = windSpeed > 50 ? '#dc3545' : windSpeed > 30 ? '#e6a700' : '#198754';
        document.getElementById('ws-storm').style.color = stormRisk > 66 ? '#dc3545' : stormRisk > 33 ? '#e6a700' : '#198754';

        // Update panel cuaca kiri
        document.getElementById('weather-panel').innerHTML =
            '<div style="background:var(--dark-bg);border-radius:8px;padding:12px;margin-bottom:8px;' +
                'border-left:3px solid ' + overlayColor + ';">' +
                '<div style="color:' + overlayColor + ';font-weight:700;font-size:13px;margin-bottom:8px;">' +
                    getWeatherIcon(stormRisk, windSpeed, precipitation) + ' ' + overlayLabel +
                '</div>' +
                weatherRow('Curah Hujan', precipitation + ' mm', precipitation > 10 ? '#dc3545' : '#198754') +
                weatherRow('Kecepatan Angin', windSpeed + ' km/h', windSpeed > 30 ? '#e6a700' : '#198754') +
                weatherRow('Storm Risk Score', stormRisk + ' / 100', stormRisk > 66 ? '#dc3545' : stormRisk > 33 ? '#e6a700' : '#198754') +
                weatherRow('Suhu', (w.temperature || '—') + '°C', '#4b4f66') +
                weatherRow('Deskripsi', w.weather_description || '—', '#4b4f66') +
            '</div>' +
            '<div style="font-size:11px;color:var(--text-muted-custom);text-align:center;">' +
                'Data dari Open-Meteo API · ' + (w.fetched_at ? new Date(w.fetched_at).toLocaleString('id-ID') : '—') +
            '</div>';
    } else {
        document.getElementById('weather-panel').innerHTML =
            '<div style="color:var(--text-muted-custom);font-size:12px;text-align:center;padding:10px 0;">Data cuaca tidak tersedia</div>';
    }

    // Update panel info negara kiri
    updateCountryPanel(country, latestRisk);

    // Pelabuhan terdekat
    showNearbyPorts(lat, lng);
});

function getWeatherIcon(storm, wind, rain) {
    if (storm > 66) return '⛈';
    if (wind > 50)  return '🌪';
    if (wind > 30)  return '💨';
    if (rain > 10)  return '🌧';
    if (rain > 5)   return '🌦';
    return '☀';
}

function weatherRow(label, value, color) {
    return '<div style="display:flex;justify-content:space-between;padding:4px 0;border-bottom:1px solid var(--card-border);">' +
        '<span style="color:var(--text-muted-custom);font-size:12px;">' + label + '</span>' +
        '<span style="color:' + color + ';font-size:12px;font-weight:600;">' + value + '</span>' +
    '</div>';
}

function updateCountryPanel(country, risk) {
    var flagImg = country.flag_url
        ? '<img src="' + country.flag_url + '" style="width:60px;border-radius:4px;box-shadow:0 2px 8px rgba(31,35,51,0.15);">'
        : '';
    var riskLevel = risk ? risk.risk_level : null;
    var riskColor = riskLevel === 'High' ? '#ff6b7a' : riskLevel === 'Medium' ? '#ffc107' : '#25b574';

    document.getElementById('country-info-panel').innerHTML =
        '<div style="text-align:center;margin-bottom:12px;">' +
            flagImg +
            '<h6 style="color:var(--text-main);margin:8px 0 4px;font-weight:700;">' + country.name + '</h6>' +
            '<small style="color:var(--text-muted-custom);">' + (country.capital || '—') + ' · ' + (country.region || '—') + '</small>' +
            (riskLevel ? '<br><span class="risk-badge risk-' + riskLevel.toLowerCase() + '" style="margin-top:8px;font-size:11px;">' + riskLevel + ' Risk</span>' : '') +
        '</div>' +
        '<div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">' +
            infoBox('bi-translate',   'Mata Uang', country.currency_code || '—') +
            infoBox('bi-geo-alt',     'Subregion', country.subregion    || '—') +
            infoBox('bi-arrow-up',    'Latitude',  parseFloat(country.latitude  || 0).toFixed(2)) +
            infoBox('bi-arrow-right', 'Longitude', parseFloat(country.longitude || 0).toFixed(2)) +
        '</div>';
}

function showNearbyPorts(lat, lng) {
    var nearby = allPorts
        .map(function(p) {
            return Object.assign({}, p, {
                dist: Math.sqrt(Math.pow(p.latitude - lat, 2) + Math.pow(p.longitude - lng, 2))
            });
        })
        .sort(function(a, b) { return a.dist - b.dist; })
        .slice(0, 5);

    document.getElementById('nearby-ports').innerHTML = nearby.map(function(p) {
        return '<div style="padding:6px 0;border-bottom:1px solid var(--card-border);cursor:pointer;" ' +
               'onclick="map.flyTo([' + p.latitude + ',' + p.longitude + '],8)">' +
                   '<div style="color:var(--text-main);font-size:12px;font-weight:500;">' + p.name + '</div>' +
                   '<div style="color:var(--text-muted-custom);font-size:11px;">' + p.country_name + ' · ' + p.size_category + '</div>' +
               '</div>';
    }).join('');
}

function infoBox(icon, label, value) {
    return '<div style="background:var(--dark-bg);border-radius:8px;padding:8px;text-align:center;">' +
        '<i class="bi ' + icon + '" style="color:#fd7e14;font-size:13px;"></i>' +
        '<div style="color:var(--text-main);font-size:11px;font-weight:600;margin-top:3px;">' + value + '</div>' +
        '<div style="color:var(--text-muted-custom);font-size:10px;">' + label + '</div>' +
    '</div>';
}
</script>
@endpush