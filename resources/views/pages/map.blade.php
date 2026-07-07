@extends('layouts.app')
@section('title', 'Peta Global')
@section('content')

<div class="row g-3">
    <div class="col-md-3">
        <div class="dash-card" style="height:600px;overflow-y:auto;">
            <div class="dash-card-title">
                <i class="bi bi-geo-alt-fill"></i> Negara Aktif
            </div>
            <div id="country-info-panel" style="margin-top:15px;">
                <div style="color:var(--text-muted-custom);font-size:13px;text-align:center;padding:20px 0;">
                    Pilih negara dari search box di atas
                </div>
            </div>
            <div class="dash-card-title" style="margin-top:20px;">
                <i class="bi bi-anchor"></i> Pelabuhan Terdekat
            </div>
            <div id="nearby-ports" style="margin-top:10px;">
                <div style="color:var(--text-muted-custom);font-size:12px;">—</div>
            </div>
        </div>
    </div>

    <div class="col-md-9">
        <div class="dash-card" style="padding:0;overflow:hidden;">
            <div id="world-map" style="height:600px;border-radius:12px;"></div>
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

let allPorts      = [];
let countryMarker = null;

// Load semua pelabuhan
fetch('/api/ports')
    .then(r => r.json())
    .then(data => {
        if (!data.success) return;
        allPorts = data.data;
        allPorts.forEach(port => {
            const isLarge = port.size_category === 'Large';
            L.circleMarker([port.latitude, port.longitude], {
                radius      : isLarge ? 7 : 5,
                fillColor   : isLarge ? '#0d6efd' : '#6c757d',
                color       : '#fff',
                weight      : 1.5,
                fillOpacity : 0.85,
            }).addTo(map)
            .bindPopup(
                '<b>' + port.name + '</b><br>' +
                port.country_name + '<br>' +
                '<small>' + port.size_category + ' Port</small>'
            );
        });
    });

// Dengarkan event negara dipilih
window.addEventListener('countrySelected', function (e) {
    const country = e.detail.country;
    if (!country.latitude || !country.longitude) return;

    const lat = parseFloat(country.latitude);
    const lng = parseFloat(country.longitude);

    // Animasi peta ke negara yang dipilih
    map.flyTo([lat, lng], 5, { duration: 1.5 });

    // Hapus marker lama
    if (countryMarker) map.removeLayer(countryMarker);

    // Tambah marker baru
    const flagImg  = country.flag_url
        ? '<img src="' + country.flag_url + '" style="width:40px;border-radius:3px;margin-bottom:6px;"><br>'
        : '';
    const capital  = country.capital  || '';
    const region   = country.region   || '';

    countryMarker = L.marker([lat, lng])
        .addTo(map)
        .bindPopup(
            '<div style="text-align:center;min-width:140px;">' +
            flagImg +
            '<b>' + country.name + '</b><br>' +
            '<small>' + capital + (capital && region ? ' · ' : '') + region + '</small>' +
            '</div>'
        )
        .openPopup();

    updateCountryPanel(country);
    showNearbyPorts(lat, lng);
});

function updateCountryPanel(country) {
    const currency  = country.currency_code || '—';
    const subregion = country.subregion     || '—';
    const lat       = parseFloat(country.latitude  || 0).toFixed(2);
    const lng       = parseFloat(country.longitude || 0).toFixed(2);
    const flagImg   = country.flag_url
        ? '<img src="' + country.flag_url + '" style="width:60px;border-radius:4px;box-shadow:0 2px 8px rgba(0,0,0,0.3);">'
        : '';
    const capital  = country.capital  || '—';
    const region   = country.region   || '—';

    document.getElementById('country-info-panel').innerHTML =
        '<div style="text-align:center;margin-bottom:15px;">' +
            flagImg +
            '<h6 style="color:#fff;margin:10px 0 4px;font-weight:700;">' + country.name + '</h6>' +
            '<small style="color:var(--text-muted-custom);">' + capital + ' · ' + region + '</small>' +
        '</div>' +
        '<div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-top:10px;">' +
            infoBox('bi-translate',   'Mata Uang', currency)  +
            infoBox('bi-geo-alt',     'Subregion', subregion) +
            infoBox('bi-arrow-up',    'Latitude',  lat)       +
            infoBox('bi-arrow-right', 'Longitude', lng)       +
        '</div>';
}

function showNearbyPorts(lat, lng) {
    const nearby = allPorts
        .map(p => Object.assign({}, p, {
            dist: Math.sqrt(
                Math.pow(p.latitude  - lat, 2) +
                Math.pow(p.longitude - lng, 2)
            )
        }))
        .sort((a, b) => a.dist - b.dist)
        .slice(0, 5);

    document.getElementById('nearby-ports').innerHTML = nearby.map(function(p) {
        return '<div style="padding:6px 0;border-bottom:1px solid var(--card-border);cursor:pointer;" ' +
               'onclick="map.flyTo([' + p.latitude + ',' + p.longitude + '],8)">' +
                   '<div style="color:#fff;font-size:12px;font-weight:500;">' + p.name + '</div>' +
                   '<div style="color:var(--text-muted-custom);font-size:11px;">' + p.country_name + ' · ' + p.size_category + '</div>' +
               '</div>';
    }).join('');
}

function infoBox(icon, label, value) {
    return '<div style="background:var(--dark-bg);border-radius:8px;padding:8px;text-align:center;">' +
               '<i class="bi ' + icon + '" style="color:#0d6efd;font-size:14px;"></i>' +
               '<div style="color:#fff;font-size:12px;font-weight:600;margin-top:3px;">' + value + '</div>' +
               '<div style="color:var(--text-muted-custom);font-size:10px;">' + label + '</div>' +
           '</div>';
}
</script>
@endpush