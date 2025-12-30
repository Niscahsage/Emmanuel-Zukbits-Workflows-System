<?php
// app/views/partials/navbar.php
// Renders the top navigation bar for logged-in users.

use App\core\Auth;

$app  = app_config();
$user = Auth::user();

$appName = $app['name'] ?? 'App';

/** @var ?string $logoText */ // optional (from db/config)
$logoText = $logoText ?? 'ZB';
?>
<nav class="zb-topbar navbar navbar-expand-lg border-bottom w-100">
    <div class="container-fluid px-3 px-md-4">
        <button class="btn btn-outline-secondary btn-sm me-2 d-lg-none" type="button" id="zbSidebarToggle">
            <i class="bi bi-list"></i>
        </button>

        <a class="navbar-brand d-flex align-items-center gap-2 mb-0" href="/">
            <span class="zb-logo"><?= htmlspecialchars($logoText, ENT_QUOTES, 'UTF-8') ?></span>
            <span class="fw-semibold"><?= htmlspecialchars($appName, ENT_QUOTES, 'UTF-8') ?></span>
        </a>

        <div class="ms-auto d-flex align-items-center gap-2">
            <button class="btn btn-outline-secondary btn-sm" type="button" id="zbThemeToggle" title="Theme">
                <i class="bi bi-moon"></i>
            </button>

            <div class="dropdown">
                <button class="btn btn-outline-secondary btn-sm dropdown-toggle d-flex align-items-center gap-2"
                        type="button" data-zb-dropdown="user">
                    <span class="zb-avatar">
                        <?= strtoupper(substr($user['name'] ?? 'U', 0, 1)) ?>
                    </span>
                    <span class="d-none d-md-inline text-truncate" style="max-width: 160px;">
                        <?= htmlspecialchars($user['name'] ?? 'User', ENT_QUOTES, 'UTF-8') ?>
                    </span>
                </button>

                <div class="zb-dropdown-menu shadow" data-zb-menu="user">
                    <div class="px-3 py-2 border-bottom">
                        <div class="fw-semibold"><?= htmlspecialchars($user['name'] ?? 'User', ENT_QUOTES, 'UTF-8') ?></div>
                        <?php if (!empty($user['email'])): ?>
                            <div class="small text-muted"><?= htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8') ?></div>
                        <?php endif; ?>
                        <?php if (!empty($user['role_name'])): ?>
                            <div class="small text-muted"><?= htmlspecialchars($user['role_name'], ENT_QUOTES, 'UTF-8') ?></div>
                        <?php endif; ?>
                    </div>
                    <a class="dropdown-item px-3 py-2 d-block text-decoration-none" href="/profile">
                        <i class="bi bi-person me-2"></i>Profile
                    </a>
                    <a class="dropdown-item px-3 py-2 d-block text-decoration-none" href="/settings">
                        <i class="bi bi-gear me-2"></i>Settings
                    </a>
                    <div class="border-top"></div>
                    <a class="dropdown-item px-3 py-2 d-block text-decoration-none text-danger" href="/logout">
                        <i class="bi bi-box-arrow-right me-2"></i>Logout
                    </a>
                </div>
            </div>
        </div>
    </div>
</nav>

<style>
    .zb-topbar { background: var(--zb-surface, #0b1020); }
    body[data-theme="light"] .zb-topbar { background: #ffffff; }

    .zb-logo {
        width: 34px;
        height: 34px;
        border-radius: .6rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        color: #fff;
        background: linear-gradient(135deg, #38bdf8, #a855f7);
    }

    .zb-avatar {
        width: 26px;
        height: 26px;
        border-radius: .55rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        color: #fff;
        background: linear-gradient(135deg, #38bdf8, #a855f7);
    }

    /* Simple dropdown (vanilla) */
    .zb-dropdown-menu {
        position: absolute;
        right: 0;
        top: calc(100% + .5rem);
        min-width: 240px;
        border-radius: .75rem;
        border: 1px solid rgba(255,255,255,.10);
        background: var(--zb-surface, #0b1020);
        display: none;
        z-index: 1050;
        overflow: hidden;
    }
    body[data-theme="light"] .zb-dropdown-menu {
        border-color: rgba(0,0,0,.10);
        background: #ffffff;
    }
    .zb-dropdown-menu .dropdown-item:hover { background: rgba(56,189,248,.10); }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Theme
        const saved = localStorage.getItem('zb-theme');
        if (saved) document.body.setAttribute('data-theme', saved);

        const themeBtn = document.getElementById('zbThemeToggle');
        if (themeBtn) {
            const icon = themeBtn.querySelector('i');
            const syncIcon = () => {
                const t = document.body.getAttribute('data-theme') || 'dark';
                icon.className = (t === 'dark') ? 'bi bi-moon' : 'bi bi-sun';
            };
            syncIcon();

            themeBtn.addEventListener('click', function () {
                const t = document.body.getAttribute('data-theme') || 'dark';
                const next = (t === 'dark') ? 'light' : 'dark';
                document.body.setAttribute('data-theme', next);
                localStorage.setItem('zb-theme', next);
                syncIcon();
            });
        }

        // Sidebar toggle event (sidebar listens too)
        const sb = document.getElementById('zbSidebarToggle');
        if (sb) sb.addEventListener('click', function () {
            document.body.classList.toggle('zb-sidebar-open');
        });

        // Simple dropdown
        const userBtn = document.querySelector('[data-zb-dropdown="user"]');
        const userMenu = document.querySelector('[data-zb-menu="user"]');

        function closeMenu() {
            if (userMenu) userMenu.style.display = 'none';
        }

        if (userBtn && userMenu) {
            userBtn.addEventListener('click', function (e) {
                e.stopPropagation();
                userMenu.style.display = (userMenu.style.display === 'block') ? 'none' : 'block';
            });

            document.addEventListener('click', function () { closeMenu(); });
            window.addEventListener('resize', function () { closeMenu(); });
        }
    });
</script>
