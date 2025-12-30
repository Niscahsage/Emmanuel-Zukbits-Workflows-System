<?php
// app/views/projects/show.php
// show.php displays full project details including milestones, documentation, and progress logs.

/** @var array $project */
/** @var array $logs */
/** @var array $documents */

use App\core\View;
use App\core\Auth;

$user    = Auth::user();
$roleKey = $user['role_key'] ?? '';

$id          = (int) ($project['id'] ?? 0);
$name        = $project['name'] ?? '';
$code        = $project['code'] ?? '';
$status      = $project['status'] ?? '';
$priority    = $project['priority'] ?? '';
$category    = $project['category'] ?? '';
$client      = $project['client_name'] ?? '';
$description = $project['description'] ?? '';
$objectives  = $project['objectives'] ?? '';
$startDate   = $project['start_date'] ?? '';
$targetEnd   = $project['target_end_date'] ?? '';
$createdAt   = $project['created_at'] ?? '';
$updatedAt   = $project['updated_at'] ?? '';

// Calculate project duration
$duration = '';
if ($startDate && $targetEnd) {
    $start = new DateTime($startDate);
    $end = new DateTime($targetEnd);
    $interval = $start->diff($end);
    $duration = $interval->format('%a days');
}

// Calculate days remaining/overdue
$daysStatus = '';
if ($targetEnd) {
    $target = new DateTime($targetEnd);
    $today = new DateTime();
    $interval = $today->diff($target);
    $days = $interval->days;
    
    if ($interval->invert) {
        $daysStatus = "Overdue by $days days";
    } else {
        $daysStatus = "$days days remaining";
    }
}
?>
<div class="zb-project-show">
    <!-- Page Header -->
    <div class="page-header">
        <div class="header-content">
            <?php require __DIR__ . '/../partials/breadcrumbs.php'; ?>
            <div class="title-section">
                <h1 class="page-title"><?= View::e($name) ?></h1>
                <p class="page-subtitle">
                    Project #<?= View::e((string)$id) ?>
                    <?php if ($code): ?> • <?= View::e($code) ?><?php endif; ?>
                </p>
            </div>
        </div>
        <div class="header-actions">
            <div class="action-buttons">
                <?php if (in_array($roleKey, ['super_admin', 'director', 'system_admin', 'marketer'], true)): ?>
                    <a href="/projects/edit?id=<?= View::e((string)$id) ?>" class="btn-action btn-secondary">
                        <i class="bi bi-pencil"></i>
                        Edit Project
                    </a>
                <?php endif; ?>
                <?php if (in_array($roleKey, ['super_admin', 'director', 'system_admin'], true)): ?>
                    <form action="/approvals/request-project" method="post" style="display: inline;">
                        <input type="hidden" name="project_id" value="<?= View::e((string)$id) ?>">
                        <button type="submit" class="btn-action btn-primary">
                            <i class="bi bi-check-circle"></i>
                            Request Completion
                        </button>
                    </form>
                <?php endif; ?>
                <div class="dropdown">
                    <button class="btn-action btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-three-dots"></i>
                        More Actions
                    </button>
                    <div class="dropdown-menu dropdown-menu-end">
                        <a class="dropdown-item" href="/projects/duplicate?id=<?= View::e((string)$id) ?>">
                            <i class="bi bi-files"></i> Duplicate Project
                        </a>
                        <a class="dropdown-item" href="/projects/export?id=<?= View::e((string)$id) ?>">
                            <i class="bi bi-download"></i> Export Project
                        </a>
                        <a class="dropdown-item" href="/documentation/create?project_id=<?= View::e((string)$id) ?>">
                            <i class="bi bi-file-earmark-plus"></i> Add Documentation
                        </a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="/projects/approvals?id=<?= View::e((string)$id) ?>">
                            <i class="bi bi-clipboard-check"></i> View Approvals
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Project Stats -->
    <div class="project-stats">
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-value"><?= ucfirst($status) ?></div>
                <div class="stat-label">Current Status</div>
                <div class="stat-trend">
                    <span class="trend-<?= $status === 'completed' ? 'up' : ($status === 'cancelled' ? 'down' : 'neutral') ?>">
                        <?= $status === 'completed' ? 'Completed' : ($status === 'cancelled' ? 'Cancelled' : 'Active') ?>
                    </span>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-value"><?= ucfirst($priority) ?></div>
                <div class="stat-label">Priority Level</div>
                <div class="stat-trend">
                    <span class="trend-<?= $priority === 'critical' ? 'up' : ($priority === 'low' ? 'down' : 'neutral') ?>">
                        <?= $priority === 'critical' ? 'High Priority' : ($priority === 'low' ? 'Low Priority' : 'Normal') ?>
                    </span>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-value"><?= $duration ?: 'N/A' ?></div>
                <div class="stat-label">Project Duration</div>
                <div class="stat-trend">
                    <span class="trend-neutral">Planned timeline</span>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-value"><?= $daysStatus ?: 'No deadline' ?></div>
                <div class="stat-label">Timeline Status</div>
                <div class="stat-trend">
                    <span class="trend-<?= strpos($daysStatus, 'Overdue') !== false ? 'down' : 'neutral' ?>">
                        <?= strpos($daysStatus, 'Overdue') !== false ? 'Behind schedule' : 'On track' ?>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="project-content">
        <div class="content-grid">
            <!-- Left Column -->
            <div class="left-column">
                <!-- Project Overview -->
                <div class="info-card">
                    <div class="card-header">
                        <h3><i class="bi bi-info-circle"></i> Project Overview</h3>
                    </div>
                    <div class="card-body">
                        <div class="info-grid">
                            <div class="info-item">
                                <span class="info-label">Project ID</span>
                                <span class="info-value">#<?= $id ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Status</span>
                                <span class="info-value status-<?= $status ?>">
                                    <?= ucwords(str_replace('_', ' ', $status)) ?>
                                </span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Priority</span>
                                <span class="info-value priority-<?= $priority ?>">
                                    <?= ucfirst($priority) ?>
                                </span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Category</span>
                                <span class="info-value"><?= $category ?: 'N/A' ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Client</span>
                                <span class="info-value"><?= $client ?: 'Internal' ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Start Date</span>
                                <span class="info-value"><?= $startDate ? date('M j, Y', strtotime($startDate)) : 'Not set' ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Target End</span>
                                <span class="info-value"><?= $targetEnd ? date('M j, Y', strtotime($targetEnd)) : 'Not set' ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Created</span>
                                <span class="info-value"><?= $createdAt ? date('M j, Y', strtotime($createdAt)) : 'N/A' ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Last Updated</span>
                                <span class="info-value"><?= $updatedAt ? date('M j, Y', strtotime($updatedAt)) : 'N/A' ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Project Description -->
                <div class="info-card">
                    <div class="card-header">
                        <h3><i class="bi bi-file-text"></i> Description</h3>
                    </div>
                    <div class="card-body">
                        <?php if ($description): ?>
                            <div class="description-content">
                                <?= nl2br(View::e($description)) ?>
                            </div>
                        <?php else: ?>
                            <div class="empty-state-sm">
                                <i class="bi bi-file-earmark"></i>
                                <p>No description provided yet.</p>
                                <?php if (in_array($roleKey, ['super_admin', 'director', 'system_admin', 'marketer'], true)): ?>
                                    <a href="/projects/edit?id=<?= View::e((string)$id) ?>" class="btn-action btn-secondary btn-sm">
                                        Add Description
                                    </a>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div class="right-column">
                <!-- Project Objectives -->
                <div class="info-card">
                    <div class="card-header">
                        <h3><i class="bi bi-bullseye"></i> Objectives & Success Metrics</h3>
                    </div>
                    <div class="card-body">
                        <?php if ($objectives): ?>
                            <div class="objectives-content">
                                <?= nl2br(View::e($objectives)) ?>
                            </div>
                        <?php else: ?>
                            <div class="empty-state-sm">
                                <i class="bi bi-bullseye"></i>
                                <p>No objectives recorded yet.</p>
                                <?php if (in_array($roleKey, ['super_admin', 'director', 'system_admin', 'marketer'], true)): ?>
                                    <a href="/projects/edit?id=<?= View::e((string)$id) ?>" class="btn-action btn-secondary btn-sm">
                                        Add Objectives
                                    </a>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Progress Log -->
                <div class="info-card">
                    <div class="card-header">
                        <h3><i class="bi bi-clock-history"></i> Progress Log</h3>
                        <button class="btn-action btn-secondary btn-sm" onclick="showAddLogModal()">
                            <i class="bi bi-plus"></i>
                            Add Entry
                        </button>
                    </div>
                    <div class="card-body">
                        <?php if (empty($logs)): ?>
                            <div class="empty-state-sm">
                                <i class="bi bi-journal"></i>
                                <p>No progress entries yet.</p>
                                <button class="btn-action btn-secondary btn-sm" onclick="showAddLogModal()">
                                    Add First Entry
                                </button>
                            </div>
                        <?php else: ?>
                            <div class="timeline">
                                <?php foreach ($logs as $log): ?>
                                    <?php
                                    $logStatus = $log['status'] ?? '';
                                    $logNote = $log['note'] ?? '';
                                    $logCreatedAt = $log['created_at'] ?? '';
                                    $logCreatedBy = $log['created_by_name'] ?? '';
                                    $logTime = $logCreatedAt ? date('g:i A', strtotime($logCreatedAt)) : '';
                                    $logDate = $logCreatedAt ? date('M j', strtotime($logCreatedAt)) : '';
                                    ?>
                                    <div class="timeline-item">
                                        <div class="timeline-marker">
                                            <i class="bi bi-circle-fill"></i>
                                        </div>
                                        <div class="timeline-content">
                                            <div class="timeline-header">
                                                <strong class="status-<?= strtolower($logStatus) ?>">
                                                    <?= ucfirst($logStatus) ?>
                                                </strong>
                                                <span class="timeline-time"><?= $logTime ?></span>
                                            </div>
                                            <?php if ($logNote): ?>
                                                <div class="timeline-body">
                                                    <?= nl2br(View::e($logNote)) ?>
                                                </div>
                                            <?php endif; ?>
                                            <div class="timeline-footer">
                                                <span class="timeline-date"><?= $logDate ?></span>
                                                <?php if ($logCreatedBy): ?>
                                                    <span class="timeline-author">• By <?= View::e($logCreatedBy) ?></span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                        
                        <div class="card-footer">
                            <a href="/projects/logs?id=<?= View::e((string)$id) ?>" class="btn-action btn-secondary btn-sm">
                                View All Entries
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Documentation -->
                <div class="info-card">
                    <div class="card-header">
                        <h3><i class="bi bi-file-earmark"></i> Documentation</h3>
                        <a href="/documentation/create?project_id=<?= View::e((string)$id) ?>" class="btn-action btn-secondary btn-sm">
                            <i class="bi bi-plus"></i>
                            Add Document
                        </a>
                    </div>
                    <div class="card-body">
                        <?php if (empty($documents)): ?>
                            <div class="empty-state-sm">
                                <i class="bi bi-folder"></i>
                                <p>No documentation linked yet.</p>
                                <a href="/documentation/create?project_id=<?= View::e((string)$id) ?>" class="btn-action btn-secondary btn-sm">
                                    Add First Document
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="documents-list">
                                <?php foreach ($documents as $doc): ?>
                                    <?php $docId = (int) ($doc['id'] ?? 0); ?>
                                    <div class="document-item">
                                        <div class="document-icon">
                                            <i class="bi bi-file-earmark-text"></i>
                                        </div>
                                        <div class="document-content">
                                            <a href="/documentation/show?id=<?= View::e((string)$docId) ?>" class="document-title">
                                                <?= View::e($doc['title'] ?? 'Untitled') ?>
                                            </a>
                                            <div class="document-meta">
                                                <?php if (!empty($doc['type'])): ?>
                                                    <span class="document-type"><?= View::e($doc['type']) ?></span>
                                                <?php endif; ?>
                                                <span class="document-date">
                                                    <?= $doc['created_at'] ? date('M j, Y', strtotime($doc['created_at'])) : '' ?>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="document-actions">
                                            <a href="/documentation/show?id=<?= View::e((string)$docId) ?>" class="btn-action-sm">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                        
                        <div class="card-footer">
                            <a href="/documentation?project_id=<?= View::e((string)$id) ?>" class="btn-action btn-secondary btn-sm">
                                View All Documentation
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats Footer -->
    <div class="quick-stats-footer">
        <div class="stats-summary">
            <div class="stat-item">
                <i class="bi bi-journal-text"></i>
                <div>
                    <div class="stat-value"><?= count($logs) ?></div>
                    <div class="stat-label">Progress Entries</div>
                </div>
            </div>
            <div class="stat-item">
                <i class="bi bi-file-earmark"></i>
                <div>
                    <div class="stat-value"><?= count($documents) ?></div>
                    <div class="stat-label">Documents</div>
                </div>
            </div>
            <div class="stat-item">
                <i class="bi bi-clock"></i>
                <div>
                    <div class="stat-value"><?= $duration ?: 'N/A' ?></div>
                    <div class="stat-label">Total Duration</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Log Modal -->
