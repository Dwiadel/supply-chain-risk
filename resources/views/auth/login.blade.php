<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Supply Chain Risk Intelligence</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            background: #0f1117;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', sans-serif;
            position: relative;
            overflow: hidden;
        }

        /* Background animasi */
        body::before {
            content: '';
            position: fixed;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(ellipse at 20% 50%, rgba(253,126,20,0.10) 0%, transparent 50%),
                        radial-gradient(ellipse at 80% 20%, rgba(37,181,116,0.06) 0%, transparent 50%),
                        radial-gradient(ellipse at 60% 80%, rgba(220,53,69,0.05) 0%, transparent 50%);
            animation: bgMove 15s ease-in-out infinite alternate;
        }

        @keyframes bgMove {
            0%   { transform: translate(0, 0); }
            100% { transform: translate(3%, 5%); }
        }

        /* Grid background */
        body::after {
            content: '';
            position: fixed;
            inset: 0;
            background-image:
                linear-gradient(rgba(42,45,62,0.3) 1px, transparent 1px),
                linear-gradient(90deg, rgba(42,45,62,0.3) 1px, transparent 1px);
            background-size: 40px 40px;
            pointer-events: none;
        }

        .login-wrapper {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 440px;
            padding: 20px;
        }

        /* Logo */
        .login-logo {
            text-align: center;
            margin-bottom: 32px;
        }

        .logo-icon {
            width: 64px;
            height: 64px;
            background: linear-gradient(135deg, #fd7e14, #e8590c);
            border-radius: 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 16px;
            box-shadow: 0 8px 32px rgba(253,126,20,0.35);
        }

        .login-logo h4 {
            color: #fff;
            font-weight: 800;
            font-size: 20px;
            margin: 0 0 4px;
            letter-spacing: -0.3px;
        }

        .login-logo p {
            color: #8b8fa8;
            font-size: 13px;
            margin: 0;
        }

        /* Card */
        .login-card {
            background: rgba(26,29,39,0.95);
            border: 1px solid #2a2d3e;
            border-radius: 16px;
            padding: 36px;
            backdrop-filter: blur(20px);
            box-shadow: 0 20px 60px rgba(0,0,0,0.5), 0 0 0 1px rgba(255,255,255,0.03);
        }

        .login-card h5 {
            color: #fff;
            font-weight: 700;
            font-size: 18px;
            margin-bottom: 6px;
        }

        .login-card p {
            color: #8b8fa8;
            font-size: 13px;
            margin-bottom: 28px;
        }

        /* Form */
        .form-label {
            color: #b0b3c8;
            font-size: 13px;
            font-weight: 500;
            margin-bottom: 6px;
        }

        .form-control {
            background: #0f1117 !important;
            border: 1px solid #2a2d3e !important;
            color: #fff !important;
            border-radius: 8px;
            padding: 11px 14px 11px 40px;
            font-size: 14px;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .form-control:focus {
            border-color: #fd7e14 !important;
            box-shadow: 0 0 0 3px rgba(253,126,20,0.18) !important;
            outline: none;
        }

        .form-control::placeholder { color: #4a4d5e !important; }

        .input-wrapper {
            position: relative;
            margin-bottom: 16px;
        }

        .input-icon {
            position: absolute;
            left: 13px;
            top: 50%;
            transform: translateY(-50%);
            color: #8b8fa8;
            font-size: 15px;
            pointer-events: none;
        }

        .toggle-password {
            position: absolute;
            right: 13px;
            top: 50%;
            transform: translateY(-50%);
            color: #8b8fa8;
            cursor: pointer;
            font-size: 15px;
            background: none;
            border: none;
            padding: 0;
        }

        .toggle-password:hover { color: #fff; }

        /* Remember me */
        .form-check-input {
            background-color: #0f1117 !important;
            border-color: #2a2d3e !important;
            width: 16px;
            height: 16px;
        }

        .form-check-input:checked {
            background-color: #fd7e14 !important;
            border-color: #fd7e14 !important;
        }

        .form-check-label {
            color: #8b8fa8;
            font-size: 13px;
            cursor: pointer;
        }

        /* Button */
        .btn-login {
            background: linear-gradient(135deg, #fd7e14, #e8590c);
            border: none;
            color: #fff;
            padding: 12px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            width: 100%;
            cursor: pointer;
            transition: transform 0.15s, box-shadow 0.15s;
            box-shadow: 0 4px 15px rgba(253,126,20,0.35);
            margin-top: 8px;
        }

        .btn-login:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(253,126,20,0.45);
        }

        .btn-login:active { transform: translateY(0); }

        /* Error */
        .alert-error {
            background: rgba(220,53,69,0.1);
            border: 1px solid rgba(220,53,69,0.3);
            border-radius: 8px;
            padding: 12px 14px;
            color: #ff6b7a;
            font-size: 13px;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* Demo credentials */
        .demo-creds {
            margin-top: 20px;
            padding: 14px;
            background: rgba(253,126,20,0.08);
            border: 1px solid rgba(253,126,20,0.25);
            border-radius: 8px;
        }

        .demo-creds-title {
            color: #fd9843;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-bottom: 8px;
        }

        .demo-cred-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 5px 0;
            border-bottom: 1px solid rgba(42,45,62,0.5);
        }

        .demo-cred-item:last-child { border-bottom: none; }

        .demo-cred-label {
            color: #8b8fa8;
            font-size: 12px;
        }

        .demo-cred-value {
            color: #b0b3c8;
            font-size: 12px;
            font-family: monospace;
            cursor: pointer;
            padding: 2px 8px;
            background: rgba(255,255,255,0.05);
            border-radius: 4px;
            transition: background 0.15s;
        }

        .demo-cred-value:hover { background: rgba(253,126,20,0.2); color: #fff; }

        /* Footer */
        .login-footer {
            text-align: center;
            margin-top: 20px;
            color: #4a4d5e;
            font-size: 12px;
        }

        /* Loading state */
        .btn-login.loading {
            opacity: 0.7;
            pointer-events: none;
        }
    </style>
</head>
<body>

<div class="login-wrapper">

    {{-- Logo --}}
    <div class="login-logo">
        <div class="logo-icon">
            <i class="bi bi-globe2" style="color:#fff;font-size:28px;"></i>
        </div>
        <h4>Supply Chain Risk</h4>
        <p>Global Risk Intelligence Platform</p>
    </div>

    {{-- Card Login --}}
    <div class="login-card">
        <h5>Selamat Datang</h5>
        <p>Masuk untuk mengakses dashboard monitoring risiko rantai pasok global</p>

        {{-- Error --}}
        @if($errors->any())
        <div class="alert-error">
            <i class="bi bi-exclamation-triangle-fill"></i>
            {{ $errors->first() }}
        </div>
        @endif

        {{-- Form --}}
        <form method="POST" action="{{ route('login.post') }}" id="login-form">
            @csrf

            {{-- Email --}}
            <div>
                <label class="form-label">Email Address</label>
                <div class="input-wrapper">
                    <i class="bi bi-envelope-fill input-icon"></i>
                    <input type="email" name="email" class="form-control"
                           placeholder="admin@supplychain.test"
                           value="{{ old('email') }}" required autofocus>
                </div>
            </div>

            {{-- Password --}}
            <div>
                <label class="form-label">Password</label>
                <div class="input-wrapper">
                    <i class="bi bi-lock-fill input-icon"></i>
                    <input type="password" name="password" id="password-input"
                           class="form-control" placeholder="••••••••" required>
                    <button type="button" class="toggle-password"
                            onclick="togglePassword()">
                        <i class="bi bi-eye" id="toggle-icon"></i>
                    </button>
                </div>
            </div>

            {{-- Remember Me --}}
            <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
                <input type="checkbox" class="form-check-input" name="remember"
                       id="remember" style="margin:0;">
                <label class="form-check-label" for="remember">Ingat saya</label>
            </div>

            {{-- Submit --}}
            <button type="submit" class="btn-login" id="login-btn">
                <i class="bi bi-box-arrow-in-right"></i> Masuk ke Dashboard
            </button>
        </form>

        {{-- Demo Credentials --}}
        <div class="demo-creds">
            <div class="demo-creds-title"><i class="bi bi-info-circle"></i> Akun Demo</div>
            <div class="demo-cred-item">
                <span class="demo-cred-label">👑 Admin</span>
                <span class="demo-cred-value"
                      onclick="fillCredentials('admin@supplychain.test','password')">
                    admin@supplychain.test
                </span>
            </div>
            <div class="demo-cred-item">
                <span class="demo-cred-label">👤 User</span>
                <span class="demo-cred-value"
                      onclick="fillCredentials('user@supplychain.test','password')">
                    user@supplychain.test
                </span>
            </div>
            <div style="color:#4a4d5e;font-size:11px;margin-top:8px;text-align:center;">
                Klik email di atas untuk isi otomatis · Password: <code style="color:#8b8fa8;">password</code>
            </div>
        </div>
    </div>

    {{-- Footer --}}
    <div class="login-footer">
        Global Supply Chain Risk Intelligence Platform<br>
        Universitas Malikussaleh · {{ date('Y') }}
    </div>
    {{-- Link ke admin login --}}
<div style="text-align:center;margin-top:15px;">
    <a href="{{ route('admin.login') }}"
       style="color:#4a4d5e;font-size:12px;text-decoration:none;
              display:inline-flex;align-items:center;gap:5px;transition:color 0.2s;"
       onmouseenter="this.style.color='#8b8fa8'"
       onmouseleave="this.style.color='#4a4d5e'">
        <i class="bi bi-shield-lock-fill" style="color:#dc3545;"></i>
        Masuk sebagai Administrator
    </a>
</div>

<script>
function togglePassword() {
    var input = document.getElementById('password-input');
    var icon  = document.getElementById('toggle-icon');
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'bi bi-eye-slash';
    } else {
        input.type = 'password';
        icon.className = 'bi bi-eye';
    }
}

function fillCredentials(email, password) {
    document.querySelector('input[name="email"]').value    = email;
    document.querySelector('input[name="password"]').value = password;
    document.querySelector('input[name="email"]').focus();
}

document.getElementById('login-form').addEventListener('submit', function() {
    var btn = document.getElementById('login-btn');
    btn.classList.add('loading');
    btn.innerHTML = '<span style="display:inline-block;width:14px;height:14px;border:2px solid rgba(255,255,255,0.3);border-top-color:#fff;border-radius:50%;animation:spin 0.6s linear infinite;margin-right:6px;"></span> Memproses...';
});

// Spin animation
var style = document.createElement('style');
style.textContent = '@keyframes spin { to { transform: rotate(360deg); } }';
document.head.appendChild(style);
</script>
</body>
</html>