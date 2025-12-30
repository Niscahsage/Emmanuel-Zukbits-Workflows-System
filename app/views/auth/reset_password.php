<?php
// reset_password.php

use App\core\View;

$token = (string)($_GET['token'] ?? '');
$email = (string)($_GET['email'] ?? '');
$error = (string)($_GET['error'] ?? '');
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#050816">
    <title>Set New Password - ZukBits</title>

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
            width:min(520px, 100%);
            background:var(--color-surface);
            border:1px solid var(--color-border);
            border-radius:16px;
            padding:18px;
        }
        .auth-title{font-size:1.1rem;font-weight:800;margin:6px 0 4px;text-align:center;}
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
            border:1px solid rgba(239,68,68,.35);
            padding:10px 12px;
            margin-bottom:12px;
            background:rgba(239,68,68,.08);
            color:var(--color-danger);
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
        @media (max-height: 720px){
            .auth-main{align-items:flex-start;padding-top:10px;}
        }
    </style>
</head>
<body>
<div class="auth-shell">
    <header class="auth-top">
        <a class="auth-brand" href="/login" aria-label="Go to login">
            <span class="brand-badge">ZB</span>
            <span class="brand-name">ZukBits</span>
        </a>
    </header>

    <main class="auth-main">
        <section class="auth-card">
            <h1 class="auth-title">Set new password</h1>
            <p class="auth-subtitle">Choose a strong password for your account.</p>

            <?php if ($error !== ''): ?>
                <div class="auth-alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <?= View::e($error) ?>
                </div>
            <?php endif; ?>

            <form method="post" action="/reset-password" class="needs-validation" novalidate>
                <input type="hidden" name="token" value="<?= View::e($token) ?>">

                <div class="mb-3">
                    <label for="email" class="form-label">Email address</label>
                    <input
                        type="email"
                        name="email"
                        id="email"
                        class="form-control"
                        required
                        value="<?= View::e($email) ?>"
                        readonly
                    >
                    <div class="invalid-feedback">Email is required.</div>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">New password</label>
                    <div class="input-group">
                        <span class="input-group-text bg-transparent text-muted border-0 pe-0">
                            <i class="bi bi-lock"></i>
                        </span>
                        <input
                            type="password"
                            name="password"
                            id="password"
                            class="form-control"
                            required
                            minlength="8"
                            autocomplete="new-password"
                            placeholder="Minimum 8 characters"
                        >
                        <button class="pwd-toggle" type="button" data-toggle="pwd" data-target="password">
                            <i class="bi bi-eye"></i>
                        </button>
                        <div class="invalid-feedback">Password must be at least 8 characters.</div>
                    </div>
                    <div class="form-text text-muted mt-2">Use letters + numbers + symbols if possible.</div>
                </div>

                <div class="mb-3">
                    <label for="password_confirmation" class="form-label">Confirm new password</label>
                    <div class="input-group">
                        <span class="input-group-text bg-transparent text-muted border-0 pe-0">
                            <i class="bi bi-lock-fill"></i>
                        </span>
                        <input
                            type="password"
                            name="password_confirmation"
                            id="password_confirmation"
                            class="form-control"
                            required
                            minlength="8"
                            autocomplete="new-password"
                            placeholder="Repeat password"
                        >
                        <button class="pwd-toggle" type="button" data-toggle="pwd" data-target="password_confirmation">
                            <i class="bi bi-eye"></i>
                        </button>
                        <div class="invalid-feedback">Please confirm your password.</div>
                    </div>
                    <div class="text-danger small mt-2 d-none" id="matchError">
                        <i class="bi bi-x-circle me-1"></i>Passwords do not match.
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary py-2" id="submitBtn">
                        <i class="bi bi-check-circle me-2"></i>Set new password
                    </button>
                    <a href="/login" class="auth-link text-center">
                        <i class="bi bi-arrow-left me-1"></i>Back to login
                    </a>
                </div>
            </form>
        </section>
    </main>

    <footer class="auth-foot">
        &copy; <?= View::e(date('Y')) ?> ZukBits Online
    </footer>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Bootstrap validation
    document.querySelectorAll('.needs-validation').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            const p1 = document.getElementById('password');
            const p2 = document.getElementById('password_confirmation');
            const matchError = document.getElementById('matchError');

            const match = p1 && p2 ? (p1.value === p2.value) : true;
            if (matchError) matchError.classList.toggle('d-none', match);

            if (!form.checkValidity() || !match) {
                e.preventDefault();
                e.stopPropagation();
            }
            form.classList.add('was-validated');
        });
    });

    // Password toggles
    document.querySelectorAll('[data-toggle="pwd"]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const id = btn.getAttribute('data-target');
            const input = document.getElementById(id);
            const icon = btn.querySelector('i');
            if (!input || !icon) return;

            const isPwd = input.getAttribute('type') === 'password';
            input.setAttribute('type', isPwd ? 'text' : 'password');
            icon.className = isPwd ? 'bi bi-eye-slash' : 'bi bi-eye';
        });
    });
});
</script>
</body>
</html>
