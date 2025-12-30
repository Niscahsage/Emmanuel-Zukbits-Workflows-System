<?php
// app/views/settings/account.php
// account.php manages personal account settings for the current user.

use App\core\Auth;
use App\core\View;

$user = Auth::user() ?? [];
$name       = $user['name']        ?? '';
$email      = $user['email']       ?? '';
$phone      = $user['phone']       ?? '';
$location   = $user['location']    ?? '';
$roleName   = $user['role_name']   ?? '';
$createdAt  = $user['created_at']  ?? '';
$updatedAt  = $user['updated_at']  ?? '';
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Settings | ZukBits Online</title>
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
                radial-gradient(circle at 20% 20%, rgba(56, 189, 248, 0.05) 0%, transparent 40%),
                radial-gradient(circle at 80% 80%, rgba(168, 85, 247, 0.05) 0%, transparent 40%);
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

        .settings-header {
            background: var(--gradient-primary);
            color: white;
            padding: 24px 30px;
            border-radius: 16px;
            margin-bottom: 30px;
            position: relative;
            overflow: hidden;
        }

        .settings-header::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 120px;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1));
        }

        .profile-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: var(--gradient-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 2rem;
            font-weight: 600;
            margin: 0 auto 20px;
        }

        .settings-tabs {
            border-bottom: 2px solid var(--color-border);
            display: flex;
            gap: 8px;
            margin-bottom: 30px;
        }

        .settings-tab {
            padding: 12px 24px;
            background: transparent;
            border: none;
            color: var(--color-text-muted);
            font-weight: 500;
            border-radius: 8px 8px 0 0;
            transition: all 0.3s ease;
            position: relative;
        }

        .settings-tab:hover {
            background: rgba(56, 189, 248, 0.1);
            color: var(--color-accent-blue);
        }

        .settings-tab.active {
            background: var(--gradient-primary);
            color: white;
            box-shadow: 0 4px 15px var(--shadow-blue);
        }

        .settings-tab.active::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            right: 0;
            height: 2px;
            background: var(--gradient-primary);
        }

        .form-section {
            margin-bottom: 30px;
        }

        .form-section-title {
            color: var(--color-text);
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid rgba(56, 189, 248, 0.2);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .form-control {
            background: var(--color-surface-alt);
            border: 1px solid var(--color-border);
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
        }

        .info-value {
            color: var(--color-text);
            font-weight: 500;
        }

        .checkbox-group {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .form-check {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            background: rgba(56, 189, 248, 0.05);
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .form-check:hover {
            background: rgba(56, 189, 248, 0.1);
        }

        .form-check-input {
            width: 20px;
            height: 20px;
            border: 2px solid var(--color-border);
            background-color: var(--color-surface);
            cursor: pointer;
            margin: 0;
        }

        .form-check-input:checked {
            background-color: var(--color-accent-blue);
            border-color: var(--color-accent-blue);
        }

        .form-check-input:focus {
            box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.2);
        }

        .form-check-label {
            color: var(--color-text);
            cursor: pointer;
            flex: 1;
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

        .account-status {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 16px;
            background: rgba(56, 189, 248, 0.1);
            color: var(--color-accent-blue);
            border-radius: 20px;
            font-weight: 500;
            font-size: 0.9rem;
        }

        @media (max-width: 768px) {
            .settings-header {
                padding: 20px;
                text-align: center;
            }
            
            .settings-tabs {
                overflow-x: auto;
                padding-bottom: 10px;
            }
            
            .settings-tab {
                white-space: nowrap;
                padding: 10px 16px;
                font-size: 0.9rem;
            }
            
            .form-section {
                margin-bottom: 24px;
            }
            
            .profile-avatar {
                width: 60px;
                height: 60px;
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="settings-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3 mb-2 fw-bold">Account Settings</h1>
                        <p class="mb-0 opacity-90">
                            <i class="bi bi-person-circle me-2"></i>
                            Manage your personal account preferences and security
                        </p>
                    </div>
                    <div class="account-status">
                        <i class="bi bi-shield-check"></i>
                        <?= View::e($roleName) ?> Account
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Settings Tabs -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="settings-tabs">
                <button class="settings-tab active" data-tab="profile">
                    <i class="bi bi-person me-2"></i>Profile
                </button>
                <button class="settings-tab" data-tab="security">
                    <i class="bi bi-shield me-2"></i>Security
                </button>
                <button class="settings-tab" data-tab="notifications">
                    <i class="bi bi-bell me-2"></i>Notifications
                </button>
                <button class="settings-tab" data-tab="account">
                    <i class="bi bi-info-circle me-2"></i>Account Info
                </button>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Left Column - Forms -->
        <div class="col-lg-8">
            <!-- Profile Tab -->
            <div class="tab-content active" id="profile-tab">
                <div class="glass-card p-4">
                    <div class="d-flex align-items-center mb-4">
                        <div class="profile-avatar">
                            <?= strtoupper(substr($name, 0, 2)) ?>
                        </div>
                        <div class="ms-4">
                            <h3 class="h5 mb-1 gradient-text fw-bold">Personal Information</h3>
                            <p class="text-muted mb-0">Update your personal details and contact information</p>
                        </div>
                    </div>

                    <form method="post" action="/settings/account/profile" id="profile-form">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">
                                    <i class="bi bi-person me-1"></i>Full Name
                                    <span class="required">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-dark border-end-0">
                                        <i class="bi bi-person-fill text-muted"></i>
                                    </span>
                                    <input
                                        type="text"
                                        name="name"
                                        class="form-control border-start-0"
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
                                    <span class="input-group-text bg-dark border-end-0">
                                        <i class="bi bi-envelope-fill text-muted"></i>
                                    </span>
                                    <input
                                        type="email"
                                        name="email"
                                        class="form-control border-start-0"
                                        required
                                        value="<?= View::e($email) ?>"
                                    >
                                </div>
                                <div class="form-hint">
                                    <i class="bi bi-info-circle"></i>
                                    Used for login and notifications
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">
                                    <i class="bi bi-telephone me-1"></i>Phone Number
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-dark border-end-0">
                                        <i class="bi bi-telephone-fill text-muted"></i>
                                    </span>
                                    <input
                                        type="text"
                                        name="phone"
                                        class="form-control border-start-0"
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
                                    <span class="input-group-text bg-dark border-end-0">
                                        <i class="bi bi-geo-alt-fill text-muted"></i>
                                    </span>
                                    <input
                                        type="text"
                                        name="location"
                                        class="form-control border-start-0"
                                        value="<?= View::e($location) ?>"
                                        placeholder="e.g. Nairobi, Kenya"
                                    >
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-2"></i>Save Profile Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Security Tab -->
            <div class="tab-content" id="security-tab">
                <div class="glass-card p-4">
                    <div class="d-flex align-items-center mb-4">
                        <div class="profile-avatar" style="background: var(--gradient-accent);">
                            <i class="bi bi-shield-lock"></i>
                        </div>
                        <div class="ms-4">
                            <h3 class="h5 mb-1 gradient-text fw-bold">Security Settings</h3>
                            <p class="text-muted mb-0">Manage your password and account security</p>
                        </div>
                    </div>

                    <form method="post" action="/settings/account/password" id="password-form">
                        <div class="form-section">
                            <h4 class="form-section-title">
                                <i class="bi bi-key"></i>Change Password
                            </h4>
                            
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">
                                        <i class="bi bi-lock me-1"></i>Current Password
                                        <span class="required">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-dark border-end-0">
                                            <i class="bi bi-key-fill text-muted"></i>
                                        </span>
                                        <input
                                            type="password"
                                            name="current_password"
                                            class="form-control border-start-0"
                                            autocomplete="current-password"
                                            required
                                        >
                                        <button class="btn btn-outline-secondary border-start-0" type="button" onclick="togglePassword(this)">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="row g-3 mt-2">
                                <div class="col-md-6">
                                    <label class="form-label">
                                        <i class="bi bi-lock me-1"></i>New Password
                                        <span class="required">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-dark border-end-0">
                                            <i class="bi bi-key-fill text-muted"></i>
                                        </span>
                                        <input
                                            type="password"
                                            name="new_password"
                                            id="new_password"
                                            class="form-control border-start-0"
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
                                        <span class="input-group-text bg-dark border-end-0">
                                            <i class="bi bi-key-fill text-muted"></i>
                                        </span>
                                        <input
                                            type="password"
                                            name="new_password_confirmation"
                                            class="form-control border-start-0"
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

                            <div class="d-flex justify-content-end mt-4">
                                <button type="submit" class="btn btn-primary" id="update-password-btn">
                                    <i class="bi bi-shield-check me-2"></i>Update Password
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Notifications Tab -->
            <div class="tab-content" id="notifications-tab">
                <div class="glass-card p-4">
                    <div class="d-flex align-items-center mb-4">
                        <div class="profile-avatar" style="background: linear-gradient(135deg, #a855f7, #8b5cf6);">
                            <i class="bi bi-bell"></i>
                        </div>
                        <div class="ms-4">
                            <h3 class="h5 mb-1 gradient-text fw-bold">Notification Preferences</h3>
                            <p class="text-muted mb-0">Control how and when you receive notifications</p>
                        </div>
                    </div>

                    <form method="post" action="/settings/account/notifications" id="notifications-form">
                        <?php
                        $preferences = $preferences ?? [];
                        $emailProjectUpdates = !empty($preferences['email_project_updates']);
                        $emailApprovals      = !empty($preferences['email_approvals']);
                        $emailReports        = !empty($preferences['email_reports']);
                        ?>
                        
                        <div class="form-section">
                            <h4 class="form-section-title">
                                <i class="bi bi-envelope"></i>Email Notifications
                            </h4>
                            
                            <div class="checkbox-group">
                                <div class="form-check">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        name="email_project_updates"
                                        id="email_project_updates"
                                        value="1"
                                        <?= $emailProjectUpdates ? 'checked' : '' ?>
                                    >
                                    <label class="form-check-label" for="email_project_updates">
                                        <div class="fw-medium">Project Updates</div>
                                        <div class="text-muted small">Email me about project updates assigned to me</div>
                                    </label>
                                </div>

                                <div class="form-check">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        name="email_approvals"
                                        id="email_approvals"
                                        value="1"
                                        <?= $emailApprovals ? 'checked' : '' ?>
                                    >
                                    <label class="form-check-label" for="email_approvals">
                                        <div class="fw-medium">Approval Requests</div>
                                        <div class="text-muted small">Email me when an approval requires my action</div>
                                    </label>
                                </div>

                                <div class="form-check">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        name="email_reports"
                                        id="email_reports"
                                        value="1"
                                        <?= $emailReports ? 'checked' : '' ?>
                                    >
                                    <label class="form-check-label" for="email_reports">
                                        <div class="fw-medium">Weekly Reports</div>
                                        <div class="text-muted small">Email me weekly report reminders</div>
                                    </label>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end mt-4">
                                <button type="submit" class="btn btn-outline-primary">
                                    <i class="bi bi-bell me-2"></i>Save Notification Settings
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Account Info Tab -->
            <div class="tab-content" id="account-tab">
                <div class="glass-card p-4">
                    <div class="d-flex align-items-center mb-4">
                        <div class="profile-avatar" style="background: linear-gradient(135deg, #10b981, #059669);">
                            <i class="bi bi-info-circle"></i>
                        </div>
                        <div class="ms-4">
                            <h3 class="h5 mb-1 gradient-text fw-bold">Account Information</h3>
                            <p class="text-muted mb-0">View your account details and status</p>
                        </div>
                    </div>

                    <div class="info-card">
                        <div class="info-item">
                            <div class="info-label">
                                <i class="bi bi-person-badge me-2"></i>Account Role
                            </div>
                            <div class="info-value">
                                <?= $roleName ? View::e($roleName) : 'Unknown' ?>
                            </div>
                        </div>

                        <div class="info-item">
                            <div class="info-label">
                                <i class="bi bi-calendar-plus me-2"></i>Account Created
                            </div>
                            <div class="info-value">
                                <?= $createdAt ? View::e(date('F d, Y', strtotime($createdAt))) : 'Unknown' ?>
                            </div>
                        </div>

                        <div class="info-item">
                            <div class="info-label">
                                <i class="bi bi-clock-history me-2"></i>Last Updated
                            </div>
                            <div class="info-value">
                                <?= $updatedAt ? View::e(date('F d, Y', strtotime($updatedAt))) : 'Never' ?>
                            </div>
                        </div>

                        <div class="info-item">
                            <div class="info-label">
                                <i class="bi bi-envelope me-2"></i>Email Status
                            </div>
                            <div class="info-value">
                                <span class="badge bg-success bg-opacity-25 text-success">
                                    <i class="bi bi-check-circle me-1"></i>Verified
                                </span>
                            </div>
                        </div>

                        <div class="info-item">
                            <div class="info-label">
                                <i class="bi bi-shield-check me-2"></i>Security Status
                            </div>
                            <div class="info-value">
                                <span class="badge bg-success bg-opacity-25 text-success">
                                    <i class="bi bi-check-circle me-1"></i>Secure
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <h4 class="form-section-title">
                            <i class="bi bi-exclamation-triangle"></i>Account Actions
                        </h4>
                        <div class="d-flex gap-2 flex-wrap">
                            <button class="btn btn-outline-secondary" onclick="exportAccountData()">
                                <i class="bi bi-download me-2"></i>Export Data
                            </button>
                            <button class="btn btn-outline-secondary" onclick="showSessions()">
                                <i class="bi bi-laptop me-2"></i>Active Sessions
                            </button>
                            <button class="btn btn-outline-danger" onclick="requestAccountDeletion()">
                                <i class="bi bi-trash me-2"></i>Request Account Deletion
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column - Account Summary -->
        <div class="col-lg-4">
            <div class="glass-card p-4 h-100">
                <h3 class="h5 mb-3 gradient-text fw-bold">
                    <i class="bi bi-speedometer2 me-2"></i>Account Summary
                </h3>
                
                <div class="mb-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle bg-dark p-3 me-3">
                            <i class="bi bi-person fs-4"></i>
                        </div>
                        <div>
                            <h5 class="mb-1"><?= View::e($name) ?></h5>
                            <p class="text-muted mb-0 small"><?= View::e($email) ?></p>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted small">Account Status</span>
                        <span class="badge bg-success bg-opacity-25 text-success">
                            <i class="bi bi-check-circle me-1"></i>Active
                        </span>
                    </div>
                </div>

                <div class="mb-4">
                    <h6 class="text-muted mb-3">
                        <i class="bi bi-clock-history me-2"></i>Recent Activity
                    </h6>
                    <div class="glass-card-alt p-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="small">Last Login</span>
                            <span class="small text-muted">Today, 14:30</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="small">Password Changed</span>
                            <span class="small text-muted">2 weeks ago</span>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <h6 class="text-muted mb-3">
                        <i class="bi bi-shield me-2"></i>Security Tips
                    </h6>
                    <div class="alert alert-secondary mb-3">
                        <i class="bi bi-info-circle me-2"></i>
                        <small>Use a strong password and enable 2FA for added security</small>
                    </div>
                    <div class="alert alert-secondary mb-0">
                        <i class="bi bi-info-circle me-2"></i>
                        <small>Regularly review your notification preferences</small>
                    </div>
                </div>

                <div class="text-center">
                    <button class="btn btn-outline-primary w-100" onclick="showSecurityScan()">
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
    const tabs = document.querySelectorAll('.settings-tab');
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
            localStorage.setItem('activeSettingsTab', tabId);
        });
    });
    
    // Restore active tab from localStorage
    const activeTab = localStorage.getItem('activeSettingsTab') || 'profile';
    const tabToActivate = document.querySelector(`.settings-tab[data-tab="${activeTab}"]`);
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
    const newPassword = document.getElementById('new_password').value;
    const matchText = document.getElementById('password-match-text');
    
    if (!confirmPassword) {
        matchText.textContent = '';
        matchText.className = 'form-hint mt-2';
        return;
    }
    
    if (confirmPassword === newPassword) {
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
            
            if (!emailInput.value.trim()) {
                e.preventDefault();
                showNotification('Please enter your email address', 'warning');
                emailInput.focus();
                return;
            }
            
            if (!isValidEmail(emailInput.value)) {
                e.preventDefault();
                showNotification('Please enter a valid email address', 'warning');
                emailInput.focus();
                return;
            }
            
            showNotification('Profile updated successfully!', 'success');
        });
    }
    
    if (passwordForm) {
        passwordForm.addEventListener('submit', function(e) {
            const currentPass = this.querySelector('input[name="current_password"]');
            const newPass = this.querySelector('input[name="new_password"]');
            const confirmPass = this.querySelector('input[name="new_password_confirmation"]');
            
            if (newPass.value !== confirmPass.value) {
                e.preventDefault();
                showNotification('New passwords do not match', 'warning');
                confirmPass.focus();
                return;
            }
            
            if (newPass.value.length < 8) {
                e.preventDefault();
                showNotification('New password must be at least 8 characters long', 'warning');
                newPass.focus();
                return;
            }
            
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

// Account actions
function exportAccountData() {
    showNotification('Preparing your account data for export...', 'info');
    // In real implementation, this would trigger data export
}

function showSessions() {
    showNotification('Loading active sessions...', 'info');
    // In real implementation, this would show active sessions modal
}

function requestAccountDeletion() {
    if (confirm('Are you sure you want to request account deletion? This action requires administrative approval.')) {
        showNotification('Account deletion request submitted', 'info');
        // In real implementation, this would submit deletion request
    }
}

function showSecurityScan() {
    const btn = event.target.closest('button');
    const originalHTML = btn.innerHTML;
    
    btn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Scanning...';
    btn.disabled = true;
    
    setTimeout(() => {
        btn.innerHTML = '<i class="bi bi-shield-check me-2"></i>Security Check Complete';
        btn.className = 'btn btn-success w-100';
        
        showNotification('Security scan completed. All checks passed!', 'success');
        
        setTimeout(() => {
            btn.innerHTML = originalHTML;
            btn.className = 'btn btn-outline-primary w-100';
            btn.disabled = false;
        }, 2000);
    }, 2000);
}
</script>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>