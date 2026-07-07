@extends('admin.layout')
@section('title', 'Dataset Pelabuhan')
@section('content')

<div class="row g-3">
    {{-- Form Tambah Pelabuhan --}}
    <div class="col-md-4">
        <div class="admin-card">
            <div class="admin-card-title"><i class="bi bi-plus-circle-fill"></i> Tambah Pelabuhan Baru</div>
            <form action="{{ route('admin.ports.store') }}" method="POST">
                @csrf
                <div style="margin-bottom:12px;">
                    <label style="color:var(--text-muted-custom);font-size:12px;margin-bottom:4px;display:block;">Nama Pelabuhan *</label>
                    <input type="text" name="name" class="form-control" placeholder="Port of Jakarta" required>
                </div>
                <div style="margin-bottom:12px;">
                    <label style="color:var(--text-muted-custom);font-size:12px;margin-bottom:4px;display:block;">Nama Negara *</label>
                    <input type="text" name="country_name" class="form-control" placeholder="Indonesia" required>
                </div>
                <div style="margin-bottom:12px;">
                    <label style="color:var(--text-muted-custom);font-size:12px;margin-bottom:4px;display:block;">Kode Negara (2 huruf)</label>
                    <input type="text" name="cca2" class="form-control" placeholder="ID" maxlength="2">
                </div>
                <div class="row g-2" style="margin-bottom:12px;">
                    <div class="col-6">
                        <label style="color:var(--text-muted-custom);font-size:12px;margin-bottom:4px;display:block;">Latitude *</label>
                        <input type="number" name="latitude" step="any" class="form-control" placeholder="-6.10" required>
                    </div>
                    <div class="col-6">
                        <label style="color:var(--text-muted-custom);font-size:12px;margin-bottom:4px;display:block;">Longitude *</label>
                        <input type="number" name="longitude" step="any" class="form-control" placeholder="106.88" required>
                    </div>
                </div>
                <div style="margin-bottom:16px;">
                    <label style="color:var(--text-muted-custom);font-size:12px;margin-bottom:4px;display:block;">Kategori Ukuran *</label>
                    <select name="size_category" class="form-select" required>
                        <option value="Large">Large</option>
                        <option value="Medium" selected>Medium</option>
                        <option value="Small">Small</option>
                    </select>
                </div>
                <button type="submit" class="btn-admin-primary w-100">
                    <i class="bi bi-plus-circle"></i> Tambahkan Pelabuhan
                </button>
            </form>
        </div>
    </div>

    {{-- Tabel Pelabuhan --}}
    <div class="col-md-8">
        <div class="admin-card">
            <div class="admin-card-title">
                <i class="bi bi-anchor"></i> Dataset Pelabuhan ({{ $ports->total() }})
            </div>
            <div style="max-height:550px;overflow-y:auto;">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Nama Pelabuhan</th>
                            <th>Negara</th>
                            <th>Koordinat</th>
                            <th>Ukuran</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($ports as $port)
                        <tr>
                            <td style="font-weight:500;">{{ $port->name }}</td>
                            <td>
                                {{ $port->country_name }}
                                @if($port->cca2)
                                    <span style="color:var(--text-muted-custom);font-size:11px;">({{ $port->cca2 }})</span>
                                @endif
                            </td>
                            <td style="color:var(--text-muted-custom);font-size:12px;">
                                {{ number_format($port->latitude, 4) }}, {{ number_format($port->longitude, 4) }}
                            </td>
                            <td>
                                <span class="badge-admin {{ $port->size_category === 'Large' ? 'badge-admin-red' : 'badge-admin-blue' }}">
                                    {{ $port->size_category }}
                                </span>
                            </td>
                            <td>
                                <form action="{{ route('admin.ports.delete', $port) }}" method="POST"
                                      onsubmit="return confirm('Hapus pelabuhan {{ $port->name }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            style="background:rgba(220,53,69,0.15);border:1px solid rgba(220,53,69,0.3);
                                                   color:#ff6b7a;padding:3px 10px;border-radius:6px;font-size:12px;cursor:pointer;">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div style="margin-top:15px;">{{ $ports->links() }}</div>
        </div>
    </div>
</div>

@endsection