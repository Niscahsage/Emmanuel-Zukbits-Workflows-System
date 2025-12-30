<?php
// app/repositories/UserRepository.php
// UserRepository encapsulates database operations for users.
namespace App\repositories;

use App\core\Database;
use PDO;

class UserRepository
{
    // Get all users with role info
    public function all(): array
    {
        $pdo = Database::connection();
        $stmt = $pdo->query(
            'SELECT u.id, u.name, u.email, u.is_active, u.created_at, u.updated_at,
                    r.id AS role_id, r.key AS role_key, r.name AS role_name
             FROM users u
             JOIN roles r ON r.id = u.role_id
             ORDER BY u.created_at DESC'
        );
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Find user by id with role info
    public function find(int $id): ?array
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

    // Find active user by email with role info
    public function findActiveByEmail(string $email): ?array
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

    // Create a new user
    public function create(
        string $name,
        string $email,
        string $passwordHash,
        int $roleId,
        bool $active = true
    ): int {
        $pdo = Database::connection();
        $stmt = $pdo->prepare(
            'INSERT INTO users (name, email, password_hash, role_id, is_active)
             VALUES (:name, :email, :password_hash, :role_id, :is_active)
             RETURNING id'
        );
        $stmt->execute([
            'name'          => $name,
            'email'         => $email,
            'password_hash' => $passwordHash,
            'role_id'       => $roleId,
            'is_active'     => $active,
        ]);
        return (int) $stmt->fetchColumn();
    }

    // Update an existing user (without forcing password change)
    public function update(
        int $id,
        string $name,
        string $email,
        int $roleId,
        bool $active,
        ?string $passwordHash = null
    ): bool {
        $pdo = Database::connection();

        if ($passwordHash !== null && $passwordHash !== '') {
            $sql = 'UPDATE users
                    SET name = :name,
                        email = :email,
                        role_id = :role_id,
                        is_active = :is_active,
                        password_hash = :password_hash,
                        updated_at = NOW()
                    WHERE id = :id';
            $params = [
                'name'          => $name,
                'email'         => $email,
                'role_id'       => $roleId,
                'is_active'     => $active,
                'password_hash' => $passwordHash,
                'id'            => $id,
            ];
        } else {
            $sql = 'UPDATE users
                    SET name = :name,
                        email = :email,
                        role_id = :role_id,
                        is_active = :is_active,
                        updated_at = NOW()
                    WHERE id = :id';
            $params = [
                'name'      => $name,
                'email'     => $email,
                'role_id'   => $roleId,
                'is_active' => $active,
                'id'        => $id,
            ];
        }

        $stmt = $pdo->prepare($sql);
        return $stmt->execute($params);
    }

    // Set active flag
    public function setActive(int $id, bool $active): bool
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
