<?php
// app/views/users/edit.php
// edit.php displays the form to edit an existing user account.

/** @var array $user */
/** @var array $roles */

use App\core\View;

$id        = (int)($user['id'] ?? 0);
$name      = $user['name'] ?? '';
$email     = $user['email'] ?? '';
$phone     = $user['phone'] ?? '';
$location  = $user['location'] ?? '';
$roleKey   = $user['role_key'] ?? '';
$isActive  = !empty($user['is_active']);
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User | ZukBits Online</title>
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
                radial-gradient(circle at 10% 20%, rgba(56, 189, 248, 0.05) 0%, transparent 40%),
                radial-gradient(circle at 90% 80%, rgba(168, 85, 247, 0.05) 0%, transparent 40%);
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

        .edit-header {
            background: var(--gradient-primary);
            color: white;
            padding: 24px 30px;
            border-radius: 16px;
            margin-bottom: 30px;
            position: relative;
            overflow: hidden;
        }

        .edit-header::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 100px;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1));
        }

        .user-avatar {
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
            margin-right: 20px;
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

        .role-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            background: rgba(56, 189, 248, 0.1);
            color: var(--color-accent-blue);
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .status-active {
            background: rgba(52, 199, 89, 0.1);
            color: var(--color-accent-green);
        }

        .status-inactive {
            background: rgba(148, 163, 253, 0.1);
            color: #94a3fd;
        }

        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 52px;
            height: 26px;
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
            height: 18px;
            width: 18px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked + .toggle-slider {
            background-color: var(--color-accent-green);
        }

        input:checked + .toggle-slider:before {
            transform: translateX(26px);
        }

        .password-toggle {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 16px;
        }

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
            padding: 12px 32px;
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
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-outline-danger:hover {
            background: rgba(239, 68, 68, 0.1);
            border-color: #ef4444;
        }

        .action-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 24px 0;
            border-top: 1px solid var(--color-border);
            margin-top: 24px;
        }

        .change-log {
            background: rgba(56, 189, 248, 0.05);
            border: 2px solid rgba(56, 189, 248, 0.1);
            border-radius: 12px;
            padding: 20px;
        }

        .change-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid rgba(148, 163, 253, 0.1);
        }

        .change-item:last-child {
            border-bottom: none;
        }

        @media (max-width: 768px) {
            .edit-header {
                padding: 20px;
                text-align: center;
            }
            
            .user-avatar {
                margin: 0 auto 20px;
            }
            
            .form-section {
                padding: 16px;
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
            <div class="edit-header">
                <div class="d-flex align-items-center">
                    <div class="user-avatar">
                        <?= strtoupper(substr($name, 0, 2)) ?>
                    </div>
                    <div>
                        <h1 class="h3 mb-2 fw-bold">Edit User Account</h1>
                        <p class="mb-0 opacity-90">
                            <i class="bi bi-person-circle me-2"></i>
                            <?= View::e($name) ?> · ID: #<?= $id ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Left Column - Form -->
        <div class="col-lg-8">
            <form method="post" action="/users/update" id="edit-user-form">
                <input type="hidden" name="id" value="<?= View::e((string)$id) ?>">

                <!-- Basic Information -->
                <div class="glass-card p-4 mb-4">
                    <h3 class="h5 mb-3 gradient-text fw-bold">
                        <i class="bi bi-person-circle me-2"></i>Basic Information
                    </h3>
                    
                    <div class="form-section">
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
                    </div>
                </div>

                <!-- Role & Status -->
                <div class="glass-card p-4 mb-4">
                    <h3 class="h5 mb-3 gradient-text fw-bold">
                        <i class="bi bi-shield-check me-2"></i>Role & Status
                    </h3>
                    
                    <div class="form-section">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">
                                    <i class="bi bi-person-badge me-1"></i>User Role
                                    <span class="required">*</span>
                                </label>
                                <select name="role_key" class="form-select" required>
                                    <option value="">Select role</option>
                                    <?php foreach ($roles as $role): ?>
                                        <?php
                                        $rKey  = $role['key']  ?? '';
                                        $rName = $role['name'] ?? $rKey;
                                        ?>
                                        <option value="<?= View::e($rKey) ?>"
                                            <?= $roleKey === $rKey ? 'selected' : '' ?>>
                                            <?= View::e($rName) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">
                                    <i class="bi bi-circle-fill me-1"></i>Account Status
                                    <span class="required">*</span>
                                </label>
                                <div class="status-toggle">
                                    <div>
                                        <div class="fw-medium">
                                            <?= $isActive ? 'Active Account' : 'Inactive Account' ?>
                                        </div>
                                        <div class="text-muted small">
                                            <?= $isActive ? 'User can login and access the system' : 'User cannot login to the system' ?>
                                        </div>
                                    </div>
                                    <div class="toggle-switch">
                                        <input type="checkbox" name="status" value="active" 
                                               id="status-toggle" <?= $isActive ? 'checked' : '' ?>>
                                        <span class="toggle-slider"></span>
                                    </div>
                                </div>
                                <input type="hidden" name="status_value" value="<?= $isActive ? 'active' : 'inactive' ?>" id="status-value">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="glass-card p-4 mb-4">
                    <h3 class="h5 mb-3 gradient-text fw-bold">
                        <i class="bi bi-telephone me-2"></i>Contact Information
                    </h3>
                    
                    <div class="form-section">
                        <div class="row g-3">
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
                    </div>
                </div>

                <!-- Password Reset -->
                <div class="glass-card p-4 mb-4">
                    <h3 class="h5 mb-3 gradient-text fw-bold">
                        <i class="bi bi-key me-2"></i>Password Management
                    </h3>
                    
                    <div class="form-section">
                        <div class="password-toggle">
                            <div>
                                <div class="fw-medium">Reset Password</div>
                                <div class="text-muted small">Set a new password for this user</div>
                            </div>
                            <div class="toggle-switch">
                                <input type="checkbox" id="reset-password-toggle">
                                <span class="toggle-slider"></span>
                            </div>
                        </div>

                        <div id="password-fields" style="display: none;">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">
                                        <i class="bi bi-lock me-1"></i>New Password
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="bi bi-key-fill"></i>
                                        </span>
                                        <input
                                            type="password"
                                            name="password"
                                            id="new-password"
                                            class="form-control"
                                            placeholder="Leave blank to keep current password"
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
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="bi bi-key-fill"></i>
                                        </span>
                                        <input
                                            type="password"
                                            name="password_confirmation"
                                            id="confirm-password"
                                            class="form-control"
                                            placeholder="Leave blank if not changing"
                                            oninput="checkPasswordMatch(this.value)"
                                        >
                                        <button class="btn btn-outline-secondary border-start-0" type="button" onclick="togglePassword(this)">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </div>
                                    <div id="password-match-text" class="form-hint mt-2"></div>
                                    <div class="form-hint mt-3">
                                        <i class="bi bi-info-circle"></i>
                                        User will be notified about password change
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="action-bar">
                    <div class="d-flex gap-2">
                        <a href="/users/show?id=<?= View::e((string)$id) ?>" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle me-2"></i>Cancel
                        </a>
                        <button type="button" class="btn btn-outline-danger" onclick="confirmDeactivation()">
                            <i class="bi bi-person-x me-2"></i>
                            <?= $isActive ? 'Deactivate User' : 'Activate User' ?>
                        </button>
                    </div>
                    <button type="submit" class="btn btn-primary" id="save-btn">
                        <i class="bi bi-save me-2"></i>Save Changes
                    </button>
                </div>
            </form>
        </div>

        <!-- Right Column - User Info -->
        <div class="col-lg-4">
            <div class="glass-card p-4 mb-4">
                <h3 class="h5 mb-3 gradient-text fw-bold">
                    <i class="bi bi-info-circle me-2"></i>Current User Info
                </h3>
                
                <div class="mb-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="user-avatar" style="width: 60px; height: 60px; font-size: 1.5rem;">
                            <?= strtoupper(substr($name, 0, 2)) ?>
                        </div>
                        <div>
                            <h5 class="mb-1"><?= View::e($name) ?></h5>
                            <p class="text-muted mb-0 small"><?= View::e($email) ?></p>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted small">Current Role</span>
                        <span class="role-badge">
                            <?php
                            $currentRoleName = '';
                            foreach ($roles as $role) {
                                if (($role['key'] ?? '') === $roleKey) {
                                    $currentRoleName = $role['name'] ?? $roleKey;
                                    break;
                                }
                            }
                            echo View::e($currentRoleName);
                            ?>
                        </span>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted small">Account Status</span>
                        <span class="status-badge <?= $isActive ? 'status-active' : 'status-inactive' ?>">
                            <i class="bi bi-circle-fill"></i>
                            <?= $isActive ? 'Active' : 'Inactive' ?>
                        </span>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted small">User ID</span>
                        <span class="fw-medium">#<?= $id ?></span>
                    </div>
                </div>

                <div class="alert alert-secondary mb-0">
                    <i class="bi bi-lightbulb me-2"></i>
                    <small>Changes will take effect immediately after saving.</small>
                </div>
            </div>

            <div class="glass-card p-4">
                <h3 class="h5 mb-3 gradient-text fw-bold">
                    <i class="bi bi-clock-history me-2"></i>Recent Changes
                </h3>
                
                <div class="change-log">
                    <div class="change-item">
                        <div>
                            <div class="fw-medium small">Last Updated</div>
                            <div class="text-muted small">Profile information</div>
                        </div>
                        <div class="text-muted small">Just now</div>
                    </div>
                    
                    <div class="change-item">
                        <div>
                            <div class="fw-medium small">Last Login</div>
                            <div class="text-muted small">System access</div>
                        </div>
                        <div class="text-muted small">2 hours ago</div>
                    </div>
                    
                    <div class="change-item">
                        <div>
                            <div class="fw-medium small">Password Changed</div>
                            <div class="text-muted small">Security update</div>
                        </div>
                        <div class="text-muted small">1 week ago</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
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

// Status toggle
document.addEventListener('DOMContentLoaded', function() {
    const statusToggle = document.getElementById('status-toggle');
    const statusValue = document.getElementById('status-value');
    
    statusToggle.addEventListener('change', function() {
        statusValue.value = this.checked ? 'active' : 'inactive';
    });
    
    // Password reset toggle
    const resetToggle = document.getElementById('reset-password-toggle');
    const passwordFields = document.getElementById('password-fields');
    
    resetToggle.addEventListener('change', function() {
        if (this.checked) {
            passwordFields.style.display = 'block';
            document.getElementById('new-password').focus();
        } else {
            passwordFields.style.display = 'none';
            document.getElementById('new-password').value = '';
            document.getElementById('confirm-password').value = '';
        }
    });
    
    // Form validation
    const form = document.getElementById('edit-user-form');
    form.addEventListener('submit', function(e) {
        const password = document.getElementById('new-password').value;
        const confirmPassword = document.getElementById('confirm-password').value;
        
        if (password && password.length < 8) {
            e.preventDefault();
            showNotification('Password must be at least 8 characters long', 'warning');
            return;
        }
        
        if (password && password !== confirmPassword) {
            e.preventDefault();
            showNotification('Passwords do not match', 'warning');
            return;
        }
        
        // Update button state
        const saveBtn = document.getElementById('save-btn');
        saveBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Saving...';
        saveBtn.disabled = true;
        
        showNotification('Saving user changes...', 'info');
    });
});

// Show notification
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

// Confirm deactivation
function confirmDeactivation() {
    const isActive = <?= $isActive ? 'true' : 'false' ?>;
    const action = isActive ? 'deactivate' : 'activate';
    
    if (confirm(`Are you sure you want to ${action} this user?`)) {
        const form = document.createElement('form');
        form.method = 'post';
        form.action = '/users/toggle-status';
        
        const idInput = document.createElement('input');
        idInput.type = 'hidden';
        idInput.name = 'id';
        idInput.value = '<?= $id ?>';
        
        const statusInput = document.createElement('input');
        statusInput.type = 'hidden';
        statusInput.name = 'status';
        statusInput.value = isActive ? 'inactive' : 'active';
        
        form.appendChild(idInput);
        form.appendChild(statusInput);
        document.body.appendChild(form);
        form.submit();
    }
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

// Add generate password button if password fields are visible
document.addEventListener('DOMContentLoaded', function() {
    const passwordFields = document.getElementById('password-fields');
    if (passwordFields) {
        const passwordGroup = passwordFields.querySelector('input[name="password"]').parentElement;
        const generateBtn = document.createElement('button');
        generateBtn.type = 'button';
        generateBtn.className = 'btn btn-outline-secondary border-start-0';
        generateBtn.innerHTML = '<i class="bi bi-shuffle"></i>';
        generateBtn.title = 'Generate strong password';
        generateBtn.onclick = generatePassword;
        
        passwordGroup.appendChild(generateBtn);
    }
});
</script>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>