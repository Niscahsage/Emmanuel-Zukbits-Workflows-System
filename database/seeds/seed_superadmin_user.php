// seed_superadmin_user.php seeds an initial Super Admin account for first login.
<?php
// database/seeds/seed_superadmin_user.php
// Seed initial Super Admin user.

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

// Find super_admin role id
$roleKey = 'super_admin';
$stmt = $pdo->prepare('SELECT id FROM roles WHERE key = :key LIMIT 1');
$stmt->execute(['key' => $roleKey]);
$roleId = $stmt->fetchColumn();

if (!$roleId) {
    throw new RuntimeException("Role 'super_admin' not found. Run seed_roles.php first.");
}

$adminEmail = 'emunyao2@gmail.com';
$adminName  = 'ZukBits Super Admin';
$adminPass  = 'ChangeThisPassword123!'; // change manually after first login

$passwordHash = password_hash($adminPass, PASSWORD_BCRYPT);

// Upsert user by email
$stmt = $pdo->prepare(
    'INSERT INTO users (name, email, password_hash, role_id, is_active)
     VALUES (:name, :email, :password_hash, :role_id, TRUE)
     ON CONFLICT (email) DO UPDATE
     SET name = EXCLUDED.name,
         password_hash = EXCLUDED.password_hash,
         role_id = EXCLUDED.role_id,
         is_active = TRUE,
         updated_at = NOW()
     RETURNING id'
);

$userId = $stmt->execute([
    'name'          => $adminName,
    'email'         => $adminEmail,
    'password_hash' => $passwordHash,
    'role_id'       => $roleId,
]);

echo "Super Admin seeded successfully.\n";
echo "Login email: {$adminEmail}\n";
echo "Initial password: {$adminPass}\n";
echo "Please change this password after first login.\n";
