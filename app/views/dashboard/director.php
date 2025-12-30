<?php
// Director dashboard: high-level view of projects and team performance.

use App\core\View;

/** @var array $user */
/** @var array $stats */
/** @var int   $unread_count */

$name          = $user['name'] ?? 'Director';
$totalProjects = (int) ($stats['projects_total']   ?? 0);
$ongoing       = (int) ($stats['projects_ongoing'] ?? 0);
$completed     = (int) ($stats['projects_completed'] ?? max(0, $totalProjects - $ongoing));

// Extra stats with safe defaults
$overdue        = (int) ($stats['projects_overdue']    ?? 0);
$atRisk         = (int) ($stats['projects_at_risk']    ?? 0);

// Team report metrics from ReportService summary
$reportsExpected  = (int) ($stats['team_reports_expected_this_week']  ?? 0);
$reportsSubmitted = (int) ($stats['team_reports_submitted_this_week'] ?? 0);
$submissionRate   = $reportsExpected > 0
    ? (int) round(($reportsSubmitted / $reportsExpected) * 100)
    : 0;

// Approvals summary
$pendingApprovals = (int) ($stats['approvals_pending'] ?? 0);

// Count of reports this week (can be per-team or global view)
$recentReports    = (int) ($stats['reports_this_week'] ?? 0);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Director Dashboard - ZukBits</title>
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
            border-top: 4px solid var(--color-accent-blue);
        }

        .header-title {
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-weight: 700;
            margin: 0;
        }

        .header-subtitle {
            color: var(--color-text-muted);
            font-size: 1rem;
        }

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
        }

        .metric-value.total { color: var(--color-accent-blue); }
        .metric-value.ongoing { color: var(--color-accent); }
        .metric-value.overdue { color: var(--color-accent-red); }
        .metric-value.completed { color: var(--color-accent-green); }

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

        /* Insight Cards */
        .insight-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 1.5rem;
        }

        .insight-card {
            background: var(--gradient-bg-card);
            border: 1px solid var(--color-border);
            border-radius: 16px;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .insight-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.4);
        }

        .insight-header {
            background: var(--color-surface-alt);
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid var(--color-border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .insight-title {
            font-weight: 600;
            font-size: 1.1rem;
            color: var(--color-text);
            margin: 0;
        }

        .insight-badge {
            padding: 0.35rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-danger {
            background: rgba(239, 68, 68, 0.15);
            color: var(--color-accent-red);
            border: 1px solid rgba(239, 68, 68, 0.3);
        }

        .badge-primary {
            background: rgba(56, 189, 248, 0.15);
            color: var(--color-accent-blue);
            border: 1px solid rgba(56, 189, 248, 0.3);
        }

        .badge-warning {
            background: rgba(251, 191, 36, 0.15);
            color: var(--color-accent-strong);
            border: 1px solid rgba(251, 191, 36, 0.3);
        }

        .insight-body {
            padding: 1.5rem;
        }

        .insight-description {
            color: var(--color-text-muted);
            font-size: 0.95rem;
            margin-bottom: 1.5rem;
            line-height: 1.6;
        }

        .progress-container {
            margin: 1.5rem 0;
        }

        .progress-label {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
            color: var(--color-text-muted);
            font-size: 0.875rem;
        }

        .progress-bar {
            height: 8px;
            background: var(--color-border);
            border-radius: 4px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background: var(--gradient-primary);
            border-radius: 4px;
            transition: width 0.6s ease;
        }

        .stat-highlight {
            background: rgba(56, 189, 248, 0.1);
            border: 1px solid rgba(56, 189, 248, 0.2);
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 1.5rem;
        }

        .stat-highlight strong {
            color: var(--color-accent-blue);
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

        .btn-danger {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: var(--color-accent-red);
            padding: 0.75rem 1.5rem;
            border-radius: 12px;
            font-weight: 500;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
        }

        .btn-danger:hover {
            background: rgba(239, 68, 68, 0.2);
            transform: translateY(-2px);
        }

        @media (max-width: 768px) {
            .dashboard-container {
                padding: 1rem;
            }
            
            .dashboard-header {
                padding: 1.25rem;
            }
            
            .metric-grid,
            .insight-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }
            
            .metric-card {
                padding: 1.25rem;
            }
            
            .insight-body {
                padding: 1.25rem;
            }
        }

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
        .insight-card {
            animation: fadeInUp 0.6s ease-out;
        }

        .metric-card:nth-child(2) { animation-delay: 0.1s; }
        .metric-card:nth-child(3) { animation-delay: 0.2s; }
        .metric-card:nth-child(4) { animation-delay: 0.3s; }
        .insight-card:nth-child(2) { animation-delay: 0.4s; }
        .insight-card:nth-child(3) { animation-delay: 0.5s; }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Header -->
        <div class="dashboard-header">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                <div class="mb-3 mb-md-0">
                    <h1 class="header-title h3 mb-2">
                        <i class="bi bi-building me-2"></i>Director Dashboard
                    </h1>
                    <p class="header-subtitle mb-0">
                        High-level view of projects, risk, and team performance
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
                    <i class="bi bi-grid-3x3"></i>Total Projects
                </div>
                <div class="metric-value total"><?= View::e((string)$totalProjects) ?></div>
                <p class="metric-description">All projects in the system</p>
                <a href="/projects" class="metric-link">
                    View project list <i class="bi bi-arrow-right"></i>
                </a>
            </div>

            <div class="metric-card">
                <div class="metric-label">
                    <i class="bi bi-clock-history"></i>Ongoing
                </div>
                <div class="metric-value ongoing"><?= View::e((string)$ongoing) ?></div>
                <p class="metric-description">Projects currently in progress</p>
                <a href="/projects" class="metric-link">
                    Review ongoing <i class="bi bi-arrow-right"></i>
                </a>
            </div>

            <div class="metric-card">
                <div class="metric-label">
                    <i class="bi bi-exclamation-triangle"></i>Overdue
                </div>
                <div class="metric-value overdue"><?= View::e((string)$overdue) ?></div>
                <p class="metric-description">Past target end date and not completed</p>
                <a href="/projects" class="metric-link">
                    Check overdue <i class="bi bi-arrow-right"></i>
                </a>
            </div>

            <div class="metric-card">
                <div class="metric-label">
                    <i class="bi bi-check-circle"></i>Completed
                </div>
                <div class="metric-value completed"><?= View::e((string)$completed) ?></div>
                <p class="metric-description">Projects that reached completion or approval</p>
                <a href="/projects" class="metric-link">
                    See completed <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>

        <!-- Insight Cards -->
        <div class="insight-grid">
            <div class="insight-card">
                <div class="insight-header">
                    <h3 class="insight-title">Projects at Risk</h3>
                    <span class="insight-badge badge-danger"><?= View::e((string)$atRisk) ?></span>
                </div>
                <div class="insight-body">
                    <p class="insight-description">
                        Projects flagged as at risk (overdue, blocked, or repeatedly rescheduled).
                        Focus follow-up on these to prevent escalations.
                    </p>
                    <a href="/projects" class="btn-danger">
                        <i class="bi bi-shield-exclamation"></i>Review Risk Portfolio
                    </a>
                </div>
            </div>

            <div class="insight-card">
                <div class="insight-header">
                    <h3 class="insight-title">Weekly Reports</h3>
                    <span class="insight-badge badge-primary"><?= View::e((string)$submissionRate) ?>%</span>
                </div>
                <div class="insight-body">
                    <div class="progress-container">
                        <div class="progress-label">
                            <span>Submission Rate</span>
                            <span><?= View::e((string)$reportsSubmitted) ?>/<?= View::e((string)$reportsExpected) ?></span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: <?= $submissionRate ?>%"></div>
                        </div>
                    </div>
                    <p class="insight-description">
                        Use weekly reports to track progress, identify blockers, and guide support.
                    </p>
                    <div class="feature-actions">
                        <a href="/reports/team-overview" class="btn-outline">
                            <i class="bi bi-people"></i>Team Overview
                        </a>
                        <a href="/reports" class="btn-primary">
                            <i class="bi bi-file-text"></i>All Reports
                        </a>
                    </div>
                </div>
            </div>

            <div class="insight-card">
                <div class="insight-header">
                    <h3 class="insight-title">Approvals</h3>
                    <span class="insight-badge badge-warning"><?= View::e((string)$pendingApprovals) ?> pending</span>
                </div>
                <div class="insight-body">
                    <div class="stat-highlight">
                        <strong>Recent reports this week:</strong>
                        <span class="text-muted"><?= View::e((string)$recentReports) ?></span>
                    </div>
                    <p class="insight-description">
                        Approve project completion and other key milestones from one place.
                    </p>
                    <div class="feature-actions">
                        <a href="/approvals/inbox" class="btn-primary">
                            <i class="bi bi-inbox"></i>Pending Approvals
                        </a>
                        <a href="/approvals" class="btn-outline">
                            All Approvals
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>