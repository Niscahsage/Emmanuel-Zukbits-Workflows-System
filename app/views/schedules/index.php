<?php
// app/views/schedules/index.php
// index.php lists weekly schedules for the current user or for all users, depending on role.

/** @var array $user */
/** @var array $schedules */
/** @var array $filters */

use App\core\View;

$roleKey   = $user['role_key'] ?? 'developer';
$isManager = in_array($roleKey, ['director', 'super_admin', 'system_admin'], true);

$from  = $filters['from'] ?? ($_GET['from'] ?? '');
$to    = $filters['to']   ?? ($_GET['to']   ?? '');
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weekly Schedules | ZukBits Online</title>
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

        .gradient-text {
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .stats-card {
            background: var(--gradient-primary);
            border-radius: 12px;
            padding: 20px;
            color: white;
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
            right: 0;
            width: 100px;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1));
        }

        .filter-card {
            background: rgba(56, 189, 248, 0.05);
            border: 2px solid rgba(56, 189, 248, 0.2);
            border-radius: 12px;
        }

        .btn-primary {
            background: var(--gradient-primary);
            border: none;
            color: white;
            padding: 10px 24px;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px var(--shadow-blue);
            color: white;
        }

        .btn-outline-primary {
            border: 2px solid var(--color-accent-blue);
            color: var(--color-accent-blue);
            background: transparent;
            padding: 8px 20px;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-outline-primary:hover {
            background: var(--color-accent-blue);
            color: white;
            transform: translateY(-2px);
        }

        .table-schedules {
            --bs-table-bg: transparent;
            --bs-table-border-color: var(--color-border);
            --bs-table-striped-bg: rgba(56, 189, 248, 0.03);
            --bs-table-hover-bg: rgba(56, 189, 248, 0.08);
        }

        .table-schedules th {
            color: var(--color-text);
            font-weight: 600;
            padding: 20px 16px;
            border: none;
            background: linear-gradient(90deg, rgba(56, 189, 248, 0.1), rgba(168, 85, 247, 0.1));
            position: relative;
        }

        .table-schedules th::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 16px;
            right: 16px;
            height: 2px;
            background: var(--gradient-primary);
        }

        .table-schedules td {
            padding: 20px 16px;
            border-color: var(--color-border);
            color: var(--color-text);
            vertical-align: middle;
        }

        .table-schedules tbody tr {
            border-bottom: 1px solid var(--color-border);
            transition: all 0.3s ease;
        }

        .table-schedules tbody tr:last-child {
            border-bottom: none;
        }

        .table-schedules tbody tr:hover {
            background: rgba(56, 189, 248, 0.08);
            transform: translateX(4px);
        }

        .week-badge {
            background: rgba(56, 189, 248, 0.1);
            color: var(--color-accent-blue);
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border: 1px solid rgba(56, 189, 248, 0.2);
        }

        .staff-badge {
            background: rgba(255, 200, 87, 0.1);
            color: var(--color-accent);
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .hour-badge {
            background: rgba(52, 199, 89, 0.1);
            color: var(--color-accent-green);
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.9rem;
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

        .empty-state {
            text-align: center;
            padding: 60px 20px;
        }

        .empty-state-icon {
            font-size: 4rem;
            color: var(--color-border);
            margin-bottom: 20px;
            opacity: 0.5;
        }

        .quick-actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        @media (max-width: 768px) {
            .stats-card {
                padding: 15px;
            }
            
            .table-schedules {
                font-size: 0.9rem;
            }
            
            .table-schedules th,
            .table-schedules td {
                padding: 12px 8px;
            }
            
            .quick-actions {
                flex-direction: column;
                width: 100%;
            }
            
            .quick-actions .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 fw-bold gradient-text">Weekly Schedules</h1>
                    <p class="text-muted mb-0">
                        <i class="bi bi-calendar-week me-1"></i>
                        <?= $isManager ? 'View weekly plans across your team' : 'Your weekly plans by week' ?>
                    </p>
                </div>
                <div class="quick-actions">
                    <a href="/schedules/create" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>Plan This Week
                    </a>
                    <?php if ($isManager): ?>
                        <a href="/schedules/weekly-overview" class="btn btn-outline-primary">
                            <i class="bi bi-people me-2"></i>Team Overview
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Row -->
    <?php if (!empty($schedules)): ?>
    <div class="row mb-4">
        <?php
        $totalHours = 0;
        $totalTasks = 0;
        $uniqueWeeks = [];
        
        foreach ($schedules as $s) {
            $week = $s['week_start_date'] ?? $s['week_start'] ?? '';
            $hours = (float)($s['total_estimated_hours'] ?? $s['total_hours'] ?? 0);
            $tasks = (int)($s['items_count'] ?? 0);
            
            $totalHours += $hours;
            $totalTasks += $tasks;
            
            if ($week && !in_array($week, $uniqueWeeks)) {
                $uniqueWeeks[] = $week;
            }
        }
        ?>
        
        <div class="col-xl-3 col-lg-6 mb-4">
            <div class="stats-card">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="mb-2 opacity-75">Total Schedules</h6>
                        <h3 class="mb-0"><?= count($schedules) ?></h3>
                    </div>
                    <i class="bi bi-calendar-week fs-2 opacity-50"></i>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-lg-6 mb-4">
            <div class="glass-card p-4 h-100">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="mb-2 text-muted">Total Tasks</h6>
                        <h3 class="mb-0"><?= $totalTasks ?></h3>
                    </div>
                    <i class="bi bi-list-task fs-2 text-muted opacity-50"></i>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-lg-6 mb-4">
            <div class="glass-card p-4 h-100">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="mb-2 text-muted">Planned Hours</h6>
                        <h3 class="mb-0"><?= round($totalHours, 1) ?></h3>
                    </div>
                    <i class="bi bi-clock fs-2 text-muted opacity-50"></i>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-lg-6 mb-4">
            <div class="glass-card p-4 h-100">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="mb-2 text-muted">Unique Weeks</h6>
                        <h3 class="mb-0"><?= count($uniqueWeeks) ?></h3>
                    </div>
                    <i class="bi bi-calendar-range fs-2 text-muted opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Filters -->
    <?php if ($isManager): ?>
    <div class="glass-card p-4 mb-4 filter-card">
        <h3 class="h6 mb-3 gradient-text fw-bold">
            <i class="bi bi-funnel me-2"></i>Filter Schedules
        </h3>
        <form method="get" action="/schedules" class="row g-3">
            <div class="col-md-3">
                <label class="form-label small fw-medium">From Date</label>
                <input type="date" name="from" class="form-control" value="<?= View::e($from) ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-medium">To Date</label>
                <input type="date" name="to" class="form-control" value="<?= View::e($to) ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-medium">Team Member</label>
                <select class="form-select" name="user_id">
                    <option value="">All Members</option>
                    <!-- Dynamic options would go here -->
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <div class="d-flex gap-2 w-100">
                    <button type="submit" class="btn btn-primary flex-grow-1">
                        <i class="bi bi-search me-1"></i>Apply
                    </button>
                    <a href="/schedules" class="btn btn-outline-secondary px-3">
                        <i class="bi bi-arrow-clockwise"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>
    <?php endif; ?>

    <!-- Schedules Table -->
    <div class="glass-card p-4">
        <?php if (empty($schedules)): ?>
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="bi bi-calendar-week"></i>
                </div>
                <h4 class="text-muted mb-3">No schedules found</h4>
                <p class="text-muted mb-4">
                    Start planning your week by creating your first schedule
                </p>
                <a href="/schedules/create" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i>Create Your First Schedule
                </a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-schedules">
                    <thead>
                    <tr>
                        <th>Week</th>
                        <?php if ($isManager): ?>
                            <th>Team Member</th>
                        <?php endif; ?>
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
                        $ownerName = $s['user_name'] ?? $s['owner_name'] ?? $user['name'] ?? '';
                        $itemsCnt  = (int)($s['items_count'] ?? 0);
                        $hours     = $s['total_estimated_hours'] ?? $s['total_hours'] ?? null;
                        $summary   = $s['summary_plan'] ?? '';
                        $createdAt = $s['created_at'] ?? '';
                        ?>
                        <tr>
                            <td>
                                <div class="week-badge">
                                    <i class="bi bi-calendar-week"></i>
                                    <?= View::e($weekStart) ?>
                                </div>
                            </td>
                            <?php if ($isManager): ?>
                                <td>
                                    <span class="staff-badge">
                                        <i class="bi bi-person-circle"></i>
                                        <?= View::e($ownerName) ?>
                                    </span>
                                </td>
                            <?php endif; ?>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-dark"><?= $itemsCnt ?></span>
                                    <span class="text-muted small">tasks</span>
                                </div>
                            </td>
                            <td>
                                <?php if ($hours !== null && $hours !== ''): ?>
                                    <span class="hour-badge">
                                        <i class="bi bi-clock"></i>
                                        <?= View::e((string)round((float)$hours, 1)) ?>h
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
// Filter functionality
document.addEventListener('DOMContentLoaded', function() {
    // Date range validation
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

    // Quick search functionality
    const searchInput = document.createElement('input');
    searchInput.type = 'search';
    searchInput.className = 'form-control';
    searchInput.placeholder = 'Search schedules...';
    searchInput.style.marginBottom = '20px';
    
    const headerRow = document.querySelector('.d-flex.justify-content-between.align-items-center');
    if (headerRow) {
        const container = headerRow.parentElement;
        container.insertBefore(searchInput, headerRow.nextSibling);
        
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('.table-schedules tbody tr');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });
    }

    // Row click functionality
    const tableRows = document.querySelectorAll('.table-schedules tbody tr');
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
</script>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>