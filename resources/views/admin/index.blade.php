@extends('admin.layout')
@section('title', 'Admin Dashboard')
@section('content')

{{-- Stat Cards --}}
<div class="row g-3 mb-4">
    <div class="col-md-4 col-6">
        <div class="stat-card" style="border-left:4px solid #0d6efd;">
            <div class="stat-card-label"><i class="bi bi-people-fill"></i> Total User</div>
            <div class="stat-card-value">{{ $stats['total_users'] }}</div>
        </div>
    </div>
    <div class="col-md-4 col-6">
        <div class="stat-card" style="border-left:4px solid #25b574;">
            <div class="stat-card-label"><i class="bi bi-globe2"></i> Negara Terdaftar</div>
            <div class="stat-card-value">{{ $stats['total_countries'] }}</div>
        </div>
    </div>
    <div class="col-md-4 col-6">
        <div class="stat-card" style="border-left:4px solid #ffc107;">
            <div class="stat-card-label"><i class="bi bi-anchor"></i> Data Pelabuhan</div>
            <div class="stat-card-value">{{ $stats['total_ports'] }}</div>
        </div>
    </div>
    <div class="col-md-6 col-6">
        <div class="stat-card" style="border-left:4px solid #dc3545;">
            <div class="stat-card-label"><i class="bi bi-file-text-fill"></i> Artikel Analisis</div>
            <div class="stat-card-value">{{ $stats['total_articles'] }}</div>
        </div>
    </div>
    <div class="col-md-6 col-12">
        <div class="stat-card" style="border-left:4px solid #6f42c1;">
            <div class="stat-card-label"><i class="bi bi-speedometer2"></i> Risk Score Dihitung</div>
            <div class="stat-card-value">{{ $stats['total_risks'] }}</div>
        </div>
    </div>
</div>

<div class="row g-3">
    {{-- Recent Users --}}
    <div class="col-md-6">
        <div class="admin-card">
            <div class="admin-card-title"><i class="bi bi-people-fill"></i> User Terbaru</div>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Bergabung</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentUsers as $user)
                    <tr>
                        <td style="font-weight:500;">{{ $user->name }}</td>
                        <td style="color:var(--text-muted-custom);">{{ $user->email }}</td>
                        <td>
                            <span class="badge-admin {{ $user->role === 'admin' ? 'badge-admin-red' : 'badge-admin-blue' }}">
                                {{ $user->role }}
                            </span>
                        </td>
                        <td style="color:var(--text-muted-custom);">{{ $user->created_at->format('d M Y') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div style="margin-top:12px;">
                <a href="{{ route('admin.users') }}" style="color:#dc3545;font-size:13px;text-decoration:none;">
                    Lihat semua user <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>

    {{-- Recent Risk Scores --}}
    <div class="col-md-6">
        <div class="admin-card">
            <div class="admin-card-title"><i class="bi bi-speedometer2"></i> Risk Score Terbaru</div>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Negara</th>
                        <th>Score</th>
                        <th>Level</th>
                        <th>Waktu</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentRisks as $risk)
                    <tr>
                        <td style="font-weight:500;">{{ $risk->country->name ?? '—' }}</td>
                        <td style="color:#fff;font-weight:700;">{{ $risk->total_score }}</td>
                        <td>
                            <span class="badge-admin {{ $risk->risk_level === 'High' ? 'badge-admin-red' : 'badge-admin-blue' }}">
                                {{ $risk->risk_level }}
                            </span>
                        </td>
                        <td style="color:var(--text-muted-custom);font-size:12px;">
                            {{ \Carbon\Carbon::parse($risk->calculated_at)->format('d M H:i') }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection