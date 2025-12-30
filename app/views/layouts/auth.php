<?php
// app/views/layouts/auth.php
// Layout for authentication pages (login, reset password, etc.)

$app       = app_config();
$appName   = $app['name'] ?? 'App';
$pageTitle = $pageTitle ?? 'Login';

// optional: controller can pass logo text from db/config
$logoText  = $logoText ?? ($app['logo_text'] ?? null);

// optional: controller can pass footer links from db
$footerLinks = $footerLinks ?? [];
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?= htmlspecialchars($appName . ' – ' . $pageTitle, ENT_QUOTES, 'UTF-8') ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#0b1020">

    <!-- Bootstrap 5 CSS (CDN) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons (CDN) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root{
            --zb-bg:#0b1020;
            --zb-surface:#0f172a;
            --zb-text:#f7f7ff;
            --zb-muted:#c3c5d4;
            --zb-border:rgba(255,255,255,.10);
            --zb-accent:#38bdf8;
        }
        body[data-theme="light"]{
            --zb-bg:#f5f7fb;
            --zb-surface:#ffffff;
            --zb-text:#0b1020;
            --zb-muted:#667085;
            --zb-border:rgba(0,0,0,.10);
            --zb-accent:#0ea5e9;
        }

        body{
            background: var(--zb-bg);
            color: var(--zb-text);
            min-height: 100vh;
        }

        .zb-auth-wrap{
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.25rem;
        }

        .zb-auth-card{
            width: 100%;
            max-width: 460px;
            background: var(--zb-surface);
            border: 1px solid var(--zb-border);
            border-radius: 1rem;
            box-shadow: 0 18px 50px rgba(0,0,0,.35);
            overflow: hidden;
        }

        .zb-auth-head{
            padding: 1.25rem 1.25rem 1rem;
            border-bottom: 1px solid var(--zb-border);
        }

        .zb-brand{
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap:.75rem;
        }

        .zb-brand-left{
            display:flex;
            align-items:center;
            gap:.75rem;
            min-width: 0;
        }

        .zb-logo{
            width: 40px;
            height: 40px;
            border-radius: .8rem;
            display:inline-flex;
            align-items:center;
            justify-content:center;
            font-weight: 800;
            color: #fff;
            background: var(--zb-accent);
            flex: 0 0 auto;
        }

        .zb-brand-text{
            min-width: 0;
        }

        .zb-brand-name{
            margin: 0;
            font-size: 1rem;
            font-weight: 700;
            line-height: 1.2;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .zb-brand-sub{
            margin: 0;
            font-size: .85rem;
            color: var(--zb-muted);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .zb-auth-body{
            padding: 1.25rem;
        }

        .zb-auth-foot{
            padding: .9rem 1.25rem;
            border-top: 1px solid var(--zb-border);
            color: var(--zb-muted);
            font-size: .85rem;
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap: .75rem;
            flex-wrap: wrap;
        }

        .form-control, .form-select{
            background: transparent;
            color: var(--zb-text);
            border-color: var(--zb-border);
        }

        body[data-theme="light"] .form-control,
        body[data-theme="light"] .form-select{
            background: #fff;
        }

        .form-control:focus, .form-select:focus{
            border-color: var(--zb-accent);
            box-shadow: 0 0 0 .25rem rgba(56,189,248,.15);
        }

        .btn-zb{
            background: var(--zb-accent);
            border-color: var(--zb-accent);
            color: #fff;
            font-weight: 600;
        }

        .btn-zb:hover{
            opacity: .92;
            color: #fff;
        }
    </style>
</head>
<body>
<div class="zb-auth-wrap">
    <div class="zb-auth-card">
        <div class="zb-auth-head">
            <div class="zb-brand">
                <div class="zb-brand-left">
                    <div class="zb-logo"><?= htmlspecialchars($logoText ?: mb_substr($appName, 0, 1), ENT_QUOTES, 'UTF-8') ?></div>
                    <div class="zb-brand-text">
                        <p class="zb-brand-name"><?= htmlspecialchars($appName, ENT_QUOTES, 'UTF-8') ?></p>
                        <p class="zb-brand-sub"><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></p>
                    </div>
                </div>

                <button class="btn btn-outline-secondary btn-sm" type="button" id="zbThemeToggle" title="Theme">
                    <i class="bi bi-moon"></i>
                </button>
            </div>
        </div>

        <div class="zb-auth-body">
            <!-- Page content (forms) -->
            <?= $content ?>
        </div>

        <div class="zb-auth-foot">
            <span>&copy; <?= date('Y') ?> <?= htmlspecialchars($appName, ENT_QUOTES, 'UTF-8') ?></span>

            <?php if (!empty($footerLinks) && is_array($footerLinks)): ?>
                <div class="d-flex gap-3">
                    <?php foreach ($footerLinks as $l): ?>
                        <?php
                        $label = $l['label'] ?? '';
                        $url   = $l['url'] ?? '';
                        if ($label === '' || $url === '') continue;
                        ?>
                        <a class="text-decoration-none small" href="<?= htmlspecialchars($url, ENT_QUOTES, 'UTF-8') ?>">
                            <?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Bootstrap JS (CDN) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Theme
        const saved = localStorage.getItem('zb-theme');
        if (saved) document.body.setAttribute('data-theme', saved);

        const btn = document.getElementById('zbThemeToggle');
        if (!btn) return;

        const icon = btn.querySelector('i');
        const sync = () => {
            const t = document.body.getAttribute('data-theme') || 'dark';
            icon.className = (t === 'dark') ? 'bi bi-moon' : 'bi bi-sun';
        };
        sync();

        btn.addEventListener('click', function () {
            const t = document.body.getAttribute('data-theme') || 'dark';
            const next = (t === 'dark') ? 'light' : 'dark';
            document.body.setAttribute('data-theme', next);
            localStorage.setItem('zb-theme', next);
            sync();
        });
    });
</script>
</body>
</html>
