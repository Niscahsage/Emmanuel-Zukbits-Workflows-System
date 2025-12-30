<?php
// app/config/routes.php
// Defines all HTTP routes for the ZukBits Workflows System.

use App\core\Router;

// -------------------------
// Public / guest routes
// -------------------------
Router::get('/login', 'AuthController@loginForm', ['guest']);
Router::post('/login', 'AuthController@login', ['guest']);

// Logout should be protected – only logged-in users
Router::post('/logout', 'AuthController@logout', ['auth']);

Router::get('/forgot-password', 'AuthController@forgotPasswordForm', ['guest']);
Router::post('/forgot-password', 'AuthController@sendResetLink', ['guest']);
Router::get('/reset-password', 'AuthController@resetPasswordForm', ['guest']);
Router::post('/reset-password', 'AuthController@resetPassword', ['guest']);

// -------------------------
// Dashboard / home
// -------------------------

// Default home – redirects/serves role-specific dashboard via controller logic
Router::get('/', 'DashboardController@index', ['auth']);

// Explicit dashboard routes for clarity & alignment with spec
Router::get('/dashboard', 'DashboardController@index', ['auth']);

// Role-specific entry points (all hit the same controller@index but with role guards)
Router::get('/dashboard/superadmin', 'DashboardController@index', ['auth', 'role:super_admin']);
Router::get('/dashboard/director', 'DashboardController@index', ['auth', 'role:director']);
Router::get('/dashboard/system-admin', 'DashboardController@index', ['auth', 'role:system_admin']);
Router::get('/dashboard/marketer', 'DashboardController@index', ['auth', 'role:marketer']);
Router::get('/dashboard/developer', 'DashboardController@index', ['auth', 'role:developer']);

// -------------------------
// Profile / account
// -------------------------
Router::get('/profile', 'ProfileController@show', ['auth']);
Router::post('/profile/update', 'ProfileController@update', ['auth']);
Router::post('/profile/password', 'ProfileController@updatePassword', ['auth']);

// -------------------------
// Projects
// -------------------------
Router::get('/projects', 'ProjectController@index', ['auth']);
Router::get('/projects/create', 'ProjectController@create', ['auth']);
Router::post('/projects/store', 'ProjectController@store', ['auth']);
Router::get('/projects/show', 'ProjectController@show', ['auth']);   // expects ?id=
Router::get('/projects/edit', 'ProjectController@edit', ['auth']);   // expects ?id=
Router::post('/projects/update', 'ProjectController@update', ['auth']);
Router::get('/projects/archive', 'ProjectController@archive', ['auth']); // list archived
Router::get('/projects/approvals', 'ProjectController@approvals', ['auth']);

// (Optional but recommended) route to change project status via POST
// Router::post('/projects/change-status', 'ProjectController@changeStatus', ['auth']);

// -------------------------
// Approvals
// -------------------------
Router::get('/approvals', 'ApprovalController@index', ['auth']);
Router::get('/approvals/inbox', 'ApprovalController@inbox', ['auth']);
Router::get('/approvals/show', 'ApprovalController@show', ['auth']); // expects ?id=
Router::post('/approvals/act', 'ApprovalController@act', ['auth']);

// -------------------------
// Credentials
// -------------------------
Router::get('/credentials', 'CredentialController@index', ['auth']);
Router::get('/credentials/create', 'CredentialController@create', ['auth']);
Router::post('/credentials/store', 'CredentialController@store', ['auth']);
Router::get('/credentials/show', 'CredentialController@show', ['auth']); // expects ?id=
Router::get('/credentials/edit', 'CredentialController@edit', ['auth']); // expects ?id=
Router::post('/credentials/update', 'CredentialController@update', ['auth']);

