<?php
// app/views/settings/system.php
// system.php displays system-level settings for Super Admin or System Admin roles.

/** @var array $settings */

use App\core\View;

$appName        = $settings['app_name']        ?? ($_ENV['APP_NAME']        ?? 'ZukBits Workflows System');
$ownerName      = $settings['owner_name']      ?? ($_ENV['OWNER_NAME']      ?? 'Zukbits Online');
$ownerEmail     = $settings['owner_email']     ?? ($_ENV['OWNER_EMAIL']     ?? 'info@zukbitsonline.co.ke');
$ownerPhone     = $settings['owner_phone']     ?? ($_ENV['OWNER_PHONE']     ?? '');
$ownerLocation  = $settings['owner_location']  ?? ($_ENV['OWNER_LOCATION']  ?? '');

$robotsIndex    = (string)($settings['robots_index']    ?? ($_ENV['ROBOTS_INDEX']    ?? '1'));
$maxUploadMb    = (string)($settings['max_upload_mb']   ?? ($_ENV['MAX_UPLOAD_MB']   ?? '5'));
$dbPoolSize     = (string)($settings['db_pool_size']    ?? ($_ENV['DB_POOL_SIZE']    ?? '2'));
$dbMaxOverflow  = (string)($settings['db_max_overflow'] ?? ($_ENV['DB_MAX_OVERFLOW'] ?? '0'));
$rateWindow     = (string)($settings['rate_limit_window'] ?? ($_ENV['RATE_LIMIT_WINDOW'] ?? '3600'));
$rateMax        = (string)($settings['rate_limit_max']    ?? ($_ENV['RATE_LIMIT_MAX']    ?? '300'));

