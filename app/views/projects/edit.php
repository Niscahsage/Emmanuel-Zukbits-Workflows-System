<?php
// app/views/projects/edit.php
// edit.php displays the form to update an existing project.

/** @var array $project */

use App\core\View;
use App\core\Auth;

$user = Auth::user();
$id          = (int) ($project['id'] ?? 0);
$name        = $project['name'] ?? '';
$code        = $project['code'] ?? '';
$client      = $project['client_name'] ?? '';
$description = $project['description'] ?? '';
$objectives  = $project['objectives'] ?? '';
$category    = $project['category'] ?? '';
$priority    = $project['priority'] ?? '';
$status      = $project['status'] ?? 'draft';
$startDate   = $project['start_date'] ?? '';
$targetEnd   = $project['target_end_date'] ?? '';
?>
<div class="zb-project-edit">
    <!-- Page Header -->
    <div class="page-header">
        <div class="header-content">
            <?php require __DIR__ . '/../partials/breadcrumbs.php'; ?>
            <div class="title-section">
                <h1 class="page-title">Edit Project</h1>
                <p class="page-subtitle">
                    Update project details and configuration
                </p>
            </div>
        </div>
        <div class="header-actions">
            <a href="/projects/show?id=<?= View::e((string)$id) ?>" class="btn-action btn-secondary">
                <i class="bi bi-arrow-left"></i>
                Back to Project
            </a>
            <button type="button" class="btn-action btn-danger" onclick="showDeleteModal()">
                <i class="bi bi-trash"></i>
                Delete Project
            </button>
        </div>
    </div>

    <!-- Project Stats -->
    <div class="project-stats">
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-value"><?= strtoupper(substr($name, 0, 2)) ?></div>
                <div class="stat-label">Project ID</div>
                <div class="stat-trend">
                    <span>#<?= $id ?></span>
                </div>
            </div>
            
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
                <div class="stat-value"><?= $category ?: 'N/A' ?></div>
                <div class="stat-label">Category</div>
                <div class="stat-trend">
                    <span class="trend-neutral">Classification</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Form -->
    <div class="form-container">
        <form action="/projects/update" method="post" id="editProjectForm" class="form-card">
            <input type="hidden" name="id" value="<?= View::e((string)$id) ?>">
            
            <!-- Basic Information -->
            <div class="form-section">
                <h3 class="form-section-title">
                    <i class="bi bi-info-circle"></i>
                    Basic Information
                </h3>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">
                            Project Name *
                            <span class="required-indicator">*</span>
                        </label>
                        <input type="text" 
                               name="name" 
                               class="form-control" 
                               required
                               value="<?= View::e($name) ?>"
                               placeholder="Enter project name">
                        <div class="form-hint">A clear, descriptive name for your project</div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Project Code</label>
                        <input type="text" 
                               name="code" 
                               class="form-control" 
                               value="<?= View::e($code) ?>"
                               placeholder="e.g. ZKB-2025-01">
                        <div class="form-hint">Internal reference code for tracking</div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Category</label>
                        <select name="category" class="form-select">
                            <option value="">Select Category</option>
                            <option value="web" <?= $category === 'web' ? 'selected' : '' ?>>Web Development</option>
                            <option value="mobile" <?= $category === 'mobile' ? 'selected' : '' ?>>Mobile App</option>
                            <option value="marketing" <?= $category === 'marketing' ? 'selected' : '' ?>>Marketing Campaign</option>
                            <option value="research" <?= $category === 'research' ? 'selected' : '' ?>>Research Project</option>
                            <option value="internal" <?= $category === 'internal' ? 'selected' : '' ?>>Internal Project</option>
                            <option value="other" <?= $category === 'other' ? 'selected' : '' ?>>Other</option>
                        </select>
                        <div class="form-hint">Primary category for organization</div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Client / Stakeholder</label>
                        <input type="text" 
                               name="client_name" 
                               class="form-control" 
                               value="<?= View::e($client) ?>"
                               placeholder="Client or internal department">
                        <div class="form-hint">Who is this project for?</div>
                    </div>
                </div>
            </div>
            
            <!-- Project Details -->
            <div class="form-section">
                <h3 class="form-section-title">
                    <i class="bi bi-calendar"></i>
                    Project Details
                </h3>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="draft" <?= $status === 'draft' ? 'selected' : '' ?>>Draft</option>
                            <option value="planning" <?= $status === 'planning' ? 'selected' : '' ?>>Planning</option>
                            <option value="ongoing" <?= $status === 'ongoing' ? 'selected' : '' ?>>Ongoing</option>
                            <option value="on_hold" <?= $status === 'on_hold' ? 'selected' : '' ?>>On Hold</option>
                            <option value="completed" <?= $status === 'completed' ? 'selected' : '' ?>>Completed</option>
                            <option value="cancelled" <?= $status === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                        </select>
                        <div class="form-hint">Current project status</div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Priority</label>
                        <select name="priority" class="form-select">
                            <option value="medium" <?= $priority === 'medium' ? 'selected' : '' ?>>Medium</option>
                            <option value="low" <?= $priority === 'low' ? 'selected' : '' ?>>Low</option>
                            <option value="high" <?= $priority === 'high' ? 'selected' : '' ?>>High</option>
                            <option value="critical" <?= $priority === 'critical' ? 'selected' : '' ?>>Critical</option>
                        </select>
                        <div class="form-hint">Project priority level</div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Start Date</label>
                        <input type="date" 
                               name="start_date" 
                               class="form-control"
                               value="<?= View::e($startDate) ?>">
                        <div class="form-hint">When did work begin?</div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Target End Date</label>
                        <input type="date" 
                               name="target_end_date" 
                               class="form-control"
                               value="<?= View::e($targetEnd) ?>">
                        <div class="form-hint">When do you aim to complete?</div>
                    </div>
                </div>
            </div>
            
            <!-- Project Content -->
            <div class="form-section">
                <h3 class="form-section-title">
                    <i class="bi bi-file-text"></i>
                    Project Content
                </h3>
                
                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea name="description" 
                              rows="6" 
                              class="form-control"
                              placeholder="Provide a detailed description of the project..."><?= View::e($description) ?></textarea>
                    <div class="form-hint">Describe the project scope and purpose</div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Objectives & Success Metrics</label>
                    <textarea name="objectives" 
                              rows="6" 
                              class="form-control"
                              placeholder="List key goals and how success will be measured..."><?= View::e($objectives) ?></textarea>
                    <div class="form-hint">What are the key deliverables and success criteria?</div>
                </div>
            </div>
            
            <!-- Advanced Settings -->
            <div class="form-section">
                <h3 class="form-section-title">
                    <i class="bi bi-gear"></i>
                    Advanced Settings
                </h3>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">
                            <input type="checkbox" name="notify_team" checked>
                            Notify Team Members
                        </label>
                        <div class="form-hint">Send email notifications about changes</div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">
                            <input type="checkbox" name="update_timeline" checked>
                            Update Project Timeline
                        </label>
                        <div class="form-hint">Adjust timeline based on status changes</div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">
                            <input type="checkbox" name="archive_old_versions">
                            Archive Previous Version
                        </label>
                        <div class="form-hint">Save previous project state for reference</div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">
                            <input type="checkbox" name="require_approval">
                            Require Approval for Changes
                        </label>
                        <div class="form-hint">Major changes require manager approval</div>
                    </div>
                </div>
            </div>
            
            <!-- Form Actions -->
            <div class="form-actions">
                <div class="action-buttons">
                    <button type="button" class="btn-action btn-secondary" onclick="previewChanges()">
                        <i class="bi bi-eye"></i>
                        Preview Changes
                    </button>
                    <button type="button" class="btn-action btn-secondary" onclick="resetForm()">
                        <i class="bi bi-arrow-counterclockwise"></i>
                        Reset
                    </button>
                </div>
                <div class="submit-buttons">
                    <a href="/projects/show?id=<?= View::e((string)$id) ?>" class="btn-action btn-secondary">
                        Cancel
                    </a>
                    <button type="submit" class="btn-action btn-primary">
                        <i class="bi bi-check-circle"></i>
                        Save Changes
                    </button>
                </div>
            </div>
        </form>
        
        <!-- Change History -->
        <div class="change-history">
            <div class="history-card">
                <h4><i class="bi bi-clock-history"></i> Recent Changes</h4>
                <div class="history-list">
                    <div class="history-item">
                        <div class="history-content">
                            <div class="history-title">Project created</div>
                            <div class="history-meta">By System • 2 days ago</div>
                        </div>
                    </div>
                    <div class="history-item">
                        <div class="history-content">
                            <div class="history-title">Status updated to "Ongoing"</div>
                            <div class="history-meta">By <?= htmlspecialchars($user['name'] ?? 'Admin', ENT_QUOTES, 'UTF-8') ?> • 1 day ago</div>
                        </div>
                    </div>
                    <div class="history-item">
                        <div class="history-content">
                            <div class="history-title">Description updated</div>
                            <div class="history-meta">By <?= htmlspecialchars($user['name'] ?? 'Admin', ENT_QUOTES, 'UTF-8') ?> • 12 hours ago</div>
                        </div>
                    </div>
                </div>
                <a href="/projects/history?id=<?= View::e((string)$id) ?>" class="btn-action btn-secondary btn-block">
                    <i class="bi bi-list-check"></i>
                    View Full History
                </a>
            </div>
            
            <!-- Quick Actions -->
            <div class="quick-actions-card">
                <h4><i class="bi bi-lightning"></i> Quick Actions</h4>
                <div class="quick-actions">
                    <a href="/projects/approvals?id=<?= View::e((string)$id) ?>" class="btn-action btn-secondary btn-block">
                        <i class="bi bi-clipboard-check"></i>
                        Manage Approvals
                    </a>
                    <a href="/documentation?project_id=<?= View::e((string)$id) ?>" class="btn-action btn-secondary btn-block">
                        <i class="bi bi-file-earmark"></i>
                        View Documentation
                    </a>
                    <a href="/projects/duplicate?id=<?= View::e((string)$id) ?>" class="btn-action btn-secondary btn-block">
                        <i class="bi bi-files"></i>
                        Duplicate Project
                    </a>
                    <a href="/projects/export?id=<?= View::e((string)$id) ?>" class="btn-action btn-secondary btn-block">
                        <i class="bi bi-download"></i>
                        Export Project
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal-overlay" id="deleteModal" style="display: none;">
    <div class="modal">
        <div class="modal-header">
            <h3>Delete Project</h3>
            <button class="modal-close" onclick="hideDeleteModal()">
                <i class="bi bi-x"></i>
            </button>
        </div>
        <div class="modal-body">
            <div class="warning-icon">
                <i class="bi bi-exclamation-triangle"></i>
            </div>
            <h4>Are you sure you want to delete this project?</h4>
            <p>This action cannot be undone. All project data, including documentation and approvals, will be permanently deleted.</p>
            <div class="project-preview">
                <strong><?= View::e($name) ?></strong>
                <?php if ($code): ?>
                    <span class="text-muted">• <?= View::e($code) ?></span>
                <?php endif; ?>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn-action btn-secondary" onclick="hideDeleteModal()">
                Cancel
            </button>
            <form action="/projects/delete" method="post" style="display: inline;">
                <input type="hidden" name="id" value="<?= View::e((string)$id) ?>">
                <button type="submit" class="btn-action btn-danger">
                    <i class="bi bi-trash"></i>
                    Delete Project
                </button>
            </form>
        </div>
    </div>
