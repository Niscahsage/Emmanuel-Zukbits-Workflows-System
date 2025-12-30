<?php
// app/views/partials/sidebar.php
// Left sidebar navigation with links based on the user's role.

use App\core\Auth;

$user = Auth::user();
$currentPath = $_SERVER['REQUEST_URI'] ?? '/';

/** @var array $navSections */ 
// expected structure (already RBAC-filtered by controller/service):
// [
//   ['title' => 'Main', 'items' => [
//       ['label'=>'Dashboard','url'=>'/','icon'=>'bi-speedometer2','active_on'=>['/','/dashboard']],
//       ['label'=>'Projects','url'=>'/projects','icon'=>'bi-kanban','badge'=>$counts['projects'] ?? null],
//   ]],
// ]
$navSections = $navSections ?? [];
?>
<aside class="zb-sidebar" id="zbSidebar">
    <div class="zb-sidebar-inner">
        <div class="zb-sidebar-head d-flex align-items-center justify-content-between px-3 py-3 border-bottom">
            <div class="d-flex align-items-center gap-2">
                <span class="zb-sb-logo">ZB</span>
                <div class="lh-sm">
                    <div class="fw-semibold text-truncate" style="max-width: 170px;">
                        <?= htmlspecialchars(app_config()['name'] ?? 'App', ENT_QUOTES, 'UTF-8') ?>
                    </div>
                    <div class="small text-muted text-truncate" style="max-width: 170px;">
                        <?= htmlspecialchars($user['role_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                    </div>
                </div>
            </div>
            <button class="btn btn-outline-secondary btn-sm d-lg-none" type="button" id="zbSidebarClose">
                <i class="bi bi-x"></i>
            </button>
        </div>

        <div class="px-3 py-3 border-bottom">
            <div class="d-flex align-items-center gap-2">
                <span class="zb-sb-avatar">
                    <?= strtoupper(substr($user['name'] ?? 'U', 0, 1)) ?>
                </span>
                <div class="min-w-0">
                    <div class="fw-semibold text-truncate"><?= htmlspecialchars($user['name'] ?? 'User', ENT_QUOTES, 'UTF-8') ?></div>
                    <?php if (!empty($user['email'])): ?>
                        <div class="small text-muted text-truncate"><?= htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8') ?></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="zb-sidebar-body">
            <?php foreach ($navSections as $section): ?>
                <?php
                $title = $section['title'] ?? '';
                $items = $section['items'] ?? [];
                if (!is_array($items) || empty($items)) continue;
                ?>
                <?php if ($title !== ''): ?>
                    <div class="zb-sb-section-title px-3 pt-3 pb-2 small text-uppercase text-muted">
                        <?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?>
                    </div>
                <?php endif; ?>

                <ul class="list-unstyled mb-2">
                    <?php foreach ($items as $it): ?>
                        <?php
                        $label = $it['label'] ?? '';
                        $url   = $it['url'] ?? '#';
                        $icon  = $it['icon'] ?? 'bi-circle';
                        $badge = $it['badge'] ?? null;
                        $activeOn = $it['active_on'] ?? null;

                        $isActive = false;
                        if (is_array($activeOn)) {
                            foreach ($activeOn as $p) {
                                if ($p === '/' && $currentPath === '/') $isActive = true;
                                if ($p !== '/' && strpos($currentPath, $p) === 0) $isActive = true;
                            }
                        } else {
                            if ($url === '/' && $currentPath === '/') $isActive = true;
                            if ($url !== '/' && strpos($currentPath, $url) === 0) $isActive = true;
                        }
                        ?>
                        <li class="mb-1">
                            <a class="zb-sb-link d-flex align-items-center gap-2 px-3 py-2 rounded-3 text-decoration-none <?= $isActive ? 'active' : '' ?>"
                               href="<?= htmlspecialchars($url, ENT_QUOTES, 'UTF-8') ?>">
                                <i class="bi <?= htmlspecialchars($icon, ENT_QUOTES, 'UTF-8') ?>"></i>
                                <span class="flex-grow-1 text-truncate"><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></span>
                                <?php if ($badge !== null && $badge !== ''): ?>
                                    <span class="badge rounded-pill bg-secondary"><?= htmlspecialchars((string)$badge, ENT_QUOTES, 'UTF-8') ?></span>
                                <?php endif; ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endforeach; ?>
        </div>

        <div class="zb-sidebar-foot border-top p-3">
            <div class="d-flex gap-2">
                <a class="btn btn-outline-secondary btn-sm w-100" href="/profile"><i class="bi bi-person me-1"></i>Profile</a>
                <a class="btn btn-outline-danger btn-sm w-100" href="/logout"><i class="bi bi-box-arrow-right me-1"></i>Logout</a>
            </div>
        </div>
    </div>
</aside>

<style>
    :root{
        --zb-surface:#0b1020;
        --zb-border:rgba(255,255,255,.08);
        --zb-text:#f7f7ff;
        --zb-muted:#c3c5d4;
        --zb-accent:#38bdf8;
    }
    body[data-theme="light"]{
        --zb-surface:#ffffff;
        --zb-border:rgba(0,0,0,.08);
        --zb-text:#0b1020;
        --zb-muted:#667085;
        --zb-accent:#0ea5e9;
    }

    .zb-sidebar{
        position: fixed;
        top: 0;
        left: 0;
        height: 100dvh;
        width: 280px;
        background: var(--zb-surface);
        border-right: 1px solid var(--zb-border);
        z-index: 1040;
        transform: translateX(-100%);
        transition: transform .2s ease;
    }

    /* Desktop default visible */
    @media (min-width: 992px){
        .zb-sidebar{ transform: translateX(0); }
        body.zb-sidebar-collapsed .zb-sidebar{ width: 84px; }
        body.zb-sidebar-collapsed .zb-sidebar .zb-sb-section-title,
        body.zb-sidebar-collapsed .zb-sidebar .zb-sb-link span,
        body.zb-sidebar-collapsed .zb-sidebar .zb-sidebar-foot{ display:none; }
    }

    /* Mobile open */
    body.zb-sidebar-open .zb-sidebar{ transform: translateX(0); }

    .zb-sidebar-inner{ height: 100%; display:flex; flex-direction:column; }
    .zb-sidebar-body{ overflow:auto; flex:1; padding-bottom: .5rem; }

    .zb-sb-logo{
        width: 34px; height: 34px; border-radius: .6rem;
        display:inline-flex; align-items:center; justify-content:center;
        font-weight: 800; color:#fff;
        background: linear-gradient(135deg,#38bdf8,#a855f7);
        flex: 0 0 auto;
    }

    .zb-sb-avatar{
        width: 38px; height: 38px; border-radius: .75rem;
        display:inline-flex; align-items:center; justify-content:center;
        font-weight: 800; color:#fff;
        background: linear-gradient(135deg,#38bdf8,#a855f7);
        flex: 0 0 auto;
    }

    .zb-sb-link{ color: var(--zb-text); }
    .zb-sb-link:hover{ background: rgba(56,189,248,.10); }
    .zb-sb-link.active{
        background: rgba(56,189,248,.16);
        outline: 1px solid rgba(56,189,248,.25);
    }

    /* Mobile backdrop */
    .zb-sb-backdrop{
        position: fixed; inset: 0;
        background: rgba(0,0,0,.45);
        z-index: 1035;
        display: none;
    }
    body.zb-sidebar-open .zb-sb-backdrop{ display:block; }
</style>

<div class="zb-sb-backdrop" id="zbSidebarBackdrop"></div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const closeBtn = document.getElementById('zbSidebarClose');
        const backdrop = document.getElementById('zbSidebarBackdrop');

        function closeSidebar(){
            document.body.classList.remove('zb-sidebar-open');
        }

        if (closeBtn) closeBtn.addEventListener('click', closeSidebar);
        if (backdrop) backdrop.addEventListener('click', closeSidebar);

        // Desktop collapse toggle (optional button injection)
        if (window.innerWidth >= 992) {
            const topbar = document.querySelector('.zb-topbar .container-fluid');
            if (topbar && !document.getElementById('zbCollapseSidebar')) {
                const btn = document.createElement('button');
                btn.id = 'zbCollapseSidebar';
                btn.type = 'button';
                btn.className = 'btn btn-outline-secondary btn-sm me-2 d-none d-lg-inline-flex';
                btn.innerHTML = '<i class="bi bi-layout-sidebar-inset"></i>';
                btn.title = 'Toggle sidebar';
                topbar.insertBefore(btn, topbar.firstChild);

                const saved = localStorage.getItem('zb-sidebar-collapsed');
                if (saved === 'true') document.body.classList.add('zb-sidebar-collapsed');

                btn.addEventListener('click', function () {
                    document.body.classList.toggle('zb-sidebar-collapsed');
                    localStorage.setItem('zb-sidebar-collapsed', document.body.classList.contains('zb-sidebar-collapsed') ? 'true' : 'false');
                });
            }
        }
    });
</script>
