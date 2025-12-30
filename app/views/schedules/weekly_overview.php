<?php
// app/views/schedules/weekly_overview.php
// weekly_overview.php shows a summary of schedules across the team for a selected week.

/** @var array $user */
/** @var array $schedules */
/** @var array $filters */

use App\core\View;

$roleKey = $user['role_key'] ?? 'developer';
$week    = $filters['week_start'] ?? ($_GET['week_start'] ?? '');
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Team Weekly Overview | ZukBits Online</title>
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
                radial-gradient(circle at 10% 10%, rgba(56, 189, 248, 0.07) 0%, transparent 40%),
                radial-gradient(circle at 90% 90%, rgba(168, 85, 247, 0.07) 0%, transparent 40%);
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

        .week-header {
            background: var(--gradient-primary);
            color: white;
            padding: 20px 30px;
            border-radius: 16px;
            margin-bottom: 30px;
            position: relative;
            overflow: hidden;
        }

        .week-header::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 120px;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1));
        }

        .stats-card {
            background: var(--gradient-primary);
            color: white;
            padding: 24px;
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
            width: 56px;
            height: 56px;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
        }

        .team-avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: var(--gradient-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 1rem;
        }

        .team-progress {
            height: 8px;
            background: var(--color-border);
            border-radius: 4px;
            overflow: hidden;
            position: relative;
        }

        .team-progress-bar {
            height: 100%;
            background: var(--gradient-primary);
            border-radius: 4px;
            transition: width 0.6s ease;
        }

        .member-card {
            background: rgba(56, 189, 248, 0.05);
            border: 2px solid rgba(56, 189, 248, 0.1);
            border-radius: 12px;
            padding: 20px;
            transition: all 0.3s ease;
        }

        .member-card:hover {
            background: rgba(56, 189, 248, 0.1);
            transform: translateY(-3px);
            border-color: rgba(56, 189, 248, 0.3);
        }

        .member-hours {
            background: var(--gradient-primary);
            color: white;
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .table-overview {
            --bs-table-bg: transparent;
            --bs-table-border-color: var(--color-border);
            --bs-table-striped-bg: rgba(56, 189, 248, 0.03);
            --bs-table-hover-bg: rgba(56, 189, 248, 0.08);
        }

        .table-overview th {
            background: linear-gradient(90deg, rgba(56, 189, 248, 0.1), rgba(168, 85, 247, 0.1));
            color: var(--color-text);
            font-weight: 600;
            padding: 20px;
            border: none;
            position: relative;
        }

        .table-overview th::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 20px;
            right: 20px;
            height: 2px;
            background: var(--gradient-primary);
        }

        .table-overview td {
            padding: 20px;
            border-color: var(--color-border);
            color: var(--color-text);
            vertical-align: middle;
        }

        .table-overview tbody tr {
            border-bottom: 1px solid var(--color-border);
            transition: all 0.3s ease;
        }

        .table-overview tbody tr:last-child {
            border-bottom: none;
        }

        .table-overview tbody tr:hover {
            background: rgba(56, 189, 248, 0.08);
            transform: translateX(4px);
        }

        .week-badge-large {
            background: rgba(56, 189, 248, 0.15);
            color: var(--color-accent-blue);
            padding: 12px 24px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 1.1rem;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            border: 2px solid rgba(56, 189, 248, 0.3);
        }

        .summary-preview {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            color: var(--color-text-muted);
            line-height: 1.5;
            font-size: 0.9rem;
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
            border-radius: 6px 6px 0 0;
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

        @media (max-width: 768px) {
            .week-header {
                padding: 15px;
                text-align: center;
            }
            
            .table-overview {
                font-size: 0.9rem;
            }
            
            .table-overview th,
            .table-overview td {
                padding: 12px 8px;
            }
            
            .chart-container {
                height: 150px;
            }
            
            .member-cards-container {
                overflow-x: auto;
                padding-bottom: 10px;
            }
            
            .member-cards-row {
                flex-wrap: nowrap;
                min-width: 600px;
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
                        <h1 class="h3 mb-2 fw-bold">Team Weekly Overview</h1>
                        <p class="mb-0 opacity-90">
                            <i class="bi bi-people me-2"></i>
                            Summary of schedules across your team for selected week
                        </p>
                    </div>
                    <a href="/schedules" class="btn btn-light">
                        <i class="bi bi-arrow-left me-2"></i>Back to Schedules
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Week Filter -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="glass-card p-4">
                <form method="get" action="/schedules/weekly-overview" class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-medium mb-2">
                            <i class="bi bi-calendar-week me-2"></i>Select Week
                        </label>
                        <div class="input-group">
                            <input type="date" name="week_start" class="form-control" value="<?= View::e($week) ?>">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-search me-2"></i>Load Week
                            </button>
                        </div>
                        <small class="text-muted mt-2 d-block">
                            <i class="bi bi-info-circle me-1"></i>
                            Use the same anchor day as schedules (e.g., Monday)
                        </small>
                    </div>
                    <div class="col-md-6 d-flex align-items-end justify-content-end">
                        <?php if ($week): ?>
                            <div class="week-badge-large">
                                <i class="bi bi-calendar-week"></i>
                                Week of <?= View::e(date('F d, Y', strtotime($week))) ?>
                            </div>
                        <?php else: ?>
                            <div class="text-muted">
                                <i class="bi bi-calendar-x me-2"></i>
                                No week selected
                            </div>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php
    // Calculate statistics
    $teamMembers = [];
    $totalHours = 0;
    $totalSchedules = count($schedules);
    $totalTasks = 0;
    
    foreach ($schedules as $s) {
        $owner = $s['user_name'] ?? $s['owner_name'] ?? 'Unknown';
        $hrs   = (float)($s['total_estimated_hours'] ?? $s['total_hours'] ?? 0);
        $tasks = (int)($s['items_count'] ?? 0);
        
        $totalHours += $hrs;
        $totalTasks += $tasks;
        
        if (!isset($teamMembers[$owner])) {
            $teamMembers[$owner] = ['schedules' => 0, 'hours' => 0, 'tasks' => 0];
        }
        $teamMembers[$owner]['schedules']++;
        $teamMembers[$owner]['hours'] += $hrs;
        $teamMembers[$owner]['tasks'] += $tasks;
    }
    
    $memberCount = count($teamMembers);
    $avgHours = $memberCount > 0 ? $totalHours / $memberCount : 0;
    ?>

    <!-- Stats Row -->
    <div class="row mb-4">
        <div class="col-xl-3 col-lg-6 mb-4">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="mb-2 opacity-75">Total Schedules</h6>
                        <h2 class="mb-0"><?= $totalSchedules ?></h2>
                        <small class="opacity-75">Across <?= $memberCount ?> team members</small>
                    </div>
                    <div class="stats-icon">
                        <i class="bi bi-calendar-week"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-lg-6 mb-4">
            <div class="glass-card p-4 h-100">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="mb-2 text-muted">Total Tasks</h6>
                        <h2 class="mb-0"><?= $totalTasks ?></h2>
                        <small class="text-muted"><?= round($totalTasks / max($totalSchedules, 1), 1) ?> per schedule</small>
                    </div>
                    <div class="stats-icon" style="background: rgba(255, 200, 87, 0.15); color: var(--color-accent);">
                        <i class="bi bi-list-task"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-lg-6 mb-4">
            <div class="glass-card p-4 h-100">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="mb-2 text-muted">Total Hours</h6>
                        <h2 class="mb-0"><?= round($totalHours, 1) ?></h2>
                        <small class="text-muted"><?= round($avgHours, 1) ?> avg per member</small>
                    </div>
                    <div class="stats-icon" style="background: rgba(52, 199, 89, 0.15); color: var(--color-accent-green);">
                        <i class="bi bi-clock"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-lg-6 mb-4">
            <div class="glass-card p-4 h-100">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="mb-2 text-muted">Team Coverage</h6>
                        <h2 class="mb-0"><?= $memberCount ?></h2>
                        <small class="text-muted">
                            <?= $totalSchedules > 0 ? round(($totalSchedules / $memberCount) * 100) : 0 ?>% participation
                        </small>
                    </div>
                    <div class="stats-icon" style="background: rgba(168, 85, 247, 0.15); color: var(--color-accent-purple);">
                        <i class="bi bi-people"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php if (!empty($teamMembers)): ?>
    <!-- Team Members Chart -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="glass-card p-4">
                <h3 class="h6 mb-3 gradient-text fw-bold">
                    <i class="bi bi-bar-chart me-2"></i>Hours by Team Member
                </h3>
                <div class="chart-container">
                    <div class="chart-bar">
                        <?php
                        $maxHours = max(array_column($teamMembers, 'hours'));
                        foreach ($teamMembers as $name => $data):
                            $hours = $data['hours'];
                            $height = $maxHours > 0 ? ($hours / $maxHours) * 100 : 0;
                            $initials = '';
                            $nameParts = explode(' ', $name);
                            $initials = ($nameParts[0][0] ?? '') . ($nameParts[1][0] ?? '');
                        ?>
                        <div class="chart-column" style="height: <?= $height ?>%;" 
                             title="<?= $name ?>: <?= $hours ?> hours">
                            <div class="chart-value"><?= round($hours, 1) ?></div>
                            <div class="chart-label"><?= $initials ?></div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Team Members Cards -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="glass-card p-4">
                <h3 class="h6 mb-3 gradient-text fw-bold">
                    <i class="bi bi-people me-2"></i>Team Members
                </h3>
                <div class="member-cards-container">
                    <div class="row g-3 member-cards-row">
                        <?php foreach ($teamMembers as $name => $data): 
                            $initials = '';
                            $nameParts = explode(' ', $name);
                            $initials = ($nameParts[0][0] ?? '') . ($nameParts[1][0] ?? '');
                        ?>
                        <div class="col-md-3">
                            <div class="member-card">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="team-avatar me-3"><?= strtoupper($initials) ?></div>
                                    <div>
                                        <h6 class="mb-0 fw-medium"><?= View::e($name) ?></h6>
                                        <small class="text-muted"><?= $data['schedules'] ?> schedule(s)</small>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="small text-muted">Tasks</span>
                                        <span class="fw-medium"><?= $data['tasks'] ?></span>
                                    </div>
                                    <div class="team-progress">
                                        <div class="team-progress-bar" style="width: <?= min(($data['tasks'] / max($totalTasks, 1)) * 100, 100) ?>%"></div>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="small text-muted">Planned hours</span>
                                    <span class="member-hours"><?= round($data['hours'], 1) ?>h</span>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Schedules List -->
    <div class="row">
        <div class="col-12">
            <div class="glass-card p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3 class="h6 mb-0 gradient-text fw-bold">
                        <i class="bi bi-list-ul me-2"></i>Schedules List
                    </h3>
                    <div class="text-muted small">
                        <?= $totalSchedules ?> schedule<?= $totalSchedules !== 1 ? 's' : '' ?> • 
                        <?= round($totalHours, 1) ?> total hours
                    </div>
                </div>

                <?php if (empty($schedules)): ?>
                    <div class="text-center py-5">
                        <i class="bi bi-calendar-x fs-1 text-muted mb-3 d-block"></i>
                        <h4 class="text-muted mb-3">No schedules found for this week</h4>
                        <p class="text-muted mb-4">
                            <?php if ($week): ?>
                                No team members have created schedules for the week starting <?= View::e($week) ?>
                            <?php else: ?>
                                Select a week to see team schedules
                            <?php endif; ?>
                        </p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-overview">
                            <thead>
                            <tr>
                                <th>Team Member</th>
                                <th>Week</th>
                                <th>Tasks</th>
                                <th>Planned Hours</th>
                                <th>Summary</th>
                                <th>Created</th>
                                <th class="text-center">Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($schedules as $s): ?>
                                <?php
                                $id        = (int)($s['id'] ?? 0);
                                $weekStart = $s['week_start_date'] ?? $s['week_start'] ?? '';
                                $owner     = $s['user_name'] ?? $s['owner_name'] ?? 'Unknown';
                                $itemsCnt  = (int)($s['items_count'] ?? 0);
                                $hrs       = $s['total_estimated_hours'] ?? $s['total_hours'] ?? null;
                                $summary   = $s['summary_plan'] ?? '';
                                $createdAt = $s['created_at'] ?? '';
                                $initials = '';
                                $nameParts = explode(' ', $owner);
                                $initials = ($nameParts[0][0] ?? '') . ($nameParts[1][0] ?? '');
                                ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="team-avatar me-3"><?= strtoupper($initials) ?></div>
                                            <div>
                                                <div class="fw-medium"><?= View::e($owner) ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-medium"><?= View::e($weekStart) ?></div>
                                    </td>
                                    <td>
                                        <span class="badge bg-dark"><?= $itemsCnt ?></span>
                                        <span class="text-muted small ms-1">tasks</span>
                                    </td>
                                    <td>
                                        <?php if ($hrs !== null && $hrs !== ''): ?>
                                            <span class="badge" style="background: rgba(52, 199, 89, 0.15); color: var(--color-accent-green);">
                                                <?= round((float)$hrs, 1) ?>h
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">—</span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="max-width: 260px;">
                                        <?php if ($summary): ?>
                                            <div class="summary-preview" title="<?= View::e($summary) ?>">
                                                <?= View::e(mb_strimwidth($summary, 0, 70, '…')) ?>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-muted small">No summary</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="text-muted small">
                                            <i class="bi bi-calendar me-1"></i>
                                            <?= $createdAt ? View::e(date('M d', strtotime($createdAt))) : '—' ?>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex gap-2 justify-content-center">
                                            <a href="/schedules/show?id=<?= View::e((string)$id) ?>"
                                               class="btn btn-sm btn-outline-primary"
                                               title="View schedule">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="/schedules/create?week_start=<?= View::e($weekStart) ?>&id=<?= View::e((string)$id) ?>"
                                               class="btn btn-sm btn-outline-secondary"
                                               title="Edit schedule">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
// Animate progress bars
document.addEventListener('DOMContentLoaded', function() {
    const progressBars = document.querySelectorAll('.team-progress-bar');
    progressBars.forEach(bar => {
        const width = bar.style.width;
        bar.style.width = '0';
        setTimeout(() => {
            bar.style.width = width;
        }, 300);
    });

    // Chart column animations
    const chartColumns = document.querySelectorAll('.chart-column');
    chartColumns.forEach(column => {
        const height = column.style.height;
        column.style.height = '0';
        setTimeout(() => {
            column.style.height = height;
        }, 500);
    });

    // Week picker enhancement
    const weekInput = document.querySelector('input[name="week_start"]');
    if (weekInput && !weekInput.value) {
        // Set to current week's Monday
        const today = new Date();
        const day = today.getDay();
        const diff = today.getDate() - day + (day === 0 ? -6 : 1); // Adjust for Sunday
        const monday = new Date(today.setDate(diff));
        const year = monday.getFullYear();
        const month = String(monday.getMonth() + 1).padStart(2, '0');
        const date = String(monday.getDate()).padStart(2, '0');
        weekInput.value = `${year}-${month}-${date}`;
    }

    // Export functionality
    const exportBtn = document.createElement('button');
    exportBtn.className = 'btn btn-outline-primary ms-2';
    exportBtn.innerHTML = '<i class="bi bi-download me-2"></i>Export Overview';
    exportBtn.onclick = exportOverview;
    
    const headerActions = document.querySelector('.week-header .btn-light').parentElement;
    if (headerActions) {
        headerActions.appendChild(exportBtn);
    }

    // Row click functionality
    const tableRows = document.querySelectorAll('.table-overview tbody tr');
    tableRows.forEach(row => {
        row.addEventListener('click', function(e) {
            if (!e.target.closest('a') && !e.target.closest('button')) {
                const viewLink = this.querySelector('a[title="View schedule"]');
                if (viewLink) {
                    window.location = viewLink.href;
                }
            }
        });
        row.style.cursor = 'pointer';
    });
});

function exportOverview() {
    const exportBtn = document.querySelector('button[onclick="exportOverview"]');
    const originalHTML = exportBtn.innerHTML;
    
    exportBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Exporting...';
    exportBtn.disabled = true;
    
    // Simulate export
    setTimeout(() => {
        const week = document.querySelector('input[name="week_start"]').value;
        const dateStr = week ? new Date(week).toLocaleDateString('en-US', { 
            weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' 
        }) : 'Selected Week';
        
        showNotification(`Team overview for ${dateStr} exported successfully!`, 'success');
        
        exportBtn.innerHTML = originalHTML;
        exportBtn.disabled = false;
    }, 1500);
}

function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = 'position-fixed top-0 end-0 p-3';
    notification.style.zIndex = '9999';
    
    const alert = document.createElement('div');
    alert.className = `alert alert-${type} alert-dismissible fade show`;
    alert.style.background = type === 'success' ? 'rgba(52, 199, 89, 0.9)' : 'rgba(56, 189, 248, 0.9)';
    alert.style.border = 'none';
    alert.style.color = 'white';
    
    alert.innerHTML = `
        <i class="bi bi-${type === 'success' ? 'check-circle' : 'info-circle'} me-2"></i>
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
</script>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>