</div>

<style>
    /* Edit Project Styles */
    .zb-project-edit {
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

    /* Form Container */
    .form-container {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 2rem;
    }

    .form-card {
        background: linear-gradient(135deg, rgba(11, 16, 32, 0.8), rgba(5, 8, 22, 0.9));
        border: 1px solid var(--color-border-light);
        border-radius: 12px;
        padding: 2rem;
    }

    .form-section {
        margin-bottom: 2.5rem;
        padding-bottom: 2rem;
        border-bottom: 1px solid var(--color-border);
    }

    .form-section:last-child {
        border-bottom: none;
        margin-bottom: 0;
        padding-bottom: 0;
    }

    .form-section-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--color-text);
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .form-section-title i {
        color: var(--color-accent-blue);
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1.5rem;
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

    .required-indicator {
        color: #ef4444;
        margin-left: 0.25rem;
    }

    .form-control,
    .form-select {
        width: 100%;
        background: rgba(11, 16, 32, 0.8);
        border: 1px solid var(--color-border);
        border-radius: 8px;
        padding: 0.75rem 1rem;
        color: var(--color-text);
        font-size: 0.9rem;
        transition: all 0.3s ease;
    }

    .form-control:focus,
    .form-select:focus {
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

    textarea.form-control {
        min-height: 150px;
        resize: vertical;
    }

    /* Checkbox styles */
    .form-group label input[type="checkbox"] {
        margin-right: 0.5rem;
    }

    /* Form Actions */
    .form-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 2rem;
        padding-top: 1.5rem;
        border-top: 1px solid var(--color-border);
    }

    .action-buttons,
    .submit-buttons {
        display: flex;
        gap: 0.75rem;
    }

    .btn-block {
        width: 100%;
        justify-content: center;
    }

    /* Change History */
    .change-history {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    .history-card,
    .quick-actions-card {
        background: linear-gradient(135deg, rgba(11, 16, 32, 0.8), rgba(5, 8, 22, 0.9));
        border: 1px solid var(--color-border-light);
        border-radius: 12px;
        padding: 1.5rem;
    }

    .history-card h4,
    .quick-actions-card h4 {
        font-size: 1rem;
        font-weight: 600;
        color: var(--color-text);
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .history-card h4 i {
        color: var(--color-accent-blue);
    }

    .quick-actions-card h4 i {
        color: var(--color-accent);
    }

    .history-list {
        margin-bottom: 1.5rem;
    }

    .history-item {
        padding: 0.75rem 0;
        border-bottom: 1px solid rgba(148, 163, 253, 0.1);
    }

    .history-item:last-child {
        border-bottom: none;
    }

    .history-title {
        font-size: 0.9rem;
        color: var(--color-text);
        margin-bottom: 0.25rem;
    }

    .history-meta {
        font-size: 0.8rem;
        color: var(--color-text-muted);
    }

    .quick-actions {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
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
        text-align: center;
    }

    .warning-icon {
        width: 60px;
        height: 60px;
        background: rgba(239, 68, 68, 0.15);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
        color: #ef4444;
        font-size: 1.5rem;
    }

    .modal-body h4 {
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--color-text);
        margin-bottom: 1rem;
    }

    .modal-body p {
        color: var(--color-text-muted);
        line-height: 1.5;
        margin-bottom: 1.5rem;
    }

    .project-preview {
        background: rgba(239, 68, 68, 0.1);
        border: 1px solid rgba(239, 68, 68, 0.3);
        border-radius: 8px;
        padding: 1rem;
        color: var(--color-text);
        font-size: 0.9rem;
    }

    .modal-footer {
        padding: 1.5rem;
        border-top: 1px solid var(--color-border);
        display: flex;
        justify-content: flex-end;
        gap: 0.75rem;
    }

    /* Responsive Design */
    @media (max-width: 1200px) {
        .form-container {
            grid-template-columns: 1fr;
        }
        
        .change-history {
            grid-template-columns: repeat(2, 1fr);
            display: grid;
        }
    }

    @media (max-width: 768px) {
        .zb-project-edit {
            padding: 1rem;
        }
        
        .form-card {
            padding: 1.5rem;
        }
        
        .form-grid {
            grid-template-columns: 1fr;
            gap: 1rem;
        }
        
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        
        .form-actions {
            flex-direction: column;
            gap: 1rem;
        }
        
        .action-buttons,
        .submit-buttons {
            width: 100%;
            justify-content: center;
        }
    }

    @media (max-width: 576px) {
        .stats-grid {
            grid-template-columns: 1fr;
        }
        
        .change-history {
            grid-template-columns: 1fr;
        }
        
        .modal {
            width: 95%;
        }
    }
</style>

<script>
    // Form validation
    document.getElementById('editProjectForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Basic validation
        const nameInput = document.querySelector('[name="name"]');
        if (!nameInput.value.trim()) {
            showError(nameInput, 'Project name is required');
            nameInput.focus();
            return;
        }
        
        // Show loading state
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalHTML = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Saving...';
        submitBtn.disabled = true;
        
        // Simulate save process
        setTimeout(() => {
            // In real app, this would submit the form
            console.log('Form submitted:', new FormData(this));
            
            // Show success message
            showSuccessMessage();
        }, 1500);
    });
    
    function showError(input, message) {
        // Remove any existing error
        const existingError = input.parentElement.querySelector('.error-message');
        if (existingError) {
            existingError.remove();
        }
        
        // Add error styling
        input.style.borderColor = '#ef4444';
        input.style.boxShadow = '0 0 0 3px rgba(239, 68, 68, 0.15)';
        
        // Create error message
        const error = document.createElement('div');
        error.className = 'error-message';
        error.style.color = '#ef4444';
        error.style.fontSize = '0.8rem';
        error.style.marginTop = '0.5rem';
        error.textContent = message;
        
        input.parentElement.appendChild(error);
        
        // Scroll to error
        input.scrollIntoView({ behavior: 'smooth', block: 'center' });
        input.focus();
    }
    
    function showSuccessMessage() {
        const success = document.createElement('div');
        success.className = 'success-message';
        success.innerHTML = `
            <div class="success-content">
                <i class="bi bi-check-circle-fill"></i>
                <div>
                    <h4>Changes Saved Successfully!</h4>
                    <p>Project has been updated with your changes.</p>
                </div>
            </div>
            <div class="success-actions">
                <a href="/projects/show?id=<?= View::e((string)$id) ?>" class="btn-action btn-primary">View Project</a>
            </div>
        `;
        
        // Replace form with success message
        const formCard = document.querySelector('.form-card');
        formCard.innerHTML = '';
        formCard.appendChild(success);
        
        // Add success styling
        success.style.padding = '2rem';
        success.style.textAlign = 'center';
        
        const successContent = success.querySelector('.success-content');
        successContent.style.display = 'flex';
        successContent.style.flexDirection = 'column';
        successContent.style.alignItems = 'center';
        successContent.style.gap = '1rem';
        successContent.style.marginBottom = '2rem';
        
        successContent.querySelector('i').style.color = '#34c759';
        successContent.querySelector('i').style.fontSize = '3rem';
        
        successContent.querySelector('h4').style.fontSize = '1.5rem';
        successContent.querySelector('h4').style.fontWeight = '600';
        successContent.querySelector('h4').style.color = 'var(--color-text)';
        successContent.querySelector('h4').style.margin = '0 0 0.5rem';
        
        successContent.querySelector('p').style.color = 'var(--color-text-muted)';
        
        const successActions = success.querySelector('.success-actions');
        successActions.style.display = 'flex';
        successActions.style.gap = '1rem';
        successActions.style.justifyContent = 'center';
        
        // Auto-redirect after 3 seconds
        setTimeout(() => {
            window.location.href = '/projects/show?id=<?= View::e((string)$id) ?>';
        }, 3000);
    }
    
    // Preview changes
    function previewChanges() {
        // Collect current form values
        const formData = {
            name: document.querySelector('[name="name"]').value,
            code: document.querySelector('[name="code"]').value,
            category: document.querySelector('[name="category"]').value,
            status: document.querySelector('[name="status"]').value,
            priority: document.querySelector('[name="priority"]').value,
            description: document.querySelector('[name="description"]').value,
            objectives: document.querySelector('[name="objectives"]').value
        };
        
        // Create preview modal
        const preview = document.createElement('div');
        preview.className = 'modal-overlay';
        preview.id = 'previewModal';
        preview.innerHTML = `
            <div class="modal" style="max-width: 600px;">
                <div class="modal-header">
                    <h3>Preview Changes</h3>
                    <button class="modal-close" onclick="document.getElementById('previewModal').remove()">
                        <i class="bi bi-x"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="preview-content">
                        <h4>Project Summary</h4>
                        <div class="preview-grid">
                            <div class="preview-item">
                                <span class="preview-label">Name:</span>
                                <span class="preview-value">${formData.name}</span>
                            </div>
                            <div class="preview-item">
                                <span class="preview-label">Code:</span>
                                <span class="preview-value">${formData.code || 'N/A'}</span>
                            </div>
                            <div class="preview-item">
                                <span class="preview-label">Category:</span>
                                <span class="preview-value">${formData.category || 'N/A'}</span>
                            </div>
                            <div class="preview-item">
                                <span class="preview-label">Status:</span>
                                <span class="preview-value">${formData.status.replace('_', ' ') || 'N/A'}</span>
                            </div>
                            <div class="preview-item">
                                <span class="preview-label">Priority:</span>
                                <span class="preview-value">${formData.priority || 'N/A'}</span>
                            </div>
                        </div>
                        <div class="preview-section">
                            <h5>Description Preview</h5>
                            <div class="preview-text">${formData.description || 'No description provided'}</div>
                        </div>
                        <div class="preview-section">
                            <h5>Objectives Preview</h5>
                            <div class="preview-text">${formData.objectives || 'No objectives provided'}</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn-action btn-secondary" onclick="document.getElementById('previewModal').remove()">
                        Close Preview
                    </button>
                    <button class="btn-action btn-primary" onclick="document.getElementById('editProjectForm').submit()">
                        Save Changes
                    </button>
                </div>
            </div>
        `;
        
        document.body.appendChild(preview);
        
        // Add preview styles
        const style = document.createElement('style');
        style.textContent = `
            .preview-content h4 {
                font-size: 1.1rem;
                font-weight: 600;
                color: var(--color-text);
                margin-bottom: 1rem;
                padding-bottom: 0.75rem;
                border-bottom: 1px solid var(--color-border);
            }
            
            .preview-grid {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 1rem;
                margin-bottom: 1.5rem;
            }
            
            .preview-item {
                display: flex;
                flex-direction: column;
                gap: 0.25rem;
            }
            
            .preview-label {
                font-size: 0.8rem;
                color: var(--color-text-muted);
            }
            
            .preview-value {
                font-size: 0.9rem;
                color: var(--color-text);
                font-weight: 500;
            }
            
            .preview-section {
                margin-bottom: 1.5rem;
            }
            
            .preview-section h5 {
                font-size: 1rem;
                font-weight: 600;
                color: var(--color-text);
                margin-bottom: 0.5rem;
            }
            
            .preview-text {
                background: rgba(11, 16, 32, 0.5);
                border: 1px solid var(--color-border);
                border-radius: 8px;
                padding: 1rem;
                color: var(--color-text);
                line-height: 1.5;
                white-space: pre-wrap;
                max-height: 200px;
                overflow-y: auto;
            }
        `;
        document.head.appendChild(style);
    }
    
    // Reset form
    function resetForm() {
        if (confirm('Are you sure you want to reset all changes? This cannot be undone.')) {
            document.getElementById('editProjectForm').reset();
            showToast('Form reset to original values', 'info');
        }
    }
    
    // Delete modal functions
    function showDeleteModal() {
        document.getElementById('deleteModal').style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
    
    function hideDeleteModal() {
        document.getElementById('deleteModal').style.display = 'none';
        document.body.style.overflow = 'auto';
    }
    
    // Toast notifications
    function showToast(message, type) {
        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        toast.innerHTML = `
            <div class="toast-content">
                <i class="bi bi-info-circle-fill"></i>
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
    
    // Auto-save draft
    let autoSaveTimeout;
    document.querySelectorAll('.form-control, .form-select, input[type="checkbox"]').forEach(input => {
        input.addEventListener('change', () => {
            clearTimeout(autoSaveTimeout);
            autoSaveTimeout = setTimeout(() => {
                saveDraft();
            }, 1000);
        });
    });
    
    function saveDraft() {
        const formData = new FormData(document.getElementById('editProjectForm'));
        const draft = {};
        formData.forEach((value, key) => {
            draft[key] = value;
        });
        
        localStorage.setItem(`projectDraft_<?= $id ?>`, JSON.stringify(draft));
        console.log('Draft auto-saved');
    }
    
    // Load draft on page load
    document.addEventListener('DOMContentLoaded', () => {
        const savedDraft = localStorage.getItem(`projectDraft_<?= $id ?>`);
        if (savedDraft) {
            try {
                const draft = JSON.parse(savedDraft);
                Object.keys(draft).forEach(key => {
                    const input = document.querySelector(`[name="${key}"]`);
                    if (input) {
                        if (input.type === 'checkbox') {
                            input.checked = draft[key] === 'on';
                        } else {
                            input.value = draft[key];
                        }
                    }
                });
                
                showToast('Unsaved changes restored', 'info');
            } catch (e) {
                console.error('Failed to load draft:', e);
            }
        }
        
        // Clear draft on successful save
        window.addEventListener('beforeunload', () => {
            localStorage.removeItem(`projectDraft_<?= $id ?>`);
        });
    });
</script>