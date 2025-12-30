<?php
// app/views/approvals/index.php
// Lists all approval requests visible to the current user.

/** @var array $approvals */

use App\core\View;
use App\core\Auth;

$user = Auth::user();

$pageTitle = 'Approval Requests';
$subtitle  = 'Track and manage approvals across the system';

$statuses = ['pending' => 0, 'approved' => 0, 'rejected' => 0];

if (!empty($approvals) && is_array($approvals)) {
    foreach ($approvals as $a) {
        $s = $a['status'] ?? 'pending';
        if (!isset($statuses[$s])) {
            $statuses[$s] = 0;
        }
        $statuses[$s]++;
    }
}

$pendingCount = (int) ($statuses['pending'] ?? 0);

$actions = [
    ['label' => 'Pending Inbox', 'url' => '/approvals/inbox', 'type' => 'primary', 'icon' => 'bi-inbox-fill', 'badge' => $pendingCount > 0 ? (string)$pendingCount : null],
];
?>

<div class="mb-4">
    <?php require __DIR__ . '/../partials/header.php'; ?>
</div>

<?php if (empty($approvals)): ?>
    <div class="zb-surface p-4 text-center">
        <div class="zb-empty-icon mb-3">
            <i class="bi bi-clipboard-check"></i>
        </div>
        <h5 class="mb-2">No approval requests found</h5>
        <p class="text-muted mb-0">There are no approvals in the system yet.</p>
    </div>
<?php else: ?>
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-4">
            <div class="zb-surface p-3 h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="text-muted small text-uppercase">Pending</div>
                    <i class="bi bi-clock-history text-warning"></i>
                </div>
                <div class="fs-3 fw-bold mt-2"><?= View::e((string)$statuses['pending']) ?></div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="zb-surface p-3 h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="text-muted small text-uppercase">Approved</div>
                    <i class="bi bi-check-circle text-success"></i>
                </div>
                <div class="fs-3 fw-bold mt-2"><?= View::e((string)$statuses['approved']) ?></div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="zb-surface p-3 h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="text-muted small text-uppercase">Rejected</div>
                    <i class="bi bi-x-circle text-danger"></i>
                </div>
                <div class="fs-3 fw-bold mt-2"><?= View::e((string)$statuses['rejected']) ?></div>
            </div>
        </div>
    </div>

    <div class="zb-surface p-0 overflow-hidden">
        <div class="d-flex align-items-center justify-content-between p-3 border-bottom">
            <div class="fw-semibold d-flex align-items-center gap-2">
                <i class="bi bi-list-task text-muted"></i> All Requests
            </div>
            <div class="text-muted small">Total: <?= View::e((string)count($approvals)) ?></div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="zb-thead">
                <tr>
                    <th class="ps-3">ID</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th>Target</th>
                    <th>Requested By</th>
                    <th>Requested At</th>
                    <th class="text-end pe-3">Action</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($approvals as $approval): ?>
                    <?php
                    $id        = (int) ($approval['id'] ?? 0);
                    $type      = $approval['approval_type'] ?? '';
                    $status    = $approval['status'] ?? 'pending';
                    $targetId  = $approval['target_id'] ?? null;
                    $requester = $approval['requester_name'] ?? 'Unknown';
                    $createdAt = $approval['created_at'] ?? '';

                    $typeLabel = $type === 'project_completion'
                        ? 'Project completion'
                        : ($type !== '' ? $type : 'Unknown');
                    ?>
                    <tr>
                        <td class="ps-3 fw-semibold">#<?= View::e((string)$id) ?></td>
                        <td><span class="badge text-bg-secondary"><?= View::e($typeLabel) ?></span></td>
                        <td>
                            <?php if ($status === 'approved'): ?>
                                <span class="badge text-bg-success"><i class="bi bi-check-circle me-1"></i>Approved</span>
                            <?php elseif ($status === 'rejected'): ?>
                                <span class="badge text-bg-danger"><i class="bi bi-x-circle me-1"></i>Rejected</span>
                            <?php else: ?>
                                <span class="badge text-bg-warning"><i class="bi bi-clock me-1"></i>Pending</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (!empty($targetId)): ?>
                                <span class="text-muted"><i class="bi bi-hash me-1"></i><?= View::e((string)$targetId) ?></span>
                            <?php else: ?>
                                <span class="text-muted">–</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-person-circle text-muted"></i>
                                <span><?= View::e($requester) ?></span>
                            </div>
                        </td>
                        <td>
                            <?php if ($createdAt): ?>
                                <span class="text-muted small"><i class="bi bi-calendar3 me-1"></i><?= View::e($createdAt) ?></span>
                            <?php else: ?>
                                <span class="text-muted">–</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end pe-3">
                            <a href="/approvals/show?id=<?= View::e((string)$id) ?>" class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-eye me-1"></i> View
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>

<style>
    .zb-surface {
        background: var(--color-surface);
        border: 1px solid var(--color-border);
        border-radius: 12px;
    }

    body[data-theme="light"] .zb-surface {
        background: #fff;
    }

    .zb-surface .border-bottom {
        border-color: var(--color-border) !important;
    }

    .zb-thead th {
        font-size: .8rem;
        text-transform: uppercase;
        letter-spacing: .04em;
        color: var(--color-text-muted);
        background: rgba(0,0,0,.08);
        border-bottom: 1px solid var(--color-border);
    }

    body[data-theme="light"] .zb-thead th {
        background: rgba(0,0,0,.03);
    }

    .zb-empty-icon {
        width: 64px;
        height: 64px;
        border-radius: 14px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: rgba(56, 189, 248, .10);
        border: 1px solid rgba(56, 189, 248, .22);
    }

    .zb-empty-icon i {
        font-size: 1.8rem;
        color: var(--color-accent-blue);
    }
</style>
