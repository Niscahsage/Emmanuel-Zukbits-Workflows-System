<?php
// app/views/notifications/index.php
// index.php lists notifications for the currently logged-in user.

/** @var array $notifications */

use App\core\View;
use App\core\Auth;

$user = Auth::user();

// NOTE: This view is wired to the main app layout (no <html>, <head>, CDNs, or body tags).
// Keep the file-level comment above.
?>

<style>
    /* Keep page-scoped styles only (avoid resetting :root if your layout already defines it) */
    .notifications-container{max-width:1200px;margin:0 auto;}
    .page-header{margin-bottom:2rem;}
    .page-title{font-size:1.75rem;font-weight:700;margin:0 0 .5rem;color:var(--color-text);display:flex;align-items:center;gap:.75rem;}
    .page-subtitle{color:var(--color-text-muted);font-size:.95rem;margin:0;}
    .header-actions{display:flex;gap:.75rem;align-items:center;flex-wrap:wrap;}

    .btn-mark-all{background:var(--gradient-primary);border:none;color:#fff;font-weight:600;padding:.5rem 1.25rem;border-radius:8px;transition:.2s;display:flex;align-items:center;gap:.5rem;}
    .btn-mark-all:hover{transform:translateY(-2px);box-shadow:0 8px 25px rgba(56,189,248,.35);}

    .notifications-empty-state{background:var(--gradient-bg-card);border:1px solid var(--color-border-light);border-radius:16px;padding:3rem 2rem;text-align:center;margin:2rem 0;}
    .empty-icon{width:80px;height:80px;background:rgba(56,189,248,.1);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 1.5rem;color:var(--color-accent-blue);font-size:2rem;}
    .empty-title{font-size:1.25rem;font-weight:600;color:var(--color-text);margin-bottom:.5rem;}
    .empty-description{color:var(--color-text-muted);margin-bottom:1.5rem;}

    .filter-tabs{display:flex;gap:.5rem;margin-bottom:1.5rem;overflow-x:auto;padding-bottom:.5rem;}
    .filter-tab{background:rgba(11,16,32,.6);border:1px solid var(--color-border);color:var(--color-text-muted);padding:.5rem 1.25rem;border-radius:8px;font-weight:500;transition:.2s;white-space:nowrap;}
    .filter-tab:hover,.filter-tab.active{background:var(--gradient-primary);color:#fff;border-color:transparent;}
    .filter-tab-badge{background:rgba(255,255,255,.15);color:#fff;font-size:.75rem;padding:.15rem .5rem;border-radius:10px;margin-left:.5rem;}

    .notification-list{display:flex;flex-direction:column;gap:.75rem;}
    .notification-card{background:var(--gradient-bg-card);border:1px solid var(--color-border-light);border-radius:12px;padding:1.25rem;transition:.2s;position:relative;overflow:hidden;}
    .notification-card.unread{border-left:4px solid var(--color-accent-blue);background:radial-gradient(circle at top left, rgba(56,189,248,.15), rgba(15,23,42,.96));}
    .notification-card:hover{transform:translateY(-2px);box-shadow:0 8px 30px rgba(0,0,0,.3);border-color:rgba(148,163,253,.5);}

    .notification-header{display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:.75rem;}
    .notification-icon{width:40px;height:40px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:1.25rem;margin-right:.75rem;flex-shrink:0;}
    .icon-info{background:rgba(56,189,248,.15);color:var(--color-accent-blue);}
    .icon-success{background:rgba(52,199,89,.15);color:var(--color-accent-green);}
    .icon-warning{background:rgba(251,191,36,.15);color:var(--color-accent);}
    .icon-error{background:rgba(239,68,68,.15);color:var(--color-accent-red);}
    .icon-system{background:rgba(168,85,247,.15);color:var(--color-accent-purple);}

    .notification-content{flex:1;}
    .notification-title{font-size:1rem;font-weight:600;color:var(--color-text);margin:0 0 .25rem;display:flex;align-items:center;gap:.5rem;}
    .notification-title .badge{font-size:.7rem;padding:.15rem .5rem;border-radius:6px;font-weight:600;}
    .badge-project{background:rgba(56,189,248,.15);color:var(--color-accent-blue);border:1px solid rgba(56,189,248,.3);}
    .badge-system{background:rgba(168,85,247,.15);color:var(--color-accent-purple);border:1px solid rgba(168,85,247,.3);}
    .badge-alert{background:rgba(239,68,68,.15);color:var(--color-accent-red);border:1px solid rgba(239,68,68,.3);}

    .notification-body{color:var(--color-text-muted);font-size:.9rem;line-height:1.5;margin-bottom:.75rem;}
    .notification-meta{display:flex;align-items:center;justify-content:space-between;margin-top:.75rem;padding-top:.75rem;border-top:1px solid rgba(148,163,253,.1);gap:.75rem;flex-wrap:wrap;}
    .notification-time{font-size:.8rem;color:var(--color-text-muted);display:flex;align-items:center;gap:.5rem;flex-wrap:wrap;}
    .notification-time i{color:var(--color-accent-blue);}
    .notification-actions{display:flex;gap:.5rem;flex-wrap:wrap;justify-content:flex-end;}

    .btn-notification{padding:.25rem .75rem;border-radius:6px;font-size:.8rem;font-weight:500;transition:.2s;border:none;display:flex;align-items:center;gap:.25rem;}
    .btn-mark-read{background:rgba(56,189,248,.1);color:var(--color-accent-blue);border:1px solid rgba(56,189,248,.3);}
    .btn-mark-read:hover{background:rgba(56,189,248,.2);border-color:var(--color-accent-blue);}
    .btn-view{background:rgba(52,199,89,.1);color:var(--color-accent-green);border:1px solid rgba(52,199,89,.3);text-decoration:none;}
    .btn-view:hover{background:rgba(52,199,89,.2);border-color:var(--color-accent-green);}
    .btn-dismiss{background:rgba(239,68,68,.1);color:var(--color-accent-red);border:1px solid rgba(239,68,68,.3);}
    .btn-dismiss:hover{background:rgba(239,68,68,.2);border-color:var(--color-accent-red);}

    .notification-link{color:var(--color-accent-blue);text-decoration:none;font-size:.85rem;display:inline-flex;align-items:center;gap:.25rem;transition:.2s;}
    .notification-link:hover{color:var(--color-accent);text-decoration:underline;}

    .stats-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:1rem;margin-bottom:2rem;}
    .stat-card{background:var(--gradient-bg-card);border:1px solid var(--color-border-light);border-radius:12px;padding:1.25rem;transition:.2s;}
    .stat-card:hover{transform:translateY(-2px);box-shadow:0 8px 25px rgba(0,0,0,.3);}
    .stat-value{font-size:2rem;font-weight:700;color:var(--color-text);margin-bottom:.25rem;}
    .stat-label{font-size:.85rem;color:var(--color-text-muted);text-transform:uppercase;letter-spacing:.5px;}
    .stat-trend{font-size:.8rem;display:flex;align-items:center;gap:.25rem;margin-top:.5rem;}
    .trend-up{color:var(--color-accent-green);}
    .trend-down{color:var(--color-accent-red);}

    @media (max-width:768px){
        .page-header{flex-direction:column;gap:1rem;align-items:flex-start;}
        .header-actions{width:100%;justify-content:flex-end;}
        .notification-meta{flex-direction:column;align-items:flex-start;}
        .notification-actions{width:100%;justify-content:flex-end;}
        .stats-grid{grid-template-columns:repeat(2,1fr);}
    }
    @media (max-width:576px){
        .stats-grid{grid-template-columns:1fr;}
        .filter-tabs{flex-wrap:wrap;}
        .notification-card{padding:1rem;}
    }
</style>

<div class="notifications-container">
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h1 class="page-title"><i class="bi bi-bell-fill"></i>Notifications</h1>
            <p class="page-subtitle">Stay updated with messages and alerts related to your projects, approvals, and schedules.</p>
        </div>

        <div class="header-actions">
            <?php if (!empty($notifications)): ?>
                <form method="post" action="/notifications/mark-all-read" class="m-0" id="markAllForm">
                    <button type="submit" class="btn-mark-all" id="markAllBtn">
                        <i class="bi bi-check-all"></i>Mark All as Read
                    </button>
                </form>

                <button class="btn btn-outline-secondary" type="button" id="clearAllBtn">
                    <i class="bi bi-trash me-1"></i>Clear All
                </button>
            <?php endif; ?>
        </div>
    </div>

    <?php
    $unreadCount = 0;
    $todayCount = 0;
    $weekCount = 0;
    $totalCount = is_array($notifications) ? count($notifications) : 0;

    if (!empty($notifications)) {
        $oneDayAgo = strtotime('-1 day');
        $oneWeekAgo = strtotime('-7 days');

        foreach ($notifications as $n) {
            $isRead = !empty($n['is_read']);
            $createdAt = strtotime($n['created_at'] ?? '');

            if (!$isRead) $unreadCount++;
            if ($createdAt && $createdAt >= $oneDayAgo) $todayCount++;
            if ($createdAt && $createdAt >= $oneWeekAgo) $weekCount++;
        }
    }
    ?>

    <?php if (!empty($notifications)): ?>
        <div class="stats-grid" id="statsGrid">
            <div class="stat-card">
                <div class="stat-value" id="statTotal"><?= (int)$totalCount ?></div>
                <div class="stat-label">Total Notifications</div>
                <div class="stat-trend"><span class="trend-up">+<?= (int)$weekCount ?> this week</span></div>
            </div>

            <div class="stat-card">
                <div class="stat-value" id="statUnread"><?= (int)$unreadCount ?></div>
                <div class="stat-label">Unread</div>
                <div class="stat-trend">
                    <?php if ($unreadCount > 0): ?>
                        <span class="trend-up">Requires attention</span>
                    <?php else: ?>
                        <span class="trend-down">All caught up!</span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-value" id="statToday"><?= (int)$todayCount ?></div>
                <div class="stat-label">Today</div>
                <div class="stat-trend">
                    <?php if ($todayCount > 0): ?>
                        <span class="trend-up">Active today</span>
                    <?php else: ?>
                        <span class="trend-down">No new today</span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-value"><?= (int)$weekCount ?></div>
                <div class="stat-label">This Week</div>
                <div class="stat-trend"><span class="trend-up">Last 7 days</span></div>
            </div>
        </div>

        <div class="filter-tabs" id="filterTabs">
            <button class="filter-tab active" type="button" data-filter="all">
                All Notifications <span class="filter-tab-badge" id="badgeAll"><?= (int)$totalCount ?></span>
            </button>
            <button class="filter-tab" type="button" data-filter="unread">
                Unread <span class="filter-tab-badge" id="badgeUnread"><?= (int)$unreadCount ?></span>
            </button>
            <button class="filter-tab" type="button" data-filter="today">
                Today <span class="filter-tab-badge" id="badgeToday"><?= (int)$todayCount ?></span>
            </button>
            <button class="filter-tab" type="button" data-filter="system">System</button>
            <button class="filter-tab" type="button" data-filter="alerts">Alerts</button>
        </div>
    <?php endif; ?>

    <?php if (empty($notifications)): ?>
        <div class="notifications-empty-state">
            <div class="empty-icon"><i class="bi bi-bell-slash"></i></div>
            <h3 class="empty-title">No notifications yet</h3>
            <p class="empty-description">You're all caught up! New notifications will appear here when available.</p>
            <a href="/dashboard" class="btn btn-primary"><i class="bi bi-arrow-left me-2"></i>Return to Dashboard</a>
        </div>
    <?php else: ?>
        <div class="notification-list" id="notificationList">
            <?php foreach ($notifications as $n): ?>
                <?php
                $id = (int)($n['id'] ?? 0);
                $title = (string)($n['title'] ?? ($n['subject'] ?? 'Notification'));
                $body = (string)($n['body'] ?? '');
                $linkUrl = (string)($n['link_url'] ?? ($n['url'] ?? ''));
                $createdAt = (string)($n['created_at'] ?? '');
                $isRead = !empty($n['is_read']);
                $type = (string)($n['type'] ?? 'info');

                $iconClass = 'icon-info';
                $icon = 'bi-info-circle';

                switch ($type) {
                    case 'success':
                    case 'completed':
                        $iconClass = 'icon-success';
                        $icon = 'bi-check-circle';
                        break;
                    case 'warning':
                    case 'alert':
                        $iconClass = 'icon-warning';
                        $icon = 'bi-exclamation-triangle';
                        break;
                    case 'error':
                    case 'failed':
                        $iconClass = 'icon-error';
                        $icon = 'bi-x-circle';
                        break;
                    case 'system':
                        $iconClass = 'icon-system';
                        $icon = 'bi-gear';
                        break;
                }

                $timeAgo = '';
                if ($createdAt) {
                    $t = strtotime($createdAt);
                    $diff = $t ? (time() - $t) : 0;

                    if ($diff < 60) $timeAgo = 'Just now';
                    elseif ($diff < 3600) $timeAgo = floor($diff / 60) . ' min ago';
                    elseif ($diff < 86400) {
                        $h = floor($diff / 3600);
                        $timeAgo = $h . ' hour' . ($h != 1 ? 's' : '') . ' ago';
                    } else {
                        $d = floor($diff / 86400);
                        $timeAgo = $d == 1 ? 'Yesterday' : $d . ' days ago';
                    }
                }
                ?>

                <div class="notification-card <?= $isRead ? '' : 'unread' ?>"
                     data-type="<?= View::e($type) ?>"
                     data-read="<?= $isRead ? 'true' : 'false' ?>"
                     data-ts="<?= $createdAt ? (int)strtotime($createdAt) : 0 ?>">

                    <div class="notification-header">
                        <div class="d-flex align-items-start w-100">
                            <div class="notification-icon <?= View::e($iconClass) ?>">
                                <i class="bi <?= View::e($icon) ?>"></i>
                            </div>

                            <div class="notification-content">
                                <h3 class="notification-title">
                                    <?= View::e($title) ?>
                                    <?php if ($type === 'alert' || $type === 'warning' || $type === 'error'): ?>
                                        <span class="badge badge-alert">Alert</span>
                                    <?php elseif ($type === 'system'): ?>
                                        <span class="badge badge-system">System</span>
                                    <?php endif; ?>
                                </h3>

                                <?php if ($body !== ''): ?>
                                    <p class="notification-body"><?= nl2br(View::e($body)) ?></p>
                                <?php endif; ?>

                                <?php if ($linkUrl): ?>
                                    <a href="<?= View::e($linkUrl) ?>" class="notification-link">
                                        <i class="bi bi-arrow-up-right"></i>View related item
                                    </a>
                                <?php endif; ?>

                                <div class="notification-meta">
                                    <div class="notification-time">
                                        <i class="bi bi-clock"></i>
                                        <?php if ($createdAt): ?>
                                            <span><?= View::e($timeAgo) ?></span>
                                            <span class="text-muted ms-2">• <?= View::e(date('M j, Y g:i A', strtotime($createdAt))) ?></span>
                                        <?php else: ?>
                                            <span class="text-muted">–</span>
                                        <?php endif; ?>
                                    </div>

                                    <div class="notification-actions">
                                        <?php if (!$isRead): ?>
                                            <form method="post" action="/notifications/mark-read" class="d-inline markReadForm">
                                                <input type="hidden" name="id" value="<?= View::e((string)$id) ?>">
                                                <button type="submit" class="btn-notification btn-mark-read">
                                                    <i class="bi bi-check"></i>Mark Read
                                                </button>
                                            </form>
                                        <?php endif; ?>

                                        <?php if ($linkUrl): ?>
                                            <a href="<?= View::e($linkUrl) ?>" class="btn-notification btn-view">
                                                <i class="bi bi-eye"></i>View
                                            </a>
                                        <?php endif; ?>

                                        <button class="btn-notification btn-dismiss" type="button" data-dismiss-id="<?= (int)$id ?>">
                                            <i class="bi bi-x"></i>Dismiss
                                        </button>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                </div>
            <?php endforeach; ?>
        </div>

        <div class="d-flex justify-content-center mt-4">
            <button class="btn btn-outline-secondary" type="button" id="loadMoreBtn">
                <i class="bi bi-arrow-clockwise me-2"></i>Load Older Notifications
            </button>
        </div>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const list = document.getElementById('notificationList');
    const tabsWrap = document.getElementById('filterTabs');
    const statsGrid = document.getElementById('statsGrid');

    const badgeUnread = document.getElementById('badgeUnread');
    const badgeToday = document.getElementById('badgeToday');
    const badgeAll = document.getElementById('badgeAll');

    const statTotal = document.getElementById('statTotal');
    const statUnread = document.getElementById('statUnread');
    const statToday = document.getElementById('statToday');

    function oneDayAgoTs() {
        return Date.now() - (24 * 60 * 60 * 1000);
    }

    function updateCounts() {
        const cards = Array.from(document.querySelectorAll('.notification-card'));
        const unread = cards.filter(c => c.getAttribute('data-read') === 'false');
        const today = cards.filter(c => {
            const ts = Number(c.getAttribute('data-ts') || 0) * 1000;
            return ts >= oneDayAgoTs();
        });

        if (badgeAll) badgeAll.textContent = cards.length;
        if (badgeUnread) badgeUnread.textContent = unread.length;
        if (badgeToday) badgeToday.textContent = today.length;

        if (statTotal) statTotal.textContent = cards.length;
        if (statUnread) statUnread.textContent = unread.length;
        if (statToday) statToday.textContent = today.length;

        if (cards.length === 0) showEmptyState();
    }

    function showEmptyState() {
        if (!list) return;
        list.innerHTML = `
            <div class="notifications-empty-state">
                <div class="empty-icon"><i class="bi bi-bell-slash"></i></div>
                <h3 class="empty-title">No notifications</h3>
                <p class="empty-description">All notifications have been cleared. New notifications will appear here when available.</p>
            </div>
        `;
        if (tabsWrap) tabsWrap.style.display = 'none';
        if (statsGrid) statsGrid.style.display = 'none';
    }

    function setActiveTab(btn) {
        document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
        btn.classList.add('active');
    }

    function filterCards(filter) {
        const cards = document.querySelectorAll('.notification-card');
        const now = Date.now();
        const cutoff = now - (24 * 60 * 60 * 1000);

        cards.forEach(card => {
            const type = card.getAttribute('data-type') || '';
            const isRead = card.getAttribute('data-read') === 'true';
            const ts = Number(card.getAttribute('data-ts') || 0) * 1000;

            let show = false;
            if (filter === 'all') show = true;
            if (filter === 'unread') show = !isRead;
            if (filter === 'today') show = ts >= cutoff;
            if (filter === 'system') show = type === 'system';
            if (filter === 'alerts') show = (type === 'alert' || type === 'warning' || type === 'error');

            card.style.display = show ? 'block' : 'none';
        });
    }

    // Tabs: no implicit global `event` usage
    document.querySelectorAll('.filter-tab').forEach(btn => {
        btn.addEventListener('click', (e) => {
            const filter = btn.getAttribute('data-filter') || 'all';
            setActiveTab(btn);
            filterCards(filter);
        });
    });

    // Dismiss: UI-only (wire to backend when you add route)
    document.querySelectorAll('[data-dismiss-id]').forEach(btn => {
        btn.addEventListener('click', () => {
            const id = btn.getAttribute('data-dismiss-id');
            if (!confirm('Are you sure you want to dismiss this notification?')) return;
            const card = btn.closest('.notification-card');
            if (!card) return;
            card.style.opacity = '0';
            card.style.transform = 'translateX(60px)';
            setTimeout(() => {
                card.remove();
                updateCounts();
            }, 220);
        });
    });

    // Clear all: UI-only (wire to backend when you add route)
    const clearAllBtn = document.getElementById('clearAllBtn');
    if (clearAllBtn) {
        clearAllBtn.addEventListener('click', () => {
            if (!confirm('Are you sure you want to clear all notifications? This cannot be undone.')) return;
            const cards = Array.from(document.querySelectorAll('.notification-card'));
            cards.forEach((c, i) => {
                setTimeout(() => {
                    c.style.opacity = '0';
                    c.style.transform = 'translateX(60px)';
                    setTimeout(() => {
                        c.remove();
                        if (i === cards.length - 1) updateCounts();
                    }, 220);
                }, i * 60);
            });
        });
    }

    // Mark all read: prevent fake submit, animate UI, then submit if you want server-side
    const markAllForm = document.getElementById('markAllForm');
    const markAllBtn = document.getElementById('markAllBtn');
    if (markAllForm && markAllBtn) {
        markAllForm.addEventListener('submit', (e) => {
            e.preventDefault();

            const original = markAllBtn.innerHTML;
            markAllBtn.disabled = true;
            markAllBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Marking...';

            setTimeout(() => {
                document.querySelectorAll('.notification-card.unread').forEach((card) => {
                    card.classList.remove('unread');
                    card.setAttribute('data-read', 'true');
                    const form = card.querySelector('.markReadForm');
                    if (form) form.remove();
                });

                updateCounts();
                markAllBtn.disabled = false;
                markAllBtn.innerHTML = original;

                // If you want REAL backend mark-all, uncomment next line:
                // markAllForm.submit();
            }, 450);
        });
    }

    // Load more: placeholder
    const loadMoreBtn = document.getElementById('loadMoreBtn');
    if (loadMoreBtn) {
        loadMoreBtn.addEventListener('click', () => {
            loadMoreBtn.disabled = true;
            loadMoreBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Loading...';
            setTimeout(() => {
                loadMoreBtn.innerHTML = '<i class="bi bi-arrow-clockwise me-2"></i>No more notifications';
            }, 900);
        });
    }

    updateCounts();
});
</script>
