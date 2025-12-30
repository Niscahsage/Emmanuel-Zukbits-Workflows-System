<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Details | ZukBits Dashboard</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <style>
        /* Import color variables */
        :root {
            /* ===== CORE COLORS ===== */
            --color-bg: #050816;
            --color-surface: #0b1020;
            --color-surface-alt: #111827;
            --color-surface-soft: #0f172a;
            
            /* ===== ACCENT COLORS ===== */
            --color-accent: #ffc857;
            --color-accent-strong: #fbbf24;
            --color-accent-soft: rgba(255, 200, 87, 0.15);
            --color-accent-blue: #38bdf8;
            --color-accent-purple: #a855f7;
            --color-accent-green: #34c759;
            
            /* ===== TEXT COLORS ===== */
            --color-text: #f7f7ff;
            --color-text-muted: #c3c5d4;
            
            /* ===== BORDER COLORS ===== */
            --color-border: #22263b;
            --color-border-light: rgba(148, 163, 253, 0.35);
            --color-border-medium: rgba(148, 163, 253, 0.45);
            --color-border-accent: rgba(148, 163, 253, 0.8);
            
            /* ===== GRADIENTS ===== */
            --gradient-primary: linear-gradient(135deg, var(--color-accent-blue), var(--color-accent-purple));
            --gradient-accent: linear-gradient(135deg, var(--color-accent-strong), #f97316);
            --gradient-bg-card: radial-gradient(circle at top left, rgba(148, 163, 253, 0.18), rgba(15, 23, 42, 0.96));
            --gradient-seedlings: linear-gradient(135deg, rgba(34, 197, 94, 0.2), rgba(15, 23, 42, 0.96));
            
            /* ===== SHADOWS ===== */
            --shadow-blue: rgba(56, 189, 248, 0.35);
            --shadow-yellow: rgba(251, 191, 36, 0.4);
            --shadow-dark: rgba(0, 0, 0, 0.45);
        }

        /* Global Styles */
        body {
            background-color: var(--color-bg);
            color: var(--color-text);
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }
        ::-webkit-scrollbar-track {
            background: var(--color-surface);
        }
        ::-webkit-scrollbar-thumb {
            background: var(--color-border);
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: var(--color-border-light);
        }

        /* Dashboard Layout */
        .dashboard-container {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            width: 260px;
            background: var(--color-surface);
            border-right: 1px solid var(--color-border);
            position: fixed;
            height: 100vh;
            left: 0;
            top: 0;
            z-index: 1000;
            transition: all 0.3s ease;
        }

        .sidebar-collapsed {
            transform: translateX(-260px);
        }

        .sidebar-header {
            padding: 1.5rem;
            border-bottom: 1px solid var(--color-border);
        }

        .brand-logo {
            font-size: 1.5rem;
            font-weight: 700;
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-decoration: none;
        }

        .sidebar-menu {
            padding: 1rem 0;
        }

        .nav-item {
            margin-bottom: 0.25rem;
        }

        .nav-link {
            color: var(--color-text-muted);
            padding: 0.75rem 1.5rem;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .nav-link:hover, .nav-link.active {
            color: var(--color-text);
            background: var(--color-surface-soft);
            border-right: 3px solid var(--color-accent);
        }

        .nav-link i {
            font-size: 1.1rem;
            width: 24px;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: 260px;
            transition: all 0.3s ease;
        }

        .main-content-expanded {
            margin-left: 0;
        }

        /* Top Navigation */
        .top-nav {
            background: var(--color-surface);
            border-bottom: 1px solid var(--color-border);
            padding: 1rem 1.5rem;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .nav-toggler {
            background: transparent;
            border: 1px solid var(--color-border);
            color: var(--color-text);
            border-radius: 8px;
            padding: 0.5rem 0.75rem;
            transition: all 0.3s ease;
        }

        .nav-toggler:hover {
            border-color: var(--color-border-light);
            background: var(--color-surface-soft);
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--gradient-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            color: white;
        }

        /* Cards */
        .dashboard-card {
            background: var(--gradient-bg-card);
            border: 1px solid var(--color-border);
            border-radius: 12px;
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
        }

        .dashboard-card:hover {
            border-color: var(--color-border-light);
            box-shadow: 0 8px 32px var(--shadow-dark);
            transform: translateY(-2px);
        }

        .card-header {
            background: transparent;
            border-bottom: 1px solid var(--color-border);
            padding: 1.25rem 1.5rem;
        }

        .card-body {
            padding: 1.5rem;
        }

        /* Buttons */
        .btn-gradient-primary {
            background: var(--gradient-primary);
            border: none;
            color: white;
            padding: 0.5rem 1.25rem;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-gradient-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px var(--shadow-blue);
        }

        .btn-gradient-accent {
            background: var(--gradient-accent);
            border: none;
            color: #020617;
            padding: 0.5rem 1.25rem;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-gradient-accent:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px var(--shadow-yellow);
        }

        .btn-outline-light {
            border: 1px solid var(--color-border);
            color: var(--color-text);
            background: transparent;
            transition: all 0.3s ease;
        }

        .btn-outline-light:hover {
            background: var(--color-surface-soft);
            border-color: var(--color-border-light);
        }

        /* Badges */
        .badge-status {
            padding: 0.35rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .badge-active {
            background: var(--gradient-seedlings);
            color: var(--color-accent-green);
            border: 1px solid rgba(52, 199, 89, 0.3);
        }

        .badge-inactive {
            background: rgba(148, 163, 253, 0.1);
            color: var(--color-text-muted);
            border: 1px solid var(--color-border);
        }

        /* User Profile */
        .user-profile-header {
            padding: 2rem 0;
            position: relative;
        }

        .profile-avatar-lg {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: var(--gradient-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            font-weight: 700;
            color: white;
            margin-bottom: 1rem;
            border: 3px solid var(--color-surface);
            box-shadow: 0 4px 20px var(--shadow-blue);
        }

        .user-title {
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .user-subtitle {
            color: var(--color-text-muted);
            font-size: 0.9rem;
        }

        /* Detail Items */
        .detail-item {
            border-bottom: 1px solid var(--color-border);
            padding: 1rem 0;
        }

        .detail-item:last-child {
            border-bottom: none;
        }

        .detail-label {
            color: var(--color-text-muted);
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.25rem;
        }

        .detail-value {
            font-size: 1rem;
            font-weight: 500;
        }

        /* Timeline Activity */
        .timeline-item {
            position: relative;
            padding-left: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .timeline-item:before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--color-accent-blue);
        }

        .timeline-item:after {
            content: '';
            position: absolute;
            left: 3px;
            top: 8px;
            width: 2px;
            height: calc(100% + 1rem);
            background: var(--color-border);
        }

        .timeline-item:last-child:after {
            display: none;
        }

        /* Action Cards */
        .action-card {
            background: var(--color-surface-soft);
            border: 1px solid var(--color-border);
            border-radius: 10px;
            padding: 1.25rem;
            transition: all 0.3s ease;
            margin-bottom: 1rem;
        }

        .action-card:hover {
            border-color: var(--color-border-light);
            background: var(--color-surface-alt);
        }

        .action-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
        }

        .icon-success {
            background: rgba(52, 199, 89, 0.15);
            color: var(--color-accent-green);
        }

        .icon-danger {
            background: rgba(239, 68, 68, 0.15);
            color: #ef4444;
        }

        .icon-primary {
            background: rgba(56, 189, 248, 0.15);
            color: var(--color-accent-blue);
        }

        /* Responsive Design */
        @media (max-width: 992px) {
            .sidebar {
                transform: translateX(-260px);
            }
            
            .sidebar-mobile-open {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .overlay {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0, 0, 0, 0.5);
                z-index: 999;
                display: none;
            }
            
            .overlay-active {
                display: block;
            }
        }

        @media (max-width: 768px) {
            .user-profile-header {
                text-align: center;
            }
            
            .profile-avatar-lg {
                margin: 0 auto 1rem;
            }
        }

        /* Loading States */
        .skeleton {
            background: linear-gradient(90deg, var(--color-surface) 25%, var(--color-surface-alt) 50%, var(--color-surface) 75%);
            background-size: 200% 100%;
            animation: loading 1.5s infinite;
        }

        @keyframes loading {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }

        /* Custom Form Controls */
        .form-control-dark {
            background: var(--color-surface);
            border: 1px solid var(--color-border);
            color: var(--color-text);
            border-radius: 8px;
        }

        .form-control-dark:focus {
            background: var(--color-surface-alt);
            border-color: var(--color-border-light);
            color: var(--color-text);
            box-shadow: 0 0 0 3px var(--color-accent-soft);
        }

        /* Toast Notifications */
        .toast-container {
            position: fixed;
            top: 1rem;
            right: 1rem;
            z-index: 1050;
        }

        .custom-toast {
            background: var(--color-surface);
            border: 1px solid var(--color-border);
            border-radius: 10px;
            box-shadow: 0 4px 20px var(--shadow-dark);
            backdrop-filter: blur(10px);
        }
    </style>
</head>
<body>
    <!-- Dashboard Container -->
    <div class="dashboard-container">
        
        <!-- Mobile Overlay -->
        <div class="overlay" id="mobileOverlay"></div>
        
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <a href="#" class="brand-logo">ZukBits</a>
            </div>
            
            <div class="sidebar-menu">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="bi bi-speedometer2"></i>
                            Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="#">
                            <i class="bi bi-people-fill"></i>
                            Users
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="bi bi-bar-chart-fill"></i>
                            Analytics
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="bi bi-gear-fill"></i>
                            Settings
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="bi bi-shield-check"></i>
                            Security
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="bi bi-question-circle-fill"></i>
                            Support
                        </a>
                    </li>
                </ul>
            </div>
            
            <div class="sidebar-footer p-3 border-top border-color-border">
                <div class="d-flex align-items-center">
                    <div class="user-avatar me-3">AD</div>
                    <div>
                        <div class="text-sm fw-semibold">Admin User</div>
                        <div class="text-xs text-muted">Administrator</div>
                    </div>
                </div>
            </div>
        </aside>
        
        <!-- Main Content -->
        <main class="main-content" id="mainContent">
            <!-- Top Navigation -->
            <nav class="top-nav">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-3">
                        <button class="nav-toggler d-lg-none" id="mobileMenuToggler">
                            <i class="bi bi-list"></i>
                        </button>
                        <h1 class="h5 mb-0 d-none d-lg-block">User Management</h1>
                    </div>
                    
                    <div class="d-flex align-items-center gap-3">
                        <button class="btn btn-outline-light btn-sm" id="themeToggler">
                            <i class="bi bi-moon-stars"></i>
                        </button>
                        <div class="dropdown">
                            <button class="btn btn-outline-light btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="bi bi-bell"></i>
                                <span class="badge bg-danger rounded-pill">3</span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><h6 class="dropdown-header">Notifications</h6></li>
                                <li><a class="dropdown-item" href="#">New user registered</a></li>
                                <li><a class="dropdown-item" href="#">System updated</a></li>
                                <li><a class="dropdown-item" href="#">Security alert</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </nav>
            
            <!-- Main Content Area -->
            <div class="container-fluid p-4">
                <!-- Breadcrumb -->
                <nav aria-label="breadcrumb" class="mb-4">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#" class="text-decoration-none">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="#" class="text-decoration-none">Users</a></li>
                        <li class="breadcrumb-item active" aria-current="page">User Details</li>
                    </ol>
                </nav>
                
                <!-- User Profile Header -->
                <div class="user-profile-header mb-5">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <div class="d-flex align-items-center gap-4">
                                <div class="profile-avatar-lg"><?= mb_substr($name, 0, 1) ?></div>
                                <div>
                                    <h1 class="user-title"><?= htmlspecialchars($name) ?></h1>
                                    <div class="user-subtitle">
                                        User #<?= htmlspecialchars((string)$id) ?> • <?= htmlspecialchars($roleName) ?>
                                        <?php if ($isActive): ?>
                                            <span class="badge-status badge-active ms-2">Active</span>
                                        <?php else: ?>
                                            <span class="badge-status badge-inactive ms-2">Inactive</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex gap-2 justify-content-md-end mt-3 mt-md-0">
                                <a href="/users/edit?id=<?= htmlspecialchars((string)$id) ?>" class="btn btn-gradient-accent">
                                    <i class="bi bi-pencil me-2"></i>Edit User
                                </a>
                                <a href="/users" class="btn btn-outline-light">
                                    <i class="bi bi-arrow-left me-2"></i>Back to Users
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row g-4">
                    <!-- Left Column -->
                    <div class="col-lg-8">
                        <!-- User Details Card -->
                        <div class="dashboard-card mb-4">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0"><i class="bi bi-person-badge me-2"></i>User Details</h5>
                                <span class="text-muted" style="font-size: 0.85rem;">Last updated: <?= $updatedAt ? htmlspecialchars($updatedAt) : '–' ?></span>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="detail-item">
                                            <div class="detail-label">Full Name</div>
                                            <div class="detail-value"><?= htmlspecialchars($name) ?></div>
                                        </div>
                                        <div class="detail-item">
                                            <div class="detail-label">Email Address</div>
                                            <div class="detail-value">
                                                <a href="mailto:<?= htmlspecialchars($email) ?>" class="text-decoration-none">
                                                    <i class="bi bi-envelope me-2"></i><?= htmlspecialchars($email) ?>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="detail-item">
                                            <div class="detail-label">Phone Number</div>
                                            <div class="detail-value">
                                                <?php if ($phone): ?>
                                                    <a href="tel:<?= htmlspecialchars($phone) ?>" class="text-decoration-none">
                                                        <i class="bi bi-telephone me-2"></i><?= htmlspecialchars($phone) ?>
                                                    </a>
                                                <?php else: ?>
                                                    <span class="text-muted">Not provided</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="detail-item">
                                            <div class="detail-label">Location</div>
                                            <div class="detail-value">
                                                <?php if ($location): ?>
                                                    <i class="bi bi-geo-alt me-2"></i><?= htmlspecialchars($location) ?>
                                                <?php else: ?>
                                                    <span class="text-muted">Not specified</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="detail-item">
                                            <div class="detail-label">Role & Permissions</div>
                                            <div class="detail-value">
                                                <span class="badge bg-dark border border-color-border me-2"><?= htmlspecialchars($roleName) ?></span>
                                                <span class="text-muted" style="font-size: 0.85rem;">Full system access</span>
                                            </div>
                                        </div>
                                        <div class="detail-item">
                                            <div class="detail-label">Account Status</div>
                                            <div class="detail-value">
                                                <?php if ($isActive): ?>
                                                    <span class="badge-status badge-active">
                                                        <i class="bi bi-check-circle me-1"></i>Active
                                                    </span>
                                                    <span class="text-muted ms-2" style="font-size: 0.85rem;">User can access system</span>
                                                <?php else: ?>
                                                    <span class="badge-status badge-inactive">
                                                        <i class="bi bi-x-circle me-1"></i>Inactive
                                                    </span>
                                                    <span class="text-muted ms-2" style="font-size: 0.85rem;">User access is restricted</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Activity Timeline -->
                        <div class="dashboard-card">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Activity Timeline</h5>
                            </div>
                            <div class="card-body">
                                <div class="timeline-item">
                                    <div class="mb-2">
                                        <span class="fw-semibold">Account Created</span>
                                        <span class="text-muted ms-2" style="font-size: 0.85rem;"><?= $createdAt ? htmlspecialchars($createdAt) : '–' ?></span>
                                    </div>
                                    <p class="text-muted mb-0" style="font-size: 0.9rem;">User account was created in the system</p>
                                </div>
                                
                                <div class="timeline-item">
                                    <div class="mb-2">
                                        <span class="fw-semibold">Last Login</span>
                                        <span class="text-muted ms-2" style="font-size: 0.85rem;"><?= $lastLogin ? htmlspecialchars($lastLogin) : 'Never logged in' ?></span>
                                    </div>
                                    <p class="text-muted mb-0" style="font-size: 0.9rem;">Last successful authentication attempt</p>
                                </div>
                                
                                <div class="timeline-item">
                                    <div class="mb-2">
                                        <span class="fw-semibold">Profile Updated</span>
                                        <span class="text-muted ms-2" style="font-size: 0.85rem;"><?= $updatedAt ? htmlspecialchars($updatedAt) : '–' ?></span>
                                    </div>
                                    <p class="text-muted mb-0" style="font-size: 0.9rem;">Last profile information update</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Right Column -->
                    <div class="col-lg-4">
                        <!-- Admin Actions Card -->
                        <div class="dashboard-card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="bi bi-shield-check me-2"></i>Admin Actions</h5>
                            </div>
                            <div class="card-body">
                                <p class="text-muted mb-4" style="font-size: 0.9rem;">
                                    <i class="bi bi-exclamation-triangle me-2"></i>
                                    Use these actions carefully. Changes may impact user access to projects and approvals.
                                </p>
                                
                                <div class="action-card">
                                    <div class="action-icon icon-success">
                                        <i class="bi bi-person-check"></i>
                                    </div>
                                    <h6 class="fw-semibold mb-2">Account Status</h6>
                                    <p class="text-muted mb-3" style="font-size: 0.85rem;">
                                        <?php if ($isActive): ?>
                                            User account is currently active and can access the system.
                                        <?php else: ?>
                                            User account is inactive and cannot access the system.
                                        <?php endif; ?>
                                    </p>
                                    <form method="post" action="/users/toggle-status">
                                        <input type="hidden" name="id" value="<?= htmlspecialchars((string)$id) ?>">
                                        <?php if ($isActive): ?>
                                            <button type="submit" name="status" value="inactive" class="btn btn-outline-danger w-100">
                                                <i class="bi bi-person-x me-2"></i>Deactivate User
                                            </button>
                                        <?php else: ?>
                                            <button type="submit" name="status" value="active" class="btn btn-outline-success w-100">
                                                <i class="bi bi-person-check me-2"></i>Activate User
                                            </button>
                                        <?php endif; ?>
                                    </form>
                                </div>
                                
                                <div class="action-card">
                                    <div class="action-icon icon-primary">
                                        <i class="bi bi-key"></i>
                                    </div>
                                    <h6 class="fw-semibold mb-2">Password Reset</h6>
                                    <p class="text-muted mb-3" style="font-size: 0.85rem;">
                                        Send a password reset link to user's email address.
                                    </p>
                                    <form method="post" action="/users/reset-password">
                                        <input type="hidden" name="id" value="<?= htmlspecialchars((string)$id) ?>">
                                        <button type="submit" class="btn btn-gradient-primary w-100">
                                            <i class="bi bi-send me-2"></i>Send Reset Link
                                        </button>
                                    </form>
                                </div>
                                
                                <div class="action-card">
                                    <div class="action-icon icon-danger">
                                        <i class="bi bi-trash"></i>
                                    </div>
                                    <h6 class="fw-semibold mb-2">Danger Zone</h6>
                                    <p class="text-muted mb-3" style="font-size: 0.85rem;">
                                        Permanently delete user account and all associated data.
                                    </p>
                                    <button class="btn btn-outline-light w-100" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                        <i class="bi bi-trash me-2"></i>Delete Account
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Quick Stats -->
                        <div class="dashboard-card">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="bi bi-graph-up me-2"></i>Quick Stats</h5>
                            </div>
                            <div class="card-body">
                                <div class="row text-center">
                                    <div class="col-6 mb-3">
                                        <div class="h4 mb-1">12</div>
                                        <div class="text-muted" style="font-size: 0.85rem;">Projects</div>
                                    </div>
                                    <div class="col-6 mb-3">
                                        <div class="h4 mb-1">48</div>
                                        <div class="text-muted" style="font-size: 0.85rem;">Tasks</div>
                                    </div>
                                    <div class="col-6">
                                        <div class="h4 mb-1">7d</div>
                                        <div class="text-muted" style="font-size: 0.85rem;">Avg. Response</div>
                                    </div>
                                    <div class="col-6">
                                        <div class="h4 mb-1">98%</div>
                                        <div class="text-muted" style="font-size: 0.85rem;">Activity</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content dashboard-card">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-exclamation-triangle text-danger me-2"></i>Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this user account? This action cannot be undone.</p>
                    <div class="alert alert-danger" role="alert">
                        <i class="bi bi-exclamation-octagon me-2"></i>
                        Warning: All user data, projects, and history will be permanently removed.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger">Delete Account</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Toast Container -->
    <div class="toast-container">
        <div class="toast custom-toast" id="notificationToast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <strong class="me-auto">Notification</strong>
                <small>Just now</small>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                User details loaded successfully.
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Vanilla JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Mobile menu toggle
            const mobileMenuToggler = document.getElementById('mobileMenuToggler');
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            const mobileOverlay = document.getElementById('mobileOverlay');
            
            mobileMenuToggler.addEventListener('click', function() {
                sidebar.classList.toggle('sidebar-mobile-open');
                mobileOverlay.classList.toggle('overlay-active');
            });
            
            mobileOverlay.addEventListener('click', function() {
                sidebar.classList.remove('sidebar-mobile-open');
                mobileOverlay.classList.remove('overlay-active');
            });
            
            // Theme toggler
            const themeToggler = document.getElementById('themeToggler');
            const themeIcon = themeToggler.querySelector('i');
            
            themeToggler.addEventListener('click', function() {
                if (themeIcon.classList.contains('bi-moon-stars')) {
                    themeIcon.classList.remove('bi-moon-stars');
                    themeIcon.classList.add('bi-sun');
                    document.body.style.backgroundColor = '#f8f9fa';
                    document.body.style.color = '#212529';
                } else {
                    themeIcon.classList.remove('bi-sun');
                    themeIcon.classList.add('bi-moon-stars');
                    document.body.style.backgroundColor = '';
                    document.body.style.color = '';
                }
            });
            
            // Show toast notification
            const notificationToast = new bootstrap.Toast(document.getElementById('notificationToast'));
            setTimeout(() => {
                notificationToast.show();
            }, 1000);
            
            // Form submission handling
            const forms = document.querySelectorAll('form');
            forms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    const submitButton = this.querySelector('button[type="submit"]');
                    if (submitButton) {
                        submitButton.innerHTML = '<i class="bi bi-arrow-clockwise me-2"></i>Processing...';
                        submitButton.disabled = true;
                    }
                });
            });
            
            // Simulate loading state for demonstration
            function simulateLoading() {
                const cards = document.querySelectorAll('.dashboard-card');
                cards.forEach(card => {
                    card.classList.add('skeleton');
                    setTimeout(() => {
                        card.classList.remove('skeleton');
                    }, 1500);
                });
            }
            
            // Add hover effects to cards
            const cards = document.querySelectorAll('.dashboard-card');
            cards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-4px)';
                });
                
                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                });
            });
            
            // Update activity time dynamically
            function updateActivityTime() {
                const now = new Date();
                const timeElement = document.querySelector('.user-subtitle');
                if (timeElement) {
                    const timeString = now.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
                    timeElement.innerHTML += ` <span class="text-muted">• Viewed: ${timeString}</span>`;
                }
            }
            
            updateActivityTime();
        });
    </script>
</body>
</html>