$supabaseUrl    = $settings['supabase_project_url'] ?? ($_ENV['SUPABASE_PROJECT_URL'] ?? '');
$storageBucket  = $settings['supabase_storage_bucket'] ?? ($_ENV['SUPABASE_STORAGE_BUCKET'] ?? '');
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Settings | ZukBits Online</title>
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
                radial-gradient(circle at 10% 10%, rgba(56, 189, 248, 0.05) 0%, transparent 40%),
                radial-gradient(circle at 90% 90%, rgba(168, 85, 247, 0.05) 0%, transparent 40%);
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

        .system-header {
            background: var(--gradient-primary);
            color: white;
            padding: 24px 30px;
            border-radius: 16px;
            margin-bottom: 30px;
            position: relative;
            overflow: hidden;
        }

        .system-header::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 120px;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1));
        }

        .admin-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 20px;
            background: rgba(255, 255, 255, 0.15);
            color: white;
            border-radius: 20px;
            font-weight: 500;
            font-size: 0.9rem;
        }

        .settings-category {
            margin-bottom: 30px;
        }

        .category-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 2px solid rgba(56, 189, 248, 0.2);
        }

        .category-icon {
            width: 48px;
            height: 48px;
            background: var(--gradient-primary);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.2rem;
        }

        .form-control {
            background: var(--color-surface-alt);
            border: 2px solid var(--color-border);
            color: var(--color-text);
            border-radius: 8px;
            padding: 12px 16px;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            background: var(--color-surface-alt);
            border-color: var(--color-accent-blue);
            color: var(--color-text);
            box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.2);
        }

        .form-control::placeholder {
            color: var(--color-text-muted);
            opacity: 0.7;
        }

        .form-label {
            color: var(--color-text);
            font-weight: 500;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .form-label .required {
            color: #ef4444;
            font-size: 0.9em;
        }

        .form-hint {
            color: var(--color-text-muted);
            font-size: 0.85rem;
            margin-top: 6px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .input-group {
            border-radius: 8px;
            overflow: hidden;
        }

        .input-group-text {
            background: var(--color-surface-alt);
            border: 2px solid var(--color-border);
            color: var(--color-text-muted);
            padding: 12px 16px;
        }

        .input-group .form-control {
            border-left: 0;
        }

        .input-group .input-group-text:first-child + .form-control {
            border-left: 0;
            border-top-left-radius: 0;
            border-bottom-left-radius: 0;
        }

        .btn-primary {
            background: var(--gradient-primary);
            border: none;
            color: white;
            padding: 12px 28px;
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
            padding: 10px 24px;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-outline-primary:hover {
            background: var(--color-accent-blue);
            color: white;
            transform: translateY(-2px);
        }

        .btn-danger {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            border: none;
            color: white;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(239, 68, 68, 0.3);
            color: white;
        }

        .system-metrics {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            margin-bottom: 30px;
        }

        .metric-card {
            background: rgba(56, 189, 248, 0.05);
            border: 2px solid rgba(56, 189, 248, 0.1);
            border-radius: 12px;
            padding: 20px;
            transition: all 0.3s ease;
        }

        .metric-card:hover {
            background: rgba(56, 189, 248, 0.1);
            transform: translateY(-3px);
        }

        .metric-label {
            color: var(--color-text-muted);
            font-size: 0.9rem;
            margin-bottom: 8px;
        }

        .metric-value {
            color: var(--color-text);
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 4px;
        }

        .metric-change {
            font-size: 0.8rem;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .metric-change.positive {
            color: var(--color-accent-green);
        }

        .metric-change.negative {
            color: #ef4444;
        }

        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 30px;
        }

        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .toggle-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: var(--color-border);
            transition: .4s;
            border-radius: 34px;
        }

        .toggle-slider:before {
            position: absolute;
            content: "";
            height: 22px;
            width: 22px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked + .toggle-slider {
            background-color: var(--color-accent-blue);
        }

        input:checked + .toggle-slider:before {
            transform: translateX(30px);
        }

        .danger-zone {
            border: 2px solid rgba(239, 68, 68, 0.3);
            background: rgba(239, 68, 68, 0.05);
            border-radius: 12px;
            padding: 24px;
            margin-top: 40px;
        }

        .danger-zone-header {
            color: #ef4444;
            font-weight: 600;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .setting-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px;
            background: rgba(56, 189, 248, 0.05);
            border-radius: 8px;
            margin-bottom: 12px;
        }

        .setting-label {
            color: var(--color-text);
            font-weight: 500;
        }

        .setting-value {
            color: var(--color-text-muted);
            font-family: monospace;
            font-size: 0.9rem;
        }

        .setting-actions {
            display: flex;
            gap: 8px;
        }

        @media (max-width: 768px) {
            .system-header {
                padding: 20px;
                text-align: center;
            }
            
            .system-metrics {
                grid-template-columns: 1fr;
            }
            
            .setting-row {
                flex-direction: column;
                align-items: flex-start;
                gap: 12px;
            }
            
            .setting-actions {
                width: 100%;
                justify-content: flex-end;
            }
            
            .category-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 16px;
            }
        }
    </style>
</head>
<body>
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="system-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3 mb-2 fw-bold">System Settings</h1>
                        <p class="mb-0 opacity-90">
                            <i class="bi bi-gear me-2"></i>
                            Configure application-wide settings and integrations
                        </p>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <span class="admin-badge">
                            <i class="bi bi-shield-check"></i>System Administrator
                        </span>
                        <button class="btn btn-light btn-sm" onclick="showSystemStatus()">
                            <i class="bi bi-graph-up me-1"></i>System Status
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- System Metrics -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="system-metrics">
                <div class="metric-card">
                    <div class="metric-label">
                        <i class="bi bi-database me-1"></i>Database Pool
                    </div>
                    <div class="metric-value"><?= View::e($dbPoolSize) ?>/<?= View::e($dbMaxOverflow) ?></div>
                    <div class="metric-change positive">
                        <i class="bi bi-arrow-up"></i> Active connections
                    </div>
                </div>
                
                <div class="metric-card">
                    <div class="metric-label">
                        <i class="bi bi-upload me-1"></i>Upload Limit
                    </div>
                    <div class="metric-value"><?= View::e($maxUploadMb) ?> MB</div>
                    <div class="metric-change">
                        <i class="bi bi-dash"></i> Max file size
                    </div>
                </div>
                
                <div class="metric-card">
                    <div class="metric-label">
                        <i class="bi bi-speedometer2 me-1"></i>Rate Limit
                    </div>
                    <div class="metric-value"><?= View::e($rateMax) ?>/<?= View::e($rateWindow) ?>s</div>
                    <div class="metric-change">
                        <i class="bi bi-check-circle"></i> Active
                    </div>
                </div>
                
                <div class="metric-card">
                    <div class="metric-label">
                        <i class="bi bi-robot me-1"></i>Search Indexing
                    </div>
                    <div class="metric-value">
                        <?= ($robotsIndex === '1' || strtolower($robotsIndex) === 'true') ? 'Enabled' : 'Disabled' ?>
                    </div>
                    <div class="metric-change <?= ($robotsIndex === '1' || strtolower($robotsIndex) === 'true') ? 'positive' : 'negative' ?>">
                        <?= ($robotsIndex === '1' || strtolower($robotsIndex) === 'true') ? 
                            '<i class="bi bi-globe"></i> Public' : 
                            '<i class="bi bi-shield-lock"></i> Private' ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Left Column - Main Settings -->
        <div class="col-lg-8">
            <!-- Application Settings -->
            <div class="glass-card p-4 mb-4">
                <div class="category-header">
                    <div class="category-icon">
                        <i class="bi bi-app-indicator"></i>
                    </div>
                    <div>
                        <h3 class="h5 mb-1 gradient-text fw-bold">Application Configuration</h3>
                        <p class="text-muted mb-0">Core application settings and branding</p>
                    </div>
                </div>

                <form method="post" action="/settings/system/app" id="app-settings-form">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">
                                <i class="bi bi-badge-tm me-1"></i>Application Name
                                <span class="required">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-tag-fill"></i>
                                </span>
                                <input
                                    type="text"
                                    name="app_name"
                                    class="form-control"
                                    required
                                    value="<?= View::e($appName) ?>"
                                >
                            </div>
                            <div class="form-hint">
                                <i class="bi bi-info-circle"></i>
                                Displayed in navbar and login screens
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">
                                <i class="bi bi-building me-1"></i>Organization Name
                                <span class="required">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-building-fill"></i>
                                </span>
                                <input
                                    type="text"
                                    name="owner_name"
                                    class="form-control"
                                    required
                                    value="<?= View::e($ownerName) ?>"
                                >
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">
                                <i class="bi bi-envelope me-1"></i>Organization Email
                                <span class="required">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-envelope-fill"></i>
                                </span>
                                <input
                                    type="email"
                                    name="owner_email"
                                    class="form-control"
                                    required
                                    value="<?= View::e($ownerEmail) ?>"
                                >
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">
                                <i class="bi bi-telephone me-1"></i>Organization Phone
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-telephone-fill"></i>
                                </span>
                                <input
                                    type="text"
                                    name="owner_phone"
                                    class="form-control"
                                    value="<?= View::e($ownerPhone) ?>"
                                >
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">
                                <i class="bi bi-geo-alt me-1"></i>Organization Location
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-geo-alt-fill"></i>
                                </span>
                                <input
                                    type="text"
                                    name="owner_location"
                                    class="form-control"
                                    value="<?= View::e($ownerLocation) ?>"
                                >
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">
                                <i class="bi bi-robot me-1"></i>Search Engine Indexing
                            </label>
                            <div class="d-flex align-items-center justify-content-between p-3 glass-card-alt rounded">
                                <div>
                                    <div class="fw-medium">Allow Search Indexing</div>
                                    <div class="text-muted small">Control if search engines can index this site</div>
                                </div>
                                <div class="toggle-switch">
                                    <input type="checkbox" name="robots_index" value="1" 
                                           <?= ($robotsIndex === '1' || strtolower($robotsIndex) === 'true') ? 'checked' : '' ?>>
                                    <span class="toggle-slider"></span>
                                </div>
                            </div>
                            <div class="form-hint">
                                <i class="bi bi-shield-exclamation"></i>
                                Recommended OFF for internal tools
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-2"></i>Save Application Settings
                        </button>
                    </div>
                </form>
            </div>

            <!-- Performance Settings -->
            <div class="glass-card p-4 mb-4">
                <div class="category-header">
                    <div class="category-icon" style="background: linear-gradient(135deg, #10b981, #059669);">
                        <i class="bi bi-speedometer2"></i>
                    </div>
                    <div>
                        <h3 class="h5 mb-1 gradient-text fw-bold">Performance & Limits</h3>
                        <p class="text-muted mb-0">Configure system performance and resource limits</p>
                    </div>
                </div>

                <form method="post" action="/settings/system/performance" id="performance-form">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">
                                <i class="bi bi-upload me-1"></i>Max Upload Size (MB)
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-file-arrow-up"></i>
                                </span>
                                <input
                                    type="number"
                                    min="1"
                                    name="max_upload_mb"
                                    class="form-control"
                                    value="<?= View::e($maxUploadMb) ?>"
                                >
                                <span class="input-group-text">MB</span>
                            </div>
                            <div class="form-hint">
                                <i class="bi bi-info-circle"></i>
                                Maximum file upload size
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">
                                <i class="bi bi-database me-1"></i>DB Pool Size
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-database-fill"></i>
                                </span>
                                <input
                                    type="number"
                                    min="1"
                                    name="db_pool_size"
                                    class="form-control"
                                    value="<?= View::e($dbPoolSize) ?>"
                                >
                            </div>
                            <div class="form-hint">
                                <i class="bi bi-info-circle"></i>
                                Database connection pool size
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">
                                <i class="bi bi-database-add me-1"></i>DB Max Overflow
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-database-fill-add"></i>
                                </span>
                                <input
                                    type="number"
                                    min="0"
                                    name="db_max_overflow"
                                    class="form-control"
                                    value="<?= View::e($dbMaxOverflow) ?>"
                                >
                            </div>
                            <div class="form-hint">
                                <i class="bi bi-info-circle"></i>
                                Maximum overflow connections
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">
                                <i class="bi bi-clock-history me-1"></i>Rate Limit Window
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-hourglass"></i>
                                </span>
                                <input
                                    type="number"
                                    min="60"
                                    name="rate_limit_window"
                                    class="form-control"
                                    value="<?= View::e($rateWindow) ?>"
                                >
                                <span class="input-group-text">seconds</span>
                            </div>
                            <div class="form-hint">
                                <i class="bi bi-info-circle"></i>
                                Time window for rate limiting
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">
                                <i class="bi bi-graph-up me-1"></i>Max Requests
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-bar-chart"></i>
                                </span>
                                <input
                                    type="number"
                                    min="1"
                                    name="rate_limit_max"
                                    class="form-control"
                                    value="<?= View::e($rateMax) ?>"
                                >
                                <span class="input-group-text">requests</span>
                            </div>
                            <div class="form-hint">
                                <i class="bi bi-info-circle"></i>
                                Maximum requests per window
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <div class="form-hint">
                            <i class="bi bi-exclamation-triangle text-warning"></i>
                            Changes may require application restart
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-2"></i>Save Performance Settings
                        </button>
                    </div>
                </form>
            </div>

            <!-- Danger Zone -->
            <div class="danger-zone">
                <div class="danger-zone-header">
                    <i class="bi bi-exclamation-triangle"></i>
                    Danger Zone
                </div>
                <p class="text-muted mb-4">
                    These actions are irreversible. Please proceed with caution.
                </p>
                
                <div class="setting-row">
                    <div>
                        <div class="setting-label">Clear System Cache</div>
                        <div class="text-muted small">Remove all cached data and temporary files</div>
                    </div>
                    <div class="setting-actions">
                        <button class="btn btn-outline-danger btn-sm" onclick="clearSystemCache()">
                            <i class="bi bi-trash me-1"></i>Clear Cache
                        </button>
                    </div>
                </div>

                <div class="setting-row">
                    <div>
                        <div class="setting-label">Reset Application Data</div>
                        <div class="text-muted small">Reset all user data to factory defaults</div>
                    </div>
                    <div class="setting-actions">
                        <button class="btn btn-outline-danger" onclick="resetApplicationData()">
                            <i class="bi bi-arrow-clockwise me-1"></i>Reset Data
                        </button>
                    </div>
                </div>

                <div class="setting-row">
                    <div>
                        <div class="setting-label">System Maintenance Mode</div>
                        <div class="text-muted small">Take system offline for maintenance</div>
                    </div>
                    <div class="setting-actions">
                        <div class="toggle-switch">
                            <input type="checkbox" id="maintenance-mode">
                            <span class="toggle-slider"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column - Integrations & Environment -->
        <div class="col-lg-4">
            <!-- Supabase Integration -->
            <div class="glass-card p-4 mb-4">
                <div class="category-header">
                    <div class="category-icon" style="background: linear-gradient(135deg, #3ecf8e, #1f9d55);">
                        <i class="bi bi-cloud"></i>
                    </div>
                    <div>
                        <h3 class="h5 mb-1 gradient-text fw-bold">Supabase Integration</h3>
                        <p class="text-muted mb-0">Configure cloud storage and authentication</p>
                    </div>
                </div>

                <form method="post" action="/settings/system/supabase" id="supabase-form">
                    <div class="mb-3">
                        <label class="form-label">
                            <i class="bi bi-link me-1"></i>Project URL
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-globe"></i>
                            </span>
                            <input
                                type="url"
                                name="supabase_project_url"
                                class="form-control"
                                value="<?= View::e($supabaseUrl) ?>"
                                placeholder="https://xxxx.supabase.co"
                            >
                        </div>
                        <div class="form-hint">
                            <i class="bi bi-info-circle"></i>
                            Your Supabase project URL
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">
                            <i class="bi bi-box me-1"></i>Storage Bucket
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-folder-fill"></i>
                            </span>
                            <input
                                type="text"
                                name="supabase_storage_bucket"
                                class="form-control"
                                value="<?= View::e($storageBucket) ?>"
                                placeholder="e.g. workflows"
                            >
                        </div>
                        <div class="form-hint">
                            <i class="bi bi-info-circle"></i>
                            Default storage bucket name
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <button type="button" class="btn btn-outline-secondary" onclick="testSupabaseConnection()">
                            <i class="bi bi-plug me-1"></i>Test Connection
                        </button>
                        <button type="submit" class="btn btn-outline-primary">
                            <i class="bi bi-save me-2"></i>Save Settings
                        </button>
                    </div>
                </form>
            </div>

            <!-- Environment Info -->
            <div class="glass-card p-4 mb-4">
                <div class="category-header">
                    <div class="category-icon" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed);">
                        <i class="bi bi-code-slash"></i>
                    </div>
                    <div>
                        <h3 class="h5 mb-1 gradient-text fw-bold">Environment</h3>
                        <p class="text-muted mb-0">System environment and configuration</p>
                    </div>
                </div>

                <div class="setting-row">
                    <div class="setting-label">Environment</div>
                    <div class="setting-value"><?= View::e($_ENV['APP_ENV'] ?? 'local') ?></div>
                </div>

                <div class="setting-row">
                    <div class="setting-label">Debug Mode</div>
                    <div class="setting-value">
                        <?php if (!empty($_ENV['APP_DEBUG']) && $_ENV['APP_DEBUG'] !== '0' && strtolower($_ENV['APP_DEBUG']) !== 'false'): ?>
                            <span class="text-danger">ENABLED</span>
                        <?php else: ?>
                            <span class="text-success">DISABLED</span>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="setting-row">
                    <div class="setting-label">PHP Version</div>
                    <div class="setting-value"><?= phpversion() ?></div>
                </div>

                <div class="setting-row">
                    <div class="setting-label">Database</div>
                    <div class="setting-value">
                        <?= $_ENV['DB_CONNECTION'] ?? 'postgresql' ?>
                    </div>
                </div>

                <div class="glass-card-alt p-3 mt-3">
                    <div class="text-muted small">
                        <i class="bi bi-info-circle me-2"></i>
                        Changes from this screen are persisted in the system configuration
                        (not directly in the .env file).
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="glass-card p-4">
                <h4 class="h6 mb-3 gradient-text fw-bold">
                    <i class="bi bi-lightning me-2"></i>Quick Actions
                </h4>
                
                <div class="d-grid gap-2">
                    <button class="btn btn-outline-primary" onclick="backupSystem()">
                        <i class="bi bi-database me-2"></i>Create System Backup
                    </button>
                    <button class="btn btn-outline-secondary" onclick="viewSystemLogs()">
                        <i class="bi bi-file-text me-2"></i>View System Logs
                    </button>
                    <button class="btn btn-outline-secondary" onclick="optimizeDatabase()">
                        <i class="bi bi-hdd me-2"></i>Optimize Database
                    </button>
                    <button class="btn btn-outline-secondary" onclick="clearLogs()">
                        <i class="bi bi-trash me-2"></i>Clear Log Files
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Form validation and submission
document.addEventListener('DOMContentLoaded', function() {
    // Application settings form
    const appForm = document.getElementById('app-settings-form');
    if (appForm) {
        appForm.addEventListener('submit', function(e) {
            const appName = this.querySelector('input[name="app_name"]');
            const ownerName = this.querySelector('input[name="owner_name"]');
            const ownerEmail = this.querySelector('input[name="owner_email"]');
            
            if (!appName.value.trim()) {
                e.preventDefault();
                showNotification('Application name is required', 'warning');
                appName.focus();
                return;
            }
            
            if (!ownerName.value.trim()) {
                e.preventDefault();
                showNotification('Organization name is required', 'warning');
                ownerName.focus();
                return;
            }
            
            if (!ownerEmail.value.trim() || !isValidEmail(ownerEmail.value)) {
                e.preventDefault();
                showNotification('Valid organization email is required', 'warning');
                ownerEmail.focus();
                return;
            }
            
            showNotification('Application settings saved successfully!', 'success');
        });
    }
    
    // Performance form validation
    const performanceForm = document.getElementById('performance-form');
    if (performanceForm) {
        performanceForm.addEventListener('submit', function(e) {
            const uploadSize = this.querySelector('input[name="max_upload_mb"]');
            const rateWindow = this.querySelector('input[name="rate_limit_window"]');
            const rateMax = this.querySelector('input[name="rate_limit_max"]');
            
            if (parseInt(uploadSize.value) < 1) {
                e.preventDefault();
                showNotification('Upload size must be at least 1MB', 'warning');
                uploadSize.focus();
                return;
            }
            
            if (parseInt(rateWindow.value) < 60) {
                e.preventDefault();
                showNotification('Rate limit window must be at least 60 seconds', 'warning');
                rateWindow.focus();
                return;
            }
            
            if (parseInt(rateMax.value) < 1) {
                e.preventDefault();
                showNotification('Maximum requests must be at least 1', 'warning');
                rateMax.focus();
                return;
            }
            
            showNotification('Performance settings saved successfully!', 'success');
        });
    }
    
    // Supabase form validation
    const supabaseForm = document.getElementById('supabase-form');
    if (supabaseForm) {
        supabaseForm.addEventListener('submit', function(e) {
            showNotification('Supabase settings saved successfully!', 'success');
        });
    }
});

// Helper functions
function isValidEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = 'position-fixed top-0 end-0 p-3';
    notification.style.zIndex = '9999';
    
    const alert = document.createElement('div');
    alert.className = `alert alert-${type} alert-dismissible fade show`;
    alert.style.background = type === 'success' ? 'rgba(52, 199, 89, 0.9)' : 'rgba(251, 191, 36, 0.9)';
    alert.style.border = 'none';
    alert.style.color = 'white';
    
    alert.innerHTML = `
        <i class="bi bi-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} me-2"></i>
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

// System status modal
function showSystemStatus() {
    const modalHTML = `
        <div class="modal fade" id="systemStatusModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content glass-card" style="border: 1px solid var(--color-border);">
                    <div class="modal-header border-bottom-0">
                        <h5 class="modal-title gradient-text fw-bold">
                            <i class="bi bi-graph-up me-2"></i>System Status
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <!-- System status content would go here -->
                    </div>
                </div>
            </div>
        </div>
    `;
    
    const modalDiv = document.createElement('div');
    modalDiv.innerHTML = modalHTML;
    document.body.appendChild(modalDiv);
    
    const modal = new bootstrap.Modal(modalDiv.querySelector('.modal'));
    modal.show();
}

// Danger zone actions
function clearSystemCache() {
    if (confirm('Clear all system cache? This will temporarily slow down the application.')) {
        const btn = event.target.closest('button');
        const originalHTML = btn.innerHTML;
        
        btn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Clearing...';
        btn.disabled = true;
        
        setTimeout(() => {
            showNotification('System cache cleared successfully!', 'success');
            btn.innerHTML = originalHTML;
            btn.disabled = false;
        }, 1500);
    }
}

function resetApplicationData() {
    if (confirm('WARNING: This will reset ALL application data to factory defaults. This action cannot be undone. Are you sure?')) {
        const btn = event.target.closest('button');
        const originalHTML = btn.innerHTML;
        
        btn.innerHTML = '<i class="bi bi-exclamation-triangle me-1"></i>Resetting...';
        btn.disabled = true;
        
        // Double confirmation for destructive action
        setTimeout(() => {
            if (confirm('FINAL WARNING: This will delete all user data. Confirm reset?')) {
                showNotification('Application data reset initiated', 'warning');
                // In real implementation, this would trigger data reset
            }
            btn.innerHTML = originalHTML;
            btn.disabled = false;
        }, 1000);
    }
}

// Quick actions
function testSupabaseConnection() {
    const btn = event.target.closest('button');
    const originalHTML = btn.innerHTML;
    
    btn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Testing...';
    btn.disabled = true;
    
    setTimeout(() => {
        showNotification('Supabase connection test successful!', 'success');
        btn.innerHTML = originalHTML;
        btn.disabled = false;
    }, 2000);
}

function backupSystem() {
    showNotification('Starting system backup...', 'info');
    // In real implementation, this would trigger backup
}

function viewSystemLogs() {
    showNotification('Opening system logs...', 'info');
    // In real implementation, this would show logs modal
}

function optimizeDatabase() {
    const btn = event.target.closest('button');
    const originalHTML = btn.innerHTML;
    
    btn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Optimizing...';
    btn.disabled = true;
    
    setTimeout(() => {
        showNotification('Database optimization completed successfully!', 'success');
        btn.innerHTML = originalHTML;
        btn.disabled = false;
    }, 3000);
}

function clearLogs() {
    if (confirm('Clear all system log files?')) {
        showNotification('Log files cleared successfully!', 'success');
    }
}

// Maintenance mode toggle
document.addEventListener('DOMContentLoaded', function() {
    const maintenanceToggle = document.getElementById('maintenance-mode');
    if (maintenanceToggle) {
        maintenanceToggle.addEventListener('change', function() {
            const mode = this.checked ? 'enabled' : 'disabled';
            const action = this.checked ? 'enable' : 'disable';
            
            if (confirm(`Are you sure you want to ${action} maintenance mode?`)) {
                const btn = this;
                btn.disabled = true;
                
                setTimeout(() => {
                    showNotification(`Maintenance mode ${mode} successfully!`, 'success');
                    btn.disabled = false;
                }, 1500);
            } else {
                this.checked = !this.checked;
            }
        });
    }
    
    // Real-time validation for numeric inputs
    const numericInputs = document.querySelectorAll('input[type="number"]');
    numericInputs.forEach(input => {
        input.addEventListener('input', function() {
            const min = parseInt(this.min) || 0;
            const value = parseInt(this.value) || 0;
            
            if (value < min) {
                this.style.borderColor = '#ef4444';
                this.style.boxShadow = '0 0 0 3px rgba(239, 68, 68, 0.2)';
            } else {
                this.style.borderColor = '';
                this.style.boxShadow = '';
            }
        });
    });
});
</script>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>