// seed_roles.php seeds the database with initial core roles.
<?php
// database/seeds/seed_roles.php
// Seed core roles into workflows.roles.

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

$roles = [
    ['key' => 'super_admin',  'name' => 'Super Admin',  'description' => 'Full system access and user management.'],
    ['key' => 'director',     'name' => 'Director',     'description' => 'Executive oversight and approvals.'],
    ['key' => 'system_admin', 'name' => 'System Admin', 'description' => 'System configuration and operational management.'],
    ['key' => 'developer',    'name' => 'Developer',    'description' => 'Technical project implementation and documentation.'],
    ['key' => 'marketer',     'name' => 'Marketer',     'description' => 'Marketing projects and campaigns.'],
];

$stmt = $pdo->prepare(
    'INSERT INTO roles (key, name, description)
     VALUES (:key, :name, :description)
     ON CONFLICT (key) DO UPDATE
     SET name = EXCLUDED.name,
         description = EXCLUDED.description,
         updated_at = NOW()'
);

foreach ($roles as $role) {
    $stmt->execute($role);
}

echo "Roles seeded successfully.\n";
