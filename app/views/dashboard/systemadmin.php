<?php
// System Admin dashboard: configuration, operational oversight, workflow health.

use App\core\View;

/** @var array $user */
/** @var array $stats */
/** @var int   $unread_count */

$name          = $user['name'] ?? 'System Admin';
$totalProjects = (int) ($stats['projects_total']     ?? 0);
$ongoing       = (int) ($stats['projects_ongoing']   ?? 0);
$completed     = (int) ($stats['projects_completed'] ?? max(0, $totalProjects - $ongoing));

// Operational stats, safe defaults if backend not fully wired
$missingDocs      = (int) ($stats['projects_missing_docs']        ?? 0);
$missingCreds     = (int) ($stats['projects_missing_credentials'] ?? 0);
$unassignedDevs   = (int) ($stats['projects_without_developer']   ?? 0);
$staleProjects    = (int) ($stats['projects_without_recent_updates'] ?? 0);
$pendingApprovals = (int) ($stats['approvals_pending']            ?? 0);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Admin Dashboard - ZukBits</title>
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

        /* Operational Metrics */
        .operational-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .operational-card {
            background: var(--color-surface);
            border: 1px solid var(--color-border);
            border-radius: 16px;
            padding: 1.5rem;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .operational-card::before {
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

        .operational-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.4);
        }

        .operational-card:hover::before {
            opacity: 1;
        }

        .operational-label {
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

        .operational-value {
            font-size: 2.5rem;
            font-weight: 700;
            line-height: 1;
            margin-bottom: 0.5rem;
        }

        .operational-value.total { color: var(--color-accent-blue); }
        .operational-value.ongoing { color: var(--color-accent); }
        .operational-value.completed { color: var(--color-accent-green); }

        .operational-description {
            color: var(--color-text-muted);
            font-size: 0.875rem;
            margin-bottom: 1rem;
            line-height: 1.5;
        }

        .operational-link {
            color: var(--color-accent-blue);
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: color 0.2s ease;
        }

        .operational-link:hover {
            color: var(--color-accent);
        }

        /* Health Cards */
        .health-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 1.5rem;
        }

        .health-card {
            background: var(--gradient-bg-card);
            border: 1px solid var(--color-border);
            border-radius: 16px;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .health-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.4);
        }

        .health-header {
            background: var(--color-surface-alt);
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid var(--color-border);
        }

        .health-title {
            font-weight: 600;
            font-size: 1.1rem;
            color: var(--color-text);
            margin: 0;
        }

        .health-body {
            padding: 1.5rem;
        }

        .health-item {
            margin-bottom: 1.5rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid var(--color-border);
        }

        .health-item:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }

        .health-label {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.75rem;
        }

        .health-label strong {
            color: var(--color-text);
            font-size: 0.95rem;
        }

        .health-badge {
            padding: 0.35rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            background: rgba(251, 191, 36, 0.15);
            color: var(--color-accent-strong);
            border: 1px solid rgba(251, 191, 36, 0.3);
        }

        .health-badge.primary {
            background: rgba(56, 189, 248, 0.15);
            color: var(--color-accent-blue);
            border: 1px solid rgba(56, 189, 248, 0.3);
        }

        .health-badge.warning {
            background: rgba(251, 191, 36, 0.15);
            color: var(--color-accent-strong);
            border: 1px solid rgba(251, 191, 36, 0.3);
        }

        .health-description {
            color: var(--color-text-muted);
            font-size: 0.875rem;
            line-height: 1.5;
        }

        .health-info {
            background: rgba(56, 189, 248, 0.1);
            border: 1px solid rgba(56, 189, 248, 0.2);
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 1.5rem;
        }

        .health-info strong {
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

        .btn-link {
            color: var(--color-accent-blue);
            text-decoration: none;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: color 0.2s ease;
        }

        .btn-link:hover {
            color: var(--color-accent);
        }

        @media (max-width: 768px) {
            .dashboard-container {
                padding: 1rem;
            }
            
            .dashboard-header {
                padding: 1.25rem;
            }
            
            .operational-grid,
            .health-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }
            
            .operational-card {
                padding: 1.25rem;
            }
            
            .health-body {
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
        .operational-card,
        .health-card {
            animation: fadeInUp 0.6s ease-out;
        }

        .operational-card:nth-child(2) { animation-delay: 0.1s; }
        .operational-card:nth-child(3) { animation-delay: 0.2s; }
        .health-card:nth-child(2) { animation-delay: 0.3s; }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Header -->
        <div class="dashboard-header">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                <div class="mb-3 mb-md-0">
                    <h1 class="header-title h3 mb-2">
                        <i class="bi bi-gear-fill me-2"></i>System Admin Dashboard
                    </h1>
                    <p class="header-subtitle mb-0">
                        Configuration, operational oversight, and workflow health
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

        <!-- Operational Metrics -->
        <div class="operational-grid">
            <div class="operational-card">
                <div class="operational-label">
                    <i class="bi bi-grid"></i>Projects (All)
                </div>
                <div class="operational-value total"><?= View::e((string)$totalProjects) ?></div>
                <p class="operational-description">Total projects currently registered in the system</p>
                <a href="/projects" class="operational-link">
                    Browse projects <i class="bi bi-arrow-right"></i>
                </a>
            </div>

            <div class="operational-card">
                <div class="operational-label">
                    <i class="bi bi-clock-history"></i>Ongoing
                </div>
                <div class="operational-value ongoing"><?= View::e((string)$ongoing) ?></div>
                <p class="operational-description">Projects with active work in progress</p>
                <a href="/projects" class="operational-link">
                    Check workloads <i class="bi bi-arrow-right"></i>
                </a>
            </div>

            <div class="operational-card">
                <div class="operational-label">
                    <i class="bi bi-check-circle"></i>Completed
                </div>
                <div class="operational-value completed"><?= View::e((string)$completed) ?></div>
                <p class="operational-description">Projects completed and out of the active pipeline</p>
                <a href="/projects" class="operational-link">
                    Inspect history <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>

        <!-- Health Cards -->
        <div class="health-grid">
            <div class="health-card">
                <div class="health-header">
                    <h3 class="health-title">Documentation & Credentials</h3>
                </div>
                <div class="health-body">
                    <div class="health-item">
                        <div class="health-label">
                            <strong>Projects missing documentation</strong>
                            <span class="health-badge warning"><?= View::e((string)$missingDocs) ?></span>
                        </div>
                        <p class="health-description">
                            Ensure active projects have at least basic technical and operational docs.
                        </p>
                    </div>

                    <div class="health-item">
                        <div class="health-label">
                            <strong>Projects missing credentials</strong>
                            <span class="health-badge warning"><?= View::e((string)$missingCreds) ?></span>
                        </div>
                        <p class="health-description">
                            Store environment keys and access details securely to avoid bottlenecks.
                        </p>
                    </div>

                    <div class="feature-actions">
                        <a href="/documentation" class="btn-outline">
                            <i class="bi bi-file-text"></i>Documentation
                        </a>
                        <a href="/credentials" class="btn-primary">
                            <i class="bi bi-key"></i>Credentials
                        </a>
                    </div>
                </div>
            </div>

            <div class="health-card">
                <div class="health-header">
                    <h3 class="health-title">Assignments & Approvals</h3>
                </div>
                <div class="health-body">
                    <div class="health-item">
                        <div class="health-label">
                            <strong>Projects without developer assigned</strong>
                            <span class="health-badge warning"><?= View::e((string)$unassignedDevs) ?></span>
                        </div>
                        <p class="health-description">
                            Assign owners to avoid orphaned workstreams.
                        </p>
                    </div>

                    <div class="health-item">
                        <div class="health-label">
                            <strong>Stale active projects</strong>
                            <span class="health-badge warning"><?= View::e((string)$staleProjects) ?></span>
                        </div>
                        <p class="health-description">
                            Active projects with no recent updates; follow up with the team.
                        </p>
                    </div>

                    <div class="health-item">
                        <div class="health-label">
                            <strong>Pending approvals</strong>
                            <span class="health-badge primary"><?= View::e((string)$pendingApprovals) ?></span>
                        </div>
                        <p class="health-description">
                            Critical sign-offs awaiting attention.
                        </p>
                    </div>

                    <div class="feature-actions">
                        <a href="/users" class="btn-primary">
                            <i class="bi bi-people"></i>Manage Users
                        </a>
                        <a href="/approvals" class="btn-outline">
                            <i class="bi bi-check-circle"></i>Approvals
                        </a>
                        <a href="/settings/system" class="btn-link">
                            System Settings <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>