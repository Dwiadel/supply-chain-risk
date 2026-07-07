@extends('layouts.app')
@section('title', 'Pelabuhan Dunia')
@section('content')
<h5 style="color:#fff;margin-bottom:20px;">Port Location Dashboard</h5>
<div class="row g-3">
    <div class="col-md-4">
        <div class="dash-card">
            <div class="dash-card-title"><i class="bi bi-search"></i> Cari Pelabuhan</div>
            <input type="text" id="port-search" placeholder="Nama pelabuhan atau negara..."
                   class="form-control mt-2"
                   style="background:var(--dark-bg);border:1px solid var(--card-border);color:#fff;">
            <div id="port-list" style="margin-top:15px;max-height:500px;overflow-y:auto;"></div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="dash-card" style="padding:0;overflow:hidden;">
            <div id="port-map" style="height:550px;border-radius:12px;"></div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
const portMap = L.map('port-map', { center: [20, 0], zoom: 2 });
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(portMap);

let allPorts = [];
const portListEl = document.getElementById('port-list');
const portSearchEl = document.getElementById('port-search');

fetch('/api/ports').then(r => r.json()).then(data => {
    if (!data.success) return;
    allPorts = data.data;
    renderPortList(allPorts);

    allPorts.forEach(port => {
        L.circleMarker([port.latitude, port.longitude], {
            radius: port.size_category === 'Large' ? 8 : 5,
            fillColor: port.size_category === 'Large' ? '#0d6efd' : '#6c757d',
            color: '#fff', weight: 1, fillOpacity: 0.85
        }).addTo(portMap)
        .bindPopup(`<b>${port.name}</b><br>${port.country_name}<br><small>${port.size_category} Port</small>`);
    });
});

portSearchEl.addEventListener('input', function () {
    const q = this.value.toLowerCase();
    renderPortList(allPorts.filter(p =>
        p.name.toLowerCase().includes(q) || p.country_name.toLowerCase().includes(q)
    ));
});

function renderPortList(ports) {
    portListEl.innerHTML = ports.map(p => `
        <div style="padding:8px 0;border-bottom:1px solid var(--card-border);cursor:pointer;"
             onclick="portMap.setView([${p.latitude},${p.longitude}],8)">
            <div style="color:#fff;font-size:13px;font-weight:500;">${p.name}</div>
            <div style="color:var(--text-muted-custom);font-size:11px;">${p.country_name} · ${p.size_category}</div>
        </div>
    `).join('') || '<div style="color:var(--text-muted-custom);padding:20px;text-align:center;">Tidak ada hasil</div>';
}
</script>
@endpush