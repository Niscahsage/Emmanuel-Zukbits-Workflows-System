<?php
// app/views/reports/team_overview.php
// team_overview.php provides a summary of reports across the team for a given period.

/** @var array $user */
/** @var array $reports */
/** @var array $filters */

use App\core\View;

$roleKey = $user['role_key'] ?? 'developer';
$from    = $filters['from'] ?? ($_GET['from'] ?? '');
$to      = $filters['to']   ?? ($_GET['to']   ?? '');
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Team Reports | ZukBits Online</title>
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
                radial-gradient(circle at 5% 10%, rgba(56, 189, 248, 0.07) 0%, transparent 40%),
                radial-gradient(circle at 95% 90%, rgba(168, 85, 247, 0.07) 0%, transparent 40%);
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

        .stats-card {
            background: var(--gradient-primary);
            color: white;
            padding: 20px;
            border-radius: 12px;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px var(--shadow-blue);
        }

        .stats-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--color-accent-blue), var(--color-accent-purple));
        }

        .stats-icon {
            width: 48px;
            height: 48px;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .filter-card {
            background: rgba(56, 189, 248, 0.05);
            border: 2px solid rgba(56, 189, 248, 0.2);
            border-radius: 12px;
        }

        .form-control, .form-select {
            background: var(--color-surface-alt);
            border: 1px solid var(--color-border);
            color: var(--color-text);
            border-radius: 8px;
            padding: 10px 12px;
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            background: var(--color-surface-alt);
            border-color: var(--color-accent-blue);
            color: var(--color-text);
            box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.2);
        }

        .btn-filter {
            background: var(--gradient-primary);
            color: white;
            border: none;
            padding: 10px 24px;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-filter:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px var(--shadow-blue);
            color: white;
        }

        .table-team {
            --bs-table-bg: transparent;
            --bs-table-border-color: var(--color-border);
            --bs-table-striped-bg: rgba(56, 189, 248, 0.03);
            --bs-table-hover-bg: rgba(56, 189, 248, 0.08);
        }

        .table-team th {
            color: var(--color-text);
            font-weight: 600;
            padding: 20px 16px;
            border: none;
            background: linear-gradient(90deg, rgba(56, 189, 248, 0.1), rgba(168, 85, 247, 0.1));
            position: relative;
        }

        .table-team th::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 16px;
            right: 16px;
            height: 2px;
            background: var(--gradient-primary);
        }

        .table-team td {
            padding: 20px 16px;
            border-color: var(--color-border);
            color: var(--color-text);
            vertical-align: middle;
        }

        .table-team tbody tr {
            border-bottom: 1px solid var(--color-border);
            transition: all 0.3s ease;
        }

        .table-team tbody tr:last-child {
            border-bottom: none;
        }

        .table-team tbody tr:hover {
            background: rgba(56, 189, 248, 0.08);
            transform: translateX(4px);
        }

        .team-member {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .team-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--gradient-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .status-indicator {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .indicator-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
        }

        .status-approved {
            background: rgba(52, 199, 89, 0.15);
            color: var(--color-accent-green);
        }

        .status-approved .indicator-dot {
            background: var(--color-accent-green);
        }

        .status-reviewed {
            background: rgba(255, 200, 87, 0.15);
            color: var(--color-accent);
        }

        .status-reviewed .indicator-dot {
            background: var(--color-accent);
        }

        .status-pending {
            background: rgba(148, 163, 253, 0.15);
            color: #94a3fd;
        }

        .status-pending .indicator-dot {
            background: #94a3fd;
        }

        .chart-container {
            height: 200px;
            position: relative;
            margin: 20px 0;
        }

        .chart-bar {
            display: flex;
            align-items: flex-end;
            height: 100%;
            gap: 8px;
            padding: 0 20px;
        }

        .chart-column {
            flex: 1;
            background: var(--gradient-primary);
            border-radius: 4px 4px 0 0;
            min-height: 10px;
            position: relative;
            transition: all 0.3s ease;
        }

        .chart-column:hover {
            opacity: 0.8;
            transform: scaleY(1.05);
        }

        .chart-label {
            position: absolute;
            bottom: -25px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 0.8rem;
            color: var(--color-text-muted);
        }

        .chart-value {
            position: absolute;
            top: -25px;
            left: 0;
            right: 0;
            text-align: center;
            font-weight: 600;
            font-size: 0.9rem;
            color: var(--color-text);
        }

        .export-buttons {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .btn-export {
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(56, 189, 248, 0.1);
            color: var(--color-accent-blue);
            border: 1px solid rgba(56, 189, 248, 0.3);
            transition: all 0.3s ease;
        }

        .btn-export:hover {
            background: rgba(56, 189, 248, 0.2);
            transform: translateY(-2px);
        }

        @media (max-width: 768px) {
            .stats-card {
                padding: 15px;
            }
            
            .table-team {
                font-size: 0.9rem;
            }
            
            .table-team th,
            .table-team td {
                padding: 12px 8px;
            }
            
            .export-buttons {
                justify-content: center;
                margin-top: 10px;
            }
            
            .chart-container {
                height: 150px;
            }
        }
    </style>
</head>
<body>
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h1 class="h3 fw-bold gradient-text">Team Weekly Reports</h1>
                    <p class="text-muted mb-0">
                        <i class="bi bi-people me-1"></i>
                        Overview of submitted weekly reports across your team
                    </p>
                </div>
                <div class="export-buttons">
                    <a href="/reports" class="btn-export">
                        <i class="bi bi-arrow-left me-1"></i>My Reports
                    </a>
                    <button class="btn-export" onclick="exportToCSV()">
                        <i class="bi bi-file-earmark-spreadsheet me-1"></i>Export CSV
                    </button>
                    <button class="btn-export" onclick="exportToPDF()">
                        <i class="bi bi-file-pdf me-1"></i>Export PDF
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="glass-card p-4 mb-4 filter-card">
        <h3 class="h6 mb-3 gradient-text fw-bold">
            <i class="bi bi-funnel me-2"></i>Filter Reports
        </h3>
        <form method="get" action="/reports/team-overview" class="row g-3">
            <div class="col-md-3">
                <label class="form-label small fw-medium">From Date</label>
                <input type="date" name="from" class="form-control" value="<?= View::e($from) ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-medium">To Date</label>
                <input type="date" name="to" class="form-control" value="<?= View::e($to) ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-medium">Status</label>
                <select class="form-select" name="status">
                    <option value="">All Status</option>
                    <option value="submitted">Submitted</option>
                    <option value="reviewed">Reviewed</option>
                    <option value="approved">Approved</option>
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <div class="d-flex gap-2 w-100">
                    <button type="submit" class="btn-filter flex-grow-1">
                        <i class="bi bi-search me-1"></i>Apply Filters
                    </button>
                    <a href="/reports/team-overview" class="btn btn-outline-secondary px-3">
                        <i class="bi bi-arrow-clockwise"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Stats Row -->
    <div class="row mb-4">
        <?php
        $total = count($reports);
        $approved = 0;
        $reviewed = 0;
        $submitted = 0;
        $uniqueMembers = [];
        
        foreach ($reports as $r) {
            $status = $r['status'] ?? 'submitted';
            $ownerName = $r['user_name'] ?? $r['owner_name'] ?? '';
            
            if ($ownerName && !in_array($ownerName, $uniqueMembers)) {
                $uniqueMembers[] = $ownerName;
            }
            
            if ($status === 'approved') {
                $approved++;
            } elseif ($status === 'reviewed') {
                $reviewed++;
            } else {
                $submitted++;
            }
        }
        
        $pending = max(0, $total - $approved - $reviewed);
        $memberCount = count($uniqueMembers);
        ?>
        
        <div class="col-xl-3 col-lg-6 mb-4">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="mb-2 opacity-75">Total Reports</h6>
                        <h2 class="mb-0"><?= View::e((string)$total) ?></h2>
                        <small class="opacity-75">Across <?= $memberCount ?> team members</small>
                    </div>
                    <div class="stats-icon">
                        <i class="bi bi-file-text"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-lg-6 mb-4">
            <div class="glass-card p-4 h-100">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="mb-2 text-muted">Approved</h6>
                        <h2 class="mb-0 text-success"><?= View::e((string)$approved) ?></h2>
                        <small class="text-muted">
                            <?= $total > 0 ? round(($approved / $total) * 100) : 0 ?>% of total
                        </small>
                    </div>
                    <div class="stats-icon" style="background: rgba(52, 199, 89, 0.15); color: var(--color-accent-green);">
                        <i class="bi bi-check-circle"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-lg-6 mb-4">
            <div class="glass-card p-4 h-100">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="mb-2 text-muted">Reviewed</h6>
                        <h2 class="mb-0 text-warning"><?= View::e((string)$reviewed) ?></h2>
                        <small class="text-muted">
                            <?= $total > 0 ? round(($reviewed / $total) * 100) : 0 ?>% of total
                        </small>
                    </div>
                    <div class="stats-icon" style="background: rgba(255, 200, 87, 0.15); color: var(--color-accent);">
                        <i class="bi bi-eye"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-lg-6 mb-4">
            <div class="glass-card p-4 h-100">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="mb-2 text-muted">Pending Review</h6>
                        <h2 class="mb-0 text-muted"><?= View::e((string)$pending) ?></h2>
                        <small class="text-muted">
                            <?= $total > 0 ? round(($pending / $total) * 100) : 0 ?>% of total
                        </small>
                    </div>
                    <div class="stats-icon" style="background: rgba(148, 163, 253, 0.15); color: #94a3fd;">
                        <i class="bi bi-clock"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart Section -->
    <?php if (!empty($reports)): ?>
    <div class="glass-card p-4 mb-4">
        <h3 class="h6 mb-3 gradient-text fw-bold">
            <i class="bi bi-bar-chart me-2"></i>Reports Overview
        </h3>
        <div class="chart-container">
            <div class="chart-bar">
                <?php
                // Group by status for chart
                $statusData = [
                    'Approved' => $approved,
                    'Reviewed' => $reviewed,
                    'Pending' => $pending
                ];
                
                $maxValue = max($approved, $reviewed, $pending, 1);
                
                foreach ($statusData as $label => $value):
                    $height = ($value / $maxValue) * 100;
                ?>
                <div class="chart-column" style="height: <?= $height ?>%;" 
                     title="<?= $label ?>: <?= $value ?>">
                    <div class="chart-value"><?= $value ?></div>
                    <div class="chart-label"><?= $label ?></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Reports Table -->
    <div class="glass-card p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="h6 mb-0 gradient-text fw-bold">
                <i class="bi bi-list-check me-2"></i>Team Reports
            </h3>
            <div class="text-muted small">
                Showing <?= count($reports) ?> report<?= count($reports) !== 1 ? 's' : '' ?>
            </div>
        </div>

        <?php if (empty($reports)): ?>
            <div class="text-center py-5">
                <i class="bi bi-people fs-1 text-muted mb-3 d-block"></i>
                <h4 class="text-muted mb-3">No team reports found</h4>
                <p class="text-muted mb-4">Try adjusting your filters or date range</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-team">
                    <thead>
                    <tr>
                        <th>Week</th>
                        <th>Team Member</th>
                        <th>Summary</th>
                        <th>Status</th>
                        <th>Submitted</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($reports as $r): ?>
                        <?php
                        $id        = (int)($r['id'] ?? 0);
                        $week      = $r['week_start_date'] ?? $r['week_start'] ?? '';
                        $ownerName = $r['user_name'] ?? $r['owner_name'] ?? '';
                        $summary   = $r['overall_summary'] ?? '';
                        $status    = $r['status'] ?? 'submitted';
                        $createdAt = $r['created_at'] ?? '';
                        $initials = '';
                        if ($ownerName) {
                            $nameParts = explode(' ', $ownerName);
                            $initials = ($nameParts[0][0] ?? '') . ($nameParts[1][0] ?? '');
                        }
                        ?>
                        <tr>
                            <td>
                                <div class="fw-medium"><?= View::e($week) ?></div>
                            </td>
                            <td>
                                <div class="team-member">
                                    <div class="team-avatar"><?= strtoupper($initials) ?></div>
                                    <div>
                                        <div class="fw-medium"><?= View::e($ownerName) ?></div>
                                    </div>
                                </div>
                            </td>
                            <td style="max-width: 300px;">
                                <?php if ($summary): ?>
                                    <div class="summary-preview" style="-webkit-line-clamp: 2;">
                                        <?= View::e(mb_strimwidth($summary, 0, 100, '…')) ?>
                                    </div>
                                <?php else: ?>
                                    <span class="text-muted">No summary</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($status === 'approved'): ?>
                                    <span class="status-indicator status-approved">
                                        <span class="indicator-dot"></span>
                                        Approved
                                    </span>
                                <?php elseif ($status === 'reviewed'): ?>
                                    <span class="status-indicator status-reviewed">
                                        <span class="indicator-dot"></span>
                                        Reviewed
                                    </span>
                                <?php else: ?>
                                    <span class="status-indicator status-pending">
                                        <span class="indicator-dot"></span>
                                        Submitted
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="text-muted small">
                                    <i class="bi bi-calendar me-1"></i>
                                    <?= $createdAt ? View::e(date('M d', strtotime($createdAt))) : '—' ?>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <a href="/reports/show?id=<?= View::e((string)$id) ?>"
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye me-1"></i>View
                                    </a>
                                    <?php if ($status === 'submitted'): ?>
                                        <button class="btn btn-sm" 
                                                style="background: rgba(56, 189, 248, 0.1); color: var(--color-accent-blue);"
                                                onclick="reviewReport(<?= $id ?>)">
                                            <i class="bi bi-check-lg"></i>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <nav class="mt-4">
                <ul class="pagination justify-content-center">
                    <li class="page-item">
                        <a class="page-link" href="#" style="background: var(--color-surface); border-color: var(--color-border); color: var(--color-text);">
                            <i class="bi bi-chevron-left"></i>
                        </a>
                    </li>
                    <li class="page-item active">
                        <a class="page-link" href="#" style="background: var(--gradient-primary); border: none;">1</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="#" style="background: var(--color-surface); border-color: var(--color-border); color: var(--color-text);">2</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="#" style="background: var(--color-surface); border-color: var(--color-border); color: var(--color-text);">3</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="#" style="background: var(--color-surface); border-color: var(--color-border); color: var(--color-text);">
                            <i class="bi bi-chevron-right"></i>
                        </a>
                    </li>
                </ul>
            </nav>
        <?php endif; ?>
    </div>
</div>

<script>
function exportToCSV() {
    showNotification('Preparing CSV export...', 'info');
    // In real implementation, this would trigger CSV generation
}

function exportToPDF() {
    showNotification('Generating PDF report...', 'info');
    // In real implementation, this would trigger PDF generation
}

function reviewReport(reportId) {
    if (confirm('Mark this report as reviewed?')) {
        const reviewBtn = event.target.closest('button');
        const originalHTML = reviewBtn.innerHTML;
        
        reviewBtn.innerHTML = '<i class="bi bi-hourglass-split"></i>';
        reviewBtn.disabled = true;
        
        setTimeout(() => {
            reviewBtn.innerHTML = '<i class="bi bi-check-circle-fill text-success"></i>';
            
            // Update status in the table
            const statusCell = reviewBtn.closest('tr').querySelector('.status-indicator');
            if (statusCell) {
                statusCell.className = 'status-indicator status-reviewed';
                statusCell.innerHTML = '<span class="indicator-dot"></span>Reviewed';
            }
            
            showNotification('Report marked as reviewed!', 'success');
        }, 1000);
    }
}

function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = 'position-fixed top-0 end-0 p-3';
    notification.style.zIndex = '9999';
    
    const alert = document.createElement('div');
    alert.className = `alert alert-${type === 'success' ? 'success' : 'info'} alert-dismissible fade show`;
    alert.style.background = type === 'success' ? 'rgba(52, 199, 89, 0.9)' : 'rgba(56, 189, 248, 0.9)';
    alert.style.border = 'none';
    alert.style.color = 'white';
    
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

// Chart hover effects
document.addEventListener('DOMContentLoaded', function() {
    const chartColumns = document.querySelectorAll('.chart-column');
    chartColumns.forEach(column => {
        column.addEventListener('mouseenter', function() {
            this.style.opacity = '0.9';
        });
        
        column.addEventListener('mouseleave', function() {
            this.style.opacity = '1';
        });
    });

    // Filter date validation
    const fromInput = document.querySelector('input[name="from"]');
    const toInput = document.querySelector('input[name="to"]');
    
    if (fromInput && toInput) {
        fromInput.addEventListener('change', function() {
            if (toInput.value && this.value > toInput.value) {
                toInput.value = this.value;
            }
        });
        
        toInput.addEventListener('change', function() {
            if (fromInput.value && this.value < fromInput.value) {
                fromInput.value = this.value;
            }
        });
    }
});
</script>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>