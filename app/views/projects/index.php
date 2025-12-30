<?php
// app/views/projects/index.php
// index.php lists projects with filters such as status, priority, and category.

/** @var array $projects */
/** @var array $filters */

use App\core\View;
use App\core\Auth;

$user    = Auth::user();
$roleKey = $user['role_key'] ?? '';

$statusFilter   = $filters['status']   ?? ($_GET['status']   ?? '');
$priorityFilter = $filters['priority'] ?? ($_GET['priority'] ?? '');
$categoryFilter = $filters['category'] ?? ($_GET['category'] ?? '');
$search         = $filters['search']   ?? ($_GET['search']   ?? '');

// Calculate stats
$totalProjects = count($projects);
$activeProjects = 0;
$completedProjects = 0;
$highPriority = 0;

foreach ($projects as $p) {
    $status = $p['status'] ?? '';
    $priority = $p['priority'] ?? '';
    
    if (in_array($status, ['ongoing', 'planning'])) {
        $activeProjects++;
    } elseif ($status === 'completed') {
        $completedProjects++;
    }
    
    if (in_array($priority, ['high', 'critical'])) {
        $highPriority++;
    }
}
?>
<div class="zb-projects-index">
    <!-- Page Header -->
    <div class="page-header">
        <div class="header-content">
            <?php require __DIR__ . '/../partials/breadcrumbs.php'; ?>
            <div class="title-section">
                <h1 class="page-title">Projects</h1>
                <p class="page-subtitle">
                    Manage and track all your projects in one place
                </p>
            </div>
        </div>
        <div class="header-actions">
            <?php if (in_array($roleKey, ['super_admin', 'director', 'system_admin', 'marketer'], true)): ?>
                <a href="/projects/create" class="btn-action btn-primary">
                    <i class="bi bi-plus-circle"></i>
                    New Project
                </a>
            <?php endif; ?>
            <button class="btn-action btn-secondary" onclick="toggleViewMode()">
                <i class="bi bi-grid"></i>
                <span id="viewModeText">Grid View</span>
            </button>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="quick-stats">
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-value"><?= $totalProjects ?></div>
                <div class="stat-label">Total Projects</div>
                <div class="stat-trend">
                    <span class="trend-up">All categories</span>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-value text-success"><?= $activeProjects ?></div>
                <div class="stat-label">Active Projects</div>
                <div class="stat-trend">
                    <span class="trend-up">Currently in progress</span>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-value text-warning"><?= $completedProjects ?></div>
                <div class="stat-label">Completed</div>
                <div class="stat-trend">
                    <span class="trend-up">Successfully delivered</span>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-value text-danger"><?= $highPriority ?></div>
                <div class="stat-label">High Priority</div>
                <div class="stat-trend">
                    <span class="trend-up">Requires attention</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="filter-section">
        <div class="search-box">
            <i class="bi bi-search search-icon"></i>
            <input type="text" 
                   class="search-input" 
                   placeholder="Search projects by name, code, or client..."
                   name="search"
                   value="<?= View::e($search) ?>"
                   onkeyup="filterProjects()">
        </div>
        
        <div class="filter-buttons">
            <div class="filter-group">
                <button class="filter-btn <?= !$statusFilter ? 'active' : '' ?>" onclick="setStatusFilter('')">
                    All Status
                </button>
                <?php foreach (['ongoing', 'planning', 'on_hold', 'completed', 'cancelled'] as $status): ?>
                    <button class="filter-btn <?= $statusFilter === $status ? 'active' : '' ?>" 
                            onclick="setStatusFilter('<?= $status ?>')">
                        <?= ucwords(str_replace('_', ' ', $status)) ?>
                    </button>
                <?php endforeach; ?>
            </div>
            
            <div class="filter-group">
                <button class="filter-btn <?= !$priorityFilter ? 'active' : '' ?>" onclick="setPriorityFilter('')">
                    All Priority
                </button>
                <?php foreach (['low', 'medium', 'high', 'critical'] as $priority): ?>
                    <button class="filter-btn <?= $priorityFilter === $priority ? 'active' : '' ?>" 
                            onclick="setPriorityFilter('<?= $priority ?>')">
                        <?= ucfirst($priority) ?>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Projects Grid (Default View) -->
    <div class="projects-grid" id="projectsGrid">
        <?php foreach ($projects as $p): ?>
            <?php
            $id = (int) ($p['id'] ?? 0);
            $name = $p['name'] ?? '';
            $code = $p['code'] ?? '';
            $status = $p['status'] ?? '';
            $priority = $p['priority'] ?? '';
            $category = $p['category'] ?? '';
            $client = $p['client_name'] ?? '';
            $targetEnd = $p['target_end_date'] ?? '';
            $description = $p['description'] ?? '';
            
            // Status colors and icons
            $statusClass = '';
            $statusIcon = '';
            switch ($status) {
                case 'ongoing':
                    $statusClass = 'status-ongoing';
                    $statusIcon = 'bi-play-circle';
                    break;
                case 'planning':
                    $statusClass = 'status-planning';
                    $statusIcon = 'bi-calendar';
                    break;
                case 'on_hold':
                    $statusClass = 'status-on-hold';
                    $statusIcon = 'bi-pause-circle';
                    break;
                case 'completed':
                    $statusClass = 'status-completed';
                    $statusIcon = 'bi-check-circle';
                    break;
                case 'cancelled':
                    $statusClass = 'status-cancelled';
                    $statusIcon = 'bi-x-circle';
                    break;
                default:
                    $statusClass = 'status-draft';
                    $statusIcon = 'bi-file-earmark';
            }
            
            // Priority colors
            $priorityClass = '';
            switch ($priority) {
                case 'critical':
                    $priorityClass = 'priority-critical';
                    break;
                case 'high':
                    $priorityClass = 'priority-high';
                    break;
                case 'medium':
                    $priorityClass = 'priority-medium';
                    break;
                case 'low':
                    $priorityClass = 'priority-low';
                    break;
            }
            
            // Truncate description
            $truncatedDesc = strlen($description) > 120 ? substr($description, 0, 120) . '...' : $description;
            
            // Calculate days until target
            $daysUntil = '';
            if ($targetEnd) {
                $targetDate = new DateTime($targetEnd);
                $today = new DateTime();
                $interval = $today->diff($targetDate);
                $days = $interval->days;
                $daysUntil = $interval->invert ? "Overdue by $days days" : "Due in $days days";
            }
            ?>
            
            <div class="project-card" 
                 data-status="<?= $status ?>"
                 data-priority="<?= $priority ?>"
                 data-category="<?= strtolower($category) ?>">
                <div class="card-header">
                    <div class="project-status <?= $statusClass ?>">
                        <i class="bi <?= $statusIcon ?>"></i>
                        <?= ucwords(str_replace('_', ' ', $status)) ?>
                    </div>
                    <div class="project-priority <?= $priorityClass ?>">
                        <i class="bi bi-flag"></i>
                        <?= ucfirst($priority) ?>
                    </div>
                </div>
                
                <div class="card-body">
                    <div class="project-meta">
                        <?php if ($code): ?>
                            <span class="project-code"><?= View::e($code) ?></span>
                        <?php endif; ?>
                        <?php if ($category): ?>
                            <span class="project-category"><?= View::e($category) ?></span>
                        <?php endif; ?>
                    </div>
                    
                    <h3 class="project-title">
                        <a href="/projects/show?id=<?= View::e((string)$id) ?>">
                            <?= View::e($name) ?>
                        </a>
                    </h3>
                    
                    <?php if ($client): ?>
                        <div class="project-client">
                            <i class="bi bi-building"></i>
                            <?= View::e($client) ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($truncatedDesc): ?>
                        <p class="project-description">
                            <?= nl2br(View::e($truncatedDesc)) ?>
                        </p>
                    <?php endif; ?>
                    
                    <?php if ($targetEnd): ?>
                        <div class="project-timeline">
                            <i class="bi bi-calendar"></i>
                            <span>Target: <?= date('M j, Y', strtotime($targetEnd)) ?></span>
                            <?php if ($daysUntil): ?>
                                <span class="timeline-status <?= strpos($daysUntil, 'Overdue') !== false ? 'overdue' : '' ?>">
                                    • <?= $daysUntil ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="card-footer">
                    <div class="project-actions">
                        <a href="/projects/show?id=<?= View::e((string)$id) ?>" class="btn-action-sm btn-secondary">
                            <i class="bi bi-eye"></i>
                            View
                        </a>
                        <?php if (in_array($roleKey, ['super_admin', 'director', 'system_admin', 'marketer'], true)): ?>
                            <a href="/projects/edit?id=<?= View::e((string)$id) ?>" class="btn-action-sm btn-secondary">
                                <i class="bi bi-pencil"></i>
                                Edit
                            </a>
                        <?php endif; ?>
                        <div class="dropdown">
                            <button class="btn-action-sm btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="bi bi-three-dots"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a class="dropdown-item" href="/projects/duplicate?id=<?= View::e((string)$id) ?>">
                                    <i class="bi bi-files"></i> Duplicate
                                </a>
                                <a class="dropdown-item" href="/projects/export?id=<?= View::e((string)$id) ?>">
                                    <i class="bi bi-download"></i> Export
                                </a>
                                <div class="dropdown-divider"></div>
                                <?php if (in_array($roleKey, ['super_admin', 'director', 'system_admin'])): ?>
                                    <a class="dropdown-item text-danger" href="#" onclick="archiveProject(<?= $id ?>)">
                                        <i class="bi bi-archive"></i> Archive
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        
        <?php if (empty($projects)): ?>
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="bi bi-kanban"></i>
                </div>
                <h3>No Projects Found</h3>
                <p>No projects match your current filters. Try adjusting your search or create a new project.</p>
                <a href="/projects/create" class="btn-action btn-primary">
                    <i class="bi bi-plus-circle"></i>
                    Create Your First Project
                </a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Projects Table (Hidden by Default) -->
    <div class="projects-table" id="projectsTable" style="display: none;">
        <div class="table-card">
            <div class="table-header">
                <h3>Projects List</h3>
                <div class="table-actions">
                    <button class="btn-action btn-secondary" onclick="exportProjects()">
                        <i class="bi bi-download"></i>
                        Export
                    </button>
                </div>
            </div>
            <div class="table-body">
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Code</th>
                            <th>Status</th>
                            <th>Priority</th>
                            <th>Category</th>
                            <th>Client</th>
                            <th>Target End</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($projects as $p): ?>
                            <?php
                            $id = (int) ($p['id'] ?? 0);
                            $status = $p['status'] ?? '';
                            $priority = $p['priority'] ?? '';
                            ?>
                            <tr>
                                <td>
                                    <strong><?= View::e($p['name'] ?? '') ?></strong>
                                </td>
                                <td>
                                    <span class="badge-id"><?= View::e($p['code'] ?? '') ?></span>
                                </td>
                                <td>
                                    <span class="status-badge status-<?= $status ?>">
                                        <?= ucwords(str_replace('_', ' ', $status)) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="priority-badge priority-<?= $priority ?>">
                                        <?= ucfirst($priority) ?>
                                    </span>
                                </td>
                                <td><?= View::e($p['category'] ?? '') ?></td>
                                <td><?= View::e($p['client_name'] ?? '') ?></td>
                                <td>
                                    <div class="date-cell">
                                        <i class="bi bi-calendar"></i>
                                        <?= View::e($p['target_end_date'] ?? '') ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="/projects/show?id=<?= View::e((string)$id) ?>" class="btn-action-sm btn-secondary">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <?php if (in_array($roleKey, ['super_admin', 'director', 'system_admin', 'marketer'], true)): ?>
                                            <a href="/projects/edit?id=<?= View::e((string)$id) ?>" class="btn-action-sm btn-secondary">
                                                <i class="bi bi-pencil"></i>
                                            </a>
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

    <!-- Quick Actions Footer -->
    <div class="quick-actions-footer">
        <div class="action-buttons">
            <a href="/projects/archive" class="btn-action btn-secondary">
                <i class="bi bi-archive"></i>
                View Archive
            </a>
            <a href="/projects/reports" class="btn-action btn-secondary">
                <i class="bi bi-graph-up"></i>
                Generate Reports
            </a>
            <a href="/projects/templates" class="btn-action btn-secondary">
                <i class="bi bi-file-earmark"></i>
                Project Templates
            </a>
        </div>
    </div>
