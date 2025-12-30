<?php
// app/views/approvals/show.php
// Displays details of a specific approval request and its decision history.

/** @var array $approval */
/** @var array $decisions */

use App\core\View;
use App\core\Auth;

$user    = Auth::user();
$roleKey = $user['role_key'] ?? 'developer';

$id        = (int) ($approval['id'] ?? 0);
$type      = $approval['approval_type'] ?? '';
$status    = $approval['status'] ?? 'pending';
$targetId  = $approval['target_id'] ?? null;
$requester = $approval['requester_name'] ?? 'Unknown';
$createdAt = $approval['created_at'] ?? '';
$updatedAt = $approval['updated_at'] ?? '';

$typeLabel = $type === 'project_completion' ? 'Project completion' : ($type !== '' ? $type : 'Unknown');

$canDecide = (
    in_array($roleKey, ['super_admin', 'director', 'system_admin'], true)
    && $status === 'pending'
);

$pageTitle = 'Approval #' . $id;
$subtitle  = 'Review request details and decision history';

$actions = [
    ['label' => 'Back to list', 'url' => '/approvals', 'type' => 'secondary', 'icon' => 'bi-arrow-left'],
    ['label' => 'Pending inbox', 'url' => '/approvals/inbox', 'type' => 'secondary', 'icon' => 'bi-inbox'],
];
?>

<div class="mb-4">
    <?php require __DIR__ . '/../partials/header.php'; ?>
</div>

<div class="row g-3 mb-4">
    <div class="col-12 col-lg-8">
        <div class="zb-surface p-4 h-100">
            <div class="d-flex align-items-start justify-content-between gap-3 mb-3">
                <div>
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <span class="badge text-bg-secondary"><?= View::e($typeLabel) ?></span>

                        <?php if ($status === 'approved'): ?>
                            <span class="badge text-bg-success"><i class="bi bi-check-circle me-1"></i>Approved</span>
                        <?php elseif ($status === 'rejected'): ?>
                            <span class="badge text-bg-danger"><i class="bi bi-x-circle me-1"></i>Rejected</span>
                        <?php else: ?>
                            <span class="badge text-bg-warning"><i class="bi bi-clock me-1"></i>Pending</span>
                        <?php endif; ?>
                    </div>
                    <div class="text-muted small mt-2">Approval request metadata</div>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-12 col-md-6">
                    <div class="zb-kv">
                        <div class="zb-k">Requested by</div>
                        <div class="zb-v"><i class="bi bi-person-circle text-muted me-1"></i><?= View::e($requester) ?></div>
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <div class="zb-kv">
                        <div class="zb-k">Requested at</div>
                        <div class="zb-v"><i class="bi bi-calendar3 text-muted me-1"></i><?= $createdAt ? View::e($createdAt) : '–' ?></div>
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <div class="zb-kv">
                        <div class="zb-k">Last updated</div>
                        <div class="zb-v"><i class="bi bi-clock-history text-muted me-1"></i><?= $updatedAt ? View::e($updatedAt) : '–' ?></div>
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <div class="zb-kv">
                        <div class="zb-k">Target</div>
                        <div class="zb-v">
                            <?php if ($targetId): ?>
                                <?php if ($type === 'project_completion'): ?>
                                    <a class="text-decoration-none" href="/projects/show?id=<?= View::e((string)$targetId) ?>">
                                        Project #<?= View::e((string)$targetId) ?> <i class="bi bi-box-arrow-up-right ms-1"></i>
                                    </a>
                                <?php else: ?>
                                    <?= View::e((string)$targetId) ?>
                                <?php endif; ?>
                            <?php else: ?>
                                –
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="col-12 col-lg-4">
        <div class="zb-surface p-4 h-100">
            <div class="fw-semibold mb-2">Decision access</div>

            <?php if ($canDecide): ?>
                <div class="alert alert-success mb-0">
                    <i class="bi bi-shield-check me-1"></i>
                    You can approve or reject this request.
                </div>
            <?php elseif ($status === 'pending'): ?>
                <div class="alert alert-info mb-0">
                    <i class="bi bi-info-circle me-1"></i>
                    Pending. Only directors, system admins, or super admins can decide.
                </div>
            <?php else: ?>
                <div class="alert alert-secondary mb-0">
                    <i class="bi bi-check2-all me-1"></i>
                    This request is already finalized.
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Decision history -->
<?php if (!empty($decisions)): ?>
    <div class="zb-surface p-0 overflow-hidden mb-4">
        <div class="d-flex align-items-center justify-content-between p-3 border-bottom">
            <div class="fw-semibold d-flex align-items-center gap-2">
                <i class="bi bi-clock-history text-muted"></i> Decision History
            </div>
            <div class="text-muted small">Entries: <?= View::e((string)count($decisions)) ?></div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="zb-thead">
                <tr>
                    <th class="ps-3">Decision</th>
                    <th>Approver</th>
                    <th>Comment</th>
                    <th class="pe-3">Decision Date</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($decisions as $d): ?>
                    <?php
                    $decision  = $d['decision'] ?? '';
                    $comment   = $d['comment'] ?? '';
                    $decider   = $d['approver_name'] ?? 'Unknown';
                    $decidedAt = $d['decided_at'] ?? '';
                    ?>
                    <tr>
                        <td class="ps-3">
                            <?php if ($decision === 'approved'): ?>
                                <span class="badge text-bg-success"><i class="bi bi-check-circle me-1"></i>Approved</span>
                            <?php elseif ($decision === 'rejected'): ?>
                                <span class="badge text-bg-danger"><i class="bi bi-x-circle me-1"></i>Rejected</span>
                            <?php else: ?>
                                <span class="badge text-bg-secondary"><?= View::e(ucfirst($decision)) ?></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-person-check text-muted"></i>
                                <span><?= View::e($decider) ?></span>
                            </div>
                        </td>
                        <td>
                            <?php if ($comment !== ''): ?>
                                <div class="zb-note"><?= nl2br(View::e($comment)) ?></div>
                            <?php else: ?>
                                <span class="text-muted fst-italic">No comment</span>
                            <?php endif; ?>
                        </td>
                        <td class="pe-3">
                            <?= $decidedAt ? '<span class="text-muted small"><i class="bi bi-calendar-check me-1"></i>' . View::e($decidedAt) . '</span>' : '<span class="text-muted">–</span>' ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php else: ?>
    <div class="zb-surface p-4 text-center mb-4">
        <div class="zb-empty-icon mb-3">
            <i class="bi bi-journal-text"></i>
        </div>
        <h6 class="mb-1">No decisions recorded yet</h6>
        <p class="text-muted mb-0">This approval is awaiting its first decision.</p>
    </div>
