<?php
// app/views/schedules/create.php
// create.php displays a form to create or update a weekly schedule.

/** @var array|null $schedule */
/** @var array|null $items */

use App\core\View;

$isEdit      = !empty($schedule) && !empty($schedule['id']);
$scheduleId  = $isEdit ? (int)$schedule['id'] : 0;
$weekStart   = $schedule['week_start_date'] ?? $schedule['week_start'] ?? ($_GET['week_start'] ?? '');
$summaryPlan = $schedule['summary_plan'] ?? '';
$actionUrl   = $isEdit ? '/schedules/update' : '/schedules/store';

// items: array of rows with id, project_id, project_name, description, estimated_hours, day_of_week
$rows = $items ?? [];
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $isEdit ? 'Edit Weekly Schedule' : 'Create Weekly Schedule' ?> | ZukBits Online</title>
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

        .gradient-border {
            position: relative;
            background: linear-gradient(var(--color-surface), var(--color-surface)) padding-box,
                        var(--gradient-primary) border-box;
            border: 2px solid transparent;
            border-radius: 12px;
        }

        .form-control, .form-select, .form-control-sm, .form-select-sm {
            background: var(--color-surface-alt);
            border: 1px solid var(--color-border);
            color: var(--color-text);
            border-radius: 8px;
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

        .btn-outline-secondary {
            border: 2px solid var(--color-border);
            color: var(--color-text-muted);
            background: transparent;
            padding: 10px 24px;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-outline-secondary:hover {
            background: rgba(56, 189, 248, 0.1);
            border-color: var(--color-accent-blue);
            color: var(--color-accent-blue);
        }

        .btn-outline-danger {
            border: 2px solid rgba(239, 68, 68, 0.3);
            color: #ef4444;
            background: transparent;
            padding: 6px 16px;
            border-radius: 8px;
            font-size: 0.8rem;
            transition: all 0.3s ease;
        }

        .btn-outline-danger:hover {
            background: rgba(239, 68, 68, 0.1);
            border-color: #ef4444;
        }

        .table-schedule {
            --bs-table-bg: transparent;
            --bs-table-border-color: var(--color-border);
        }

        .table-schedule thead {
            background: linear-gradient(90deg, rgba(56, 189, 248, 0.1), rgba(168, 85, 247, 0.1));
            border-bottom: 2px solid var(--color-border);
        }

        .table-schedule th {
            color: var(--color-text);
            font-weight: 600;
            padding: 16px;
            border: none;
            font-size: 0.85rem;
        }

        .table-schedule td {
            padding: 16px;
            border-color: var(--color-border);
            color: var(--color-text);
            vertical-align: middle;
        }

        .table-schedule tbody tr {
            transition: all 0.3s ease;
            border-bottom: 1px solid rgba(148, 163, 253, 0.1);
        }

        .table-schedule tbody tr:last-child {
            border-bottom: none;
        }

        .table-schedule tbody tr:hover {
            background: rgba(56, 189, 248, 0.05);
        }

        .week-picker {
            background: var(--color-surface-alt);
            border: 2px solid var(--color-border);
            border-radius: 8px;
            padding: 10px;
            transition: all 0.3s ease;
        }

        .week-picker:focus-within {
            border-color: var(--color-accent-blue);
            box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.2);
        }

        .task-input-group {
            position: relative;
        }

        .task-input-group textarea {
            min-height: 80px;
            resize: vertical;
        }

        .task-input-group:focus-within::before {
            content: '';
            position: absolute;
            left: -4px;
            top: 8px;
            bottom: 8px;
            width: 3px;
            background: var(--gradient-primary);
            border-radius: 3px;
        }

        .day-select {
            position: relative;
        }

        .day-select .form-select {
            appearance: none;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23c3c5d4' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 12px center;
            background-size: 16px 12px;
            padding-right: 40px;
        }

        .hour-input {
            position: relative;
        }

        .hour-input::after {
            content: 'hrs';
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--color-text-muted);
            font-size: 0.8rem;
        }

        .hour-input .form-control {
            padding-right: 40px;
        }

        .schedule-header {
            background: var(--gradient-primary);
            color: white;
            padding: 16px 24px;
            border-radius: 12px;
            margin-bottom: 24px;
            position: relative;
            overflow: hidden;
        }

        .schedule-header::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 100px;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1));
        }

        .action-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 0;
            border-top: 1px solid var(--color-border);
            margin-top: 20px;
        }

        @media (max-width: 768px) {
            .table-schedule {
                font-size: 0.9rem;
            }
            
            .table-schedule th,
            .table-schedule td {
                padding: 12px 8px;
            }
            
            .table-responsive {
                margin-left: -12px;
                margin-right: -12px;
                padding: 0 12px;
            }
            
            .action-bar {
                flex-direction: column;
                gap: 16px;
            }
            
            .action-bar .btn {
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
            <div class="schedule-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3 mb-2 fw-bold"><?= $isEdit ? 'Edit Weekly Schedule' : 'Create Weekly Schedule' ?></h1>
                        <p class="mb-0 opacity-90">
                            <i class="bi bi-calendar-week me-1"></i>
                            Plan your work for the selected week
                        </p>
                    </div>
                    <div class="d-flex align-items-center">
                        <span class="badge bg-dark bg-opacity-50 me-2"><?= count($rows) ?> tasks</span>
                        <?php if ($isEdit): ?>
                            <span class="badge bg-success bg-opacity-25 text-success">Editing</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form method="post" action="<?= View::e($actionUrl) ?>" id="schedule-form">
        <?php if ($isEdit): ?>
            <input type="hidden" name="id" value="<?= View::e((string)$scheduleId) ?>">
        <?php endif; ?>

        <div class="row">
            <!-- Left Column - Basic Info -->
            <div class="col-lg-4 mb-4">
                <div class="glass-card p-4 h-100">
                    <h3 class="h5 mb-3 gradient-text fw-bold">Schedule Details</h3>
                    
                    <div class="mb-4">
                        <label class="form-label fw-medium mb-2">
                            <i class="bi bi-calendar-plus me-2"></i>Week Starting *
                        </label>
                        <div class="week-picker">
                            <input
                                type="date"
                                name="week_start"
                                class="form-control border-0 bg-transparent p-0"
                                required
                                value="<?= View::e($weekStart) ?>"
                            >
                        </div>
                        <small class="text-muted mt-2 d-block">
                            <i class="bi bi-info-circle me-1"></i>
                            Use Monday (or your chosen anchor day) as the week start.
                        </small>
                    </div>

                    <div class="mb-0">
                        <label class="form-label fw-medium mb-2">
                            <i class="bi bi-card-text me-2"></i>Summary Plan
                        </label>
                        <div class="glass-card-alt p-3">
                            <textarea
                                name="summary_plan"
                                rows="4"
                                class="form-control bg-transparent border-0"
                                placeholder="Short description of key focus areas for this week..."
                                style="resize: none;"
                            ><?= View::e($summaryPlan) ?></textarea>
                        </div>
                        <small class="text-muted mt-2 d-block">
                            <i class="bi bi-lightbulb me-1"></i>
                            Optional: Outline your main goals and priorities
                        </small>
                    </div>
                </div>
            </div>

            <!-- Right Column - Tasks -->
            <div class="col-lg-8">
                <div class="glass-card p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h3 class="h5 mb-0 gradient-text fw-bold">Planned Tasks</h3>
                            <p class="text-muted mb-0 small">
                                Add tasks, link to projects, and estimate hours
                            </p>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="button" 
                                    class="btn btn-sm btn-outline-primary d-flex align-items-center"
                                    onclick="addScheduleRow()">
                                <i class="bi bi-plus-lg me-1"></i>Add Task
                            </button>
                            <button type="button" 
                                    class="btn btn-sm btn-outline-secondary d-flex align-items-center"
                                    onclick="calculateTotalHours()">
                                <i class="bi bi-calculator me-1"></i>Calculate
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-schedule" id="schedule-items-table">
                            <thead>
                            <tr>
                                <th style="width:26%;">Task Description</th>
                                <th style="width:20%;">Project ID</th>
                                <th style="width:20%;">Day of Week</th>
                                <th style="width:15%;">Estimated Hours</th>
                                <th style="width:10%;" class="text-center">Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if (!empty($rows)): ?>
                                <?php foreach ($rows as $row): ?>
                                    <?php
                                    $rowId   = $row['id'] ?? '';
                                    $projId  = $row['project_id'] ?? '';
                                    $desc    = $row['description'] ?? '';
                                    $hrs     = $row['estimated_hours'] ?? '';
                                    $day     = $row['day_of_week'] ?? '';
                                    ?>
                                    <tr>
                                        <td>
                                            <div class="task-input-group">
                                                <input type="hidden" name="item_id[]" value="<?= View::e((string)$rowId) ?>">
                                                <textarea
                                                    name="item_description[]"
                                                    rows="2"
                                                    class="form-control"
                                                    required
                                                    placeholder="Describe the task or activity..."
                                                ><?= View::e($desc) ?></textarea>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="position-relative">
                                                <input
                                                    type="number"
                                                    name="item_project_id[]"
                                                    class="form-control"
                                                    placeholder="Enter project ID"
                                                    value="<?= View::e((string)$projId) ?>"
                                                >
                                                <?php if ($projId): ?>
                                                    <small class="text-muted position-absolute start-0 bottom-0 translate-y-full mt-1">
                                                        <i class="bi bi-link-45deg me-1"></i>Linked
                                                    </small>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="day-select">
                                                <select name="item_day[]" class="form-select">
                                                    <?php
                                                    $days = ['','monday','tuesday','wednesday','thursday','friday','saturday','sunday'];
                                                    foreach ($days as $d):
                                                        ?>
                                                        <option value="<?= View::e($d) ?>"
                                                            <?= $d === $day ? 'selected' : '' ?>>
                                                            <?php if ($d === ''): ?>
                                                                <span class="text-muted">Any Day</span>
                                                            <?php else: ?>
                                                                <i class="bi bi-calendar-day me-1"></i><?= ucfirst($d) ?>
                                                            <?php endif; ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="hour-input">
                                                <input
                                                    type="number"
                                                    step="0.25"
                                                    min="0"
                                                    name="item_estimated_hours[]"
                                                    class="form-control"
                                                    value="<?= View::e((string)$hrs) ?>"
                                                    placeholder="0.00"
                                                    onchange="updateHourTotal()"
                                                >
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <button type="button"
                                                    class="btn btn-sm btn-outline-danger px-3"
                                                    onclick="removeScheduleRow(this)"
                                                    title="Remove task">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <!-- Initial empty row -->
                                <tr>
                                    <td>
                                        <div class="task-input-group">
                                            <input type="hidden" name="item_id[]" value="">
                                            <textarea
                                                name="item_description[]"
                                                rows="2"
                                                class="form-control"
                                                required
                                                placeholder="Describe the task or activity..."
                                            ></textarea>
                                        </div>
                                    </td>
                                    <td>
                                        <input
                                            type="number"
                                            name="item_project_id[]"
                                            class="form-control"
                                            placeholder="Enter project ID"
                                        >
                                    </td>
                                    <td>
                                        <div class="day-select">
                                            <select name="item_day[]" class="form-select">
                                                <option value="">Any Day</option>
                                                <option value="monday"><i class="bi bi-calendar-day me-1"></i>Monday</option>
                                                <option value="tuesday"><i class="bi bi-calendar-day me-1"></i>Tuesday</option>
                                                <option value="wednesday"><i class="bi bi-calendar-day me-1"></i>Wednesday</option>
                                                <option value="thursday"><i class="bi bi-calendar-day me-1"></i>Thursday</option>
                                                <option value="friday"><i class="bi bi-calendar-day me-1"></i>Friday</option>
                                                <option value="saturday"><i class="bi bi-calendar-day me-1"></i>Saturday</option>
                                                <option value="sunday"><i class="bi bi-calendar-day me-1"></i>Sunday</option>
                                            </select>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="hour-input">
                                            <input
                                                type="number"
                                                step="0.25"
                                                min="0"
                                                name="item_estimated_hours[]"
                                                class="form-control"
                                                placeholder="0.00"
                                                onchange="updateHourTotal()"
                                            >
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <button type="button"
                                                class="btn btn-sm btn-outline-danger px-3"
                                                onclick="removeScheduleRow(this)"
                                                title="Remove task">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Hour Total -->
                    <div class="mt-4 d-flex justify-content-between align-items-center">
                        <div class="text-muted small">
                            <i class="bi bi-info-circle me-1"></i>
                            Use approximate hours per task so directors and admins can see your workload.
                        </div>
                        <div class="glass-card-alt px-4 py-2">
                            <span class="text-muted me-2">Total Estimated Hours:</span>
                            <span id="total-hours" class="fw-bold gradient-text">0.00</span>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="action-bar">
                    <a href="/schedules" class="btn btn-outline-secondary px-4">
                        <i class="bi bi-arrow-left me-2"></i>Cancel
                    </a>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-save me-2"></i>
                        <?= $isEdit ? 'Save Changes' : 'Create Weekly Schedule' ?>
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
// Update hour total calculation
function updateHourTotal() {
    const hourInputs = document.querySelectorAll('input[name="item_estimated_hours[]"]');
    let total = 0;
    
    hourInputs.forEach(input => {
        const value = parseFloat(input.value) || 0;
        total += value;
    });
    
    document.getElementById('total-hours').textContent = total.toFixed(2);
}

// addScheduleRow adds a new blank row to the tasks table
function addScheduleRow() {
    const tbody = document.querySelector('#schedule-items-table tbody');
    if (!tbody) return;

    const tr = document.createElement('tr');
    tr.innerHTML = `
        <td>
            <div class="task-input-group">
                <input type="hidden" name="item_id[]" value="">
                <textarea
                    name="item_description[]"
                    rows="2"
                    class="form-control"
                    required
                    placeholder="Describe the task or activity..."
                ></textarea>
            </div>
        </td>
        <td>
            <input
                type="number"
                name="item_project_id[]"
                class="form-control"
                placeholder="Enter project ID"
            >
        </td>
        <td>
            <div class="day-select">
                <select name="item_day[]" class="form-select">
                    <option value="">Any Day</option>
                    <option value="monday"><i class="bi bi-calendar-day me-1"></i>Monday</option>
                    <option value="tuesday"><i class="bi bi-calendar-day me-1"></i>Tuesday</option>
                    <option value="wednesday"><i class="bi bi-calendar-day me-1"></i>Wednesday</option>
                    <option value="thursday"><i class="bi bi-calendar-day me-1"></i>Thursday</option>
                    <option value="friday"><i class="bi bi-calendar-day me-1"></i>Friday</option>
                    <option value="saturday"><i class="bi bi-calendar-day me-1"></i>Saturday</option>
                    <option value="sunday"><i class="bi bi-calendar-day me-1"></i>Sunday</option>
                </select>
            </div>
        </td>
        <td>
            <div class="hour-input">
                <input
                    type="number"
                    step="0.25"
                    min="0"
                    name="item_estimated_hours[]"
                    class="form-control"
                    placeholder="0.00"
                    onchange="updateHourTotal()"
                >
            </div>
        </td>
        <td class="text-center">
            <button type="button"
                    class="btn btn-sm btn-outline-danger px-3"
                    onclick="removeScheduleRow(this)"
                    title="Remove task">
                <i class="bi bi-trash"></i>
            </button>
        </td>
    `;
    tbody.appendChild(tr);
    
    // Focus the new textarea
    setTimeout(() => {
        const newTextarea = tr.querySelector('textarea');
        if (newTextarea) newTextarea.focus();
    }, 100);
}

// removeScheduleRow removes the row for the clicked button
function removeScheduleRow(button) {
    const tr = button.closest('tr');
    const tbody = tr && tr.parentElement;
    if (!tr || !tbody) return;

    if (tbody.children.length === 1) {
        // If last row, just clear inputs instead of removing
        tr.querySelectorAll('textarea, input').forEach(function(el) {
            if (el.type === 'hidden') return;
            if (el.tagName === 'TEXTAREA') {
                el.value = '';
            } else if (el.type === 'number') {
                el.value = '';
            }
        });
        tr.querySelector('select') && (tr.querySelector('select').value = '');
    } else {
        tbody.removeChild(tr);
    }
    
    updateHourTotal();
}

// Calculate total hours
function calculateTotalHours() {
    updateHourTotal();
    const total = document.getElementById('total-hours').textContent;
    
    // Show notification
    const notification = document.createElement('div');
    notification.className = 'position-fixed top-0 end-0 p-3';
    notification.style.zIndex = '9999';
    notification.innerHTML = `
        <div class="alert alert-info alert-dismissible fade show" style="background: rgba(56, 189, 248, 0.9); border: none; color: white;">
            <i class="bi bi-calculator me-2"></i>Total estimated hours: <strong>${total}</strong>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.querySelector('.alert').classList.remove('show');
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// Form validation
document.addEventListener('DOMContentLoaded', function() {
    updateHourTotal(); // Initial calculation
    
    const form = document.getElementById('schedule-form');
    const weekInput = form.querySelector('input[name="week_start"]');
    
    // Set min date to today if not editing
    if (!<?= $isEdit ? 'true' : 'false' ?> && !weekInput.value) {
        const today = new Date();
        const year = today.getFullYear();
        const month = String(today.getMonth() + 1).padStart(2, '0');
        const day = String(today.getDate()).padStart(2, '0');
        weekInput.value = `${year}-${month}-${day}`;
    }
    
    // Real-time validation
    const textareas = form.querySelectorAll('textarea[name="item_description[]"]');
    textareas.forEach(textarea => {
        textarea.addEventListener('input', function() {
            const parent = this.closest('.task-input-group');
            if (this.value.trim()) {
                parent.classList.add('has-content');
            } else {
                parent.classList.remove('has-content');
            }
        });
    });
    
    // Form submission
    form.addEventListener('submit', function(e) {
        const hasEmptyTasks = Array.from(textareas).some(ta => !ta.value.trim());
        if (hasEmptyTasks) {
            e.preventDefault();
            alert('Please fill in all task descriptions before submitting.');
            return;
        }
    });
});
</script>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>