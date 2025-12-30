<?php
// app/views/projects/approvals.php
// approvals.php shows the approval status and history for a specific project.

/** @var array $project */
/** @var array $approvals */

use App\core\View;
use App\core\Auth;

$user = Auth::user();

$id   = (int)($project['id'] ?? 0);
$name = (string)($project['name'] ?? '');
$code = (string)($project['code'] ?? '');
?>
<div class="zb-project-approvals">
    <div class="page-header">
        <div class="header-content">
            <?php require __DIR__ . '/../partials/breadcrumbs.php'; ?>
            <div class="title-section">
                <h1 class="page-title">Approval History</h1>
                <p class="page-subtitle">
                    For project: <strong><?= View::e($name) ?></strong>
                    <?php if ($code): ?> • <?= View::e($code) ?><?php endif; ?>
                </p>
            </div>
        </div>
        <div class="header-actions">
            <a href="/projects/show?id=<?= View::e((string)$id) ?>" class="btn-action btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to Project
            </a>
        </div>
    </div>

    <?php if (empty($approvals)): ?>
        <div class="empty-state">
            <div class="empty-icon"><i class="bi bi-clipboard-check"></i></div>
            <h3>No Approval Requests</h3>
            <p>No approval requests have been recorded for this project yet.</p>
            <a href="/approvals/create?project_id=<?= View::e((string)$id) ?>" class="btn-action btn-primary">
                <i class="bi bi-plus-circle"></i> Create First Approval
            </a>
        </div>
    <?php else: ?>
        <?php
        $total = count($approvals);
        $approved = $pending = $rejected = 0;

        foreach ($approvals as $a) {
            $status = (string)($a['status'] ?? 'pending');
            if ($status === 'approved') $approved++;
            elseif ($status === 'rejected') $rejected++;
            else $pending++;
        }
        ?>

        <div class="approval-stats">
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-value"><?= (int)$total ?></div>
                    <div class="stat-label">Total Requests</div>
                    <div class="stat-trend"><span class="trend-up">All approvals</span></div>
                </div>

                <div class="stat-card">
                    <div class="stat-value text-success"><?= (int)$approved ?></div>
                    <div class="stat-label">Approved</div>
                    <div class="stat-trend">
                        <span class="trend-up"><?= $total > 0 ? (int)round(($approved / $total) * 100) : 0 ?>% success rate</span>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-value text-warning"><?= (int)$pending ?></div>
                    <div class="stat-label">Pending</div>
                    <div class="stat-trend"><span class="trend-up">Awaiting review</span></div>
                </div>

                <div class="stat-card">
                    <div class="stat-value text-danger"><?= (int)$rejected ?></div>
                    <div class="stat-label">Rejected</div>
                    <div class="stat-trend"><span class="trend-down">Requires attention</span></div>
                </div>
            </div>
        </div>

        <div class="timeline-container">
            <h3 class="timeline-title">Approval Timeline</h3>
            <div class="timeline">
                <?php foreach ($approvals as $a): ?>
                    <?php
                    $aid = (int)($a['id'] ?? 0);
                    $type = (string)($a['approval_type'] ?? '');
                    $status = (string)($a['status'] ?? 'pending');
                    $requester = (string)($a['requester_name'] ?? ($a['requested_by'] ?? ''));
                    $createdAt = (string)($a['created_at'] ?? '');
                    $updatedAt = (string)($a['updated_at'] ?? '');

                    $requestDate = $createdAt ? date('M j, Y', strtotime($createdAt)) : '';
                    $decisionDate = $updatedAt ? date('M j, Y', strtotime($updatedAt)) : '';

                    $statusClass = 'pending';
                    $statusIcon = 'bi-clock-fill';
                    if ($status === 'approved') { $statusClass = 'approved'; $statusIcon = 'bi-check-circle-fill'; }
                    elseif ($status === 'rejected') { $statusClass = 'rejected'; $statusIcon = 'bi-x-circle-fill'; }
                    ?>
                    <div class="timeline-item <?= View::e($statusClass) ?>">
                        <div class="timeline-marker"><i class="bi <?= View::e($statusIcon) ?>"></i></div>

                        <div class="timeline-content">
                            <div class="timeline-header">
                                <div class="timeline-title">
                                    <h4>#<?= View::e((string)$aid) ?> - <?= View::e(ucwords(str_replace('_', ' ', $type))) ?></h4>
                                    <span class="status-badge <?= View::e($statusClass) ?>"><?= View::e(ucfirst($status)) ?></span>
                                </div>
                                <div class="timeline-actions">
                                    <a href="/approvals/show?id=<?= View::e((string)$aid) ?>" class="btn-timeline">
                                        <i class="bi bi-eye"></i> View Details
                                    </a>
                                </div>
                            </div>

                            <div class="timeline-body">
                                <div class="timeline-meta">
                                    <div class="meta-item"><i class="bi bi-person"></i><span>Requested by: <strong><?= View::e($requester) ?></strong></span></div>
                                    <div class="meta-item"><i class="bi bi-calendar"></i><span>Requested: <?= View::e($requestDate) ?></span></div>
                                    <?php if ($decisionDate): ?>
                                        <div class="meta-item"><i class="bi bi-calendar-check"></i><span>Decided: <?= View::e($decisionDate) ?></span></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="approval-table-container">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">All Approval Requests</h3>
                    <div class="card-actions">
                        <button class="btn-action btn-secondary" type="button" id="exportApprovalsBtn">
                            <i class="bi bi-download"></i> Export
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ID</th><th>Type</th><th>Status</th><th>Requester</th><th>Requested</th><th>Last Decision</th><th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($approvals as $a): ?>
                                    <?php
                                    $aid = (int)($a['id'] ?? 0);
                                    $type = (string)($a['approval_type'] ?? '');
                                    $status = (string)($a['status'] ?? 'pending');
                                    $requester = (string)($a['requester_name'] ?? ($a['requested_by'] ?? ''));
                                    $createdAt = (string)($a['created_at'] ?? '');
                                    $updatedAt = (string)($a['updated_at'] ?? '');
                                    ?>
                                    <tr>
                                        <td><span class="badge-id">#<?= View::e((string)$aid) ?></span></td>
                                        <td>
                                            <div class="type-cell">
                                                <i class="bi bi-clipboard-check"></i>
                                                <?= View::e(ucwords(str_replace('_', ' ', $type))) ?>
                                            </div>
                                        </td>
                                        <td><span class="status-badge status-<?= View::e($status) ?>"><?= View::e(ucfirst($status)) ?></span></td>
                                        <td>
                                            <div class="user-cell">
                                                <div class="user-avatar-sm"><?= View::e(strtoupper(substr($requester, 0, 1))) ?></div>
                                                <?= View::e($requester) ?>
                                            </div>
                                        </td>
                                        <td><div class="date-cell"><i class="bi bi-calendar"></i><?= View::e($createdAt) ?></div></td>
                                        <td>
                                            <div class="date-cell">
                                                <i class="bi bi-clock"></i>
                                                <?= $updatedAt ? View::e($updatedAt) : '<span class="text-muted">—</span>' ?>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="action-buttons">
                                                <a href="/approvals/show?id=<?= View::e((string)$aid) ?>" class="btn-action-sm btn-secondary"><i class="bi bi-eye"></i></a>
                                                <?php if (in_array(($user['role_key'] ?? ''), ['super_admin','director','system_admin'], true)): ?>
                                                    <a href="/approvals/edit?id=<?= View::e((string)$aid) ?>" class="btn-action-sm btn-secondary"><i class="bi bi-pencil"></i></a>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
