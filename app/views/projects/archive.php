<?php
// app/views/projects/archive.php
// archive.php lists archived or completed projects for reference.

/** @var array $projects */

use App\core\View;
use App\core\Auth;

$user = Auth::user();
$roleKey = (string)($user['role_key'] ?? '');

function time_ago_php(string $datetime): string {
    $time = strtotime($datetime);
    if (!$time) return '';
    $diff = time() - $time;
    if ($diff < 60) return 'just now';
    if ($diff < 3600) return floor($diff / 60) . ' min ago';
    if ($diff < 86400) { $h = floor($diff / 3600); return $h . ' hour' . ($h !== 1 ? 's' : '') . ' ago'; }
    $d = floor($diff / 86400);
    return $d === 1 ? 'yesterday' : $d . ' days ago';
}
?>
<div class="zb-project-archive">
    <div class="page-header">
        <div class="header-content">
            <?php require __DIR__ . '/../partials/breadcrumbs.php'; ?>
            <div class="title-section">
                <h1 class="page-title">Archived Projects</h1>
                <p class="page-subtitle">Historical projects kept for reference and reporting</p>
            </div>
        </div>
        <div class="header-actions">
            <a href="/projects" class="btn-action btn-primary"><i class="bi bi-arrow-left"></i> Back to Active</a>
            <button class="btn-action btn-secondary" type="button" id="exportArchiveBtn"><i class="bi bi-download"></i> Export Archive</button>
        </div>
    </div>

    <?php if (empty($projects)): ?>
        <div class="empty-state">
            <div class="empty-icon"><i class="bi bi-archive"></i></div>
            <h3>No Archived Projects</h3>
            <p>No archived or completed projects found in the system.</p>
            <a href="/projects/create" class="btn-action btn-primary"><i class="bi bi-plus-circle"></i> Create New Project</a>
        </div>
    <?php else: ?>
        <?php
        $total = count($projects);
        $completed = $cancelled = $onHold = 0;
        $totalDuration = 0.0;

        foreach ($projects as $p) {
            $status = (string)($p['status'] ?? '');
            if ($status === 'completed') $completed++;
            elseif ($status === 'cancelled') $cancelled++;
            elseif ($status === 'on_hold') $onHold++;

            $start = (string)($p['start_date'] ?? '');
            $end = (string)($p['completed_at'] ?? ($p['updated_at'] ?? ''));
            if ($start && $end) {
                $st = strtotime($start);
                $et = strtotime($end);
                if ($st && $et && $et >= $st) $totalDuration += ($et - $st) / 86400;
            }
        }
        $avgDuration = $total > 0 ? (int)round($totalDuration / $total) : 0;
        ?>

        <div class="archive-stats">
            <div class="stats-grid">
                <div class="stat-card"><div class="stat-value"><?= (int)$total ?></div><div class="stat-label">Total Archived</div><div class="stat-trend"><span class="trend-up">Historical records</span></div></div>
                <div class="stat-card"><div class="stat-value text-success"><?= (int)$completed ?></div><div class="stat-label">Successfully Completed</div><div class="stat-trend"><span class="trend-up"><?= $total ? (int)round(($completed / $total) * 100) : 0 ?>% success rate</span></div></div>
                <div class="stat-card"><div class="stat-value text-warning"><?= (int)$onHold ?></div><div class="stat-label">On Hold</div><div class="stat-trend"><span class="trend-up">May resume</span></div></div>
                <div class="stat-card"><div class="stat-value text-danger"><?= (int)$cancelled ?></div><div class="stat-label">Cancelled</div><div class="stat-trend"><span class="trend-down">Terminated early</span></div></div>
            </div>

            <div class="stats-grid mt-3">
                <div class="stat-card"><div class="stat-value"><?= (int)$avgDuration ?></div><div class="stat-label">Avg. Duration (Days)</div><div class="stat-trend"><span class="trend-up">Across all projects</span></div></div>
                <div class="stat-card"><div class="stat-value"><?= (int)date('Y') ?></div><div class="stat-label">Current Year</div><div class="stat-trend"><span class="trend-up">Archive maintained</span></div></div>
            </div>
        </div>

        <div class="filter-section">
            <div class="search-box">
                <i class="bi bi-search search-icon"></i>
                <input type="text" class="search-input" placeholder="Search archived projects..." id="archiveSearch">
            </div>

            <div class="filter-buttons">
                <div class="filter-group" id="archiveFilters">
                    <button class="filter-btn active" type="button" data-filter="all">All (<?= (int)$total ?>)</button>
                    <button class="filter-btn" type="button" data-filter="completed">Completed (<?= (int)$completed ?>)</button>
                    <button class="filter-btn" type="button" data-filter="cancelled">Cancelled (<?= (int)$cancelled ?>)</button>
                    <button class="filter-btn" type="button" data-filter="on_hold">On Hold (<?= (int)$onHold ?>)</button>
                </div>
            </div>
        </div>

        <div class="projects-grid" id="projectsGrid">
            <?php foreach ($projects as $p): ?>
                <?php
                $id = (int)($p['id'] ?? 0);
                $name = (string)($p['name'] ?? '');
                $code = (string)($p['code'] ?? '');
                $status = (string)($p['status'] ?? '');
                $client = (string)($p['client_name'] ?? '');
                $completedAt = (string)($p['completed_at'] ?? ($p['updated_at'] ?? ''));
                $description = (string)($p['description'] ?? '');
                $category = (string)($p['category'] ?? '');

                $statusClass = $status === 'completed' ? 'status-completed' : ($status === 'cancelled' ? 'status-cancelled' : ($status === 'on_hold' ? 'status-on-hold' : ''));
                $formattedDate = $completedAt ? date('M j, Y', strtotime($completedAt)) : '';
                $timeAgo = $completedAt ? time_ago_php($completedAt) : '';
                $truncatedDesc = strlen($description) > 120 ? substr($description, 0, 120) . '...' : $description;
                ?>
                <div class="project-card" data-status="<?= View::e($status) ?>">
                    <div class="card-header">
                        <div class="project-status <?= View::e($statusClass) ?>">
                            <i class="bi <?= View::e($status === 'completed' ? 'bi-check-circle' : ($status === 'cancelled' ? 'bi-x-circle' : 'bi-pause-circle')) ?>"></i>
                            <?= View::e(ucwords(str_replace('_', ' ', $status))) ?>
                        </div>
                        <div class="project-actions">
                            <a href="/projects/show?id=<?= View::e((string)$id) ?>" class="btn-action-sm"><i class="bi bi-eye"></i></a>
                            <?php if (in_array($roleKey, ['super_admin','director','system_admin'], true)): ?>
                                <a href="/projects/restore?id=<?= View::e((string)$id) ?>" class="btn-action-sm btn-warning"><i class="bi bi-arrow-counterclockwise"></i></a>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="card-body">
                        <h3 class="project-title">
                            <?= View::e($name) ?>
                            <?php if ($code): ?><span class="project-code"><?= View::e($code) ?></span><?php endif; ?>
                        </h3>

                        <?php if ($category): ?>
                            <div class="project-category"><i class="bi bi-tag"></i><?= View::e($category) ?></div>
                        <?php endif; ?>

                        <?php if ($client): ?>
                            <div class="project-client"><i class="bi bi-building"></i><?= View::e($client) ?></div>
                        <?php endif; ?>

                        <?php if ($truncatedDesc): ?>
                            <p class="project-description"><?= nl2br(View::e($truncatedDesc)) ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="card-footer">
                        <div class="project-meta">
                            <?php if ($formattedDate): ?>
                                <div class="meta-item">
                                    <i class="bi bi-calendar-check"></i>
                                    <span><?= View::e($formattedDate) ?></span>
                                    <?php if ($timeAgo): ?><small class="text-muted">(<?= View::e($timeAgo) ?>)</small><?php endif; ?>
                                </div>
                            <?php endif; ?>
                            <div class="meta-item"><i class="bi bi-clock-history"></i><span>Archived</span></div>
                        </div>

                        <a href="/projects/show?id=<?= View::e((string)$id) ?>" class="btn-action btn-secondary btn-sm">
                            View Details <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="table-view-toggle">
            <button class="btn-action btn-secondary" type="button" id="toggleArchiveViewBtn">
                <i class="bi bi-table"></i> Switch to Table View
            </button>
        </div>

        <div class="archive-table" id="archiveTable" style="display:none;">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Archived Projects - Table View</h3>
                    <div class="card-actions">
                        <button class="btn-action btn-secondary" type="button" id="exportArchiveCsvBtn">
                            <i class="bi bi-download"></i> Export CSV
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr><th>Name</th><th>Code</th><th>Status</th><th>Client</th><th>Category</th><th>Completed At</th><th>Actions</th></tr>
                            </thead>
                            <tbody>
                                <?php foreach ($projects as $p): ?>
                                    <?php $id = (int)($p['id'] ?? 0); ?>
                                    <tr>
                                        <td><strong><?= View::e((string)($p['name'] ?? '')) ?></strong></td>
                                        <td><span class="badge-id"><?= View::e((string)($p['code'] ?? '')) ?></span></td>
                                        <td><span class="status-badge status-<?= View::e((string)($p['status'] ?? '')) ?>"><?= View::e(ucwords(str_replace('_', ' ', (string)($p['status'] ?? '')))) ?></span></td>
                                        <td><?= View::e((string)($p['client_name'] ?? '')) ?></td>
                                        <td><?= View::e((string)($p['category'] ?? '')) ?></td>
                                        <td><div class="date-cell"><i class="bi bi-calendar"></i><?= View::e((string)($p['completed_at'] ?? ($p['updated_at'] ?? ''))) ?></div></td>
                                        <td>
                                            <div class="action-buttons">
                                                <a href="/projects/show?id=<?= View::e((string)$id) ?>" class="btn-action-sm btn-secondary"><i class="bi bi-eye"></i></a>
                                                <?php if (in_array($roleKey, ['super_admin','director','system_admin'], true)): ?>
                                                    <a href="/projects/restore?id=<?= View::e((string)$id) ?>" class="btn-action-sm btn-warning"><i class="bi bi-arrow-counterclockwise"></i></a>
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
    const grid = document.getElementById('projectsGrid');
    const table = document.getElementById('archiveTable');
    const toggleBtn = document.getElementById('toggleArchiveViewBtn');
    const searchInput = document.getElementById('archiveSearch');
    const filtersWrap = document.getElementById('archiveFilters');

    const projects = () => Array.from(document.querySelectorAll('.project-card'));

    function applyFilter(status) {
        projects().forEach(card => {
            const matches = (status === 'all' || (card.dataset.status === status));
            card.style.display = matches ? 'flex' : 'none';
        });
    }

    function applySearch(q) {
        const query = q.trim().toLowerCase();
        projects().forEach(card => {
            const text = card.textContent.toLowerCase();
            card.style.display = text.includes(query) ? 'flex' : 'none';
        });
    }

    if (filtersWrap) {
        filtersWrap.addEventListener('click', (e) => {
            const btn = e.target.closest('.filter-btn');
            if (!btn) return;
            const status = btn.dataset.filter || 'all';
            filtersWrap.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            applyFilter(status);
            if (searchInput && searchInput.value.trim()) applySearch(searchInput.value);
        });
    }

    if (searchInput) {
        searchInput.addEventListener('input', () => {
            applySearch(searchInput.value);
        });
    }

    if (toggleBtn && grid && table) {
        toggleBtn.addEventListener('click', () => {
            const isGrid = grid.style.display !== 'none';
            grid.style.display = isGrid ? 'none' : 'grid';
            table.style.display = isGrid ? 'block' : 'none';
            toggleBtn.innerHTML = isGrid
                ? '<i class="bi bi-grid"></i> Switch to Grid View'
                : '<i class="bi bi-table"></i> Switch to Table View';
        });
    }

    const exportBtn = document.getElementById('exportArchiveBtn');
    const exportCsvBtn = document.getElementById('exportArchiveCsvBtn');

    const rows = <?php
        $rows = [];
        foreach ($projects as $p) {
            $rows[] = [
                (string)($p['name'] ?? ''),
                (string)($p['code'] ?? ''),
                (string)($p['status'] ?? ''),
                (string)($p['client_name'] ?? ''),
                (string)($p['category'] ?? ''),
                (string)($p['completed_at'] ?? ($p['updated_at'] ?? '')),
            ];
        }
        echo json_encode($rows, JSON_UNESCAPED_SLASHES);
    ?>;

    function csvEscape(cell) {
        const s = String(cell ?? '');
        return `"${s.replaceAll('"', '""')}"`;
    }

    function exportArchive() {
        const header = ['Name','Code','Status','Client','Category','Completed At'].map(csvEscape).join(',');
        const body = rows.map(r => r.map(csvEscape).join(',')).join('\n');
        const csv = header + '\n' + body;

        const blob = new Blob([csv], { type: 'text/csv;charset=utf-8' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `project-archive-<?= date('Y-m-d') ?>.csv`;
        document.body.appendChild(a);
        a.click();
        a.remove();
        URL.revokeObjectURL(url);
    }

    if (exportBtn) exportBtn.addEventListener('click', exportArchive);
    if (exportCsvBtn) exportCsvBtn.addEventListener('click', exportArchive);
})();
</script>