<div class="modal-overlay" id="addLogModal" style="display: none;">
    <div class="modal">
        <div class="modal-header">
            <h3>Add Progress Entry</h3>
            <button class="modal-close" onclick="hideAddLogModal()">
                <i class="bi bi-x"></i>
            </button>
        </div>
        <div class="modal-body">
            <form id="addLogForm">
                <input type="hidden" name="project_id" value="<?= View::e((string)$id) ?>">
                
                <div class="form-group">
                    <label class="form-label">Status Update</label>
                    <select name="status" class="form-select" required>
                        <option value="">Select Status</option>
                        <option value="planning">Planning</option>
                        <option value="ongoing">Ongoing</option>
                        <option value="review">Under Review</option>
                        <option value="completed">Completed</option>
                        <option value="on_hold">On Hold</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Notes</label>
                    <textarea name="note" 
                              rows="4" 
                              class="form-control"
                              placeholder="Describe the progress or update..."
                              required></textarea>
                </div>
                
                <div class="form-group">
                    <label class="form-label">
                        <input type="checkbox" name="notify_team" checked>
                        Notify Team Members
                    </label>
                    <div class="form-hint">Send email notifications about this update</div>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn-action btn-secondary" onclick="hideAddLogModal()">
                Cancel
            </button>
            <button type="submit" form="addLogForm" class="btn-action btn-primary">
                <i class="bi bi-check-circle"></i>
                Add Entry
            </button>
        </div>
    </div>
