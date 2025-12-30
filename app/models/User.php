<?php
// app/models/User.php
// User model represents a system user, including authentication details and role information.

namespace App\models;

use App\core\Database;
use PDO;

class User
{
    // Find user by id, including role data
    public static function find(int $id): ?array
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare(
            'SELECT u.*, r.key AS role_key, r.name AS role_name
             FROM users u
             JOIN roles r ON r.id = u.role_id
             WHERE u.id = :id'
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    // Find active user by email with role data
    public static function findByEmail(string $email): ?array
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare(
            'SELECT u.*, r.key AS role_key, r.name AS role_name
             FROM users u
             JOIN roles r ON r.id = u.role_id
             WHERE u.email = :email AND u.is_active = TRUE
             LIMIT 1'
        );
        $stmt->execute(['email' => $email]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    // Get all users with role names
    public static function all(): array
    {
        $pdo = Database::connection();
        $stmt = $pdo->query(
            'SELECT u.id, u.name, u.email, u.is_active, u.created_at,
                    r.name AS role_name, r.key AS role_key
             FROM users u
             JOIN roles r ON r.id = u.role_id
             ORDER BY u.created_at DESC'
        );
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Activate or deactivate user
    public static function setActive(int $id, bool $active): bool
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare(
            'UPDATE users
             SET is_active = :active, updated_at = NOW()
             WHERE id = :id'
        );
        return $stmt->execute([
            'active' => $active,
            'id'     => $id,
        ]);
    }
}