(() => {
    const exportBtn = document.getElementById('exportApprovalsBtn');
    if (!exportBtn) return;

    const approvals = <?php
        $rows = [];
        foreach ($approvals as $a) {
            $rows[] = [
                (string)($a['id'] ?? ''),
                (string)($a['approval_type'] ?? ''),
                (string)($a['status'] ?? ''),
                (string)($a['requester_name'] ?? ($a['requested_by'] ?? '')),
                (string)($a['created_at'] ?? ''),
                (string)($a['updated_at'] ?? ''),
            ];
        }
        echo json_encode($rows, JSON_UNESCAPED_SLASHES);
    ?>;

    function csvEscape(cell) {
        const s = String(cell ?? '');
        return `"${s.replaceAll('"', '""')}"`;
    }

    exportBtn.addEventListener('click', () => {
        exportBtn.disabled = true;
        const original = exportBtn.innerHTML;
        exportBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Exporting...';

        // Build CSV
        const header = ['ID','Type','Status','Requester','Requested','Last Decision'].map(csvEscape).join(',');
        const body = approvals.map(r => r.map(csvEscape).join(',')).join('\n');
        const csv = header + '\n' + body;

        const blob = new Blob([csv], { type: 'text/csv;charset=utf-8' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `project-<?= (int)$id ?>-approvals-<?= date('Y-m-d') ?>.csv`;
        document.body.appendChild(a);
        a.click();
        a.remove();
        URL.revokeObjectURL(url);

        exportBtn.innerHTML = original;
        exportBtn.disabled = false;
    });
})();
</script>
