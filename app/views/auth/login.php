<?php
// login.php

use App\core\View;

$error = (string)($_GET['error'] ?? '');
$loggedOut = isset($_GET['logged_out']) && $_GET['logged_out'] !== '';
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#050816">
    <title>Login - ZukBits</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        :root{
            --color-bg:#050816;
            --color-surface:#0b1020;
            --color-text:#f7f7ff;
            --color-text-muted:#c3c5d4;
            --color-border:#22263b;
            --color-primary:#38bdf8;
            --color-danger:#ef4444;
            --color-success:#34c759;
        }
        html,body{height:100%;}
        body{
            margin:0;
            background:var(--color-bg);
            color:var(--color-text);
            font-family:system-ui,-apple-system,"Segoe UI",Roboto,Arial,sans-serif;
        }
        .auth-shell{min-height:100%;display:flex;flex-direction:column;}
        .auth-top{padding:18px 14px;text-align:center;}
        .auth-brand{display:inline-flex;align-items:center;gap:10px;text-decoration:none;color:var(--color-text);}
        .brand-badge{
            width:40px;height:40px;border-radius:12px;
            display:inline-flex;align-items:center;justify-content:center;
            background:rgba(56,189,248,.12);
            border:1px solid rgba(56,189,248,.25);
            color:var(--color-primary);
            font-weight:800;
        }
        .brand-name{font-weight:800;letter-spacing:.2px;}
        .auth-main{flex:1;display:flex;align-items:center;justify-content:center;padding:14px;}
        .auth-card{
            width:min(460px, 100%);
            background:var(--color-surface);
            border:1px solid var(--color-border);
            border-radius:16px;
            padding:18px;
        }
        .auth-title{font-size:1.15rem;font-weight:800;margin:6px 0 4px;text-align:center;}
        .auth-subtitle{color:var(--color-text-muted);font-size:.92rem;margin:0 0 14px;text-align:center;}
        .form-label{font-weight:600;}
        .form-control{
            background:rgba(255,255,255,.02);
            border:1px solid var(--color-border);
            color:var(--color-text);
        }
        .form-control:focus{
            background:rgba(255,255,255,.03);
            border-color:rgba(56,189,248,.6);
            box-shadow:0 0 0 .2rem rgba(56,189,248,.12);
            color:var(--color-text);
        }
        .btn-primary{
            background:var(--color-primary);
            border:none;
            font-weight:800;
        }
        .btn-primary:hover{filter:brightness(.95);}
        .auth-link{color:var(--color-primary);text-decoration:none;}
        .auth-link:hover{text-decoration:underline;}
        .auth-alert{
            border-radius:12px;
            border:1px solid var(--color-border);
            padding:10px 12px;
            margin-bottom:12px;
            background:rgba(255,255,255,.02);
        }
        .auth-alert.danger{
            border-color:rgba(239,68,68,.35);
            background:rgba(239,68,68,.08);
            color:var(--color-danger);
        }
        .auth-alert.success{
            border-color:rgba(52,199,89,.35);
            background:rgba(52,199,89,.08);
            color:var(--color-success);
        }
        .pwd-toggle{
            border:1px solid var(--color-border);
            background:rgba(255,255,255,.02);
            color:var(--color-text-muted);
            border-radius:10px;
            padding:0 12px;
        }
        .pwd-toggle:hover{color:var(--color-text);}
        .auth-foot{padding:12px 14px;text-align:center;color:var(--color-text-muted);font-size:.85rem;}
        @media (max-height: 680px){
            .auth-main{align-items:flex-start;padding-top:10px;}
        }
    </style>
</head>
<body>
<div class="auth-shell">
    <header class="auth-top">
        <a class="auth-brand" href="/login" aria-label="Login">
            <span class="brand-badge">ZB</span>
            <span class="brand-name">ZukBits</span>
        </a>
    </header>

    <main class="auth-main">
        <section class="auth-card">
            <h1 class="auth-title">Welcome back</h1>
            <p class="auth-subtitle">Sign in to continue.</p>

            <?php if ($loggedOut): ?>
                <div class="auth-alert success">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    You’ve been logged out.
                </div>
            <?php endif; ?>

            <?php if ($error !== ''): ?>
                <div class="auth-alert danger">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <?= View::e($error) ?>
                </div>
            <?php endif; ?>

            <form method="post" action="/login" class="needs-validation" novalidate>
                <div class="mb-3">
                    <label for="email" class="form-label">Email address</label>
                    <div class="input-group">
                        <span class="input-group-text bg-transparent text-muted border-0 pe-0">
                            <i class="bi bi-envelope"></i>
                        </span>
                        <input
                            type="email"
                            name="email"
                            id="email"
                            class="form-control"
                            required
                            autocomplete="email"
                            placeholder="you@example.com"
                        >
                        <div class="invalid-feedback">Enter a valid email.</div>
                    </div>
                </div>

                <div class="mb-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <label for="password" class="form-label mb-0">Password</label>
                        <a href="/password/forgot" class="auth-link small">Forgot password?</a>
                    </div>

                    <div class="input-group mt-2">
                        <span class="input-group-text bg-transparent text-muted border-0 pe-0">
                            <i class="bi bi-lock"></i>
                        </span>
                        <input
                            type="password"
                            name="password"
                            id="password"
                            class="form-control"
                            required
                            autocomplete="current-password"
                            placeholder="••••••••"
                        >
                        <button class="pwd-toggle" type="button" id="togglePwd" aria-label="Toggle password">
                            <i class="bi bi-eye" id="togglePwdIcon"></i>
                        </button>
                        <div class="invalid-feedback">Password is required.</div>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center my-3">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="remember" name="remember" value="1">
                        <label class="form-check-label" for="remember">Remember me</label>
                    </div>
                    <span class="text-muted small"><i class="bi bi-shield-check me-1"></i>Secure sign-in</span>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary py-2">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Sign in
                    </button>
                    <div class="text-center text-muted small">
                        Need access? <span class="text-muted">Contact administrator.</span>
                    </div>
                </div>
            </form>
        </section>
    </main>

    <footer class="auth-foot">
        Secured System &bull; &copy; <?= View::e(date('Y')) ?> ZukBits Online
    </footer>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Bootstrap validation
    document.querySelectorAll('.needs-validation').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            if (!form.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
            }
            form.classList.add('was-validated');
        });
    });

    // Password toggle
    const toggle = document.getElementById('togglePwd');
    const input = document.getElementById('password');
    const icon = document.getElementById('togglePwdIcon');

    if (toggle && input && icon) {
        toggle.addEventListener('click', function () {
            const isPwd = input.getAttribute('type') === 'password';
            input.setAttribute('type', isPwd ? 'text' : 'password');
            icon.className = isPwd ? 'bi bi-eye-slash' : 'bi bi-eye';
        });
    }
});
</script>
</body>
</html>
