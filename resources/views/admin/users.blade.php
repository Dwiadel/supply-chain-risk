@extends('admin.layout')
@section('title', 'Manajemen User')
@section('content')

<div class="admin-card">
    <div class="admin-card-title"><i class="bi bi-people-fill"></i> Daftar User ({{ $users->total() }})</div>
    <table class="admin-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Nama</th>
                <th>Email</th>
                <th>Role</th>
                <th>Bergabung</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
            <tr>
                <td style="color:var(--text-muted-custom);">{{ $user->id }}</td>
                <td style="font-weight:500;">{{ $user->name }}</td>
                <td style="color:var(--text-muted-custom);">{{ $user->email }}</td>
                <td>
                    <form action="{{ route('admin.users.role', $user) }}" method="POST" style="display:inline;">
                        @csrf @method('PATCH')
                        <select name="role" onchange="this.form.submit()"
                                style="background:var(--dark-bg);border:1px solid var(--card-border);
                                       color:var(--text-main);border-radius:6px;padding:3px 8px;font-size:12px;">
                            <option value="user"  {{ $user->role === 'user'  ? 'selected' : '' }}>user</option>
                            <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>admin</option>
                        </select>
                    </form>
                </td>
                <td style="color:var(--text-muted-custom);">{{ $user->created_at->format('d M Y') }}</td>
                <td>
                    <form action="{{ route('admin.users.delete', $user) }}" method="POST"
                          onsubmit="return confirm('Hapus user {{ $user->name }}?')">
                        @csrf @method('DELETE')
                        <button type="submit"
                                style="background:rgba(220,53,69,0.15);border:1px solid rgba(220,53,69,0.3);
                                       color:#ff6b7a;padding:4px 12px;border-radius:6px;font-size:12px;cursor:pointer;">
                            <i class="bi bi-trash3"></i> Hapus
                        </button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top:20px;">
        {{ $users->links() }}
    </div>
</div>

@endsection