// -------------------------
// Documentation
// -------------------------
Router::get('/documentation', 'DocumentationController@index', ['auth']);
Router::get('/documentation/create', 'DocumentationController@create', ['auth']);
Router::post('/documentation/store', 'DocumentationController@store', ['auth']);
Router::get('/documentation/show', 'DocumentationController@show', ['auth']); // expects ?id=
Router::get('/documentation/edit', 'DocumentationController@edit', ['auth']); // expects ?id=
Router::post('/documentation/update', 'DocumentationController@update', ['auth']);

// -------------------------
// Notifications
// -------------------------

// List notifications page (full list)
Router::get('/notifications', 'NotificationController@index', ['auth']);

// Dashboard / bell actions
Router::post(
    '/notifications/mark-read',
    'NotificationController@markRead',
    ['auth']
);

Router::post(
    '/notifications/mark-all-read',
    'NotificationController@markAllRead',
    ['auth']
);

// -------------------------
// Weekly schedules
// -------------------------
Router::get('/schedules', 'ScheduleController@index', ['auth']);
Router::get('/schedules/create', 'ScheduleController@create', ['auth']);
Router::post('/schedules/store', 'ScheduleController@store', ['auth']);
Router::post('/schedules/update', 'ScheduleController@update', ['auth']);
Router::get('/schedules/show', 'ScheduleController@show', ['auth']); // expects ?id=
Router::get('/schedules/weekly-overview', 'ScheduleController@weeklyOverview', ['auth']);

// -------------------------
// Weekly reports
// -------------------------
Router::get('/reports', 'ReportController@index', ['auth']);
Router::get('/reports/create', 'ReportController@create', ['auth']);
Router::post('/reports/store', 'ReportController@store', ['auth']);
Router::get('/reports/show', 'ReportController@show', ['auth']); // expects ?id=
Router::get('/reports/team-overview', 'ReportController@teamOverview', ['auth']);

// -------------------------
// Users (admin)
// -------------------------
// View list: super_admin + system_admin + director (overview is fine)
Router::get(
    '/users',
    'UserController@index',
    ['auth', 'role:super_admin,system_admin,director']
);

// Create/store: ONLY super_admin
Router::get(
    '/users/create',
    'UserController@create',
    ['auth', 'role:super_admin']
);

Router::post(
    '/users/store',
    'UserController@store',
    ['auth', 'role:super_admin']
);

// View single user: super_admin + system_admin + director
Router::get(
    '/users/show',
    'UserController@show',
    ['auth', 'role:super_admin,system_admin,director']
);

// Edit/update/toggle/reset: ONLY super_admin
Router::get(
    '/users/edit',
    'UserController@edit',
    ['auth', 'role:super_admin']
);

Router::post(
    '/users/update',
    'UserController@update',
    ['auth', 'role:super_admin']
);

Router::post(
    '/users/toggle-status',
    'UserController@toggleStatus',
    ['auth', 'role:super_admin']
);

Router::post(
    '/users/reset-password',
    'UserController@sendReset',
    ['auth', 'role:super_admin']
);

// -------------------------
// Settings - account
// -------------------------
Router::get('/settings/account', 'SettingsController@account', ['auth']);
Router::post('/settings/account/profile', 'SettingsController@updateAccount', ['auth']);
Router::post('/settings/account/password', 'SettingsController@updatePassword', ['auth']);
Router::post('/settings/account/notifications', 'SettingsController@updateNotifications', ['auth']);

// -------------------------
// Settings - system (super admin / system admin)
// -------------------------
Router::get(
    '/settings/system',
    'SettingsController@system',
    ['auth', 'role:super_admin,system_admin']
);

Router::post(
    '/settings/system/app',
    'SettingsController@updateSystemApp',
    ['auth', 'role:super_admin,system_admin']
);

Router::post(
    '/settings/system/performance',
    'SettingsController@updateSystemPerformance',
    ['auth', 'role:super_admin,system_admin']
);

Router::post(
    '/settings/system/supabase',
    'SettingsController@updateSystemSupabase',
    ['auth', 'role:super_admin,system_admin']
);
