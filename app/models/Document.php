<?php
// app/models/Document.php
// Document model represents project-related documentation entries in the knowledge hub.
namespace App\models;

use App\core\Database;
use PDO;

class Document
{
    // Find one document
    public static function find(int $id): ?array
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare(
            'SELECT d.*, p.name AS project_name
             FROM documents d
             LEFT JOIN projects p ON p.id = d.project_id
             WHERE d.id = :id'
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    // All documents for a project
    public static function forProject(int $projectId): array
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare(
            'SELECT d.*, p.name AS project_name
             FROM documents d
             LEFT JOIN projects p ON p.id = d.project_id
             WHERE d.project_id = :pid
             ORDER BY d.created_at DESC'
        );
        $stmt->execute(['pid' => $projectId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // All documents visible to a user (by assignment)
    public static function forUserProjects(int $userId): array
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare(
            'SELECT d.*, p.name AS project_name
             FROM documents d
             JOIN projects p ON p.id = d.project_id
             JOIN project_assignments pa ON pa.project_id = p.id
             WHERE pa.user_id = :uid
             ORDER BY d.created_at DESC'
        );
        $stmt->execute(['uid' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
