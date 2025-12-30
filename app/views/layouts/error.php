<?php
// app/views/layouts/error.php
// Layout for system and HTTP error pages.

$app     = app_config();
$appName = $app['name'] ?? 'App';

$statusCode = $statusCode ?? 500;
$errorTitle = $errorTitle ?? 'Error';
$errorSub   = $errorSub ?? 'Something went wrong.';

$pageTitle  = $pageTitle ?? ($statusCode . ' – ' . $errorTitle);

$isDebug    = (bool) ($_ENV['APP_DEBUG'] ?? false);
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
            --zb-danger:#ef4444;
        }
        body[data-theme="light"]{
            --zb-bg:#f5f7fb;
            --zb-surface:#ffffff;
            --zb-text:#0b1020;
            --zb-muted:#667085;
            --zb-border:rgba(0,0,0,.10);
            --zb-danger:#dc2626;
        }
        body{
            background: var(--zb-bg);
            color: var(--zb-text);
            min-height: 100vh;
        }
        .zb-err-wrap{
            min-height: 100vh;
            display:flex;
            align-items:center;
            justify-content:center;
            padding: 1.25rem;
        }
        .zb-err-card{
            width: 100%;
            max-width: 860px;
            background: var(--zb-surface);
            border: 1px solid var(--zb-border);
            border-radius: 1rem;
            box-shadow: 0 18px 50px rgba(0,0,0,.35);
            overflow:hidden;
        }
        .zb-err-head{
            padding: 1.25rem;
            border-bottom: 1px solid var(--zb-border);
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap: 1rem;
            flex-wrap: wrap;
        }
        .zb-code{
            font-weight: 900;
            font-size: 2.2rem;
            color: var(--zb-danger);
            line-height: 1;
        }
        .zb-err-body{
            padding: 1.25rem;
        }
        .zb-debug{
            border: 1px solid var(--zb-border);
            border-radius: .75rem;
            background: rgba(0,0,0,.08);
            padding: 1rem;
            overflow:auto;
            max-height: 320px;
        }
        body[data-theme="light"] .zb-debug{
            background: rgba(0,0,0,.04);
        }
    </style>
</head>
<body>
<div class="zb-err-wrap">
    <div class="zb-err-card">
        <div class="zb-err-head">
            <div class="d-flex align-items-center gap-3">
                <div class="zb-code"><?= htmlspecialchars((string)$statusCode, ENT_QUOTES, 'UTF-8') ?></div>
                <div>
                    <div class="h5 mb-1"><?= htmlspecialchars($errorTitle, ENT_QUOTES, 'UTF-8') ?></div>
                    <div class="text-muted"><?= htmlspecialchars($errorSub, ENT_QUOTES, 'UTF-8') ?></div>
                </div>
            </div>

            <div class="d-flex align-items-center gap-2">
                <button class="btn btn-outline-secondary btn-sm" type="button" id="zbThemeToggle" title="Theme">
                    <i class="bi bi-moon"></i>
                </button>
                <a class="btn btn-outline-secondary btn-sm" href="/" title="Home">
                    <i class="bi bi-house-door me-1"></i>Home
                </a>
                <button class="btn btn-danger btn-sm" type="button" onclick="history.back()">
                    <i class="bi bi-arrow-left me-1"></i>Back
                </button>
            </div>
        </div>

        <div class="zb-err-body">
            <?= $content ?? '' ?>

            <?php if ($isDebug && !empty($debugInfo)): ?>
                <div class="mt-3">
                    <button class="btn btn-outline-secondary btn-sm" type="button" id="zbDebugToggle">
                        <i class="bi bi-bug me-1"></i>Debug details
                    </button>

                    <div class="zb-debug mt-2 d-none" id="zbDebugBox">
                        <pre class="mb-0"><?= htmlspecialchars((string)$debugInfo, ENT_QUOTES, 'UTF-8') ?></pre>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div class="border-top p-3 small text-muted d-flex justify-content-between flex-wrap gap-2">
            <span>&copy; <?= date('Y') ?> <?= htmlspecialchars($appName, ENT_QUOTES, 'UTF-8') ?></span>
            <span><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></span>
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
        if (btn) {
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
        }

        // Debug
        const dbgBtn = document.getElementById('zbDebugToggle');
        const dbgBox = document.getElementById('zbDebugBox');
        if (dbgBtn && dbgBox) {
            dbgBtn.addEventListener('click', function () {
                dbgBox.classList.toggle('d-none');
            });
        }
    });
</script>
</body>
</html>
