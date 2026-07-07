@extends('layouts.app')
@section('title', 'Watchlist')
@section('content')
<h5 style="color:#fff;margin-bottom:20px;">
    <i class="bi bi-bookmark-star-fill"></i> Favorite Monitoring List
</h5>
<div id="watchlist-content">
    <div style="color:var(--text-muted-custom);text-align:center;padding:60px 20px;">
        <i class="bi bi-bookmark-plus" style="font-size:48px;opacity:0.3;display:block;margin-bottom:15px;"></i>
        Belum ada negara di watchlist. Cari negara dan klik tombol Watchlist untuk menambahkan.
    </div>
</div>
@endsection