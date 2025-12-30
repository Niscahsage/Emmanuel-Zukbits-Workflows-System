<?php
// app/views/users/profile.php
// profile.php displays and edits the currently logged-in user's own profile.

/** @var array $user */

use App\core\View;

$id        = (int)($user['id'] ?? 0);
$name      = $user['name'] ?? '';
$email     = $user['email'] ?? '';
$phone     = $user['phone'] ?? '';
$location  = $user['location'] ?? '';
$roleName  = $user['role_name'] ?? $user['role_key'] ?? '';
$createdAt = $user['created_at'] ?? '';
$updatedAt = $user['updated_at'] ?? '';
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile | ZukBits Online</title>
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
                radial-gradient(circle at 15% 20%, rgba(56, 189, 248, 0.05) 0%, transparent 40%),
                radial-gradient(circle at 85% 80%, rgba(168, 85, 247, 0.05) 0%, transparent 40%);
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

        .profile-header {
            background: var(--gradient-primary);
            color: white;
            padding: 30px;
            border-radius: 16px;
            margin-bottom: 30px;
            position: relative;
            overflow: hidden;
        }

        .profile-header::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 120px;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1));
        }

        .profile-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: var(--gradient-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 2.5rem;
            font-weight: 600;
            margin: 0 auto 20px;
            border: 4px solid rgba(255, 255, 255, 0.2);
        }

        .form-section {
            background: rgba(15, 23, 42, 0.5);
            border: 1px solid rgba(148, 163, 253, 0.2);
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 24px;
        }

        .form-section-title {
            color: var(--color-text);
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 2px solid rgba(56, 189, 248, 0.2);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .form-control, .form-select {
            background: var(--color-surface-alt);
            border: 2px solid var(--color-border);
            color: var(--color-text);
            border-radius: 8px;
            padding: 12px 16px;
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

        .info-card {
            background: rgba(56, 189, 248, 0.05);
            border: 2px solid rgba(56, 189, 248, 0.1);
            border-radius: 12px;
            padding: 20px;
        }

        .info-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid rgba(148, 163, 253, 0.1);
        }

        .info-item:last-child {
            border-bottom: none;
        }

        .info-label {
            color: var(--color-text-muted);
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .info-value {
            color: var(--color-text);
            font-weight: 500;
        }

        .password-strength {
            height: 6px;
            background: var(--color-border);
            border-radius: 3px;
            margin-top: 8px;
            overflow: hidden;
        }

        .password-strength-bar {
            height: 100%;
            border-radius: 3px;
            transition: width 0.3s ease;
        }

        .strength-weak { background: #ef4444; }
        .strength-fair { background: #f59e0b; }
        .strength-good { background: #10b981; }
        .strength-strong { background: var(--color-accent-green); }

        .btn-primary {
            background: var(--gradient-primary);
            border: none;
            color: white;
            padding: 12px 32px;
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
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-outline-secondary:hover {
            background: rgba(56, 189, 248, 0.1);
            border-color: var(--color-accent-blue);
            color: var(--color-accent-blue);
        }

        .security-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            background: rgba(56, 189, 248, 0.1);
            color: var(--color-accent-blue);
            border-radius: 20px;
            font-weight: 500;
            font-size: 0.9rem;
        }

        .activity-log {
            max-height: 300px;
            overflow-y: auto;
        }

        .activity-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            border-bottom: 1px solid rgba(148, 163, 253, 0.1);
        }

        .activity-item:last-child {
            border-bottom: none;
        }

        .activity-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(56, 189, 248, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--color-accent-blue);
        }

        .activity-details {
            flex: 1;
        }

        .activity-time {
            color: var(--color-text-muted);
            font-size: 0.85rem;
        }

        .profile-tabs {
            border-bottom: 2px solid var(--color-border);
            display: flex;
            gap: 8px;
            margin-bottom: 30px;
        }

        .profile-tab {
            padding: 12px 24px;
            background: transparent;
            border: none;
            color: var(--color-text-muted);
            font-weight: 500;
            border-radius: 8px 8px 0 0;
            transition: all 0.3s ease;
            position: relative;
        }

        .profile-tab:hover {
            background: rgba(56, 189, 248, 0.1);
            color: var(--color-accent-blue);
        }

        .profile-tab.active {
            background: var(--gradient-primary);
            color: white;
            box-shadow: 0 4px 15px var(--shadow-blue);
        }

        .profile-tab.active::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            right: 0;
            height: 2px;
            background: var(--gradient-primary);
        }

        @media (max-width: 768px) {
            .profile-header {
                padding: 20px;
                text-align: center;
            }
            
            .profile-avatar {
                width: 80px;
                height: 80px;
                font-size: 2rem;
            }
            
            .form-section {
                padding: 16px;
            }
            
            .profile-tabs {
                overflow-x: auto;
                padding-bottom: 10px;
            }
            
            .profile-tab {
                white-space: nowrap;
                padding: 10px 16px;
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
            <div class="profile-header">
                <div class="text-center">
                    <div class="profile-avatar">
                        <?= strtoupper(substr($name, 0, 2)) ?>
                    </div>
                    <h1 class="h3 mb-2 fw-bold">My Profile</h1>
                    <p class="mb-0 opacity-90">
                        <i class="bi bi-person-circle me-2"></i>
                        <?= View::e($name) ?> · <?= View::e($roleName) ?>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Profile Tabs -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="profile-tabs">
                <button class="profile-tab active" data-tab="profile">
                    <i class="bi bi-person me-2"></i>Profile
                </button>
                <button class="profile-tab" data-tab="security">
                    <i class="bi bi-shield me-2"></i>Security
                </button>
                <button class="profile-tab" data-tab="activity">
                    <i class="bi bi-clock-history me-2"></i>Activity
                </button>
                <button class="profile-tab" data-tab="settings">
                    <i class="bi bi-gear me-2"></i>Settings
                </button>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Left Column - Profile & Security -->
        <div class="col-lg-8">
            <!-- Profile Tab -->
            <div class="tab-content active" id="profile-tab">
                <div class="glass-card p-4 mb-4">
                    <h3 class="h5 mb-3 gradient-text fw-bold">
                        <i class="bi bi-person-circle me-2"></i>Personal Information
                    </h3>
                    
                    <div class="form-section">
                        <form method="post" action="/profile/update" id="profile-form">
                            <input type="hidden" name="id" value="<?= View::e((string)$id) ?>">
                            
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">
                                        <i class="bi bi-person me-1"></i>Full Name
                                        <span class="required">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="bi bi-person-fill"></i>
                                        </span>
                                        <input
                                            type="text"
                                            name="name"
                                            class="form-control"
                                            required
                                            value="<?= View::e($name) ?>"
                                        >
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">
                                        <i class="bi bi-envelope me-1"></i>Email Address
                                        <span class="required">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="bi bi-envelope-fill"></i>
                                        </span>
                                        <input
                                            type="email"
                                            name="email"
                                            class="form-control"
                                            required
                                            value="<?= View::e($email) ?>"
                                        >
                                    </div>
                                    <div class="form-hint">
                                        <i class="bi bi-info-circle"></i>
                                        Used for login and notifications
                                    </div>
                                </div>
                            </div>

                            <div class="row g-3 mt-2">
                                <div class="col-md-6">
                                    <label class="form-label">
                                        <i class="bi bi-phone me-1"></i>Phone Number
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="bi bi-telephone-fill"></i>
                                        </span>
                                        <input
                                            type="text"
                                            name="phone"
                                            class="form-control"
                                            value="<?= View::e($phone) ?>"
                                            placeholder="+254 700 000 000"
                                        >
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">
                                        <i class="bi bi-geo-alt me-1"></i>Location
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="bi bi-geo-alt-fill"></i>
                                        </span>
                                        <input
                                            type="text"
                                            name="location"
                                            class="form-control"
                                            value="<?= View::e($location) ?>"
                                            placeholder="e.g. Nairobi, Kenya"
                                        >
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end mt-4">
                                <button type="submit" class="btn btn-primary" id="save-profile-btn">
                                    <i class="bi bi-save me-2"></i>Save Profile
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Security Tab -->
            <div class="tab-content" id="security-tab">
                <div class="glass-card p-4 mb-4">
                    <h3 class="h5 mb-3 gradient-text fw-bold">
                        <i class="bi bi-shield me-2"></i>Security Settings
                    </h3>
                    
                    <div class="form-section">
                        <form method="post" action="/profile/password" id="password-form">
                            <div class="mb-4">
                                <label class="form-label">
                                    <i class="bi bi-lock me-1"></i>Current Password
                                    <span class="required">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-key-fill"></i>
                                    </span>
                                    <input
                                        type="password"
                                        name="current_password"
                                        class="form-control"
                                        autocomplete="current-password"
                                        required
                                    >
                                    <button class="btn btn-outline-secondary border-start-0" type="button" onclick="togglePassword(this)">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">
                                        <i class="bi bi-lock me-1"></i>New Password
                                        <span class="required">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="bi bi-key-fill"></i>
                                        </span>
                                        <input
                                            type="password"
                                            name="new_password"
                                            id="new-password"
                                            class="form-control"
                                            autocomplete="new-password"
                                            required
                                            oninput="checkPasswordStrength(this.value)"
                                        >
                                        <button class="btn btn-outline-secondary border-start-0" type="button" onclick="togglePassword(this)">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </div>
                                    <div class="password-strength mt-2">
                                        <div id="password-strength-bar" class="password-strength-bar" style="width: 0%"></div>
                                    </div>
                                    <div id="password-strength-text" class="form-hint mt-2"></div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">
                                        <i class="bi bi-lock me-1"></i>Confirm New Password
                                        <span class="required">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="bi bi-key-fill"></i>
                                        </span>
                                        <input
                                            type="password"
                                            name="new_password_confirmation"
                                            id="confirm-password"
                                            class="form-control"
                                            autocomplete="new-password"
                                            required
                                            oninput="checkPasswordMatch(this.value)"
                                        >
                                        <button class="btn btn-outline-secondary border-start-0" type="button" onclick="togglePassword(this)">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </div>
                                    <div id="password-match-text" class="form-hint mt-2"></div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mt-4">
                                <div class="form-hint">
                                    <i class="bi bi-lightbulb"></i>
                                    Use a strong password for better security
                                </div>
                                <button type="submit" class="btn btn-primary" id="update-password-btn">
                                    <i class="bi bi-shield-check me-2"></i>Update Password
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Security Status -->
                <div class="glass-card p-4">
                    <h3 class="h5 mb-3 gradient-text fw-bold">
                        <i class="bi bi-shield-check me-2"></i>Security Status
                    </h3>
                    
                    <div class="info-card">
                        <div class="info-item">
                            <div class="info-label">
                                <i class="bi bi-envelope"></i>Email Verification
                            </div>
                            <div class="info-value">
                                <span class="badge bg-success bg-opacity-25 text-success">
                                    <i class="bi bi-check-circle me-1"></i>Verified
                                </span>
                            </div>
                        </div>

                        <div class="info-item">
                            <div class="info-label">
                                <i class="bi bi-clock-history"></i>Last Password Change
                            </div>
                            <div class="info-value">
                                <?= $updatedAt ? date('M d, Y', strtotime($updatedAt)) : 'Unknown' ?>
                            </div>
                        </div>

                        <div class="info-item">
                            <div class="info-label">
                                <i class="bi bi-laptop"></i>Active Sessions
                            </div>
                            <div class="info-value">
                                <span class="badge bg-info bg-opacity-25 text-info">1 Active</span>
                            </div>
                        </div>

                        <div class="info-item">
                            <div class="info-label">
                                <i class="bi bi-shield-lock"></i>Two-Factor Authentication
                            </div>
                            <div class="info-value">
                                <span class="badge bg-secondary bg-opacity-25 text-secondary">
                                    <i class="bi bi-x-circle me-1"></i>Not Enabled
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button class="btn btn-outline-secondary" onclick="viewSessions()">
                            <i class="bi bi-laptop me-2"></i>View Sessions
                        </button>
                        <button class="btn btn-outline-secondary" onclick="enable2FA()">
                            <i class="bi bi-shield-lock me-2"></i>Enable 2FA
                        </button>
                        <button class="btn btn-outline-secondary" onclick="logoutOtherDevices()">
                            <i class="bi bi-door-closed me-2"></i>Logout Others
                        </button>
                    </div>
                </div>
            </div>

            <!-- Activity Tab -->
            <div class="tab-content" id="activity-tab">
                <div class="glass-card p-4">
                    <h3 class="h5 mb-3 gradient-text fw-bold">
                        <i class="bi bi-clock-history me-2"></i>Recent Activity
                    </h3>
                    
                    <div class="activity-log">
                        <div class="activity-item">
                            <div class="activity-icon">
                                <i class="bi bi-box-arrow-in-right"></i>
                            </div>
                            <div class="activity-details">
                                <div class="fw-medium">Logged into system</div>
                                <div class="text-muted small">From Chrome on Windows</div>
                            </div>
                            <div class="activity-time">Just now</div>
                        </div>

                        <div class="activity-item">
                            <div class="activity-icon">
                                <i class="bi bi-file-text"></i>
                            </div>
                            <div class="activity-details">
                                <div class="fw-medium">Submitted weekly report</div>
                                <div class="text-muted small">Week of Dec 10, 2024</div>
                            </div>
                            <div class="activity-time">2 hours ago</div>
                        </div>

                        <div class="activity-item">
                            <div class="activity-icon">
                                <i class="bi bi-calendar-week"></i>
                            </div>
                            <div class="activity-details">
                                <div class="fw-medium">Updated weekly schedule</div>
                                <div class="text-muted small">Added 3 new tasks</div>
                            </div>
                            <div class="activity-time">1 day ago</div>
                        </div>

                        <div class="activity-item">
                            <div class="activity-icon">
                                <i class="bi bi-key"></i>
                            </div>
                            <div class="activity-details">
                                <div class="fw-medium">Changed password</div>
                                <div class="text-muted small">Security update</div>
                            </div>
                            <div class="activity-time">1 week ago</div>
                        </div>

                        <div class="activity-item">
                            <div class="activity-icon">
                                <i class="bi bi-person"></i>
                            </div>
                            <div class="activity-details">
                                <div class="fw-medium">Updated profile information</div>
                                <div class="text-muted small">Changed phone number</div>
                            </div>
                            <div class="activity-time">2 weeks ago</div>
                        </div>
                    </div>

                    <div class="text-center mt-4">
                        <button class="btn btn-outline-secondary" onclick="loadMoreActivity()">
                            <i class="bi bi-arrow-clockwise me-2"></i>Load More Activity
                        </button>
                    </div>
                </div>
            </div>

            <!-- Settings Tab -->
            <div class="tab-content" id="settings-tab">
                <div class="glass-card p-4">
                    <h3 class="h5 mb-3 gradient-text fw-bold">
                        <i class="bi bi-gear me-2"></i>Account Settings
                    </h3>
                    
                    <div class="form-section">
                        <div class="mb-4">
                            <h6 class="form-label">
                                <i class="bi bi-bell me-2"></i>Notification Preferences
                            </h6>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="notify-email" checked>
                                <label class="form-check-label" for="notify-email">
                                    Email notifications
                                </label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="notify-reports" checked>
                                <label class="form-check-label" for="notify-reports">
                                    Weekly report reminders
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="notify-approvals">
                                <label class="form-check-label" for="notify-approvals">
                                    Approval request notifications
                                </label>
                            </div>
                        </div>

                        <div class="mb-4">
                            <h6 class="form-label">
                                <i class="bi bi-palette me-2"></i>Theme Preferences
                            </h6>
                            <select class="form-select" id="theme-select">
                                <option value="dark" selected>Dark Theme</option>
                                <option value="light">Light Theme</option>
                                <option value="auto">Auto (System)</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <h6 class="form-label">
                                <i class="bi bi-clock me-2"></i>Time Zone
                            </h6>
                            <select class="form-select" id="timezone-select">
                                <option value="Africa/Nairobi" selected>East Africa Time (EAT)</option>
                                <option value="UTC">UTC</option>
                                <option value="America/New_York">Eastern Time (ET)</option>
                                <option value="Europe/London">London Time (GMT)</option>
                            </select>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button class="btn btn-primary" onclick="saveSettings()">
                                <i class="bi bi-save me-2"></i>Save Settings
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column - Account Overview -->
        <div class="col-lg-4">
            <div class="glass-card p-4 mb-4">
                <h3 class="h5 mb-3 gradient-text fw-bold">
                    <i class="bi bi-info-circle me-2"></i>Account Overview
                </h3>
                
                <div class="info-card">
                    <div class="info-item">
                        <div class="info-label">
                            <i class="bi bi-person-badge"></i>Role
                        </div>
                        <div class="info-value">
                            <span class="security-badge">
                                <i class="bi bi-shield-check"></i>
                                <?= View::e($roleName) ?>
                            </span>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">
                            <i class="bi bi-fingerprint"></i>User ID
                        </div>
                        <div class="info-value">#<?= $id ?></div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">
                            <i class="bi bi-calendar-plus"></i>Account Created
                        </div>
                        <div class="info-value">
                            <?= $createdAt ? date('M d, Y', strtotime($createdAt)) : 'Unknown' ?>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">
                            <i class="bi bi-arrow-clockwise"></i>Last Updated
                        </div>
                        <div class="info-value">
                            <?= $updatedAt ? date('M d, Y', strtotime($updatedAt)) : 'Never' ?>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">
                            <i class="bi bi-box-arrow-in-right"></i>Last Login
                        </div>
                        <div class="info-value">Today, 14:30</div>
                    </div>
                </div>
            </div>

            <div class="glass-card p-4 mb-4">
                <h3 class="h5 mb-3 gradient-text fw-bold">
                    <i class="bi bi-bar-chart me-2"></i>Quick Stats
                </h3>
                
                <div class="row g-3">
                    <div class="col-6">
                        <div class="text-center p-3 rounded" style="background: rgba(56, 189, 248, 0.1);">
                            <div class="fw-bold fs-4">12</div>
                            <div class="text-muted small">Weekly Reports</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="text-center p-3 rounded" style="background: rgba(255, 200, 87, 0.1);">
                            <div class="fw-bold fs-4">8</div>
                            <div class="text-muted small">Active Projects</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="text-center p-3 rounded" style="background: rgba(52, 199, 89, 0.1);">
                            <div class="fw-bold fs-4">45</div>
                            <div class="text-muted small">Completed Tasks</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="text-center p-3 rounded" style="background: rgba(168, 85, 247, 0.1);">
                            <div class="fw-bold fs-4">98%</div>
                            <div class="text-muted small">On-time Rate</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="glass-card p-4">
                <h3 class="h5 mb-3 gradient-text fw-bold">
                    <i class="bi bi-shield-exclamation me-2"></i>Security Tips
                </h3>
                
                <div class="alert alert-secondary mb-3">
                    <i class="bi bi-check-circle me-2"></i>
                    <small>Your password was last changed recently</small>
                </div>
                <div class="alert alert-secondary mb-3">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <small>Consider enabling two-factor authentication</small>
                </div>
                <div class="alert alert-secondary mb-0">
                    <i class="bi bi-info-circle me-2"></i>
                    <small>Regularly review your active sessions</small>
                </div>

                <div class="text-center mt-4">
                    <button class="btn btn-outline-primary w-100" onclick="runSecurityCheck()">
                        <i class="bi bi-shield-check me-2"></i>Run Security Check
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Tab switching functionality
document.addEventListener('DOMContentLoaded', function() {
    const tabs = document.querySelectorAll('.profile-tab');
    const tabContents = document.querySelectorAll('.tab-content');
    
    tabs.forEach(tab => {
        tab.addEventListener('click', function() {
            const tabId = this.getAttribute('data-tab');
            
            // Update active tab
            tabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            
            // Show corresponding content
            tabContents.forEach(content => {
                content.classList.remove('active');
                if (content.id === `${tabId}-tab`) {
                    content.classList.add('active');
                }
            });
            
            // Save active tab to localStorage
            localStorage.setItem('activeProfileTab', tabId);
        });
    });
    
    // Restore active tab from localStorage
    const activeTab = localStorage.getItem('activeProfileTab') || 'profile';
    const tabToActivate = document.querySelector(`.profile-tab[data-tab="${activeTab}"]`);
    if (tabToActivate) {
        tabToActivate.click();
    }
});

// Password strength checker
function checkPasswordStrength(password) {
    const strengthBar = document.getElementById('password-strength-bar');
    const strengthText = document.getElementById('password-strength-text');
    
    let strength = 0;
    let text = '';
    let className = '';
    
    if (password.length >= 8) strength++;
    if (password.match(/[a-z]/) && password.match(/[A-Z]/)) strength++;
    if (password.match(/\d/)) strength++;
    if (password.match(/[^a-zA-Z\d]/)) strength++;
    
    switch(strength) {
        case 0:
        case 1:
            text = 'Weak password';
            className = 'strength-weak';
            break;
        case 2:
            text = 'Fair password';
            className = 'strength-fair';
            break;
        case 3:
            text = 'Good password';
            className = 'strength-good';
            break;
        case 4:
            text = 'Strong password';
            className = 'strength-strong';
            break;
    }
    
    const width = strength * 25;
    strengthBar.style.width = width + '%';
    strengthBar.className = 'password-strength-bar ' + className;
    strengthText.textContent = text;
    strengthText.className = 'form-hint mt-2 ' + className.replace('strength-', 'text-');
}

// Password match checker
function checkPasswordMatch(confirmPassword) {
    const password = document.getElementById('new-password').value;
    const matchText = document.getElementById('password-match-text');
    
    if (!confirmPassword) {
        matchText.textContent = '';
        matchText.className = 'form-hint mt-2';
        return;
    }
    
    if (confirmPassword === password) {
        matchText.innerHTML = '<i class="bi bi-check-circle text-success me-1"></i>Passwords match';
        matchText.className = 'form-hint mt-2 text-success';
    } else {
        matchText.innerHTML = '<i class="bi bi-exclamation-triangle text-warning me-1"></i>Passwords do not match';
        matchText.className = 'form-hint mt-2 text-warning';
    }
}

// Toggle password visibility
function togglePassword(button) {
    const input = button.parentElement.querySelector('input');
    const icon = button.querySelector('i');
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'bi bi-eye-slash';
    } else {
        input.type = 'password';
        icon.className = 'bi bi-eye';
    }
}

// Form validation
document.addEventListener('DOMContentLoaded', function() {
    const profileForm = document.getElementById('profile-form');
    const passwordForm = document.getElementById('password-form');
    
    if (profileForm) {
        profileForm.addEventListener('submit', function(e) {
            const nameInput = this.querySelector('input[name="name"]');
            const emailInput = this.querySelector('input[name="email"]');
            
            if (!nameInput.value.trim()) {
                e.preventDefault();
                showNotification('Please enter your full name', 'warning');
                nameInput.focus();
                return;
            }
            
            if (!emailInput.value.trim() || !isValidEmail(emailInput.value)) {
                e.preventDefault();
                showNotification('Please enter a valid email address', 'warning');
                emailInput.focus();
                return;
            }
            
            const saveBtn = document.getElementById('save-profile-btn');
            saveBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Saving...';
            saveBtn.disabled = true;
            
            showNotification('Profile updated successfully!', 'success');
        });
    }
    
    if (passwordForm) {
        passwordForm.addEventListener('submit', function(e) {
            const currentPass = this.querySelector('input[name="current_password"]');
            const newPass = this.querySelector('input[name="new_password"]');
            const confirmPass = this.querySelector('input[name="new_password_confirmation"]');
            
            if (newPass.value.length < 8) {
                e.preventDefault();
                showNotification('New password must be at least 8 characters long', 'warning');
                newPass.focus();
                return;
            }
            
            if (newPass.value !== confirmPass.value) {
                e.preventDefault();
                showNotification('New passwords do not match', 'warning');
                confirmPass.focus();
                return;
            }
            
            const updateBtn = document.getElementById('update-password-btn');
            updateBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Updating...';
            updateBtn.disabled = true;
            
            showNotification('Password updated successfully!', 'success');
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
    alert.style.background = type === 'success' ? 'rgba(52, 199, 89, 0.9)' : 
                             type === 'warning' ? 'rgba(251, 191, 36, 0.9)' : 
                             'rgba(56, 189, 248, 0.9)';
    alert.style.border = 'none';
    alert.style.color = 'white';
    
    alert.innerHTML = `
        <i class="bi bi-${type === 'success' ? 'check-circle' : 
                         type === 'warning' ? 'exclamation-triangle' : 'info-circle'} me-2"></i>
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

// Security actions
function viewSessions() {
    showNotification('Loading active sessions...', 'info');
}

function enable2FA() {
    showNotification('Setting up two-factor authentication...', 'info');
}

function logoutOtherDevices() {
    if (confirm('Log out all other devices? This will sign you out from all other sessions.')) {
        showNotification('Logging out other devices...', 'info');
    }
}

function loadMoreActivity() {
    const btn = event.target;
    btn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Loading...';
    btn.disabled = true;
    
    setTimeout(() => {
        showNotification('More activity loaded', 'success');
        btn.innerHTML = '<i class="bi bi-arrow-clockwise me-2"></i>Load More Activity';
        btn.disabled = false;
    }, 1000);
}

function saveSettings() {
    showNotification('Settings saved successfully!', 'success');
}

function runSecurityCheck() {
    const btn = event.target.closest('button');
    const originalHTML = btn.innerHTML;
    
    btn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Checking...';
    btn.disabled = true;
    
    setTimeout(() => {
        btn.innerHTML = '<i class="bi bi-shield-check me-2"></i>Security Check Complete';
        btn.className = 'btn btn-success w-100';
        
        showNotification('Security check completed. All systems secure!', 'success');
        
        setTimeout(() => {
            btn.innerHTML = originalHTML;
            btn.className = 'btn btn-outline-primary w-100';
            btn.disabled = false;
        }, 2000);
    }, 2000);
}

// Auto-generate password
function generatePassword() {
    const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*';
    let password = '';
    for (let i = 0; i < 12; i++) {
        password += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    
    const passwordInput = document.getElementById('new-password');
    const confirmInput = document.getElementById('confirm-password');
    
    passwordInput.value = password;
    confirmInput.value = password;
    
    checkPasswordStrength(password);
    checkPasswordMatch(password);
    
    showNotification('Strong password generated', 'success');
}

// Add generate password button to security tab
document.addEventListener('DOMContentLoaded', function() {
    const passwordGroup = document.querySelector('#new-password').parentElement;
    const generateBtn = document.createElement('button');
    generateBtn.type = 'button';
    generateBtn.className = 'btn btn-outline-secondary border-start-0';
    generateBtn.innerHTML = '<i class="bi bi-shuffle"></i>';
    generateBtn.title = 'Generate strong password';
    generateBtn.onclick = generatePassword;
    
    passwordGroup.appendChild(generateBtn);
});
</script>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>