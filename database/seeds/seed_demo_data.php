// seed_demo_data.php can seed demo projects, users, and schedules for testing.
<?php
// database/seeds/seed_demo_data.php
// Seed some demo projects, assignments, and schedules.

require_once __DIR__ . '/../../app/config/config.php';

use PDO;

$cfg = db_config();

$dsn = sprintf(
    'pgsql:host=%s;port=%d;dbname=%s;sslmode=%s',
    $cfg['host'],
    $cfg['port'],
    $cfg['database'],
    $cfg['sslmode']
);

$pdo = new PDO($dsn, $cfg['username'], $cfg['password'], [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);

$schema = $cfg['schema'] ?? 'public';
$pdo->exec('SET search_path TO ' . $schema);

// Get some users (super admin + any others you might have)
$users = $pdo->query('SELECT id, email FROM users ORDER BY id ASC')->fetchAll();

if (empty($users)) {
    throw new RuntimeException("No users found. Seed users first.");
}

// Use first user as creator
$creatorId = $users[0]['id'];

// Insert a few demo projects
$projects = [
    [
        'name'        => 'Internal ZukBits Workflows Platform',
        'code'        => 'ZWF-001',
        'client_name' => 'Zukbits Online',
        'description' => 'Core internal system for managing projects, documentation and performance.',
        'objectives'  => 'Centralize operations and performance tracking.',
        'category'    => 'internal',
        'priority'    => 'high',
        'status'      => 'ongoing',
    ],
    [
        'name'        => 'Client Website Revamp',
        'code'        => 'CWR-002',
        'client_name' => 'Sample Client Ltd.',
        'description' => 'Full redesign and backend upgrade of client website.',
        'objectives'  => 'Improve performance and UX for client platform.',
        'category'    => 'client',
        'priority'    => 'medium',
        'status'      => 'draft',
    ],
];

$projectStmt = $pdo->prepare(
    'INSERT INTO projects
        (name, code, client_name, description, objectives, category, priority, status, created_by)
     VALUES
        (:name, :code, :client_name, :description, :objectives, :category, :priority, :status, :created_by)
     ON CONFLICT (code) DO UPDATE
     SET name = EXCLUDED.name,
         client_name = EXCLUDED.client_name,
         description = EXCLUDED.description,
         objectives = EXCLUDED.objectives,
         category = EXCLUDED.category,
         priority = EXCLUDED.priority,
         status = EXCLUDED.status,
         updated_at = NOW()
     RETURNING id'
);

$projectIds = [];

foreach ($projects as $p) {
    $projectStmt->execute([
        'name'        => $p['name'],
        'code'        => $p['code'],
        'client_name' => $p['client_name'],
        'description' => $p['description'],
        'objectives'  => $p['objectives'],
        'category'    => $p['category'],
        'priority'    => $p['priority'],
        'status'      => $p['status'],
        'created_by'  => $creatorId,
    ]);
    $projectIds[] = $projectStmt->fetchColumn();
}

// Assign first user to first project as developer
if (!empty($projectIds)) {
    $firstProjectId = $projectIds[0];

    $assignStmt = $pdo->prepare(
        'INSERT INTO project_assignments (project_id, user_id, role_type)
         VALUES (:project_id, :user_id, :role_type)
         ON CONFLICT (project_id, user_id) DO NOTHING'
    );

    $assignStmt->execute([
        'project_id' => $firstProjectId,
        'user_id'    => $creatorId,
        'role_type'  => 'developer',
    ]);
}

// Optionally seed a demo weekly schedule
$today = new DateTimeImmutable('now');
$weekStart = $today->modify('monday this week')->format('Y-m-d');

$scheduleStmt = $pdo->prepare(
    'INSERT INTO weekly_schedules (user_id, week_start_date, summary_plan)
     VALUES (:user_id, :week_start_date, :summary_plan)
     ON CONFLICT (user_id, week_start_date) DO NOTHING
     RETURNING id'
);

$scheduleStmt->execute([
    'user_id'        => $creatorId,
    'week_start_date'=> $weekStart,
    'summary_plan'   => 'Initial demo week – focus on workflows system core modules.',
]);

$scheduleId = $scheduleStmt->fetchColumn();

if ($scheduleId) {
    $itemStmt = $pdo->prepare(
        'INSERT INTO weekly_schedule_items (schedule_id, description, estimated_hours, project_id)
         VALUES (:schedule_id, :description, :estimated_hours, :project_id)'
    );

    $itemStmt->execute([
        'schedule_id'     => $scheduleId,
        'description'     => 'Design database schema and core project module.',
        'estimated_hours' => 8,
        'project_id'      => $projectIds[0] ?? null,
    ]);
}

echo "Demo data seeded successfully.\n";