</div>

<style>
    /* Projects Index Styles */
    .zb-projects-index {
        padding: 1.5rem;
    }

    /* Quick Stats */
    .quick-stats {
        margin-bottom: 2rem;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
    }

    .stat-card {
        background: linear-gradient(135deg, rgba(11, 16, 32, 0.8), rgba(5, 8, 22, 0.9));
        border: 1px solid var(--color-border-light);
        border-radius: 12px;
        padding: 1.25rem;
        transition: all 0.3s ease;
        text-align: center;
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
    }

    .stat-value {
        font-size: 2rem;
        font-weight: 700;
        color: var(--color-text);
        margin-bottom: 0.25rem;
    }

    .stat-card .text-success {
        color: var(--color-accent-green);
    }

    .stat-card .text-warning {
        color: var(--color-accent);
    }

    .stat-card .text-danger {
        color: var(--color-accent-red);
    }

    .stat-label {
        font-size: 0.85rem;
        color: var(--color-text-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.5rem;
    }

    .stat-trend {
        font-size: 0.8rem;
    }

    .trend-up {
        color: var(--color-accent-green);
    }

    /* Filter Section */
    .filter-section {
        margin-bottom: 2rem;
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .search-box {
        position: relative;
        max-width: 600px;
    }

    .search-input {
        width: 100%;
        background: rgba(11, 16, 32, 0.8);
        border: 1px solid var(--color-border);
        border-radius: 10px;
        padding: 0.75rem 1rem 0.75rem 2.5rem;
        color: var(--color-text);
        font-size: 0.9rem;
        transition: all 0.3s ease;
    }

    .search-input:focus {
        outline: none;
        border-color: var(--color-accent-blue);
        box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.15);
        background: rgba(11, 16, 32, 0.95);
    }

    .search-icon {
        position: absolute;
        left: 0.75rem;
        top: 50%;
        transform: translateY(-50%);
        color: var(--color-text-muted);
        font-size: 1rem;
    }

    .filter-buttons {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .filter-group {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .filter-btn {
        padding: 0.5rem 1rem;
        background: rgba(11, 16, 32, 0.6);
        border: 1px solid var(--color-border);
        color: var(--color-text-muted);
        border-radius: 8px;
        font-size: 0.85rem;
        font-weight: 500;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .filter-btn:hover {
        border-color: var(--color-accent-blue);
        color: var(--color-accent-blue);
    }

    .filter-btn.active {
        background: linear-gradient(135deg, var(--color-accent-blue), var(--color-accent-purple));
        color: white;
        border-color: transparent;
    }

    /* Projects Grid */
    .projects-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .project-card {
        background: linear-gradient(135deg, rgba(11, 16, 32, 0.8), rgba(5, 8, 22, 0.9));
        border: 1px solid var(--color-border-light);
        border-radius: 12px;
        overflow: hidden;
        transition: all 0.3s ease;
        display: flex;
        flex-direction: column;
    }

    .project-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.4);
        border-color: var(--color-accent-blue);
    }

    .card-header {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid var(--color-border);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .project-status,
    .project-priority {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.8rem;
        font-weight: 600;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
    }

    /* Status Colors */
    .status-ongoing {
        background: rgba(56, 189, 248, 0.15);
        color: var(--color-accent-blue);
        border: 1px solid rgba(56, 189, 248, 0.3);
    }

    .status-planning {
        background: rgba(168, 85, 247, 0.15);
        color: var(--color-accent-purple);
        border: 1px solid rgba(168, 85, 247, 0.3);
    }

    .status-on-hold {
        background: rgba(251, 191, 36, 0.15);
        color: var(--color-accent);
        border: 1px solid rgba(251, 191, 36, 0.3);
    }

    .status-completed {
        background: rgba(52, 199, 89, 0.15);
        color: var(--color-accent-green);
        border: 1px solid rgba(52, 199, 89, 0.3);
    }

    .status-cancelled {
        background: rgba(239, 68, 68, 0.15);
        color: var(--color-accent-red);
        border: 1px solid rgba(239, 68, 68, 0.3);
    }

    .status-draft {
        background: rgba(148, 163, 253, 0.15);
        color: var(--color-border-accent);
        border: 1px solid rgba(148, 163, 253, 0.3);
    }

    /* Priority Colors */
    .priority-critical {
        background: rgba(239, 68, 68, 0.15);
        color: var(--color-accent-red);
        border: 1px solid rgba(239, 68, 68, 0.3);
    }

    .priority-high {
        background: rgba(249, 115, 22, 0.15);
        color: #f97316;
        border: 1px solid rgba(249, 115, 22, 0.3);
    }

    .priority-medium {
        background: rgba(251, 191, 36, 0.15);
        color: var(--color-accent);
        border: 1px solid rgba(251, 191, 36, 0.3);
    }

    .priority-low {
        background: rgba(148, 163, 253, 0.15);
        color: var(--color-border-accent);
        border: 1px solid rgba(148, 163, 253, 0.3);
    }

    .card-body {
        padding: 1.25rem;
        flex: 1;
    }

    .project-meta {
        display: flex;
        gap: 0.5rem;
        margin-bottom: 0.75rem;
        flex-wrap: wrap;
    }

    .project-code {
        background: rgba(148, 163, 253, 0.1);
        color: var(--color-text);
        padding: 0.15rem 0.5rem;
        border-radius: 4px;
        font-size: 0.8rem;
        font-family: monospace;
    }

    .project-category {
        background: rgba(56, 189, 248, 0.1);
        color: var(--color-accent-blue);
        padding: 0.15rem 0.5rem;
        border-radius: 4px;
        font-size: 0.8rem;
    }

    .project-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--color-text);
        margin: 0 0 0.75rem;
    }

    .project-title a {
        color: inherit;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .project-title a:hover {
        color: var(--color-accent-blue);
    }

    .project-client {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: var(--color-text-muted);
        font-size: 0.85rem;
        margin-bottom: 0.75rem;
    }

    .project-description {
        color: var(--color-text);
        font-size: 0.9rem;
        line-height: 1.5;
        margin: 0 0 1rem;
        opacity: 0.9;
    }

    .project-timeline {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: var(--color-text-muted);
        font-size: 0.85rem;
        padding: 0.5rem;
        background: rgba(11, 16, 32, 0.5);
        border-radius: 6px;
        border-left: 3px solid var(--color-accent-blue);
    }

    .timeline-status {
        margin-left: auto;
        font-size: 0.8rem;
    }

    .timeline-status.overdue {
        color: var(--color-accent-red);
    }

    .card-footer {
        padding: 1rem 1.25rem;
        border-top: 1px solid var(--color-border);
    }

    .project-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .btn-action-sm {
        padding: 0.375rem 0.75rem;
        border-radius: 6px;
        font-size: 0.85rem;
        min-width: 32px;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
    }

    .dropdown-menu {
        background: var(--color-surface);
        border: 1px solid var(--color-border);
        border-radius: 8px;
        padding: 0.5rem;
        min-width: 200px;
    }

    .dropdown-item {
        color: var(--color-text);
        padding: 0.5rem 0.75rem;
        border-radius: 6px;
        transition: all 0.3s ease;
    }

    .dropdown-item:hover {
        background: rgba(56, 189, 248, 0.1);
        color: var(--color-accent-blue);
    }

    .dropdown-item.text-danger:hover {
        background: rgba(239, 68, 68, 0.1);
        color: var(--color-accent-red);
    }

    /* Projects Table */
    .projects-table {
        margin-bottom: 2rem;
    }

    .table-card {
        background: linear-gradient(135deg, rgba(11, 16, 32, 0.8), rgba(5, 8, 22, 0.9));
        border: 1px solid var(--color-border-light);
        border-radius: 12px;
        overflow: hidden;
    }

    .table-header {
        padding: 1.25rem;
        border-bottom: 1px solid var(--color-border);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .table-header h3 {
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--color-text);
        margin: 0;
    }

    .table-body {
        padding: 0;
    }

    table {
        width: 100%;
        color: var(--color-text);
        border-collapse: collapse;
    }

    thead {
        background: rgba(11, 16, 32, 0.5);
    }

    th {
        padding: 0.75rem 1rem;
        text-align: left;
        font-weight: 600;
        color: var(--color-text-muted);
        text-transform: uppercase;
        font-size: 0.8rem;
        letter-spacing: 0.5px;
        border-bottom: 2px solid var(--color-border);
    }

    td {
        padding: 1rem;
        border-bottom: 1px solid var(--color-border);
        vertical-align: middle;
    }

    tbody tr:hover {
        background: rgba(56, 189, 248, 0.05);
    }

    .badge-id {
        background: rgba(148, 163, 253, 0.1);
        color: var(--color-text);
        padding: 0.25rem 0.5rem;
        border-radius: 6px;
        font-family: monospace;
        font-weight: 600;
    }

    .status-badge {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .priority-badge {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .date-cell {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.85rem;
    }

    .action-buttons {
        display: flex;
        gap: 0.5rem;
    }

    /* Empty State */
    .empty-state {
        grid-column: 1 / -1;
        text-align: center;
        padding: 3rem 2rem;
        background: linear-gradient(135deg, rgba(11, 16, 32, 0.8), rgba(5, 8, 22, 0.9));
        border: 1px solid var(--color-border-light);
        border-radius: 16px;
        margin-top: 2rem;
    }

    .empty-icon {
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, rgba(56, 189, 248, 0.15), rgba(168, 85, 247, 0.15));
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
        color: var(--color-accent-blue);
        font-size: 2rem;
    }

    .empty-state h3 {
        font-size: 1.5rem;
        font-weight: 600;
        color: var(--color-text);
        margin-bottom: 0.5rem;
    }

    .empty-state p {
        color: var(--color-text-muted);
        margin-bottom: 1.5rem;
        max-width: 400px;
        margin-left: auto;
        margin-right: auto;
    }

    /* Quick Actions Footer */
    .quick-actions-footer {
        margin-top: 2rem;
        padding-top: 1.5rem;
        border-top: 1px solid var(--color-border);
    }

    .quick-actions-footer .action-buttons {
        display: flex;
        gap: 1rem;
        justify-content: center;
        flex-wrap: wrap;
    }

    /* Responsive Design */
    @media (max-width: 1200px) {
        .projects-grid {
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        }
    }

    @media (max-width: 992px) {
        .projects-grid {
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        }
        
        .filter-section {
            flex-direction: column;
        }
    }

    @media (max-width: 768px) {
        .zb-projects-index {
            padding: 1rem;
        }
        
        .projects-grid {
            grid-template-columns: 1fr;
        }
        
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        
        .table {
            display: block;
            overflow-x: auto;
        }
        
        .project-actions {
            flex-wrap: wrap;
            gap: 0.5rem;
        }
    }

    @media (max-width: 576px) {
        .stats-grid {
            grid-template-columns: 1fr;
        }
        
        .filter-group {
            justify-content: center;
        }
        
        .quick-actions-footer .action-buttons {
            flex-direction: column;
        }
    }
</style>

<script>
    // View mode toggle
    let isGridView = true;
    
    function toggleViewMode() {
        const grid = document.getElementById('projectsGrid');
        const table = document.getElementById('projectsTable');
        const button = event.target.closest('.btn-action');
        const text = document.getElementById('viewModeText');
        
        if (isGridView) {
            grid.style.display = 'none';
            table.style.display = 'block';
            text.textContent = 'Table View';
            isGridView = false;
        } else {
            grid.style.display = 'grid';
            table.style.display = 'none';
            text.textContent = 'Grid View';
            isGridView = true;
        }
        
        // Update button icon
        const icon = button.querySelector('i');
        icon.className = isGridView ? 'bi bi-grid' : 'bi bi-table';
    }
    
    // Filter functions
    function setStatusFilter(status) {
        // Update URL
        const url = new URL(window.location);
        if (status) {
            url.searchParams.set('status', status);
        } else {
            url.searchParams.delete('status');
        }
        window.history.pushState({}, '', url);
        
        // Update active button
        document.querySelectorAll('.filter-btn').forEach(btn => {
            if (btn.textContent.includes('Status')) {
                btn.classList.remove('active');
            }
        });
        event.target.classList.add('active');
        
        // Apply filter
        filterProjects();
    }
    
    function setPriorityFilter(priority) {
        // Update URL
        const url = new URL(window.location);
        if (priority) {
            url.searchParams.set('priority', priority);
        } else {
            url.searchParams.delete('priority');
        }
        window.history.pushState({}, '', url);
        
        // Update active button
        document.querySelectorAll('.filter-btn').forEach(btn => {
            if (btn.textContent.includes('Priority')) {
                btn.classList.remove('active');
            }
        });
        event.target.classList.add('active');
        
        // Apply filter
        filterProjects();
    }
    
    function filterProjects() {
        const search = document.querySelector('.search-input').value.toLowerCase();
        const statusFilter = new URL(window.location).searchParams.get('status') || '';
        const priorityFilter = new URL(window.location).searchParams.get('priority') || '';
        
        const projects = document.querySelectorAll('.project-card');
        let visibleCount = 0;
        
        projects.forEach(project => {
            const name = project.querySelector('.project-title').textContent.toLowerCase();
            const description = project.querySelector('.project-description')?.textContent.toLowerCase() || '';
            const client = project.querySelector('.project-client')?.textContent.toLowerCase() || '';
            const status = project.dataset.status;
            const priority = project.dataset.priority;
            
            // Check search
            const matchesSearch = !search || 
                name.includes(search) || 
                description.includes(search) || 
                client.includes(search);
            
            // Check status filter
            const matchesStatus = !statusFilter || status === statusFilter;
            
            // Check priority filter
            const matchesPriority = !priorityFilter || priority === priorityFilter;
            
            if (matchesSearch && matchesStatus && matchesPriority) {
                project.style.display = 'flex';
                setTimeout(() => {
                    project.style.opacity = '1';
                    project.style.transform = 'scale(1)';
                }, 10);
                visibleCount++;
            } else {
                project.style.opacity = '0';
                project.style.transform = 'scale(0.9)';
                setTimeout(() => {
                    project.style.display = 'none';
                }, 300);
            }
        });
        
        // Update table rows
        const tableRows = document.querySelectorAll('tbody tr');
        tableRows.forEach(row => {
            const name = row.querySelector('td:first-child strong').textContent.toLowerCase();
            const status = row.querySelector('.status-badge').textContent.toLowerCase().replace(' ', '_');
            const priority = row.querySelector('.priority-badge').textContent.toLowerCase();
            
            const matchesSearch = !search || name.includes(search);
            const matchesStatus = !statusFilter || status.includes(statusFilter);
            const matchesPriority = !priorityFilter || priority === priorityFilter;
            
            if (matchesSearch && matchesStatus && matchesPriority) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
        
        // Show empty state if no projects
        const emptyState = document.querySelector('.empty-state');
        if (visibleCount === 0 && projects.length > 0) {
            if (!emptyState) {
                createEmptyState();
            }
        } else if (emptyState && visibleCount > 0) {
            emptyState.remove();
        }
    }
    
    function createEmptyState() {
        const grid = document.getElementById('projectsGrid');
        const empty = document.createElement('div');
        empty.className = 'empty-state';
        empty.innerHTML = `
            <div class="empty-icon">
                <i class="bi bi-search"></i>
            </div>
            <h3>No Projects Match Your Filters</h3>
            <p>Try adjusting your search criteria or clear filters to see all projects.</p>
            <button class="btn-action btn-secondary" onclick="clearFilters()">
                Clear All Filters
            </button>
        `;
        grid.appendChild(empty);
    }
    
    function clearFilters() {
        // Clear URL parameters
        const url = new URL(window.location);
        url.searchParams.delete('status');
        url.searchParams.delete('priority');
        url.searchParams.delete('search');
        window.history.pushState({}, '', url);
        
        // Clear search input
        document.querySelector('.search-input').value = '';
        
        // Reset filter buttons
        document.querySelectorAll('.filter-btn').forEach(btn => {
            if (btn.textContent.includes('All')) {
                btn.classList.add('active');
            } else {
                btn.classList.remove('active');
            }
        });
        
        // Show all projects
        filterProjects();
    }
    
    // Export projects
    function exportProjects() {
        console.log('Exporting projects...');
        
        const btn = event.target.closest('.btn-action');
        const originalHTML = btn.innerHTML;
        btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Exporting...';
        btn.disabled = true;
        
        setTimeout(() => {
            // Create CSV data
            const data = 'Name,Code,Status,Priority,Category,Client,Target End\n' +
                <?php 
                $exportData = [];
                foreach ($projects as $p) {
                    $exportData[] = [
                        $p['name'] ?? '',
                        $p['code'] ?? '',
                        $p['status'] ?? '',
                        $p['priority'] ?? '',
                        $p['category'] ?? '',
                        $p['client_name'] ?? '',
                        $p['target_end_date'] ?? ''
                    ];
                }
                echo json_encode($exportData);
                ?>.map(row => row.map(cell => `"${cell}"`).join(',')).join('\n');
            
            const blob = new Blob([data], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `projects-<?= date('Y-m-d') ?>.csv`;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            window.URL.revokeObjectURL(url);
            
            btn.innerHTML = originalHTML;
            btn.disabled = false;
            
            showToast('Projects exported successfully', 'success');
        }, 1000);
    }
    
    // Archive project
    function archiveProject(id) {
        if (confirm('Are you sure you want to archive this project? It will be moved to the archive section.')) {
            // In real app, this would make an API call
            console.log('Archiving project:', id);
            
            // Remove project from view
            const project = document.querySelector(`.project-card[data-id="${id}"]`);
            if (project) {
                project.style.opacity = '0';
                project.style.transform = 'translateX(100px)';
                setTimeout(() => {
                    project.remove();
                    showToast('Project archived successfully', 'success');
                }, 300);
            }
        }
    }
    
    // Toast notifications
    function showToast(message, type) {
        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        toast.innerHTML = `
            <div class="toast-content">
                <i class="bi bi-check-circle-fill"></i>
                <span>${message}</span>
            </div>
            <button class="toast-close" onclick="this.parentElement.remove()">
                <i class="bi bi-x"></i>
            </button>
        `;
        
        const container = document.querySelector('.toast-container') || createToastContainer();
        container.appendChild(toast);
        
        setTimeout(() => {
            if (toast.parentElement) {
                toast.remove();
            }
        }, 3000);
    }
    
    function createToastContainer() {
        const container = document.createElement('div');
        container.className = 'toast-container';
        document.body.appendChild(container);
        return container;
    }
    
    // Initialize
    document.addEventListener('DOMContentLoaded', () => {
        // Set initial filter states
        const url = new URL(window.location);
        const status = url.searchParams.get('status');
        const priority = url.searchParams.get('priority');
        
        if (status) {
            document.querySelectorAll('.filter-btn').forEach(btn => {
                if (btn.textContent.toLowerCase().includes(status)) {
                    btn.classList.add('active');
                } else if (btn.textContent.includes('All Status')) {
                    btn.classList.remove('active');
                }
            });
        }
        
        if (priority) {
            document.querySelectorAll('.filter-btn').forEach(btn => {
                if (btn.textContent.toLowerCase().includes(priority)) {
                    btn.classList.add('active');
                } else if (btn.textContent.includes('All Priority')) {
                    btn.classList.remove('active');
                }
            });
        }
        
        // Add data-id attributes for archiving
        document.querySelectorAll('.project-card').forEach((card, index) => {
            card.setAttribute('data-id', index + 1);
        });
    });
</script>