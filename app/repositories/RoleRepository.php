<?php
// app/repositories/RoleRepository.php
// RoleRepository encapsulates database operations for roles.
namespace App\repositories;

use App\core\Database;
use PDO;

class RoleRepository
{
    // Get all roles
    public function all(): array
    {
        $pdo = Database::connection();
        $stmt = $pdo->query(
            'SELECT id, key, name, description, created_at, updated_at
             FROM roles
             ORDER BY id ASC'
        );
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Find role by id
    public function find(int $id): ?array
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare(
            'SELECT id, key, name, description, created_at, updated_at
             FROM roles
             WHERE id = :id'
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    // Find role by key
    public function findByKey(string $key): ?array
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare(
            'SELECT id, key, name, description, created_at, updated_at
             FROM roles
             WHERE key = :key'
        );
        $stmt->execute(['key' => $key]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }
}