</div>

<style>
    /* Project Show Styles */
    .zb-project-show {
        padding: 1.5rem;
    }

    /* Stats Grid */
    .project-stats {
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

    .trend-down {
        color: var(--color-accent-red);
    }

    .trend-neutral {
        color: var(--color-text-muted);
    }

    /* Content Grid */
    .project-content {
        margin-bottom: 2rem;
    }

    .content-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 2rem;
    }

    @media (max-width: 1200px) {
        .content-grid {
            grid-template-columns: 1fr;
        }
    }

    /* Info Cards */
    .info-card {
        background: linear-gradient(135deg, rgba(11, 16, 32, 0.8), rgba(5, 8, 22, 0.9));
        border: 1px solid var(--color-border-light);
        border-radius: 12px;
        overflow: hidden;
        margin-bottom: 1.5rem;
    }

    .info-card:last-child {
        margin-bottom: 0;
    }

    .card-header {
        padding: 1.25rem;
        border-bottom: 1px solid var(--color-border);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .card-header h3 {
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--color-text);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .card-header h3 i {
        color: var(--color-accent-blue);
    }

    .card-body {
        padding: 1.5rem;
    }

    .card-footer {
        padding: 1rem 1.5rem;
        border-top: 1px solid var(--color-border);
        text-align: center;
    }

    /* Info Grid */
    .info-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
    }

    .info-item {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }

    .info-label {
        font-size: 0.8rem;
        color: var(--color-text-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .info-value {
        font-size: 0.9rem;
        color: var(--color-text);
        font-weight: 500;
    }

    /* Status and Priority Badges */
    .status-draft,
    .status-planning,
    .status-ongoing,
    .status-review,
    .status-completed,
    .status-on_hold,
    .status-cancelled,
    .priority-low,
    .priority-medium,
    .priority-high,
    .priority-critical {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .status-draft {
        background: rgba(148, 163, 253, 0.15);
        color: var(--color-border-accent);
        border: 1px solid rgba(148, 163, 253, 0.3);
    }

    .status-planning {
        background: rgba(168, 85, 247, 0.15);
        color: var(--color-accent-purple);
        border: 1px solid rgba(168, 85, 247, 0.3);
    }

    .status-ongoing {
        background: rgba(56, 189, 248, 0.15);
        color: var(--color-accent-blue);
        border: 1px solid rgba(56, 189, 248, 0.3);
    }

    .status-review {
        background: rgba(251, 191, 36, 0.15);
        color: var(--color-accent);
        border: 1px solid rgba(251, 191, 36, 0.3);
    }

    .status-completed {
        background: rgba(52, 199, 89, 0.15);
        color: var(--color-accent-green);
        border: 1px solid rgba(52, 199, 89, 0.3);
    }

    .status-on_hold {
        background: rgba(251, 191, 36, 0.15);
        color: var(--color-accent);
        border: 1px solid rgba(251, 191, 36, 0.3);
    }

    .status-cancelled {
        background: rgba(239, 68, 68, 0.15);
        color: var(--color-accent-red);
        border: 1px solid rgba(239, 68, 68, 0.3);
    }

    .priority-low {
        background: rgba(148, 163, 253, 0.15);
        color: var(--color-border-accent);
        border: 1px solid rgba(148, 163, 253, 0.3);
    }

    .priority-medium {
        background: rgba(251, 191, 36, 0.15);
        color: var(--color-accent);
        border: 1px solid rgba(251, 191, 36, 0.3);
    }

    .priority-high {
        background: rgba(249, 115, 22, 0.15);
        color: #f97316;
        border: 1px solid rgba(249, 115, 22, 0.3);
    }

    .priority-critical {
        background: rgba(239, 68, 68, 0.15);
        color: var(--color-accent-red);
        border: 1px solid rgba(239, 68, 68, 0.3);
    }

    /* Description & Objectives */
    .description-content,
    .objectives-content {
        color: var(--color-text);
        line-height: 1.6;
        white-space: pre-wrap;
    }

    /* Empty States */
    .empty-state-sm {
        text-align: center;
        padding: 2rem 1rem;
        color: var(--color-text-muted);
    }

    .empty-state-sm i {
        font-size: 2rem;
        margin-bottom: 1rem;
        opacity: 0.5;
    }

    .empty-state-sm p {
        margin-bottom: 1rem;
    }

    .btn-action.btn-sm {
        padding: 0.375rem 0.75rem;
        font-size: 0.8rem;
    }

    /* Timeline */
    .timeline {
        position: relative;
        padding-left: 2rem;
    }

    .timeline::before {
        content: '';
        position: absolute;
        left: 0.75rem;
        top: 0;
        bottom: 0;
        width: 2px;
        background: var(--color-border);
    }

    .timeline-item {
        position: relative;
        margin-bottom: 1.5rem;
    }

    .timeline-item:last-child {
        margin-bottom: 0;
    }

    .timeline-marker {
        position: absolute;
        left: -2rem;
        top: 0;
        width: 1.5rem;
        height: 1.5rem;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1;
        background: var(--color-surface);
    }

    .timeline-marker i {
        color: var(--color-accent-blue);
        font-size: 0.75rem;
    }

    .timeline-content {
        background: rgba(11, 16, 32, 0.5);
        border: 1px solid var(--color-border);
        border-radius: 8px;
        padding: 1rem;
        transition: all 0.3s ease;
    }

    .timeline-item:hover .timeline-content {
        border-color: var(--color-accent-blue);
        transform: translateX(5px);
    }

    .timeline-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.5rem;
    }

    .timeline-time {
        font-size: 0.8rem;
        color: var(--color-text-muted);
    }

    .timeline-body {
        color: var(--color-text);
        font-size: 0.9rem;
        line-height: 1.5;
        margin-bottom: 0.5rem;
    }

    .timeline-footer {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.8rem;
        color: var(--color-text-muted);
    }

    /* Documents List */
    .documents-list {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }

    .document-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 0.75rem;
        background: rgba(11, 16, 32, 0.5);
        border: 1px solid var(--color-border);
        border-radius: 8px;
        transition: all 0.3s ease;
    }

    .document-item:hover {
        border-color: var(--color-accent-blue);
        transform: translateX(5px);
    }

    .document-icon {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        background: rgba(56, 189, 248, 0.15);
        color: var(--color-accent-blue);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }

    .document-content {
        flex: 1;
    }

    .document-title {
        font-size: 0.9rem;
        font-weight: 600;
        color: var(--color-text);
        text-decoration: none;
        display: block;
        margin-bottom: 0.25rem;
    }

    .document-title:hover {
        color: var(--color-accent-blue);
    }

    .document-meta {
        display: flex;
        align-items: center;
        gap: 1rem;
        font-size: 0.8rem;
        color: var(--color-text-muted);
    }

    .document-type {
        background: rgba(148, 163, 253, 0.1);
        color: var(--color-border-accent);
        padding: 0.15rem 0.5rem;
        border-radius: 4px;
    }

    .document-actions {
        display: flex;
        gap: 0.5rem;
    }

    .btn-action-sm {
        padding: 0.375rem;
        min-width: 32px;
    }

    /* Quick Stats Footer */
    .quick-stats-footer {
        margin-top: 2rem;
        padding-top: 1.5rem;
        border-top: 1px solid var(--color-border);
    }

    .stats-summary {
        display: flex;
        justify-content: center;
        gap: 3rem;
        flex-wrap: wrap;
    }

    .stat-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        text-align: left;
    }

    .stat-item i {
        font-size: 2rem;
        color: var(--color-accent-blue);
        opacity: 0.8;
    }

    .stat-item .stat-value {
        font-size: 1.5rem;
        margin-bottom: 0.125rem;
    }

    .stat-item .stat-label {
        font-size: 0.8rem;
        margin-bottom: 0;
    }

    /* Modal Styles */
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.75);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        backdrop-filter: blur(5px);
    }

    .modal {
        background: linear-gradient(135deg, rgba(11, 16, 32, 0.95), rgba(5, 8, 22, 0.98));
        border: 1px solid var(--color-border-light);
        border-radius: 12px;
        width: 90%;
        max-width: 500px;
        overflow: hidden;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
    }

    .modal-header {
        padding: 1.5rem;
        border-bottom: 1px solid var(--color-border);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .modal-header h3 {
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--color-text);
        margin: 0;
    }

    .modal-close {
        background: none;
        border: none;
        color: var(--color-text-muted);
        font-size: 1.5rem;
        cursor: pointer;
        padding: 0.25rem;
        border-radius: 4px;
        transition: all 0.3s ease;
    }

    .modal-close:hover {
        background: rgba(239, 68, 68, 0.1);
        color: #ef4444;
    }

    .modal-body {
        padding: 2rem 1.5rem;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-label {
        display: block;
        font-weight: 500;
        color: var(--color-text);
        margin-bottom: 0.5rem;
        font-size: 0.9rem;
    }

    .form-select,
    .form-control {
        width: 100%;
        background: rgba(11, 16, 32, 0.8);
        border: 1px solid var(--color-border);
        border-radius: 8px;
        padding: 0.75rem 1rem;
        color: var(--color-text);
        font-size: 0.9rem;
        transition: all 0.3s ease;
    }

    .form-select:focus,
    .form-control:focus {
        outline: none;
        border-color: var(--color-accent-blue);
        box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.15);
        background: rgba(11, 16, 32, 0.95);
    }

    .form-hint {
        font-size: 0.8rem;
        color: var(--color-text-muted);
        margin-top: 0.5rem;
    }

    .modal-footer {
        padding: 1.5rem;
        border-top: 1px solid var(--color-border);
        display: flex;
        justify-content: flex-end;
        gap: 0.75rem;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .zb-project-show {
            padding: 1rem;
        }
        
        .info-grid {
            grid-template-columns: 1fr;
        }
        
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        
        .stats-summary {
            gap: 1.5rem;
        }
        
        .header-actions .action-buttons {
            flex-direction: column;
            width: 100%;
        }
    }

    @media (max-width: 576px) {
        .stats-grid {
            grid-template-columns: 1fr;
        }
        
        .stat-item {
            flex-direction: column;
            text-align: center;
            gap: 0.5rem;
        }
        
        .modal {
            width: 95%;
        }
    }
