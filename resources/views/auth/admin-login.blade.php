<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login — Supply Chain Risk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            background: #0a0c10;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', sans-serif;
            position: relative;
            overflow: hidden;
        }

        /* Background merah gelap */
        body::before {
            content: '';
            position: fixed;
            top: -50%; left: -50%;
            width: 200%; height: 200%;
            background:
                radial-gradient(ellipse at 20% 50%, rgba(220,53,69,0.12) 0%, transparent 50%),
                radial-gradient(ellipse at 80% 20%, rgba(180,0,30,0.08) 0%, transparent 50%),
                radial-gradient(ellipse at 50% 80%, rgba(220,53,69,0.06) 0%, transparent 50%);
            animation: bgPulse 12s ease-in-out infinite alternate;
        }

        @keyframes bgPulse {
            0%   { transform: scale(1) rotate(0deg); }
            100% { transform: scale(1.05) rotate(2deg); }
        }

        /* Grid pattern */
        body::after {
            content: '';
            position: fixed;
            inset: 0;
            background-image:
                linear-gradient(rgba(220,53,69,0.05) 1px, transparent 1px),
                linear-gradient(90deg, rgba(220,53,69,0.05) 1px, transparent 1px);
            background-size: 40px 40px;
            pointer-events: none;
        }

        .admin-login-wrapper {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 440px;
            padding: 20px;
        }

        /* Logo */
        .admin-logo {
            text-align: center;
            margin-bottom: 28px;
        }

        .admin-logo-icon {
            width: 68px;
            height: 68px;
            background: linear-gradient(135deg, #dc3545, #a71d2a);
            border-radius: 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 14px;
            box-shadow: 0 8px 32px rgba(220,53,69,0.4),
                        0 0 0 1px rgba(220,53,69,0.2);
            animation: logoPulse 3s ease-in-out infinite;
        }

        @keyframes logoPulse {
            0%, 100% { box-shadow: 0 8px 32px rgba(220,53,69,0.4), 0 0 0 1px rgba(220,53,69,0.2); }
            50%       { box-shadow: 0 8px 48px rgba(220,53,69,0.6), 0 0 0 3px rgba(220,53,69,0.15); }
        }

        .admin-logo h4 {
            color: #fff;
            font-weight: 800;
            font-size: 20px;
            margin: 0 0 4px;
        }

        .admin-logo p {
            color: #6c757d;
            font-size: 13px;
            margin: 0;
        }

        /* Badge ADMIN */
        .admin-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            background: rgba(220,53,69,0.15);
            border: 1px solid rgba(220,53,69,0.4);
            color: #ff6b7a;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-top: 8px;
        }

        /* Card */
        .admin-card {
            background: rgba(18,20,28,0.97);
            border: 1px solid rgba(220,53,69,0.25);
            border-top: 3px solid #dc3545;
            border-radius: 16px;
            padding: 36px;
            backdrop-filter: blur(20px);
            box-shadow: 0 20px 60px rgba(0,0,0,0.6),
                        0 0 40px rgba(220,53,69,0.08);
        }

        .admin-card h5 {
            color: #fff;
            font-weight: 700;
            font-size: 18px;
            margin-bottom: 4px;
        }

        .admin-card p {
            color: #6c757d;
            font-size: 13px;
            margin-bottom: 28px;
        }

        /* Warning box */
        .admin-warning {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            background: rgba(220,53,69,0.08);
            border: 1px solid rgba(220,53,69,0.2);
            border-radius: 8px;
            padding: 12px 14px;
            margin-bottom: 22px;
        }

        .admin-warning i {
            color: #dc3545;
            font-size: 16px;
            flex-shrink: 0;
            margin-top: 1px;
        }

        .admin-warning span {
            color: #adb5bd;
            font-size: 12px;
            line-height: 1.5;
        }

        /* Form */
        .form-label {
            color: #adb5bd;
            font-size: 13px;
            font-weight: 500;
            margin-bottom: 6px;
        }

        .form-control {
            background: #0a0c10 !important;
            border: 1px solid rgba(220,53,69,0.2) !important;
            color: #fff !important;
            border-radius: 8px;
            padding: 11px 14px 11px 40px;
            font-size: 14px;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .form-control:focus {
            border-color: #dc3545 !important;
            box-shadow: 0 0 0 3px rgba(220,53,69,0.15) !important;
            outline: none;
        }

        .form-control::placeholder { color: #3a3d4e !important; }

        .input-wrapper {
            position: relative;
            margin-bottom: 16px;
        }

        .input-icon {
            position: absolute;
            left: 13px;
            top: 50%;
            transform: translateY(-50%);
            color: #dc3545;
            font-size: 15px;
            pointer-events: none;
        }

        .toggle-password {
            position: absolute;
            right: 13px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
            cursor: pointer;
            font-size: 15px;
            background: none;
            border: none;
            padding: 0;
        }

        .toggle-password:hover { color: #fff; }

        /* Error */
        .alert-error {
            background: rgba(220,53,69,0.1);
            border: 1px solid rgba(220,53,69,0.4);
            border-radius: 8px;
            padding: 12px 14px;
            color: #ff6b7a;
            font-size: 13px;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* Button */
        .btn-admin-login {
            background: linear-gradient(135deg, #dc3545, #a71d2a);
            border: none;
            color: #fff;
            padding: 12px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 700;
            width: 100%;
            cursor: pointer;
            transition: transform 0.15s, box-shadow 0.15s;
            box-shadow: 0 4px 15px rgba(220,53,69,0.4);
            margin-top: 8px;
            letter-spacing: 0.3px;
        }

        .btn-admin-login:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 25px rgba(220,53,69,0.5);
        }

        .btn-admin-login:active { transform: translateY(0); }

        /* Remember */
        .form-check-input {
            background-color: #0a0c10 !important;
            border-color: rgba(220,53,69,0.3) !important;
        }

        .form-check-input:checked {
            background-color: #dc3545 !important;
            border-color: #dc3545 !important;
        }

        .form-check-label {
            color: #6c757d;
            font-size: 13px;
            cursor: pointer;
        }

        /* Back link */
        .back-link {
            text-align: center;
            margin-top: 20px;
        }

        .back-link a {
            color: #6c757d;
            font-size: 13px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: color 0.2s;
        }

        .back-link a:hover { color: #adb5bd; }

        /* Footer */
        .admin-footer {
            text-align: center;
            margin-top: 20px;
            color: #2a2d3e;
            font-size: 11px;
        }

        @keyframes spin { to { transform: rotate(360deg); } }
    </style>
</head>
<body>

<div class="admin-login-wrapper">

    {{-- Logo --}}
    <div class="admin-logo">
        <div class="admin-logo-icon">
            <i class="bi bi-shield-lock-fill" style="color:#fff;font-size:28px;"></i>
        </div>
        <h4>Admin Portal</h4>
        <p>Supply Chain Risk Intelligence</p>
        <div class="admin-badge">
            <i class="bi bi-shield-fill-exclamation"></i>
            Restricted Access
        </div>
    </div>

    {{-- Card --}}
    <div class="admin-card">
        <h5>Administrator Login</h5>
        <p>Masukkan kredensial admin untuk mengakses panel kontrol</p>

        {{-- Warning --}}
        <div class="admin-warning">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <span>Halaman ini hanya untuk administrator sistem. Akses tidak sah akan dicatat dan dilaporkan.</span>
        </div>

        {{-- Error --}}
        @if($errors->any())
        <div class="alert-error">
            <i class="bi bi-x-circle-fill"></i>
            {{ $errors->first() }}
        </div>
        @endif

        {{-- Form --}}
        <form method="POST" action="{{ route('admin.login.post') }}" id="admin-form">
            @csrf

            <div>
                <label class="form-label">Email Admin</label>
                <div class="input-wrapper">
                    <i class="bi bi-envelope-fill input-icon"></i>
                    <input type="email" name="email" class="form-control"
                           placeholder="admin@supplychain.test"
                           value="{{ old('email') }}" required autofocus>
                </div>
            </div>

            <div>
                <label class="form-label">Password Admin</label>
                <div class="input-wrapper">
                    <i class="bi bi-lock-fill input-icon"></i>
                    <input type="password" name="password" id="pwd"
                           class="form-control" placeholder="••••••••" required>
                    <button type="button" class="toggle-password" onclick="togglePwd()">
                        <i class="bi bi-eye" id="pwd-icon"></i>
                    </button>
                </div>
            </div>

            <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
                <input type="checkbox" class="form-check-input" name="remember"
                       id="remember" style="margin:0;">
                <label class="form-check-label" for="remember">Sesi admin tetap aktif</label>
            </div>

            <button type="submit" class="btn-admin-login" id="admin-btn">
                <i class="bi bi-shield-lock-fill"></i> Masuk ke Admin Panel
            </button>
        </form>
    </div>

    {{-- Back to user login --}}
    <div class="back-link">
        <a href="{{ route('login') }}">
            <i class="bi bi-arrow-left"></i> Kembali ke Login User
        </a>
    </div>

    <div class="admin-footer">
        Supply Chain Risk Intelligence · Admin Portal<br>
        Akses terbatas — Universitas Malikussaleh {{ date('Y') }}
    </div>

</div>

<script>
function togglePwd() {
    var inp  = document.getElementById('pwd');
    var icon = document.getElementById('pwd-icon');
    if (inp.type === 'password') {
        inp.type = 'text';
        icon.className = 'bi bi-eye-slash';
    } else {
        inp.type = 'password';
        icon.className = 'bi bi-eye';
    }
}

document.getElementById('admin-form').addEventListener('submit', function() {
    var btn = document.getElementById('admin-btn');
    btn.style.opacity = '0.7';
    btn.style.pointerEvents = 'none';
    btn.innerHTML = '<span style="display:inline-block;width:14px;height:14px;border:2px solid rgba(255,255,255,0.3);' +
        'border-top-color:#fff;border-radius:50%;animation:spin 0.6s linear infinite;margin-right:6px;vertical-align:middle;"></span>' +
        ' Memverifikasi...';
});
</script>
</body>
</html>