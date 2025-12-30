<?php
// Super Admin dashboard: system-wide overview.

use App\core\View;

/** @var array $user */
/** @var array $stats */
/** @var int   $unread_count */

$name          = $user['name'] ?? 'Super Admin';
$totalProjects = (int) ($stats['projects_total'] ?? 0);
$ongoing       = (int) ($stats['projects_ongoing'] ?? 0);
$completed     = (int) ($stats['projects_completed'] ?? max(0, $totalProjects - $ongoing));
?>

<div class="mb-4">
    <?php
    // Page header partial
    $pageTitle = 'Super Admin Dashboard';
    $subtitle  = 'System-wide overview and administrative controls';
    require __DIR__ . '/../partials/header.php';
    ?>
</div>

<!-- Metrics -->
<div class="row g-3 mb-4">
    <div class="col-12 col-md-4">
        <div class="zb-surface p-3 h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div class="text-muted small text-uppercase">Projects</div>
                <i class="bi bi-folder fs-4 text-muted"></i>
            </div>
            <div class="fs-2 fw-bold mt-2"><?= View::e((string)$totalProjects) ?></div>
            <a href="/projects" class="small text-decoration-none">View all projects</a>
        </div>
    </div>

    <div class="col-12 col-md-4">
        <div class="zb-surface p-3 h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div class="text-muted small text-uppercase">Ongoing</div>
                <i class="bi bi-clock fs-4 text-muted"></i>
            </div>
            <div class="fs-2 fw-bold mt-2"><?= View::e((string)$ongoing) ?></div>
            <a href="/projects" class="small text-decoration-none">Check progress</a>
        </div>
    </div>

    <div class="col-12 col-md-4">
        <div class="zb-surface p-3 h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div class="text-muted small text-uppercase">Completed</div>
                <i class="bi bi-check-circle fs-4 text-muted"></i>
            </div>
            <div class="fs-2 fw-bold mt-2"><?= View::e((string)$completed) ?></div>
            <a href="/projects" class="small text-decoration-none">Review completed</a>
        </div>
    </div>
</div>

<!-- Admin Controls -->
<div class="row g-3">
    <div class="col-12 col-lg-6">
        <div class="zb-surface p-4 h-100">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <h5 class="mb-0">User Management</h5>
                <span class="badge bg-danger-subtle text-danger">
                    Super Admin
                </span>
            </div>

            <p class="text-muted small">
                Full control over system users and access permissions.
            </p>

            <ul class="small text-muted ps-3">
                <li>Create and deactivate user accounts</li>
                <li>Assign and revoke roles</li>
                <li>Reset passwords and enforce access rules</li>
            </ul>

            <a href="/users" class="btn btn-sm btn-primary mt-2">
                <i class="bi bi-people me-1"></i> Manage Users
            </a>
        </div>
    </div>

    <div class="col-12 col-lg-6">
        <div class="zb-surface p-4 h-100">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <h5 class="mb-0">System Oversight</h5>
                <?php if ($unread_count > 0): ?>
                    <span class="badge bg-warning-subtle text-warning">
                        <?= View::e((string)$unread_count) ?> alerts
                    </span>
                <?php endif; ?>
            </div>

            <p class="text-muted small">
                High-level monitoring of approvals, reports, and operational health.
            </p>

            <ul class="small text-muted ps-3">
                <li>Review pending approvals</li>
                <li>Inspect weekly reports</li>
                <li>Monitor system activity</li>
            </ul>

            <div class="d-flex gap-2 mt-2">
                <a href="/approvals" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-check-circle me-1"></i> Approvals
                </a>
                <a href="/reports" class="btn btn-sm btn-primary">
                    <i class="bi bi-file-text me-1"></i> Reports
                </a>
            </div>
        </div>
    </div>
</div>

<style>
    /* Page-specific only */
    .zb-surface {
        background: var(--zb-surface);
        border: 1px solid var(--zb-border);
        border-radius: .75rem;
    }

    body[data-theme="light"] .zb-surface {
        background: #ffffff;
    }
</style>
