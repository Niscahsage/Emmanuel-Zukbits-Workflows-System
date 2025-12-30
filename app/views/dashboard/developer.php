<?php
// Developer dashboard: assigned projects, deadlines, weekly schedule/report status.

use App\core\View;

/** @var array $user */
/** @var array $stats */
/** @var int   $unread_count */

// Use per-user stats if available, fall back to generic ones
$name          = $user['name'] ?? 'Developer';
$totalProjects = (int) ($stats['my_projects_total']    ?? $stats['projects_total']   ?? 0);
$ongoing       = (int) ($stats['my_projects_ongoing']  ?? $stats['projects_ongoing'] ?? 0);
$completed     = (int) ($stats['my_projects_completed'] ?? max(0, $totalProjects - $ongoing));

// Overdue and deadlines from per-user stats
$overdueAssigned = (int) ($stats['my_projects_overdue'] ?? 0);
$nextDeadline    = $stats['my_next_deadline'] ?? $stats['projects_next_deadline'] ?? null;

// Weekly schedule / report flags
$hasSchedule    = !empty($stats['has_schedule_this_week']);
$hasReport      = !empty($stats['has_report_this_week']);

// Optional feedback summary if you later wire it from reports
$recentFeedback = $stats['recent_feedback_summary'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Developer Dashboard - ZukBits</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --color-bg: #050816;
            --color-surface: #0b1020;
            --color-surface-alt: #111827;
            --color-surface-soft: #0f172a;
            --color-accent: #ffc857;
            --color-accent-strong: #fbbf24;
            --color-accent-blue: #38bdf8;
            --color-accent-purple: #a855f7;
            --color-accent-green: #34c759;
            --color-accent-red: #ef4444;
            --color-text: #f7f7ff;
            --color-text-muted: #c3c5d4;
            --color-border: #22263b;
            --gradient-primary: linear-gradient(135deg, var(--color-accent-blue), var(--color-accent-purple));
            --gradient-bg-card: radial-gradient(circle at top left, rgba(148, 163, 253, 0.18), rgba(15, 23, 42, 0.96));
        }

        body {
            background: var(--color-bg);
            color: var(--color-text);
            min-height: 100vh;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        }

        .dashboard-container {
            padding: 1.5rem;
            max-width: 1400px;
            margin: 0 auto;
        }

        /* Header Styles */
        .dashboard-header {
            background: var(--gradient-bg-card);
            border: 1px solid var(--color-border);
            border-radius: 20px;
            padding: 1.5rem 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.4);
            position: relative;
            overflow: hidden;
        }

        .dashboard-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--gradient-primary);
        }

        .header-title {
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-weight: 700;
            margin: 0;
        }

        .welcome-text {
            color: var(--color-text-muted);
            font-size: 1rem;
        }

        /* Notification Button */
        .notification-btn {
            background: var(--color-surface-alt);
            border: 1px solid var(--color-border);
            border-radius: 12px;
            padding: 0.5rem 1rem;
            color: var(--color-text);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            transition: all 0.3s ease;
        }

        .notification-btn:hover {
            background: var(--color-surface);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        }

        .notification-badge {
            background: var(--gradient-primary);
            color: white;
            font-size: 0.75rem;
            min-width: 24px;
            height: 24px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-left: 0.5rem;
        }

        /* Metric Cards */
        .metric-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .metric-card {
            background: var(--color-surface);
            border: 1px solid var(--color-border);
            border-radius: 16px;
            padding: 1.5rem;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .metric-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--gradient-primary);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .metric-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.4);
        }

        .metric-card:hover::before {
            opacity: 1;
        }

        .metric-label {
            color: var(--color-text-muted);
            font-size: 0.875rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .metric-value {
            font-size: 2.5rem;
            font-weight: 700;
            line-height: 1;
            margin-bottom: 0.5rem;
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .metric-description {
            color: var(--color-text-muted);
            font-size: 0.875rem;
            margin-bottom: 1rem;
            line-height: 1.5;
        }

        .metric-link {
            color: var(--color-accent-blue);
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: color 0.2s ease;
        }

        .metric-link:hover {
            color: var(--color-accent);
        }

        /* Feature Cards */
        .feature-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 1.5rem;
        }

        .feature-card {
            background: var(--gradient-bg-card);
            border: 1px solid var(--color-border);
            border-radius: 16px;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.4);
        }

        .feature-header {
            background: var(--color-surface-alt);
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid var(--color-border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .feature-title {
            font-weight: 600;
            font-size: 1.1rem;
            color: var(--color-text);
            margin: 0;
        }

        .feature-status {
            padding: 0.35rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .status-planned {
            background: rgba(52, 199, 89, 0.15);
            color: var(--color-accent-green);
            border: 1px solid rgba(52, 199, 89, 0.3);
        }

        .status-not-planned {
            background: rgba(251, 191, 36, 0.15);
            color: var(--color-accent-strong);
            border: 1px solid rgba(251, 191, 36, 0.3);
        }

        .status-submitted {
            background: rgba(52, 199, 89, 0.15);
            color: var(--color-accent-green);
            border: 1px solid rgba(52, 199, 89, 0.3);
        }

        .status-not-submitted {
            background: rgba(251, 191, 36, 0.15);
            color: var(--color-accent-strong);
            border: 1px solid rgba(251, 191, 36, 0.3);
        }

        .feature-body {
            padding: 1.5rem;
        }

        .feature-description {
            color: var(--color-text-muted);
            font-size: 0.95rem;
            margin-bottom: 1.5rem;
            line-height: 1.6;
        }

        .feature-list {
            margin: 0 0 1.5rem 0;
            padding-left: 1.5rem;
        }

        .feature-list li {
            color: var(--color-text-muted);
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }

        .feature-info {
            background: rgba(56, 189, 248, 0.1);
            border: 1px solid rgba(56, 189, 248, 0.2);
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 1.5rem;
        }

        .feature-info strong {
            color: var(--color-accent-blue);
        }

        .feature-actions {
            display: flex;
            gap: 0.75rem;
            align-items: center;
            flex-wrap: wrap;
        }

        .btn-primary {
            background: var(--gradient-primary);
            border: none;
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 12px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(56, 189, 248, 0.25);
        }

        .btn-outline {
            background: transparent;
            border: 1px solid var(--color-border);
            color: var(--color-text);
            padding: 0.75rem 1.5rem;
            border-radius: 12px;
            font-weight: 500;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
        }

        .btn-outline:hover {
            background: var(--color-surface-alt);
            border-color: var(--color-border-light);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .dashboard-container {
                padding: 1rem;
            }
            
            .dashboard-header {
                padding: 1.25rem;
            }
            
            .metric-grid,
            .feature-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }
            
            .metric-card {
                padding: 1.25rem;
            }
            
            .feature-body {
                padding: 1.25rem;
            }
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .dashboard-header,
        .metric-card,
        .feature-card {
            animation: fadeInUp 0.6s ease-out;
        }

        .metric-card:nth-child(2) { animation-delay: 0.1s; }
        .metric-card:nth-child(3) { animation-delay: 0.2s; }
        .metric-card:nth-child(4) { animation-delay: 0.3s; }
        .feature-card:nth-child(2) { animation-delay: 0.4s; }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Header -->
        <div class="dashboard-header">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                <div class="mb-3 mb-md-0">
                    <h1 class="header-title h3 mb-2">
                        <i class="bi bi-speedometer2 me-2"></i>Welcome back, <?= View::e($name) ?>
                    </h1>
                    <p class="welcome-text mb-0">
                        Snapshot of your projects and weekly workflow
                    </p>
                </div>
                <div>
                    <a href="/notifications" class="notification-btn">
                        <i class="bi bi-bell"></i>
                        <?php if ($unread_count > 0): ?>
                            <span class="notification-badge"><?= View::e((string)$unread_count) ?></span>
                        <?php endif; ?>
                    </a>
                </div>
            </div>
        </div>

        <!-- Project Metrics -->
        <div class="metric-grid">
            <div class="metric-card">
                <div class="metric-label">
                    <i class="bi bi-folder"></i>Assigned Projects
                </div>
                <div class="metric-value"><?= View::e((string)$totalProjects) ?></div>
                <p class="metric-description">Total projects you are assigned to</p>
                <a href="/projects" class="metric-link">
                    View all projects <i class="bi bi-arrow-right"></i>
                </a>
            </div>

            <div class="metric-card">
                <div class="metric-label">
                    <i class="bi bi-clock"></i>Ongoing
                </div>
                <div class="metric-value"><?= View::e((string)$ongoing) ?></div>
                <p class="metric-description">Projects currently in progress</p>
                <a href="/projects" class="metric-link">
                    Go to project list <i class="bi bi-arrow-right"></i>
                </a>
            </div>

            <div class="metric-card">
                <div class="metric-label">
                    <i class="bi bi-check-circle"></i>Completed
                </div>
                <div class="metric-value"><?= View::e((string)$completed) ?></div>
                <p class="metric-description">Projects marked as completed</p>
                <a href="/projects" class="metric-link">
                    Review completed work <i class="bi bi-arrow-right"></i>
                </a>
            </div>

            <div class="metric-card">
                <div class="metric-label">
                    <i class="bi bi-exclamation-triangle"></i>Overdue
                </div>
                <div class="metric-value"><?= View::e((string)$overdueAssigned) ?></div>
                <p class="metric-description">Assigned projects past deadline</p>
                <a href="/projects" class="metric-link">
                    Check deadlines <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>

        <!-- Feature Cards -->
        <div class="feature-grid">
            <div class="feature-card">
                <div class="feature-header">
                    <h3 class="feature-title">Weekly Schedule</h3>
                    <span class="feature-status <?= $hasSchedule ? 'status-planned' : 'status-not-planned' ?>">
                        <?= $hasSchedule ? 'Planned' : 'Not Planned' ?>
                    </span>
                </div>
                <div class="feature-body">
                    <p class="feature-description">
                        Keep your weekly plan up to date so directors and admins can see what you are working on.
                    </p>
                    <ul class="feature-list">
                        <li>List tasks by project with estimated hours</li>
                        <li>Use clear, concise descriptions for each task</li>
                    </ul>
                    <?php if ($nextDeadline): ?>
                        <div class="feature-info">
                            <strong>Next project deadline:</strong>
                            <span class="text-muted"><?= View::e($nextDeadline) ?></span>
                        </div>
                    <?php endif; ?>
                    <div class="feature-actions">
                        <a href="/schedules" class="btn-primary">
                            <i class="bi bi-calendar-week"></i>Weekly Schedules
                        </a>
                        <a href="/schedules/create?week_start=<?= View::e(date('Y-m-d')) ?>" class="btn-outline">
                            Plan This Week
                        </a>
                    </div>
                </div>
            </div>

            <div class="feature-card">
                <div class="feature-header">
                    <h3 class="feature-title">Weekly Reports</h3>
                    <span class="feature-status <?= $hasReport ? 'status-submitted' : 'status-not-submitted' ?>">
                        <?= $hasReport ? 'Submitted' : 'Not Submitted' ?>
                    </span>
                </div>
                <div class="feature-body">
                    <p class="feature-description">
                        Submit concise summaries of what you accomplished for the week based on your schedule.
                    </p>
                    <ul class="feature-list">
                        <li>Mark tasks as completed, in progress, or blocked</li>
                        <li>Highlight challenges and support needed</li>
                    </ul>
                    <?php if ($recentFeedback): ?>
                        <div class="feature-info">
                            <strong>Recent feedback:</strong>
                            <span class="text-muted"><?= View::e($recentFeedback) ?></span>
                        </div>
                    <?php endif; ?>
                    <div class="feature-actions">
                        <a href="/reports" class="btn-outline">
                            <i class="bi bi-file-text"></i>View My Reports
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>