@extends('admin.layout')
@section('title', 'Artikel Analisis')
@section('content')

<div class="row g-3">
    {{-- Form Tambah Artikel --}}
    <div class="col-md-4">
        <div class="admin-card">
            <div class="admin-card-title"><i class="bi bi-plus-circle-fill"></i> Tulis Artikel Baru</div>
            <form action="{{ route('admin.articles.store') }}" method="POST">
                @csrf
                <div style="margin-bottom:12px;">
                    <label style="color:var(--text-muted-custom);font-size:12px;margin-bottom:4px;display:block;">Judul Artikel *</label>
                    <input type="text" name="title" class="form-control"
                           placeholder="Analisis Risiko Rantai Pasok Q3 2026" required>
                </div>
                <div style="margin-bottom:12px;">
                    <label style="color:var(--text-muted-custom);font-size:12px;margin-bottom:4px;display:block;">Kategori *</label>
                    <select name="category" class="form-select" required>
                        <option value="analysis">Analisis Risiko</option>
                        <option value="logistics">Logistik</option>
                        <option value="economy">Ekonomi</option>
                        <option value="geopolitics">Geopolitik</option>
                        <option value="weather">Cuaca & Iklim</option>
                    </select>
                </div>
                <div style="margin-bottom:16px;">
                    <label style="color:var(--text-muted-custom);font-size:12px;margin-bottom:4px;display:block;">Konten Artikel *</label>
                    <textarea name="content" class="form-control" rows="8"
                              placeholder="Tulis konten artikel analisis di sini..." required
                              style="resize:vertical;"></textarea>
                </div>
                <button type="submit" class="btn-admin-primary w-100">
                    <i class="bi bi-send-fill"></i> Publikasikan Artikel
                </button>
            </form>
        </div>
    </div>

    {{-- Daftar Artikel --}}
    <div class="col-md-8">
        <div class="admin-card">
            <div class="admin-card-title">
                <i class="bi bi-file-text-fill"></i> Daftar Artikel ({{ $articles->total() }})
            </div>
            @forelse($articles as $article)
            <div style="padding:15px 0;border-bottom:1px solid var(--card-border);">
                <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:15px;">
                    <div style="flex:1;">
                        <div style="display:flex;align-items:center;gap:8px;margin-bottom:6px;">
                            <span class="badge-admin badge-admin-blue" style="font-size:10px;">
                                {{ $article->category }}
                            </span>
                            <span style="color:var(--text-muted-custom);font-size:11px;">
                                {{ $article->created_at->format('d M Y H:i') }}
                            </span>
                        </div>
                        <div style="color:#fff;font-weight:600;font-size:14px;margin-bottom:6px;">
                            {{ $article->title }}
                        </div>
                        <div style="color:var(--text-muted-custom);font-size:12px;line-height:1.5;">
                            {{ Str::limit($article->content, 150) }}
                        </div>
                    </div>
                    <form action="{{ route('admin.articles.delete', $article) }}" method="POST"
                          onsubmit="return confirm('Hapus artikel ini?')" style="flex-shrink:0;">
                        @csrf @method('DELETE')
                        <button type="submit"
                                style="background:rgba(220,53,69,0.15);border:1px solid rgba(220,53,69,0.3);
                                       color:#ff6b7a;padding:6px 12px;border-radius:6px;font-size:12px;cursor:pointer;">
                            <i class="bi bi-trash3"></i> Hapus
                        </button>
                    </form>
                </div>
            </div>
            @empty
            <div style="text-align:center;padding:40px;color:var(--text-muted-custom);">
                <i class="bi bi-file-earmark-x" style="font-size:32px;opacity:0.3;display:block;margin-bottom:10px;"></i>
                Belum ada artikel. Tulis artikel pertama di form kiri.
            </div>
            @endforelse

            <div style="margin-top:15px;">{{ $articles->links() }}</div>
        </div>
    </div>
</div>

@endsection