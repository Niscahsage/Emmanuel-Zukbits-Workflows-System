<?php
// app/models/Role.php
// Role model represents a named role such as Super Admin, Director, System Admin, Developer, or Marketer.

namespace App\models;

use App\core\Database;
use PDO;

class Role
{
    // Get all roles
    public static function all(): array
    {
        $pdo = Database::connection();
        $stmt = $pdo->query('SELECT id, key, name, description FROM roles ORDER BY id ASC');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Find role by id
    public static function find(int $id): ?array
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('SELECT id, key, name, description FROM roles WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    // Find role by key
    public static function findByKey(string $key): ?array
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('SELECT id, key, name, description FROM roles WHERE key = :key');
        $stmt->execute(['key' => $key]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }
}
