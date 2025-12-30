<?php
// app/views/reports/show.php
// show.php displays a specific weekly report and its outcomes.

/** @var array $report */
/** @var array $items */

use App\core\View;
use App\core\Auth;

$user    = Auth::user();
$roleKey = $user['role_key'] ?? 'developer';

$id            = (int)($report['id'] ?? 0);
$weekStart     = $report['week_start_date'] ?? $report['week_start'] ?? '';
$ownerName     = $report['user_name'] ?? $report['owner_name'] ?? '';
$overall       = $report['overall_summary'] ?? '';
$challenges    = $report['challenges'] ?? '';
$supportNeeded = $report['support_needed'] ?? '';
$status        = $report['status'] ?? 'submitted';
$createdAt     = $report['created_at'] ?? '';
$updatedAt     = $report['updated_at'] ?? '';
$scheduleId    = (int)($report['schedule_id'] ?? 0);
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weekly Report | ZukBits Online</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --color-bg: #050816;
            --color-surface: #0b1020;
            --color-surface-alt: #111827;
            --color-surface-soft: #0f172a;
            --color-accent: #ffc857;
            --color-accent-strong: #fbbf24;
            --color-accent-soft: rgba(255, 200, 87, 0.15);
            --color-accent-blue: #38bdf8;
            --color-accent-purple: #a855f7;
            --color-accent-green: #34c759;
            --color-text: #f7f7ff;
            --color-text-muted: #c3c5d4;
            --color-border: #22263b;
            --gradient-primary: linear-gradient(135deg, #38bdf8, #a855f7);
            --gradient-accent: linear-gradient(135deg, #fbbf24, #f97316);
            --shadow-blue: rgba(56, 189, 248, 0.35);
        }

        body {
            background: var(--color-bg);
            color: var(--color-text);
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            min-height: 100vh;
            background-image: 
                radial-gradient(circle at 20% 30%, rgba(56, 189, 248, 0.05) 0%, transparent 50%),
                radial-gradient(circle at 80% 70%, rgba(168, 85, 247, 0.05) 0%, transparent 50%);
        }

        .glass-card {
            background: rgba(11, 16, 32, 0.7);
            border: 1px solid var(--color-border);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        }

        .glass-card-alt {
            background: rgba(15, 23, 42, 0.5);
            border: 1px solid rgba(148, 163, 253, 0.2);
            backdrop-filter: blur(5px);
            border-radius: 12px;
        }

        .gradient-text {
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .status-badge {
            padding: 8px 20px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.9rem;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .status-approved {
            background: linear-gradient(135deg, rgba(52, 199, 89, 0.2), rgba(52, 199, 89, 0.1));
            color: var(--color-accent-green);
            border: 1px solid rgba(52, 199, 89, 0.3);
        }

        .status-reviewed {
            background: linear-gradient(135deg, rgba(255, 200, 87, 0.2), rgba(255, 200, 87, 0.1));
            color: var(--color-accent);
            border: 1px solid rgba(255, 200, 87, 0.3);
        }

        .status-submitted {
            background: linear-gradient(135deg, rgba(148, 163, 253, 0.2), rgba(148, 163, 253, 0.1));
            color: #94a3fd;
            border: 1px solid rgba(148, 163, 253, 0.3);
        }

        .week-header {
            background: var(--gradient-primary);
            color: white;
            padding: 12px 24px;
            border-radius: 12px;
            margin-bottom: 24px;
            position: relative;
            overflow: hidden;
        }

        .week-header::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 100px;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1));
        }

        .table-report {
            --bs-table-bg: transparent;
            --bs-table-border-color: var(--color-border);
        }

        .table-report th {
            background: linear-gradient(90deg, rgba(56, 189, 248, 0.1), rgba(168, 85, 247, 0.1));
            color: var(--color-text);
            font-weight: 600;
            padding: 16px;
            border: none;
            position: relative;
        }

        .table-report th::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 2px;
            background: var(--gradient-primary);
        }

        .table-report td {
            padding: 20px 16px;
            border-color: var(--color-border);
            color: var(--color-text);
            vertical-align: middle;
        }

        .table-report tbody tr {
            border-bottom: 1px solid rgba(148, 163, 253, 0.1);
            transition: all 0.3s ease;
        }

        .table-report tbody tr:last-child {
            border-bottom: none;
        }

        .table-report tbody tr:hover {
            background: rgba(56, 189, 248, 0.05);
        }

        .task-status {
            padding: 6px 12px;
            border-radius: 12px;
            font-weight: 500;
            font-size: 0.8rem;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .status-completed {
            background: rgba(52, 199, 89, 0.15);
            color: var(--color-accent-green);
        }

        .status-in-progress {
            background: rgba(56, 189, 248, 0.15);
            color: var(--color-accent-blue);
        }

        .status-blocked {
            background: rgba(239, 68, 68, 0.15);
            color: #ef4444;
        }

        .status-not-started {
            background: rgba(148, 163, 253, 0.15);
            color: #94a3fd;
        }

        .progress-ring {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: conic-gradient(var(--color-accent-blue) 75%, var(--color-border) 0);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .progress-ring::before {
            content: '';
            position: absolute;
            width: 60px;
            height: 60px;
            background: var(--color-surface);
            border-radius: 50%;
        }

        .progress-ring span {
            position: relative;
            z-index: 1;
            font-weight: 700;
            font-size: 1.2rem;
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .btn-group-actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .btn-action {
            padding: 8px 20px;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-action-primary {
            background: var(--gradient-primary);
            color: white;
            border: none;
        }

        .btn-action-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px var(--shadow-blue);
            color: white;
        }

        .btn-action-secondary {
            background: rgba(56, 189, 248, 0.1);
            color: var(--color-accent-blue);
            border: 1px solid rgba(56, 189, 248, 0.3);
        }

        .btn-action-secondary:hover {
            background: rgba(56, 189, 248, 0.2);
            color: var(--color-accent-blue);
            transform: translateY(-2px);
        }

        .comment-bubble {
            background: rgba(56, 189, 248, 0.1);
            border-left: 3px solid var(--color-accent-blue);
            padding: 12px 16px;
            border-radius: 8px;
            margin: 8px 0;
            font-size: 0.9rem;
            line-height: 1.5;
        }

        .comment-bubble::before {
            content: '💬';
            margin-right: 8px;
            opacity: 0.7;
        }

        @media (max-width: 768px) {
            .week-header {
                padding: 12px;
                text-align: center;
            }
            
            .table-report {
                font-size: 0.9rem;
            }
            
            .btn-group-actions {
                width: 100%;
                justify-content: center;
            }
            
            .btn-action {
                flex: 1;
                justify-content: center;
                min-width: 120px;
            }
        }
    </style>
</head>
<body>
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="week-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h4 mb-1 fw-bold">Weekly Report</h1>
                        <p class="mb-0 opacity-90">
                            Week starting <strong><?= View::e($weekStart) ?></strong>
                            <?php if ($ownerName): ?> • <?= View::e($ownerName) ?><?php endif; ?>
                        </p>
                    </div>
                    <div class="d-flex gap-2">
                        <?php if ($status === 'submitted' && in_array($roleKey, ['director', 'super_admin', 'system_admin'])): ?>
                            <button class="btn btn-sm btn-light" onclick="approveReport(<?= $id ?>)">
                                <i class="bi bi-check-lg me-1"></i>Approve
                            </button>
                        <?php endif; ?>
                        <a href="/reports" class="btn btn-sm btn-light">
                            <i class="bi bi-arrow-left me-1"></i>Back
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats & Summary Row -->
    <div class="row mb-4">
        <!-- Status Card -->
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="glass-card p-4 h-100">
                <h3 class="h6 mb-3 gradient-text fw-bold">Report Status</h3>
                <div class="mb-4">
                    <?php if ($status === 'approved'): ?>
                        <span class="status-badge status-approved">
                            <i class="bi bi-check-circle-fill"></i>Approved
                        </span>
                    <?php elseif ($status === 'reviewed'): ?>
                        <span class="status-badge status-reviewed">
                            <i class="bi bi-eye-fill"></i>Reviewed
                        </span>
                    <?php else: ?>
                        <span class="status-badge status-submitted">
                            <i class="bi bi-clock-fill"></i>Submitted
                        </span>
                    <?php endif; ?>
                </div>
                
                <div class="glass-card-alt p-3">
                    <div class="mb-2">
                        <small class="text-muted d-block">Submitted</small>
                        <div class="fw-medium">
                            <i class="bi bi-calendar me-1"></i>
                            <?= $createdAt ? View::e(date('M d, Y', strtotime($createdAt))) : '—' ?>
                        </div>
                    </div>
                    <div>
                        <small class="text-muted d-block">Last Updated</small>
                        <div class="fw-medium">
                            <i class="bi bi-arrow-clockwise me-1"></i>
                            <?= $updatedAt ? View::e(date('M d, Y', strtotime($updatedAt))) : '—' ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Progress -->
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="glass-card p-4 h-100 d-flex flex-column justify-content-center align-items-center">
                <h3 class="h6 mb-3 gradient-text fw-bold">Completion</h3>
                <?php
                $totalTasks = count($items);
                $completedTasks = 0;
                foreach ($items as $item) {
                    if (($item['status'] ?? '') === 'completed') {
                        $completedTasks++;
                    }
                }
                $completionRate = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;
                ?>
                <div class="progress-ring mb-3">
                    <span><?= $completionRate ?>%</span>
                </div>
                <div class="text-center">
                    <div class="fw-bold h4 mb-1"><?= $completedTasks ?>/<?= $totalTasks ?></div>
                    <div class="text-muted small">Tasks Completed</div>
                </div>
            </div>
        </div>

        <!-- Overall Summary -->
        <div class="col-lg-6 mb-4">
            <div class="glass-card p-4 h-100">
                <h3 class="h6 mb-3 gradient-text fw-bold">
                    <i class="bi bi-file-text me-2"></i>Overall Summary
                </h3>
                <?php if ($overall): ?>
                    <div class="glass-card-alt p-3" style="white-space: pre-wrap; line-height: 1.6;">
                        <?= nl2br(View::e($overall)) ?>
                    </div>
                <?php else: ?>
                    <div class="alert alert-secondary mb-0">
                        <i class="bi bi-info-circle me-2"></i>
                        No summary recorded.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Challenges & Support Row -->
    <div class="row mb-4">
        <div class="col-md-6 mb-4">
            <div class="glass-card p-4 h-100">
                <h3 class="h6 mb-3 gradient-text fw-bold">
                    <i class="bi bi-exclamation-triangle me-2"></i>Challenges / Blockers
                </h3>
                <?php if ($challenges): ?>
                    <div class="glass-card-alt p-3" style="white-space: pre-wrap; line-height: 1.6;">
                        <?= nl2br(View::e($challenges)) ?>
                    </div>
                <?php else: ?>
                    <div class="alert alert-secondary mb-0">
                        <i class="bi bi-check-circle me-2"></i>
                        No challenges recorded.
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="glass-card p-4 h-100">
                <h3 class="h6 mb-3 gradient-text fw-bold">
                    <i class="bi bi-life-preserver me-2"></i>Support Needed
                </h3>
                <?php if ($supportNeeded): ?>
                    <div class="glass-card-alt p-3" style="white-space: pre-wrap; line-height: 1.6;">
                        <?= nl2br(View::e($supportNeeded)) ?>
                    </div>
                <?php else: ?>
                    <div class="alert alert-secondary mb-0">
                        <i class="bi bi-check-circle me-2"></i>
                        No support requests.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Tasks Table -->
    <div class="glass-card p-4 mb-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="h6 mb-0 gradient-text fw-bold">
                <i class="bi bi-list-task me-2"></i>Tasks and Outcomes
            </h3>
            <?php if ($scheduleId > 0): ?>
                <a href="/schedules/show?id=<?= View::e((string)$scheduleId) ?>"
                   class="btn-action btn-action-secondary">
                    <i class="bi bi-calendar-week"></i>View Schedule
                </a>
            <?php endif; ?>
        </div>

        <?php if (empty($items)): ?>
            <div class="text-center py-5">
                <i class="bi bi-clipboard-x fs-1 text-muted mb-3 d-block"></i>
                <h4 class="text-muted mb-3">No tasks linked to this report</h4>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-report">
                    <thead>
                    <tr>
                        <th style="width:36%;">Task</th>
                        <th style="width:18%;">Project</th>
                        <th style="width:10%;">Hours</th>
                        <th style="width:12%;">Status</th>
                        <th style="width:24%;">Comment</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($items as $item): ?>
                        <?php
                        $taskDesc    = $item['description'] ?? $item['schedule_item_description'] ?? '';
                        $projectName = $item['project_name'] ?? '';
                        $hours       = $item['estimated_hours'] ?? $item['planned_hours'] ?? '';
                        $statusItem  = $item['status'] ?? '';
                        $comment     = $item['comment'] ?? '';
                        ?>
                        <tr>
                            <td class="fw-medium"><?= View::e($taskDesc) ?></td>
                            <td>
                                <?php if ($projectName): ?>
                                    <span class="badge bg-dark"><?= View::e($projectName) ?></span>
                                <?php else: ?>
                                    <span class="text-muted">—</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($hours !== ''): ?>
                                    <span class="badge bg-secondary"><?= View::e((string)$hours) ?>h</span>
                                <?php else: ?>
                                    <span class="text-muted">—</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($statusItem === 'completed'): ?>
                                    <span class="task-status status-completed">
                                        <i class="bi bi-check-circle"></i>Completed
                                    </span>
                                <?php elseif ($statusItem === 'in_progress'): ?>
                                    <span class="task-status status-in-progress">
                                        <i class="bi bi-arrow-repeat"></i>In Progress
                                    </span>
                                <?php elseif ($statusItem === 'blocked'): ?>
                                    <span class="task-status status-blocked">
                                        <i class="bi bi-slash-circle"></i>Blocked
                                    </span>
                                <?php elseif ($statusItem === 'not_started'): ?>
                                    <span class="task-status status-not-started">
                                        <i class="bi bi-clock"></i>Not Started
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-light text-muted">N/A</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($comment): ?>
                                    <div class="comment-bubble">
                                        <?= nl2br(View::e($comment)) ?>
                                    </div>
                                <?php else: ?>
                                    <span class="text-muted small">No comment</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <!-- Action Buttons -->
    <div class="d-flex justify-content-between align-items-center">
        <div class="btn-group-actions">
            <a href="/reports" class="btn-action btn-action-secondary">
                <i class="bi bi-arrow-left me-1"></i>Back to Reports
            </a>
            <?php if (in_array($roleKey, ['director', 'super_admin', 'system_admin'])): ?>
                <button class="btn-action btn-action-secondary" onclick="downloadReport(<?= $id ?>)">
                    <i class="bi bi-download me-1"></i>Download PDF
                </button>
                <button class="btn-action btn-action-secondary" onclick="shareReport(<?= $id ?>)">
                    <i class="bi bi-share me-1"></i>Share
                </button>
            <?php endif; ?>
        </div>
        
        <?php if ($status === 'submitted' && in_array($roleKey, ['director', 'super_admin', 'system_admin'])): ?>
            <button class="btn-action btn-action-primary" onclick="approveReport(<?= $id ?>)">
                <i class="bi bi-check-lg me-1"></i>Approve Report
            </button>
        <?php endif; ?>
    </div>
</div>

<script>
function approveReport(reportId) {
    if (confirm('Are you sure you want to approve this report?')) {
        // Simulate API call
        const approveBtn = document.querySelector('.btn-action-primary');
        const originalText = approveBtn.innerHTML;
        
        approveBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Approving...';
        approveBtn.disabled = true;
        
        setTimeout(() => {
            approveBtn.innerHTML = '<i class="bi bi-check-circle-fill"></i> Approved';
            approveBtn.style.background = 'var(--color-accent-green)';
            
            // Update status badge
            const statusBadge = document.querySelector('.status-badge');
            if (statusBadge) {
                statusBadge.className = 'status-badge status-approved';
                statusBadge.innerHTML = '<i class="bi bi-check-circle-fill"></i>Approved';
            }
            
            // Show success message
            showNotification('Report approved successfully!', 'success');
        }, 1500);
    }
}

function downloadReport(reportId) {
    showNotification('Preparing download...', 'info');
    // In real implementation, this would trigger PDF generation
}

function shareReport(reportId) {
    if (navigator.share) {
        navigator.share({
            title: 'Weekly Report',
            text: 'Check out this weekly report',
            url: window.location.href,
        });
    } else {
        navigator.clipboard.writeText(window.location.href);
        showNotification('Link copied to clipboard!', 'success');
    }
}

function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = `position-fixed top-0 end-0 p-3`;
    notification.style.zIndex = '9999';
    
    const alert = document.createElement('div');
    alert.className = `alert alert-${type === 'success' ? 'success' : 'info'} alert-dismissible fade show`;
    alert.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    notification.appendChild(alert);
    document.body.appendChild(notification);
    
    setTimeout(() => {
        alert.classList.remove('show');
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// Add interactivity to table rows
document.addEventListener('DOMContentLoaded', function() {
    const taskRows = document.querySelectorAll('.table-report tbody tr');
    taskRows.forEach(row => {
        row.addEventListener('click', function(e) {
            if (!e.target.closest('button') && !e.target.closest('a')) {
                this.classList.toggle('expanded');
            }
        });
    });
});
</script>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>