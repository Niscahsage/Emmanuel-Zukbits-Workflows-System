<?php
// app/views/layouts/main.php
// Primary layout for authenticated pages (dashboard, modules, etc.)

$app     = app_config();
$appName = $app['name'] ?? 'App';

/** @var ?string $pageTitle */
$pageTitle = $pageTitle ?? $appName;

// optional: RBAC-filtered nav sections passed from controller/service
/** @var array $navSections */
$navSections = $navSections ?? [];

// optional: footer links from db
/** @var array $footerLinks */
$footerLinks = $footerLinks ?? [];

// optional: logo text from db/config
$logoText = $logoText ?? ($app['logo_text'] ?? null);
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
        }
        body[data-theme="light"]{
            --zb-bg:#f5f7fb;
            --zb-surface:#ffffff;
            --zb-text:#0b1020;
            --zb-muted:#667085;
            --zb-border:rgba(0,0,0,.10);
        }

        body{
            background: var(--zb-bg);
            color: var(--zb-text);
            min-height: 100vh;
        }

        .zb-shell{
            min-height: 100vh;
            display:flex;
        }

        .zb-main{
            flex:1;
            min-width: 0;
        }

        /* Desktop spacing because sidebar is fixed */
        @media (min-width: 992px){
            .zb-main{ margin-left: 280px; }
            body.zb-sidebar-collapsed .zb-main{ margin-left: 84px; }
        }

        .zb-content{
            padding: 1rem;
        }

        @media (min-width: 768px){
            .zb-content{ padding: 1.25rem; }
        }

        .zb-surface{
            background: var(--zb-surface);
            border: 1px solid var(--zb-border);
            border-radius: .75rem;
        }
    </style>
</head>
<body>
<div class="zb-shell">
    <?php
    // navbar + sidebar (partials)
    // navbar.php and sidebar.php contain their own CSS/JS as you requested
    require __DIR__ . '/../partials/navbar.php';
    require __DIR__ . '/../partials/sidebar.php';
    ?>

    <main class="zb-main">
        <div class="zb-content">
            <?php require __DIR__ . '/../partials/flash.php'; ?>

            <!-- Page content -->
            <?= $content ?>
        </div>

        <?php require __DIR__ . '/../partials/footer.php'; ?>
    </main>
</div>

<!-- Bootstrap JS (CDN) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Ensure saved theme applies even if navbar script loads later
        const saved = localStorage.getItem('zb-theme');
        if (saved) document.body.setAttribute('data-theme', saved);

        // Close sidebar on route change clicks (mobile)
        document.querySelectorAll('#zbSidebar a').forEach(a => {
            a.addEventListener('click', function () {
                if (window.innerWidth < 992) {
                    document.body.classList.remove('zb-sidebar-open');
                }
            });
        });
    });
</script>
</body>
</html>