</style>

<script>
    // Add Log Modal Functions
    function showAddLogModal() {
        document.getElementById('addLogModal').style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
    
    function hideAddLogModal() {
        document.getElementById('addLogModal').style.display = 'none';
        document.body.style.overflow = 'auto';
    }
    
    // Handle log form submission
    document.getElementById('addLogForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const btn = this.querySelector('button[type="submit"]');
        const originalHTML = btn.innerHTML;
        
        btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Adding...';
        btn.disabled = true;
        
        // Simulate API call
        setTimeout(() => {
            // In real app, this would submit to server
            console.log('Adding log entry:', Object.fromEntries(formData));
            
            // Create new timeline item
            const timeline = document.querySelector('.timeline');
            if (timeline) {
                const newItem = document.createElement('div');
                newItem.className = 'timeline-item';
                newItem.innerHTML = `
                    <div class="timeline-marker">
                        <i class="bi bi-circle-fill"></i>
                    </div>
                    <div class="timeline-content">
                        <div class="timeline-header">
                            <strong class="status-${formData.get('status').toLowerCase()}">
                                ${formData.get('status')}
                            </strong>
                            <span class="timeline-time">${new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}</span>
                        </div>
                        <div class="timeline-body">
                            ${formData.get('note')}
                        </div>
                        <div class="timeline-footer">
                            <span class="timeline-date">${new Date().toLocaleDateString([], { month: 'short', day: 'numeric' })}</span>
                            <span class="timeline-author">• By <?= htmlspecialchars($user['name'] ?? 'You', ENT_QUOTES, 'UTF-8') ?></span>
                        </div>
                    </div>
                `;
                
                // Insert at beginning
                timeline.insertBefore(newItem, timeline.firstChild);
                
                // Update stats
                updateStats();
            }
            
            // Hide modal and reset form
            hideAddLogModal();
            this.reset();
            
            // Reset button
            btn.innerHTML = originalHTML;
            btn.disabled = false;
            
            // Show success message
            showToast('Progress entry added successfully', 'success');
        }, 1000);
    });
    
    function updateStats() {
        // Update progress entries count
        const timelineItems = document.querySelectorAll('.timeline-item');
        const progressStat = document.querySelector('.stat-item:nth-child(1) .stat-value');
        if (progressStat) {
            progressStat.textContent = timelineItems.length;
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
        // Add hover effects to timeline items
        document.querySelectorAll('.timeline-item').forEach(item => {
            item.addEventListener('mouseenter', () => {
                item.querySelector('.timeline-content').style.transform = 'translateX(5px)';
                item.querySelector('.timeline-content').style.borderColor = 'var(--color-accent-blue)';
            });
            
            item.addEventListener('mouseleave', () => {
                item.querySelector('.timeline-content').style.transform = '';
                item.querySelector('.timeline-content').style.borderColor = '';
            });
        });
        
        // Add hover effects to document items
        document.querySelectorAll('.document-item').forEach(item => {
            item.addEventListener('mouseenter', () => {
                item.style.transform = 'translateX(5px)';
                item.style.borderColor = 'var(--color-accent-blue)';
            });
            
            item.addEventListener('mouseleave', () => {
                item.style.transform = '';
                item.style.borderColor = '';
            });
        });
        
        // Update stats on load
        updateStats();
    });
</script>