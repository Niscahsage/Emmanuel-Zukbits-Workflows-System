<?php
// app/views/reports/create.php
// create.php displays a form to submit a weekly report linked to a schedule.

/** @var array $schedule */
/** @var array $items */

use App\core\View;

$scheduleId = (int)($schedule['id'] ?? 0);
$weekStart  = $schedule['week_start_date'] ?? $schedule['week_start'] ?? '';
$summaryPlan = $schedule['summary_plan'] ?? '';
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Weekly Report | ZukBits Online</title>
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
        }

        .glass-card {
            background: var(--color-surface);
            border: 1px solid var(--color-border);
            backdrop-filter: blur(10px);
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        }

        .glass-card-alt {
            background: var(--color-surface-alt);
            border: 1px solid var(--color-border);
            backdrop-filter: blur(10px);
            border-radius: 12px;
        }

        .gradient-border {
            position: relative;
            background: linear-gradient(var(--color-surface), var(--color-surface)) padding-box,
                        var(--gradient-primary) border-box;
            border: 2px solid transparent;
        }

        .gradient-text {
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .accent-btn {
            background: var(--gradient-primary);
            border: none;
            color: white;
            padding: 10px 24px;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px var(--shadow-blue);
        }

        .accent-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px var(--shadow-blue);
            color: white;
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

        .form-control::placeholder {
            color: var(--color-text-muted);
            opacity: 0.7;
        }

        .table-custom {
            background: var(--color-surface-alt);
            border-radius: 8px;
            overflow: hidden;
        }

        .table-custom thead {
            background: linear-gradient(90deg, rgba(56, 189, 248, 0.1), rgba(168, 85, 247, 0.1));
            border-bottom: 2px solid var(--color-border);
        }

        .table-custom th {
            color: var(--color-text);
            font-weight: 600;
            padding: 12px 16px;
            border: none;
        }

        .table-custom td {
            padding: 12px 16px;
            border-color: var(--color-border);
            color: var(--color-text-muted);
        }

        .table-custom tbody tr {
            transition: all 0.2s ease;
        }

        .table-custom tbody tr:hover {
            background: rgba(56, 189, 248, 0.05);
        }

        .badge-status {
            background: rgba(56, 189, 248, 0.15);
            color: var(--color-accent-blue);
            padding: 4px 12px;
            border-radius: 20px;
            font-weight: 500;
        }

        .week-indicator {
            background: var(--gradient-primary);
            color: white;
            padding: 8px 16px;
            border-radius: 8px;
            display: inline-block;
            font-weight: 600;
        }

        .nav-tabs-custom {
            border-bottom: 1px solid var(--color-border);
        }

        .nav-tabs-custom .nav-link {
            color: var(--color-text-muted);
            border: none;
            padding: 12px 24px;
            border-radius: 8px 8px 0 0;
            margin-right: 4px;
        }

        .nav-tabs-custom .nav-link.active {
            background: var(--gradient-primary);
            color: white;
            border: none;
        }

        @media (max-width: 768px) {
            .table-custom {
                font-size: 0.9rem;
            }
            
            .glass-card {
                margin-left: -12px;
                margin-right: -12px;
                border-radius: 0;
                border-left: none;
                border-right: none;
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
                    <h1 class="h3 mb-2 fw-bold">Submit Weekly Report</h1>
                    <p class="text-muted mb-0">
                        Week starting <span class="week-indicator"><?= View::e($weekStart) ?></span>
                    </p>
                </div>
                <div>
                    <a href="/schedules/show?id=<?= View::e((string)$scheduleId) ?>" 
                       class="btn btn-outline-secondary me-2">
                        <i class="bi bi-arrow-left me-1"></i> Back
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Left Column - Plan Snapshot -->
        <div class="col-lg-4 mb-4">
            <div class="glass-card p-4 h-100">
                <h3 class="h5 mb-3 gradient-text fw-bold">Weekly Plan Snapshot</h3>
                <?php if ($summaryPlan): ?>
                    <div class="glass-card-alt p-3">
                        <p class="mb-0" style="white-space: pre-wrap; line-height: 1.6;">
                            <?= nl2br(View::e($summaryPlan)) ?>
                        </p>
                    </div>
                <?php else: ?>
                    <div class="alert alert-secondary mb-0">
                        <i class="bi bi-info-circle me-2"></i>
                        No summary was recorded for this schedule.
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Right Column - Main Form -->
        <div class="col-lg-8">
            <form method="post" action="/reports/store">
                <input type="hidden" name="schedule_id" value="<?= View::e((string)$scheduleId) ?>">
                <input type="hidden" name="week_start" value="<?= View::e($weekStart) ?>">

                <!-- Tasks Section -->
                <div class="glass-card p-4 mb-4">
                    <h3 class="h5 mb-3 gradient-text fw-bold">Planned Tasks & Outcomes</h3>
                    
                    <?php if (empty($items)): ?>
                        <div class="alert alert-secondary">
                            <i class="bi bi-info-circle me-2"></i>
                            No tasks were defined in this schedule.
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-custom">
                                <thead>
                                <tr>
                                    <th style="width:35%;">Task</th>
                                    <th style="width:15%;">Project</th>
                                    <th style="width:10%;">Hours</th>
                                    <th style="width:18%;">Status</th>
                                    <th style="width:22%;">Comment</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($items as $row): ?>
                                    <?php
                                    $sid       = (int)($row['id'] ?? 0);
                                    $desc      = $row['description'] ?? '';
                                    $proj      = $row['project_name'] ?? '';
                                    $hours     = $row['estimated_hours'] ?? '';
                                    ?>
                                    <tr>
                                        <td>
                                            <input type="hidden" name="item_schedule_id[]" 
                                                   value="<?= View::e((string)$sid) ?>">
                                            <div class="fw-medium"><?= View::e($desc) ?></div>
                                        </td>
                                        <td>
                                            <?php if ($proj): ?>
                                                <span class="badge-status"><?= View::e($proj) ?></span>
                                            <?php else: ?>
                                                <span class="text-muted">—</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($hours !== ''): ?>
                                                <span class="badge bg-dark"><?= View::e((string)$hours) ?>h</span>
                                            <?php else: ?>
                                                <span class="text-muted">—</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <select name="item_status[]" class="form-select form-select-sm" required>
                                                <option value="">Select...</option>
                                                <option value="completed">✅ Completed</option>
                                                <option value="in_progress">🔄 In Progress</option>
                                                <option value="blocked">⛔ Blocked</option>
                                                <option value="not_started">⏳ Not Started</option>
                                            </select>
                                        </td>
                                        <td>
                                            <textarea
                                                name="item_comment[]"
                                                rows="2"
                                                class="form-control form-control-sm"
                                                placeholder="Notes / outcomes"
                                            ></textarea>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Weekly Summary Section -->
                <div class="glass-card p-4 mb-4">
                    <h3 class="h5 mb-3 gradient-text fw-bold">Weekly Summary</h3>
                    
                    <div class="mb-4">
                        <label class="form-label fw-medium mb-2">
                            <i class="bi bi-file-text me-2"></i>Overall Summary *
                        </label>
                        <textarea
                            name="overall_summary"
                            rows="4"
                            class="form-control"
                            required
                            placeholder="Highlight what you accomplished this week, key outcomes, and overall progress."
                        ></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium mb-2">
                                <i class="bi bi-exclamation-triangle me-2"></i>Challenges / Blockers
                            </label>
                            <textarea
                                name="challenges"
                                rows="3"
                                class="form-control"
                                placeholder="Describe any challenges, blockers, or risks encountered."
                            ></textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium mb-2">
                                <i class="bi bi-life-preserver me-2"></i>Support Needed
                            </label>
                            <textarea
                                name="support_needed"
                                rows="3"
                                class="form-control"
                                placeholder="What support do you need from the director or admins?"
                            ></textarea>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <a href="/schedules/show?id=<?= View::e((string)$scheduleId) ?>" 
                       class="btn btn-outline-secondary px-4">
                        <i class="bi bi-arrow-left me-2"></i>Back to Schedule
                    </a>
                    <button type="submit" class="btn accent-btn px-4">
                        <i class="bi bi-send me-2"></i>Submit Weekly Report
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Add some interactivity
document.addEventListener('DOMContentLoaded', function() {
    // Add focus styles to form controls
    const formControls = document.querySelectorAll('.form-control, .form-select');
    formControls.forEach(control => {
        control.addEventListener('focus', function() {
            this.parentElement.classList.add('focused');
        });
        
        control.addEventListener('blur', function() {
            this.parentElement.classList.remove('focused');
        });
    });

    // Status selection enhancement
    const statusSelects = document.querySelectorAll('select[name="item_status[]"]');
    statusSelects.forEach(select => {
        select.addEventListener('change', function() {
            const selected = this.value;
            if (selected) {
                this.style.background = 'rgba(56, 189, 248, 0.1)';
                this.style.borderColor = 'var(--color-accent-blue)';
            } else {
                this.style.background = '';
                this.style.borderColor = '';
            }
        });
    });

    // Character counter for textareas
    const textareas = document.querySelectorAll('textarea[name="overall_summary"]');
    textareas.forEach(textarea => {
        const counter = document.createElement('div');
        counter.className = 'text-end small text-muted mt-1';
        counter.textContent = '0/1000 characters';
        textarea.parentElement.appendChild(counter);
        
        textarea.addEventListener('input', function() {
            const count = this.value.length;
            counter.textContent = `${count}/1000 characters`;
            
            if (count > 800) {
                counter.style.color = 'var(--color-accent)';
            } else {
                counter.style.color = 'var(--color-text-muted)';
            }
        });
    });
});
</script>

<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>