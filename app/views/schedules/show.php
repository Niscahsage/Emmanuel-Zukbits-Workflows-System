<?php
// app/views/schedules/show.php
// show.php displays details of a specific weekly schedule and its items.

/** @var array $schedule */
/** @var array $items */
/** @var array $user */

use App\core\View;

$roleKey     = $user['role_key'] ?? 'developer';
$scheduleId  = (int)($schedule['id'] ?? 0);
$weekStart   = $schedule['week_start_date'] ?? $schedule['week_start'] ?? '';
$ownerName   = $schedule['user_name'] ?? $schedule['owner_name'] ?? $user['name'] ?? '';
$summaryPlan = $schedule['summary_plan'] ?? '';
$createdAt   = $schedule['created_at'] ?? '';
$updatedAt   = $schedule['updated_at'] ?? '';

$totalItems = count($items);
$totalHours = 0;
$hoursByDay = [];

foreach ($items as $row) {
    $h = $row['estimated_hours'] ?? $row['hours'] ?? 0;
    $totalHours += (float)$h;
    
    $day = $row['day_of_week'] ?? 'any';
    if (!isset($hoursByDay[$day])) {
        $hoursByDay[$day] = 0;
    }
    $hoursByDay[$day] += (float)$h;
}
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weekly Schedule | ZukBits Online</title>
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
                radial-gradient(circle at 15% 20%, rgba(56, 189, 248, 0.07) 0%, transparent 30%),
                radial-gradient(circle at 85% 80%, rgba(168, 85, 247, 0.07) 0%, transparent 30%);
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

        .schedule-header {
            background: var(--gradient-primary);
            color: white;
            padding: 20px 30px;
            border-radius: 16px;
            margin-bottom: 30px;
            position: relative;
            overflow: hidden;
        }

        .schedule-header::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 120px;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1));
        }

        .stats-circle {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: var(--gradient-primary);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            position: relative;
            margin: 0 auto;
        }

        .stats-circle::before {
            content: '';
            position: absolute;
            inset: 4px;
            background: var(--color-surface);
            border-radius: 50%;
            z-index: 1;
        }

        .stats-circle span {
            position: relative;
            z-index: 2;
        }

        .stats-circle .value {
            font-size: 1.2rem;
            font-weight: 700;
        }

        .stats-circle .label {
            font-size: 0.7rem;
            opacity: 0.9;
        }

        .day-card {
            background: rgba(56, 189, 248, 0.05);
            border: 2px solid rgba(56, 189, 248, 0.1);
            border-radius: 12px;
            padding: 20px;
            transition: all 0.3s ease;
        }

        .day-card:hover {
            transform: translateY(-5px);
            background: rgba(56, 189, 248, 0.1);
        }

        .day-card.active {
            background: var(--gradient-primary);
            color: white;
        }

        .day-card.active .day-hours {
            background: rgba(255, 255, 255, 0.2);
        }

        .day-name {
            font-size: 0.9rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            opacity: 0.8;
        }

        .day-hours {
            background: rgba(56, 189, 248, 0.15);
            color: var(--color-accent-blue);
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .table-tasks {
            --bs-table-bg: transparent;
            --bs-table-border-color: var(--color-border);
        }

        .table-tasks th {
            background: linear-gradient(90deg, rgba(56, 189, 248, 0.1), rgba(168, 85, 247, 0.1));
            color: var(--color-text);
            font-weight: 600;
            padding: 20px;
            border: none;
            position: relative;
        }

        .table-tasks th::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 20px;
            right: 20px;
            height: 2px;
            background: var(--gradient-primary);
        }

        .table-tasks td {
            padding: 20px;
            border-color: var(--color-border);
            color: var(--color-text);
            vertical-align: middle;
        }

        .table-tasks tbody tr {
            border-bottom: 1px solid rgba(148, 163, 253, 0.1);
            transition: all 0.3s ease;
        }

        .table-tasks tbody tr:last-child {
            border-bottom: none;
        }

        .table-tasks tbody tr:hover {
            background: rgba(56, 189, 248, 0.05);
        }

        .task-day {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 12px;
            background: rgba(56, 189, 248, 0.1);
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .task-hours {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            background: rgba(52, 199, 89, 0.1);
            color: var(--color-accent-green);
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.9rem;
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
            transform: translateY(-2px);
        }

        .summary-box {
            background: linear-gradient(135deg, rgba(56, 189, 248, 0.1), rgba(168, 85, 247, 0.05));
            border-left: 4px solid var(--color-accent-blue);
            padding: 20px;
            border-radius: 12px;
            line-height: 1.6;
        }

        .time-info {
            display: flex;
            align-items: center;
            gap: 12px;
            color: var(--color-text-muted);
            font-size: 0.85rem;
        }

        .time-info i {
            width: 20px;
            text-align: center;
        }

        @media (max-width: 768px) {
            .schedule-header {
                padding: 15px;
                text-align: center;
            }
            
            .table-tasks {
                font-size: 0.9rem;
            }
            
            .table-tasks th,
            .table-tasks td {
                padding: 12px 8px;
            }
            
            .day-cards-container {
                overflow-x: auto;
                padding-bottom: 10px;
            }
            
            .day-cards-row {
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
            <div class="schedule-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3 mb-2 fw-bold">Weekly Schedule</h1>
                        <p class="mb-0 opacity-90">
                            <i class="bi bi-calendar-week me-2"></i>
                            Week starting <strong><?= View::e($weekStart) ?></strong>
                            <span class="mx-2">•</span>
                            <i class="bi bi-person-circle me-1"></i>
                            <?= View::e($ownerName) ?>
                        </p>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="/schedules/create?week_start=<?= View::e($weekStart) ?>&id=<?= View::e((string)$scheduleId) ?>"
                           class="btn btn-sm btn-light">
                            <i class="bi bi-pencil me-1"></i>Edit
                        </a>
                        <a href="/reports/create?schedule_id=<?= View::e((string)$scheduleId) ?>"
                           class="btn btn-sm btn-light">
                            <i class="bi bi-file-text me-1"></i>Submit Report
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Row -->
    <div class="row mb-4">
        <div class="col-xl-3 col-lg-6 mb-4">
            <div class="glass-card p-4 h-100">
                <div class="text-center">
                    <div class="stats-circle mb-3">
                        <span class="value"><?= $totalItems ?></span>
                        <span class="label">TASKS</span>
                    </div>
                    <h6 class="text-muted mb-0">Total Tasks</h6>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-lg-6 mb-4">
            <div class="glass-card p-4 h-100">
                <div class="text-center">
                    <div class="stats-circle mb-3">
                        <span class="value"><?= round($totalHours, 1) ?></span>
                        <span class="label">HOURS</span>
                    </div>
                    <h6 class="text-muted mb-0">Planned Hours</h6>
                </div>
            </div>
        </div>
        
        <div class="col-xl-6 mb-4">
            <div class="glass-card p-4 h-100">
                <div class="d-flex align-items-center h-100">
                    <div class="flex-grow-1">
                        <div class="time-info mb-2">
                            <i class="bi bi-calendar-plus"></i>
                            <span>Created: <?= $createdAt ? View::e(date('M d, Y', strtotime($createdAt))) : '—' ?></span>
                        </div>
                        <div class="time-info">
                            <i class="bi bi-arrow-clockwise"></i>
                            <span>Updated: <?= $updatedAt ? View::e(date('M d, Y', strtotime($updatedAt))) : '—' ?></span>
                        </div>
                    </div>
                    <div class="text-end">
                        <div class="d-flex gap-2 flex-wrap justify-content-end">
                            <a href="/schedules" class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-arrow-left me-1"></i>All Schedules
                            </a>
                            <button class="btn btn-sm btn-outline-primary" onclick="exportSchedule()">
                                <i class="bi bi-download me-1"></i>Export
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Days Distribution -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="glass-card p-4">
                <h3 class="h6 mb-3 gradient-text fw-bold">
                    <i class="bi bi-calendar3 me-2"></i>Hours by Day
                </h3>
                <div class="day-cards-container">
                    <div class="row g-3 day-cards-row">
                        <?php
                        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
                        foreach ($days as $day):
                            $hours = $hoursByDay[$day] ?? 0;
                        ?>
                        <div class="col-md">
                            <div class="day-card text-center <?= $hours > 0 ? 'active' : '' ?>">
                                <div class="day-name mb-2">
                                    <?= ucfirst($day) ?>
                                </div>
                                <div class="day-hours">
                                    <?= round($hours, 1) ?>h
                                </div>
                                <?php if ($hours > 0): ?>
                                    <div class="mt-2 small opacity-75">
                                        <?= round(($hours / $totalHours) * 100, 1) ?>%
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        
                        <!-- Any Day -->
                        <div class="col-md">
                            <div class="day-card text-center <?= ($hoursByDay['any'] ?? 0) > 0 ? 'active' : '' ?>">
                                <div class="day-name mb-2">
                                    Any Day
                                </div>
                                <div class="day-hours">
                                    <?= round(($hoursByDay['any'] ?? 0), 1) ?>h
                                </div>
                                <?php if (($hoursByDay['any'] ?? 0) > 0): ?>
                                    <div class="mt-2 small opacity-75">
                                        <?= round((($hoursByDay['any'] ?? 0) / $totalHours) * 100, 1) ?>%
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="glass-card p-4">
                <h3 class="h6 mb-3 gradient-text fw-bold">
                    <i class="bi bi-card-text me-2"></i>Weekly Focus
                </h3>
                <?php if ($summaryPlan): ?>
                    <div class="summary-box">
                        <?= nl2br(View::e($summaryPlan)) ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="bi bi-card-text fs-1 text-muted mb-3 d-block"></i>
                        <p class="text-muted mb-0">No summary recorded for this schedule</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Tasks Table -->
    <div class="row">
        <div class="col-12">
            <div class="glass-card p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3 class="h6 mb-0 gradient-text fw-bold">
                        <i class="bi bi-list-task me-2"></i>Planned Tasks
                    </h3>
                    <div class="text-muted small">
                        <?= $totalItems ?> task<?= $totalItems !== 1 ? 's' : '' ?> • <?= round($totalHours, 1) ?> hours
                    </div>
                </div>

                <?php if (empty($items)): ?>
                    <div class="text-center py-5">
                        <i class="bi bi-calendar-x fs-1 text-muted mb-3 d-block"></i>
                        <h4 class="text-muted mb-3">No tasks in this schedule</h4>
                        <p class="text-muted mb-4">Add tasks to start planning your week</p>
                        <a href="/schedules/create?week_start=<?= View::e($weekStart) ?>&id=<?= View::e((string)$scheduleId) ?>"
                           class="btn btn-primary">
                            <i class="bi bi-plus-circle me-2"></i>Add Tasks
                        </a>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-tasks">
                            <thead>
                            <tr>
                                <th style="width:36%;">Task Description</th>
                                <th style="width:20%;">Project ID</th>
                                <th style="width:18%;">Day</th>
                                <th style="width:10%;">Hours</th>
                                <th style="width:16%;">Created</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($items as $row): ?>
                                <?php
                                $desc      = $row['description'] ?? '';
                                $projId    = $row['project_id'] ?? '';
                                $day       = $row['day_of_week'] ?? '';
                                $hrs       = $row['estimated_hours'] ?? $row['hours'] ?? '';
                                $createdAtRow = $row['created_at'] ?? '';
                                ?>
                                <tr>
                                    <td class="fw-medium"><?= View::e($desc) ?></td>
                                    <td>
                                        <?php if ($projId !== ''): ?>
                                            <span class="badge bg-dark">#<?= View::e((string)$projId) ?></span>
                                        <?php else: ?>
                                            <span class="text-muted">—</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="task-day">
                                            <i class="bi bi-calendar-day"></i>
                                            <?= $day ? View::e(ucfirst($day)) : 'Any' ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="task-hours">
                                            <i class="bi bi-clock"></i>
                                            <?= $hrs !== '' ? round((float)$hrs, 1) : '—' ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-muted small">
                                            <i class="bi bi-calendar me-1"></i>
                                            <?= $createdAtRow ? View::e(date('M d', strtotime($createdAtRow))) : '—' ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>

                <!-- Action Buttons -->
                <div class="d-flex justify-content-between align-items-center mt-4 pt-4 border-top border-color">
                    <div>
                        <a href="/schedules" class="btn-action btn-action-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Back to Schedules
                        </a>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="/schedules/create?week_start=<?= View::e($weekStart) ?>&id=<?= View::e((string)$scheduleId) ?>"
                           class="btn-action btn-action-secondary">
                            <i class="bi bi-pencil me-1"></i>Edit Schedule
                        </a>
                        <a href="/reports/create?schedule_id=<?= View::e((string)$scheduleId) ?>"
                           class="btn-action btn-action-primary">
                            <i class="bi bi-file-text me-1"></i>Submit Weekly Report
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function exportSchedule() {
    // Show loading state
    const exportBtn = document.querySelector('button[onclick="exportSchedule()"]');
    const originalHTML = exportBtn.innerHTML;
    
    exportBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Exporting...';
    exportBtn.disabled = true;
    
    // Simulate export process
    setTimeout(() => {
        exportBtn.innerHTML = '<i class="bi bi-check-circle me-1"></i>Exported!';
        exportBtn.className = 'btn btn-sm btn-success';
        
        // Show notification
        showNotification('Schedule exported successfully!', 'success');
        
        // Reset button after 2 seconds
        setTimeout(() => {
            exportBtn.innerHTML = originalHTML;
            exportBtn.className = 'btn btn-sm btn-outline-primary';
            exportBtn.disabled = false;
        }, 2000);
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

// Add interactivity to day cards
document.addEventListener('DOMContentLoaded', function() {
    const dayCards = document.querySelectorAll('.day-card');
    dayCards.forEach(card => {
        card.addEventListener('click', function() {
            const day = this.querySelector('.day-name').textContent.toLowerCase().trim();
            if (day === 'any day') day = 'any';
            
            // Highlight active day
            dayCards.forEach(c => c.classList.remove('active-highlight'));
            this.classList.add('active-highlight');
            
            // Filter tasks by day
            filterTasksByDay(day);
        });
    });

    // Task row click functionality
    const taskRows = document.querySelectorAll('.table-tasks tbody tr');
    taskRows.forEach(row => {
        row.addEventListener('click', function(e) {
            if (!e.target.closest('a') && !e.target.closest('button')) {
                // Highlight the row
                taskRows.forEach(r => r.classList.remove('selected'));
                this.classList.add('selected');
                
                // Show task details
                const taskDesc = this.querySelector('td:first-child').textContent;
                const taskHours = this.querySelector('.task-hours').textContent;
                const taskDay = this.querySelector('.task-day').textContent;
                
                console.log('Selected task:', taskDesc, taskHours, taskDay);
            }
        });
    });
});

function filterTasksByDay(day) {
    const taskRows = document.querySelectorAll('.table-tasks tbody tr');
    
    taskRows.forEach(row => {
        const rowDay = row.querySelector('.task-day').textContent.toLowerCase();
        const matches = (day === 'any' && rowDay === 'any') || 
                       (day !== 'any' && rowDay.includes(day));
        
        row.style.display = matches ? '' : 'none';
    });
    
    // Update count
    const visibleRows = document.querySelectorAll('.table-tasks tbody tr[style*="display: none"]');
    const visibleCount = taskRows.length - visibleRows.length;
    
    const countElement = document.querySelector('.text-muted.small');
    if (countElement) {
        const totalHours = <?= round($totalHours, 1) ?>;
        const percentage = day === 'any' ? 100 : 
            (<?= json_encode($hoursByDay) ?>[day] || 0) / totalHours * 100;
        
        countElement.innerHTML = `
            ${visibleCount} task${visibleCount !== 1 ? 's' : ''} • 
            ${(<?= json_encode($hoursByDay) ?>[day] || 0).toFixed(1)} hours •
            ${percentage.toFixed(1)}% of total
        `;
    }
}
</script>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>