<?php endif; ?>

<!-- Decision form -->
<?php if ($canDecide): ?>
    <div class="zb-surface p-4">
        <div class="fw-semibold mb-3 d-flex align-items-center gap-2">
            <i class="bi bi-pencil-square text-muted"></i> Record Your Decision
        </div>

        <form method="post" action="/approvals/decide" class="needs-validation" novalidate>
            <input type="hidden" name="approval_id" value="<?= View::e((string)$id) ?>">

            <div class="row g-3">
                <div class="col-12 col-lg-4">
                    <label for="decision" class="form-label">Decision</label>
                    <select name="decision" id="decision" class="form-select" required>
                        <option value="" selected disabled>Choose…</option>
                        <option value="approved">Approve</option>
                        <option value="rejected">Reject</option>
                    </select>
                    <div class="invalid-feedback">Select a decision.</div>
                </div>

                <div class="col-12 col-lg-8">
                    <label for="comment" class="form-label">Comment (optional)</label>
                    <textarea
                        name="comment"
                        id="comment"
                        rows="4"
                        class="form-control"
                        maxlength="500"
                        placeholder="Short reason or notes (optional)"
                    ></textarea>
                    <div class="d-flex justify-content-between">
                        <div class="form-text">Max 500 characters</div>
                        <div class="form-text" id="commentCount">0/500</div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end mt-3">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-send-check me-1"></i> Submit
                </button>
            </div>
        </form>
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

    .zb-kv .zb-k {
        font-size: .8rem;
        color: var(--color-text-muted);
        text-transform: uppercase;
        letter-spacing: .04em;
        margin-bottom: .2rem;
    }

    .zb-kv .zb-v {
        font-weight: 600;
    }

    .zb-note {
        background: rgba(0,0,0,.10);
        border: 1px solid var(--color-border);
        border-radius: 10px;
        padding: .75rem;
    }

    body[data-theme="light"] .zb-note {
        background: rgba(0,0,0,.03);
    }

    .zb-empty-icon {
        width: 64px;
        height: 64px;
        border-radius: 14px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: rgba(255, 200, 87, .10);
        border: 1px solid rgba(255, 200, 87, .25);
    }

    .zb-empty-icon i {
        font-size: 1.8rem;
        color: var(--color-accent);
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Bootstrap validation
        document.querySelectorAll('.needs-validation').forEach(function (form) {
            form.addEventListener('submit', function (e) {
                if (!form.checkValidity()) {
                    e.preventDefault();
                    e.stopPropagation();
                }
                form.classList.add('was-validated');
            });
        });

        // Comment counter
        const comment = document.getElementById('comment');
        const counter = document.getElementById('commentCount');
        if (comment && counter) {
            const update = () => counter.textContent = comment.value.length + '/500';
            comment.addEventListener('input', update);
            update();
        }
    });
</script>
