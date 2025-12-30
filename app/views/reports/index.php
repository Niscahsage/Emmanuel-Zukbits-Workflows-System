<?php
// app/views/reports/index.php
// index.php lists weekly reports submitted by the current user or by the team, depending on role.

/** @var array $user */
/** @var array $reports */

use App\core\View;

$roleKey = $user['role_key'] ?? 'developer';
$isManager = in_array($roleKey, ['director', 'super_admin', 'system_admin'], true);
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weekly Reports | ZukBits Online</title>
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
                radial-gradient(circle at 10% 20%, rgba(56, 189, 248, 0.07) 0%, transparent 20%),
                radial-gradient(circle at 90% 80%, rgba(168, 85, 247, 0.07) 0%, transparent 20%);
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
        }

        .stats-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        }

        .nav-tabs-custom {
            border-bottom: 2px solid var(--color-border);
            gap: 4px;
        }

        .nav-tabs-custom .nav-link {
            color: var(--color-text-muted);
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            margin-right: 4px;
            background: transparent;
            transition: all 0.3s ease;
        }

        .nav-tabs-custom .nav-link:hover {
            background: rgba(56, 189, 248, 0.1);
            color: var(--color-accent-blue);
        }

        .nav-tabs-custom .nav-link.active {
            background: var(--gradient-primary);
            color: white;
            border: none;
            box-shadow: 0 4px 15px var(--shadow-blue);
        }

        .table-dashboard {
            --bs-table-bg: transparent;
            --bs-table-border-color: var(--color-border);
            --bs-table-striped-bg: rgba(56, 189, 248, 0.03);
            --bs-table-hover-bg: rgba(56, 189, 248, 0.08);
        }

        .table-dashboard th {
            color: var(--color-text-muted);
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 0.5px;
            border: none;
            padding: 16px;
            background: rgba(56, 189, 248, 0.05);
        }

        .table-dashboard td {
            padding: 20px 16px;
            border-color: var(--color-border);
            color: var(--color-text);
            vertical-align: middle;
        }

        .table-dashboard tbody tr {
            transition: all 0.3s ease;
            border-bottom: 1px solid var(--color-border);
        }

        .table-dashboard tbody tr:last-child {
            border-bottom: none;
        }

        .table-dashboard tbody tr:hover {
            background: rgba(56, 189, 248, 0.08);
            transform: translateX(4px);
        }

        .badge-status {
            padding: 6px 16px;
            border-radius: 20px;
            font-weight: 500;
            font-size: 0.8rem;
        }

        .badge-submitted {
            background: rgba(148, 163, 253, 0.15);
            color: #94a3fd;
        }

        .badge-reviewed {
            background: rgba(255, 200, 87, 0.15);
            color: var(--color-accent);
        }

        .badge-approved {
            background: rgba(52, 199, 89, 0.15);
            color: var(--color-accent-green);
        }

        .week-badge {
            background: rgba(56, 189, 248, 0.1);
            color: var(--color-accent-blue);
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: 600;
            display: inline-block;
            border: 1px solid rgba(56, 189, 248, 0.2);
        }

        .btn-outline-primary {
            border: 2px solid var(--color-accent-blue);
            color: var(--color-accent-blue);
            background: transparent;
            padding: 10px 24px;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-outline-primary:hover {
            background: var(--color-accent-blue);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px var(--shadow-blue);
        }

        .summary-preview {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            color: var(--color-text-muted);
            line-height: 1.5;
        }

        .pagination-custom .page-link {
            background: var(--color-surface);
            border-color: var(--color-border);
            color: var(--color-text);
            margin: 0 4px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .pagination-custom .page-link:hover {
            background: rgba(56, 189, 248, 0.1);
            border-color: var(--color-accent-blue);
        }

        .pagination-custom .page-item.active .page-link {
            background: var(--gradient-primary);
            border-color: transparent;
            color: white;
        }

        @media (max-width: 768px) {
            .stats-card {
                padding: 15px;
            }
            
            .table-dashboard {
                font-size: 0.9rem;
            }
            
            .table-dashboard th,
            .table-dashboard td {
                padding: 12px 8px;
            }
            
            .nav-tabs-custom .nav-link {
                padding: 8px 16px;
                font-size: 0.9rem;
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
                    <h1 class="h3 fw-bold gradient-text">Weekly Reports</h1>
                    <p class="text-muted mb-0">
                        <i class="bi bi-clock-history me-1"></i>
                        <?= $isManager ? 'Overview of weekly reports across your team' : 'Weekly reports you have submitted' ?>
                    </p>
                </div>
                <div class="d-flex gap-2">
                    <?php if ($isManager): ?>
                        <a href="/reports/team-overview" class="btn btn-outline-primary">
                            <i class="bi bi-people me-2"></i>Team Overview
                        </a>
                    <?php endif; ?>
                    <button class="btn" style="background: var(--color-surface); color: var(--color-text);">
                        <i class="bi bi-funnel"></i>
                        <span class="d-none d-md-inline">Filter</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <?php if (!empty($reports)): ?>
    <div class="row mb-4">
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="stats-card">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="mb-2 opacity-75">Total Reports</h6>
                        <h3 class="mb-0"><?= count($reports) ?></h3>
                    </div>
                    <i class="bi bi-file-text fs-2 opacity-50"></i>
                </div>
            </div>
        </div>
        
        <?php
        $approved = 0;
        $reviewed = 0;
        $submitted = 0;
        foreach ($reports as $r) {
            $status = $r['status'] ?? 'submitted';
            if ($status === 'approved') $approved++;
            elseif ($status === 'reviewed') $reviewed++;
            else $submitted++;
        }
        ?>
        
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="glass-card p-3">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="mb-2 text-muted">Approved</h6>
                        <h3 class="mb-0 text-success"><?= $approved ?></h3>
                    </div>
                    <i class="bi bi-check-circle fs-2 text-success opacity-50"></i>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="glass-card p-3">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="mb-2 text-muted">Reviewed</h6>
                        <h3 class="mb-0 text-warning"><?= $reviewed ?></h3>
                    </div>
                    <i class="bi bi-eye fs-2 text-warning opacity-50"></i>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="glass-card p-3">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="mb-2 text-muted">Pending</h6>
                        <h3 class="mb-0 text-muted"><?= $submitted ?></h3>
                    </div>
                    <i class="bi bi-clock fs-2 text-muted opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Reports Table -->
    <div class="glass-card p-4">
        <?php if (empty($reports)): ?>
            <div class="text-center py-5">
                <i class="bi bi-file-text fs-1 text-muted mb-3 d-block"></i>
                <h4 class="text-muted mb-3">No reports found</h4>
                <p class="text-muted mb-4">Submit your first weekly report to get started</p>
                <a href="/schedules" class="btn btn-outline-primary">
                    <i class="bi bi-plus-circle me-2"></i>Create Schedule
                </a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-dashboard">
                    <thead>
                    <tr>
                        <th>Week</th>
                        <?php if ($isManager): ?>
                            <th>Staff</th>
                        <?php endif; ?>
                        <th>Summary</th>
                        <th>Submitted At</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($reports as $r): ?>
                        <?php
                        $id        = (int)($r['id'] ?? 0);
                        $week      = $r['week_start_date'] ?? $r['week_start'] ?? '';
                        $ownerName = $r['user_name'] ?? $r['owner_name'] ?? $user['name'] ?? '';
                        $summary   = $r['overall_summary'] ?? '';
                        $createdAt = $r['created_at'] ?? '';
                        $status    = $r['status'] ?? 'submitted';
                        ?>
                        <tr>
                            <td>
                                <div class="week-badge"><?= View::e($week) ?></div>
                            </td>

                            <?php if ($isManager): ?>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle bg-dark p-2 me-2" style="width: 36px; height: 36px;">
                                            <i class="bi bi-person text-center d-block"></i>
                                        </div>
                                        <div>
                                            <div class="fw-medium"><?= View::e($ownerName) ?></div>
                                        </div>
                                    </div>
                                </td>
                            <?php endif; ?>

                            <td style="max-width: 280px;">
                                <div class="summary-preview">
                                    <?= View::e(mb_strimwidth($summary, 0, 120, '…')) ?>
                                </div>
                            </td>

                            <td>
                                <div class="text-muted small">
                                    <i class="bi bi-calendar me-1"></i>
                                    <?= $createdAt ? View::e(date('M d, Y', strtotime($createdAt))) : '—' ?>
                                </div>
                            </td>

                            <td>
                                <?php if ($status === 'approved'): ?>
                                    <span class="badge-status badge-approved">
                                        <i class="bi bi-check-circle me-1"></i>Approved
                                    </span>
                                <?php elseif ($status === 'reviewed'): ?>
                                    <span class="badge-status badge-reviewed">
                                        <i class="bi bi-eye me-1"></i>Reviewed
                                    </span>
                                <?php else: ?>
                                    <span class="badge-status badge-submitted">
                                        <i class="bi bi-clock me-1"></i>Submitted
                                    </span>
                                <?php endif; ?>
                            </td>

                            <td>
                                <div class="d-flex gap-2">
                                    <a href="/reports/show?id=<?= View::e((string)$id) ?>"
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye me-1"></i>View
                                    </a>
                                    <?php if ($isManager && $status === 'submitted'): ?>
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
                <ul class="pagination pagination-custom justify-content-center">
                    <li class="page-item">
                        <a class="page-link" href="#">
                            <i class="bi bi-chevron-left"></i>
                        </a>
                    </li>
                    <li class="page-item active"><a class="page-link" href="#">1</a></li>
                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                    <li class="page-item">
                        <a class="page-link" href="#">
                            <i class="bi bi-chevron-right"></i>
                        </a>
                    </li>
                </ul>
            </nav>
        <?php endif; ?>
    </div>
</div>

<script>
function reviewReport(reportId) {
    if (confirm('Mark this report as reviewed?')) {
        // In a real implementation, you would make an AJAX call here
        fetch(`/reports/${reportId}/review`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }
}

// Add row hover effects
document.addEventListener('DOMContentLoaded', function() {
    const tableRows = document.querySelectorAll('.table-dashboard tbody tr');
    tableRows.forEach(row => {
        row.addEventListener('mouseenter', function() {
            this.style.cursor = 'pointer';
        });
        
        row.addEventListener('click', function(e) {
            if (!e.target.closest('a') && !e.target.closest('button')) {
                const link = this.querySelector('a.btn-outline-primary');
                if (link) {
                    window.location = link.href;
                }
            }
        });
    });

    // Filter functionality
    const filterBtn = document.querySelector('button[style*="background: var(--color-surface)"]');
    if (filterBtn) {
        filterBtn.addEventListener('click', function() {
            const filterModal = `
                <div class="modal fade" id="filterModal" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content" style="background: var(--color-surface); border-color: var(--color-border);">
                            <div class="modal-header border-bottom-0">
                                <h5 class="modal-title gradient-text">Filter Reports</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <!-- Add filter options here -->
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            const modalDiv = document.createElement('div');
            modalDiv.innerHTML = filterModal;
            document.body.appendChild(modalDiv);
            
            const modal = new bootstrap.Modal(modalDiv.querySelector('.modal'));
            modal.show();
        });
    }
});
</script>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>