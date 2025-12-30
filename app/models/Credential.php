<?php
// app/models/Credential.php
// Credential model stores encrypted credentials and integration keys tied to projects.
namespace App\models;

use App\core\Database;
use PDO;

class Credential
{
    // Find credential by id
    public static function find(int $id): ?array
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare(
            'SELECT c.*, p.name AS project_name
             FROM credentials c
             JOIN projects p ON p.id = c.project_id
             WHERE c.id = :id'
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    // Credentials for a project
    public static function forProject(int $projectId): array
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare(
            'SELECT c.*, p.name AS project_name
             FROM credentials c
             JOIN projects p ON p.id = c.project_id
             WHERE c.project_id = :pid
             ORDER BY c.created_at DESC'
        );
        $stmt->execute(['pid' => $projectId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
