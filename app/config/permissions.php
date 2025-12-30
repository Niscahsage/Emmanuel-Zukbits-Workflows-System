<?php
// app/config/permissions.php
// Permission map for each role key.
// Controllers and middleware can check these permission strings via Auth::hasPermission().

return [
    'super_admin' => [
        'manage_users',
        'view_all_projects',
        'manage_projects',
        'view_all_reports',
        'view_all_schedules',
        'view_all_credentials',
        'manage_settings',
        'view_dashboards_all',
    ],

    'director' => [
        'view_all_projects',
        'approve_projects',
        'view_all_reports',
        'view_all_schedules',
        'view_dashboards_director',
    ],

    'system_admin' => [
        'view_all_projects',
        'manage_projects_basic',
        'view_all_reports',
        'view_all_schedules',
        'view_all_credentials',
        'view_dashboards_system_admin',
        'manage_basic_settings',
    ],

    'developer' => [
        'view_assigned_projects',
        'update_assigned_projects',
        'submit_schedules',
        'submit_reports',
        'view_own_reports',
        'view_dashboards_developer',
    ],

    'marketer' => [
        'view_assigned_projects',
        'submit_schedules',
        'submit_reports',
        'view_own_reports',
        'view_dashboards_marketer',
    ],
];
