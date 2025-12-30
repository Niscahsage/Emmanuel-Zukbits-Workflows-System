<?php
// app/views/users/create.php
// create.php displays the form to create a new user account.

/** @var array $roles */

use App\core\View;
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create User | ZukBits Online</title>
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

        .create-header {
            background: var(--gradient-primary);
            color: white;
            padding: 24px 30px;
            border-radius: 16px;
            margin-bottom: 30px;
            position: relative;
            overflow: hidden;
        }

        .create-header::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 100px;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1));
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
            padding: 6px 12px;
            background: rgba(56, 189, 248, 0.1);
            color: var(--color-accent-blue);
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
        }

        .role-option {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 8px;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .role-option:hover {
            background: rgba(56, 189, 248, 0.05);
        }

        .role-option.selected {
            background: rgba(56, 189, 248, 0.1);
            border: 2px solid var(--color-accent-blue);
        }

        .role-info {
            font-size: 0.85rem;
            color: var(--color-text-muted);
        }

        .status-toggle {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            background: rgba(56, 189, 248, 0.05);
            border-radius: 8px;
            margin-bottom: 16px;
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

        .action-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 24px 0;
            border-top: 1px solid var(--color-border);
            margin-top: 24px;
        }

        @media (max-width: 768px) {
            .create-header {
                padding: 20px;
                text-align: center;
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
            <div class="create-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3 mb-2 fw-bold">Create New User</h1>
                        <p class="mb-0 opacity-90">
                            <i class="bi bi-person-plus me-2"></i>
                            Add a new team member to the system
                        </p>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-dark bg-opacity-50">
                            <i class="bi bi-people me-1"></i>
                            <?= count($roles) ?> roles available
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Left Column - Form -->
        <div class="col-lg-8">
            <form method="post" action="/users/store" id="create-user-form">
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
                                        placeholder="Enter full name"
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
                                        placeholder="user@example.com"
                                    >
                                </div>
                                <div class="form-hint">
                                    <i class="bi bi-info-circle"></i>
                                    Will be used for login and notifications
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
                                <select name="role_key" class="form-select" required id="role-select">
                                    <option value="">Select a role</option>
                                    <?php foreach ($roles as $role): ?>
                                        <?php
                                        $rKey  = $role['key']  ?? '';
                                        $rName = $role['name'] ?? $rKey;
                                        $desc  = $role['description'] ?? '';
                                        ?>
                                        <option value="<?= View::e($rKey) ?>" data-description="<?= View::e($desc) ?>">
                                            <?= View::e($rName) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div id="role-description" class="form-hint mt-2"></div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">
                                    <i class="bi bi-circle-fill me-1"></i>Account Status
                                    <span class="required">*</span>
                                </label>
                                <div class="status-toggle">
                                    <div>
                                        <div class="fw-medium" id="status-label">Active Account</div>
                                        <div class="text-muted small">
                                            <span id="status-description">User can login and access the system</span>
                                        </div>
                                    </div>
                                    <div class="toggle-switch">
                                        <input type="checkbox" name="status" value="active" id="status-toggle" checked>
                                        <span class="toggle-slider"></span>
                                    </div>
                                </div>
                                <input type="hidden" name="status_value" value="active" id="status-value">
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
                                        placeholder="e.g. Nairobi, Kenya"
                                    >
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Password Setup -->
                <div class="glass-card p-4">
                    <h3 class="h5 mb-3 gradient-text fw-bold">
                        <i class="bi bi-key me-2"></i>Password Setup
                    </h3>
                    
                    <div class="form-section">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">
                                    <i class="bi bi-lock me-1"></i>Temporary Password
                                    <span class="required">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-key-fill"></i>
                                    </span>
                                    <input
                                        type="password"
                                        name="password"
                                        id="password"
                                        class="form-control"
                                        required
                                        placeholder="Create a temporary password"
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
                                    <i class="bi bi-lock me-1"></i>Confirm Password
                                    <span class="required">*</span>
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
                                        required
                                        placeholder="Confirm the password"
                                        oninput="checkPasswordMatch(this.value)"
                                    >
                                    <button class="btn btn-outline-secondary border-start-0" type="button" onclick="togglePassword(this)">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                                <div id="password-match-text" class="form-hint mt-2"></div>
                                <div class="form-hint mt-3">
                                    <i class="bi bi-info-circle"></i>
                                    User will be asked to change this on first login
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="action-bar">
                    <a href="/users" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Cancel
                    </a>
                    <button type="submit" class="btn btn-primary" id="create-btn">
                        <i class="bi bi-person-plus me-2"></i>Create User Account
                    </button>
                </div>
            </form>
        </div>

        <!-- Right Column - Preview -->
        <div class="col-lg-4">
            <div class="glass-card p-4 h-100">
                <h3 class="h5 mb-3 gradient-text fw-bold">
                    <i class="bi bi-eye me-2"></i>User Preview
                </h3>
                
                <div class="text-center mb-4">
                    <div class="rounded-circle bg-dark d-inline-flex align-items-center justify-content-center mb-3" 
                         style="width: 80px; height: 80px; background: var(--gradient-primary) !important;">
                        <i class="bi bi-person fs-2 text-white"></i>
                    </div>
                    <h5 id="preview-name" class="mb-1">New User</h5>
                    <p id="preview-email" class="text-muted mb-2">email@example.com</p>
                    <span id="preview-role" class="role-badge">Select Role</span>
                </div>

                <div class="mb-4">
                    <h6 class="text-muted mb-3">
                        <i class="bi bi-info-circle me-2"></i>Account Summary
                    </h6>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="small">Status</span>
                        <span id="preview-status" class="badge bg-success">Active</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="small">Password</span>
                        <span class="badge bg-warning text-dark">Temporary</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="small">First Login</span>
                        <span class="badge bg-info">Required</span>
                    </div>
                </div>

                <div class="mb-4">
                    <h6 class="text-muted mb-3">
                        <i class="bi bi-shield me-2"></i>Permissions Preview
                    </h6>
                    <div id="permissions-list" class="small text-muted">
                        Select a role to see permissions
                    </div>
                </div>

                <div class="alert alert-secondary mb-0">
                    <i class="bi bi-lightbulb me-2"></i>
                    <small>The user will receive an email with login instructions and temporary password.</small>
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
    const password = document.getElementById('password').value;
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

// Update preview
document.addEventListener('DOMContentLoaded', function() {
    const nameInput = document.querySelector('input[name="name"]');
    const emailInput = document.querySelector('input[name="email"]');
    const roleSelect = document.getElementById('role-select');
    const statusToggle = document.getElementById('status-toggle');
    const statusValue = document.getElementById('status-value');
    
    // Role description
    const roleDescription = document.getElementById('role-description');
    const permissionsList = document.getElementById('permissions-list');
    
    // Role permissions mapping
    const rolePermissions = {
        'developer': [
            'View assigned projects',
            'Create weekly schedules',
            'Submit weekly reports',
            'View own activity'
        ],
        'marketer': [
            'View assigned projects',
            'Create weekly schedules',
            'Submit weekly reports',
            'Access marketing data'
        ],
        'director': [
            'View all projects',
            'Approve reports',
            'Manage team schedules',
            'View team analytics'
        ],
        'system_admin': [
            'Full system access',
            'Manage all users',
            'Configure system settings',
            'Access all data'
        ],
        'super_admin': [
            'Full system control',
            'Manage administrators',
            'System configuration',
            'Database access'
        ]
    };
    
    // Update preview on input
    nameInput.addEventListener('input', function() {
        document.getElementById('preview-name').textContent = 
            this.value || 'New User';
    });
    
    emailInput.addEventListener('input', function() {
        document.getElementById('preview-email').textContent = 
            this.value || 'email@example.com';
    });
    
    roleSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const roleName = selectedOption.text;
        const roleKey = selectedOption.value;
        const description = selectedOption.getAttribute('data-description');
        
        // Update role badge
        const roleBadge = document.getElementById('preview-role');
        roleBadge.textContent = roleName;
        
        // Update role description
        roleDescription.innerHTML = description ? 
            `<i class="bi bi-info-circle"></i>${description}` : '';
        
        // Update permissions
        if (roleKey && rolePermissions[roleKey]) {
            const permissions = rolePermissions[roleKey];
            permissionsList.innerHTML = permissions.map(p => 
                `<div class="mb-1"><i class="bi bi-check-circle text-success me-2"></i>${p}</div>`
            ).join('');
        } else {
            permissionsList.innerHTML = '<div class="text-muted">Select a role to see permissions</div>';
        }
    });
    
    // Status toggle
    statusToggle.addEventListener('change', function() {
        const statusLabel = document.getElementById('status-label');
        const statusDesc = document.getElementById('status-description');
        const previewStatus = document.getElementById('preview-status');
        
        if (this.checked) {
            statusLabel.textContent = 'Active Account';
            statusDesc.textContent = 'User can login and access the system';
            previewStatus.textContent = 'Active';
            previewStatus.className = 'badge bg-success';
            statusValue.value = 'active';
        } else {
            statusLabel.textContent = 'Inactive Account';
            statusDesc.textContent = 'User cannot login to the system';
            previewStatus.textContent = 'Inactive';
            previewStatus.className = 'badge bg-secondary';
            statusValue.value = 'inactive';
        }
    });
    
    // Form validation
    const form = document.getElementById('create-user-form');
    form.addEventListener('submit', function(e) {
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('confirm-password').value;
        const roleSelected = roleSelect.value;
        
        if (!roleSelected) {
            e.preventDefault();
            showNotification('Please select a user role', 'warning');
            roleSelect.focus();
            return;
        }
        
        if (password.length < 8) {
            e.preventDefault();
            showNotification('Password must be at least 8 characters long', 'warning');
            return;
        }
        
        if (password !== confirmPassword) {
            e.preventDefault();
            showNotification('Passwords do not match', 'warning');
            return;
        }
        
        // Update button state
        const createBtn = document.getElementById('create-btn');
        createBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Creating User...';
        createBtn.disabled = true;
        
        showNotification('Creating user account...', 'info');
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

// Auto-generate password
function generatePassword() {
    const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*';
    let password = '';
    for (let i = 0; i < 12; i++) {
        password += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    
    const passwordInput = document.getElementById('password');
    const confirmInput = document.getElementById('confirm-password');
    
    passwordInput.value = password;
    confirmInput.value = password;
    
    checkPasswordStrength(password);
    checkPasswordMatch(password);
    
    showNotification('Strong password generated', 'success');
}

// Add generate password button
document.addEventListener('DOMContentLoaded', function() {
    const passwordGroup = document.querySelector('input[name="password"]').parentElement;
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