<?php
// app/views/approvals/inbox.php
// Shows pending approval requests that require action from the current approver.

/** @var array $approvals */

use App\core\View;
use App\core\Auth;

$user = Auth::user();

$pageTitle = 'Pending Approvals';
$subtitle  = 'Requests awaiting your review and decision';

$pendingTotal = is_array($approvals) ? count($approvals) : 0;

$actions = [
    ['label' => 'All Approvals', 'url' => '/approvals', 'type' => 'secondary', 'icon' => 'bi-list-check'],
    ['label' => 'Pending', 'url' => '/approvals/inbox', 'type' => 'secondary', 'icon' => 'bi-clock-history', 'badge' => (string)$pendingTotal],
];
?>

<div class="mb-4">
    <?php require __DIR__ . '/../partials/header.php'; ?>
</div>

<?php if (empty($approvals)): ?>
    <div class="zb-surface p-4 text-center">
        <div class="zb-empty-icon mb-3">
            <i class="bi bi-check2-circle"></i>
        </div>
        <h5 class="mb-2">All caught up</h5>
        <p class="text-muted mb-3">You have no pending approvals right now.</p>
        <a href="/approvals" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-list-check me-1"></i> View all approvals
        </a>
    </div>
<?php else: ?>
    <div class="zb-surface p-0 overflow-hidden">
        <div class="d-flex align-items-center justify-content-between p-3 border-bottom">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-inbox text-muted"></i>
                <div class="fw-semibold">Pending Requests</div>
            </div>
            <div class="text-muted small">Showing <?= View::e((string)$pendingTotal) ?></div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="zb-thead">
                <tr>
                    <th class="ps-3">ID</th>
                    <th>Type</th>
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
                    $targetId  = $approval['target_id'] ?? null;
                    $requester = $approval['requester_name'] ?? 'Unknown';
                    $createdAt = $approval['created_at'] ?? '';

                    $typeLabel = $type === 'project_completion'
                        ? 'Project completion'
                        : ($type !== '' ? $type : 'Unknown');
                    ?>
                    <tr>
                        <td class="ps-3 fw-semibold">#<?= View::e((string)$id) ?></td>
                        <td>
                            <span class="badge text-bg-secondary">
                                <?= View::e($typeLabel) ?>
                            </span>
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
                            <a href="/approvals/show?id=<?= View::e((string)$id) ?>" class="btn btn-sm btn-primary">
                                <i class="bi bi-eye me-1"></i> Review
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
        background: rgba(52, 199, 89, .12);
        border: 1px solid rgba(52, 199, 89, .25);
    }

    .zb-empty-icon i {
        font-size: 1.8rem;
        color: var(--color-accent-green);
    }
</style>
