<?php
// app/views/users/index.php
// index.php lists system users for management and overview.

/** @var array $users */
/** @var array $filters */
/** @var array $roles */

use App\core\View;
use App\core\Auth;

$currentUser = Auth::user();
$roleKey     = $currentUser['role_key'] ?? '';

$search       = $filters['search']       ?? ($_GET['search']       ?? '');
$roleFilter   = $filters['role_key']     ?? ($_GET['role_key']     ?? '');
$statusFilter = $filters['status']       ?? ($_GET['status']       ?? '');
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management | ZukBits Online</title>
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

        .gradient-text {
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .users-header {
            background: var(--gradient-primary);
            color: white;
            padding: 24px 30px;
            border-radius: 16px;
            margin-bottom: 30px;
            position: relative;
            overflow: hidden;
        }

        .users-header::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 120px;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1));
        }

        .stats-card {
            background: var(--gradient-primary);
            color: white;
            padding: 20px;
            border-radius: 12px;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px var(--shadow-blue);
        }

        .stats-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--color-accent-blue), var(--color-accent-purple));
        }

        .filter-card {
            background: rgba(56, 189, 248, 0.05);
            border: 2px solid rgba(56, 189, 248, 0.2);
            border-radius: 12px;
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

        .btn-outline-primary {
            border: 2px solid var(--color-accent-blue);
            color: var(--color-accent-blue);
            background: transparent;
            padding: 8px 20px;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-outline-primary:hover {
            background: var(--color-accent-blue);
            color: white;
            transform: translateY(-2px);
        }

        .table-users {
            --bs-table-bg: transparent;
            --bs-table-border-color: var(--color-border);
            --bs-table-striped-bg: rgba(56, 189, 248, 0.03);
            --bs-table-hover-bg: rgba(56, 189, 248, 0.08);
        }

        .table-users th {
            color: var(--color-text);
            font-weight: 600;
            padding: 20px;
            border: none;
            background: linear-gradient(90deg, rgba(56, 189, 248, 0.1), rgba(168, 85, 247, 0.1));
            position: relative;
        }

        .table-users th::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 20px;
            right: 20px;
            height: 2px;
            background: var(--gradient-primary);
        }

        .table-users td {
            padding: 20px;
            border-color: var(--color-border);
            color: var(--color-text);
            vertical-align: middle;
        }

        .table-users tbody tr {
            border-bottom: 1px solid var(--color-border);
            transition: all 0.3s ease;
        }

        .table-users tbody tr:last-child {
            border-bottom: none;
        }

        .table-users tbody tr:hover {
            background: rgba(56, 189, 248, 0.08);
            transform: translateX(4px);
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--gradient-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 0.9rem;
            margin-right: 12px;
        }

        .role-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            background: rgba(56, 189, 248, 0.1);
            color: var(--color-accent-blue);
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
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

        .last-login {
            font-size: 0.85rem;
            color: var(--color-text-muted);
        }

        .last-login i {
            width: 16px;
            text-align: center;
        }

        .pagination-custom .page-link {
            background: var(--color-surface);
            border-color: var(--color-border);
            color: var(--color-text);
            margin: 0 4px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .pagination-custom .page-link:hover {
            background: rgba(56, 189, 248, 0.1);
            border-color: var(--color-accent-blue);
        }

        .pagination-custom .page-item.active .page-link {
            background: var(--gradient-primary);
            border-color: transparent;
            color: white;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
        }

        .empty-state-icon {
            font-size: 4rem;
            color: var(--color-border);
            margin-bottom: 20px;
            opacity: 0.5;
        }

        .quick-actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .export-buttons {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .btn-export {
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(56, 189, 248, 0.1);
            color: var(--color-accent-blue);
            border: 1px solid rgba(56, 189, 248, 0.3);
            transition: all 0.3s ease;
        }

        .btn-export:hover {
            background: rgba(56, 189, 248, 0.2);
            transform: translateY(-2px);
        }

        @media (max-width: 768px) {
            .users-header {
                padding: 20px;
                text-align: center;
            }
            
            .stats-card {
                padding: 15px;
            }
            
            .table-users {
                font-size: 0.9rem;
            }
            
            .table-users th,
            .table-users td {
                padding: 12px 8px;
            }
            
            .quick-actions {
                flex-direction: column;
                width: 100%;
            }
            
            .quick-actions .btn {
                width: 100%;
            }
            
            .export-buttons {
                justify-content: center;
                margin-top: 10px;
            }
        }
    </style>
</head>
<body>
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="users-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3 mb-2 fw-bold">User Management</h1>
                        <p class="mb-0 opacity-90">
                            <i class="bi bi-people me-2"></i>
                            Manage access for developers, marketers, directors, and admins
                        </p>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <?php if ($roleKey === 'super_admin'): ?>
                            <a href="/users/create" class="btn btn-light">
                                <i class="bi bi-person-plus me-2"></i>New User
                            </a>
                        <?php endif; ?>
                        <span class="badge bg-dark bg-opacity-50">
                            <i class="bi bi-shield-check me-1"></i>Admin Access
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Row -->
    <?php if (!empty($users)): ?>
    <div class="row mb-4">
        <?php
        $totalUsers = count($users);
        $activeUsers = 0;
        $inactiveUsers = 0;
        $admins = 0;
        
        foreach ($users as $u) {
            if (!empty($u['is_active'])) {
                $activeUsers++;
            } else {
                $inactiveUsers++;
            }
            
            $role = $u['role_key'] ?? '';
            if (in_array($role, ['system_admin', 'super_admin'])) {
                $admins++;
            }
        }
        ?>
        
        <div class="col-xl-3 col-lg-6 mb-4">
            <div class="stats-card">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="mb-2 opacity-75">Total Users</h6>
                        <h3 class="mb-0"><?= $totalUsers ?></h3>
                    </div>
                    <i class="bi bi-people fs-2 opacity-50"></i>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-lg-6 mb-4">
            <div class="glass-card p-4 h-100">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="mb-2 text-muted">Active Users</h6>
                        <h3 class="mb-0 text-success"><?= $activeUsers ?></h3>
                        <small class="text-muted"><?= round(($activeUsers / $totalUsers) * 100) ?>% active</small>
                    </div>
                    <i class="bi bi-person-check fs-2 text-success opacity-50"></i>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-lg-6 mb-4">
            <div class="glass-card p-4 h-100">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="mb-2 text-muted">Administrators</h6>
                        <h3 class="mb-0 text-warning"><?= $admins ?></h3>
                        <small class="text-muted">System & Super Admins</small>
                    </div>
                    <i class="bi bi-shield-check fs-2 text-warning opacity-50"></i>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-lg-6 mb-4">
            <div class="glass-card p-4 h-100">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="mb-2 text-muted">Inactive Users</h6>
                        <h3 class="mb-0 text-muted"><?= $inactiveUsers ?></h3>
                        <small class="text-muted">Cannot access system</small>
                    </div>
                    <i class="bi bi-person-x fs-2 text-muted opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Filters & Export -->
    <div class="glass-card p-4 mb-4 filter-card">
        <div class="row g-3">
            <div class="col-md-9">
                <form method="get" action="/users" id="filter-form">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-medium">Search Users</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-search"></i>
                                </span>
                                <input type="text"
                                       name="search"
                                       class="form-control"
                                       value="<?= View::e($search) ?>"
                                       placeholder="Name or email">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-medium">Role Filter</label>
                            <select name="role_key" class="form-select">
                                <option value="">All Roles</option>
                                <?php foreach ($roles as $role): ?>
                                    <?php
                                    $rKey  = $role['key']  ?? '';
                                    $rName = $role['name'] ?? $rKey;
                                    ?>
                                    <option value="<?= View::e($rKey) ?>"
                                        <?= $roleFilter === $rKey ? 'selected' : '' ?>>
                                        <?= View::e($rName) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-medium">Status Filter</label>
                            <select name="status" class="form-select">
                                <option value="">All Status</option>
                                <option value="active"   <?= $statusFilter === 'active'   ? 'selected' : '' ?>>Active</option>
                                <option value="inactive" <?= $statusFilter === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-md-3 d-flex align-items-end justify-content-end">
                <div class="export-buttons">
                    <button type="submit" form="filter-form" class="btn btn-primary">
                        <i class="bi bi-funnel me-1"></i>Apply
                    </button>
                    <a href="/users" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-clockwise"></i>
                    </a>
                    <?php if (!empty($users)): ?>
                        <button class="btn-export" onclick="exportUsers()">
                            <i class="bi bi-download"></i>
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Users Table -->
    <div class="glass-card p-4">
        <?php if (empty($users)): ?>
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="bi bi-people"></i>
                </div>
                <h4 class="text-muted mb-3">No users found</h4>
                <p class="text-muted mb-4">
                    <?php if ($search || $roleFilter || $statusFilter): ?>
                        Try adjusting your filters or search terms
                    <?php else: ?>
                        Start by adding your first team member
                    <?php endif; ?>
                </p>
                <?php if ($roleKey === 'super_admin'): ?>
                    <a href="/users/create" class="btn btn-primary">
                        <i class="bi bi-person-plus me-2"></i>Create First User
                    </a>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-users">
                    <thead>
                    <tr>
                        <th style="width: 25%;">User</th>
                        <th style="width: 20%;">Contact</th>
                        <th style="width: 15%;">Role</th>
                        <th style="width: 15%;">Status</th>
                        <th style="width: 15%;">Last Login</th>
                        <th style="width: 10%;" class="text-center">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($users as $u): ?>
                        <?php
                        $id        = (int)($u['id'] ?? 0);
                        $isActive  = !empty($u['is_active']);
                        $roleName  = $u['role_name'] ?? $u['role_key'] ?? '';
                        $lastLogin = $u['last_login_at'] ?? '';
                        $createdAt = $u['created_at'] ?? '';
                        $initials = '';
                        $nameParts = explode(' ', $u['name'] ?? '');
                        $initials = ($nameParts[0][0] ?? '') . ($nameParts[1][0] ?? '');
                        ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="user-avatar"><?= strtoupper($initials) ?></div>
                                    <div>
                                        <div class="fw-medium"><?= View::e($u['name'] ?? '') ?></div>
                                        <div class="text-muted small">
                                            <i class="bi bi-calendar me-1"></i>
                                            <?= $createdAt ? date('M d, Y', strtotime($createdAt)) : '—' ?>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="fw-medium small"><?= View::e($u['email'] ?? '') ?></div>
                                <?php if ($u['phone']): ?>
                                    <div class="text-muted small">
                                        <i class="bi bi-telephone me-1"></i>
                                        <?= View::e($u['phone']) ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="role-badge">
                                    <i class="bi bi-person-badge"></i>
                                    <?= View::e($roleName) ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($isActive): ?>
                                    <span class="status-badge status-active">
                                        <i class="bi bi-circle-fill"></i>Active
                                    </span>
                                <?php else: ?>
                                    <span class="status-badge status-inactive">
                                        <i class="bi bi-circle"></i>Inactive
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($lastLogin): ?>
                                    <div class="last-login">
                                        <i class="bi bi-clock-history me-1"></i>
                                        <?= View::e(date('M d, Y', strtotime($lastLogin))) ?>
                                    </div>
                                    <div class="text-muted small">
                                        <?= View::e(date('H:i', strtotime($lastLogin))) ?>
                                    </div>
                                <?php else: ?>
                                    <span class="text-muted">Never logged in</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <div class="d-flex gap-2 justify-content-center">
                                    <a href="/users/show?id=<?= View::e((string)$id) ?>"
                                       class="btn btn-sm btn-outline-primary"
                                       title="View user">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <?php if ($roleKey === 'super_admin'): ?>
                                        <a href="/users/edit?id=<?= View::e((string)$id) ?>"
                                           class="btn btn-sm btn-outline-secondary"
                                           title="Edit user">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <nav class="mt-4">
                <ul class="pagination pagination-custom justify-content-center">
                    <li class="page-item">
                        <a class="page-link" href="#">
                            <i class="bi bi-chevron-left"></i>
                        </a>
                    </li>
                    <li class="page-item active">
                        <a class="page-link" href="#">1</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="#">2</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="#">3</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="#">
                            <i class="bi bi-chevron-right"></i>
                        </a>
                    </li>
                </ul>
            </nav>
        <?php endif; ?>
    </div>
</div>

<script>
// Export functionality
function exportUsers() {
    const exportBtn = event.target.closest('button');
    const originalHTML = exportBtn.innerHTML;
    
    exportBtn.innerHTML = '<i class="bi bi-hourglass-split"></i>';
    exportBtn.disabled = true;
    
    // Get current filters
    const search = document.querySelector('input[name="search"]').value;
    const role = document.querySelector('select[name="role_key"]').value;
    const status = document.querySelector('select[name="status"]').value;
    
    // Simulate export
    setTimeout(() => {
        const exportUrl = `/users/export?search=${encodeURIComponent(search)}&role_key=${role}&status=${status}`;
        window.location.href = exportUrl;
        
        showNotification('User list exported successfully!', 'success');
        
        exportBtn.innerHTML = originalHTML;
        exportBtn.disabled = false;
    }, 1500);
}

// Show notification
function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = 'position-fixed top-0 end-0 p-3';
    notification.style.zIndex = '9999';
    
    const alert = document.createElement('div');
    alert.className = `alert alert-${type} alert-dismissible fade show`;
    alert.style.background = type === 'success' ? 'rgba(52, 199, 89, 0.9)' : 'rgba(56, 189, 248, 0.9)';
    alert.style.border = 'none';
    alert.style.color = 'white';
    
    alert.innerHTML = `
        <i class="bi bi-${type === 'success' ? 'check-circle' : 'info-circle'} me-2"></i>
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

// Auto-submit filter form on select change
document.addEventListener('DOMContentLoaded', function() {
    const roleSelect = document.querySelector('select[name="role_key"]');
    const statusSelect = document.querySelector('select[name="status"]');
    
    [roleSelect, statusSelect].forEach(select => {
        if (select) {
            select.addEventListener('change', function() {
                document.getElementById('filter-form').submit();
            });
        }
    });
    
    // Search with debounce
    const searchInput = document.querySelector('input[name="search"]');
    if (searchInput) {
        let searchTimeout;
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                document.getElementById('filter-form').submit();
            }, 500);
        });
    }
    
    // Row click functionality
    const tableRows = document.querySelectorAll('.table-users tbody tr');
    tableRows.forEach(row => {
        row.addEventListener('click', function(e) {
            if (!e.target.closest('a') && !e.target.closest('button')) {
                const viewLink = this.querySelector('a[title="View user"]');
                if (viewLink) {
                    window.location = viewLink.href;
                }
            }
        });
        row.style.cursor = 'pointer';
    });
    
    // Bulk actions
    const bulkActions = document.createElement('div');
    bulkActions.className = 'd-flex justify-content-between align-items-center mb-3';
    bulkActions.innerHTML = `
        <div class="form-check">
            <input class="form-check-input" type="checkbox" id="select-all">
            <label class="form-check-label text-muted small" for="select-all">
                Select all users
            </label>
        </div>
        <div class="btn-group">
            <button class="btn btn-sm btn-outline-primary" onclick="sendBulkInvite()">
                <i class="bi bi-envelope me-1"></i>Send Invites
            </button>
            <button class="btn btn-sm btn-outline-secondary" onclick="exportSelected()">
                <i class="bi bi-download me-1"></i>Export Selected
            </button>
        </div>
    `;
    
    const tableContainer = document.querySelector('.glass-card.p-4 .table-responsive');
    if (tableContainer && tableContainer.parentElement) {
        tableContainer.parentElement.insertBefore(bulkActions, tableContainer);
    }
});

function sendBulkInvite() {
    showNotification('Sending invitations to selected users...', 'info');
}

function exportSelected() {
    showNotification('Exporting selected users...', 'info');
}
</script>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>