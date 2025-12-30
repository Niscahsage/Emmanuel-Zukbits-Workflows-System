<?php
// app/models/Project.php
// Project model represents a project or campaign being tracked in the ZukBits Workflows System.

namespace App\models;

use App\core\Database;
use PDO;

class Project
{
    // Find project by id
    public static function find(int $id): ?array
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare(
            'SELECT p.*, u.name AS creator_name
             FROM projects p
             LEFT JOIN users u ON u.id = p.created_by
             WHERE p.id = :id'
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    // Get all projects
    public static function all(): array
    {
        $pdo = Database::connection();
        $stmt = $pdo->query(
            'SELECT p.*, u.name AS creator_name
             FROM projects p
             LEFT JOIN users u ON u.id = p.created_by
             ORDER BY p.created_at DESC'
        );
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get projects assigned to a specific user
    public static function forUser(int $userId): array
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare(
            'SELECT p.*, u.name AS creator_name
             FROM projects p
             JOIN project_assignments pa ON pa.project_id = p.id
             LEFT JOIN users u ON u.id = p.created_by
             WHERE pa.user_id = :uid
             ORDER BY p.created_at DESC'
        );
        $stmt->execute(['uid' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Assign user to project with a role_type
    public static function assignUser(int $projectId, int $userId, string $roleType): bool
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare(
            'INSERT INTO project_assignments (project_id, user_id, role_type)
             VALUES (:project_id, :user_id, :role_type)
             ON CONFLICT (project_id, user_id) DO UPDATE
             SET role_type = EXCLUDED.role_type'
        );
        return $stmt->execute([
            'project_id' => $projectId,
            'user_id'    => $userId,
            'role_type'  => $roleType,
        ]);
    }

    // Get assignments for a project
    public static function assignments(int $projectId): array
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare(
            'SELECT pa.*, u.name AS user_name, u.email, u.id AS user_id
             FROM project_assignments pa
             JOIN users u ON u.id = pa.user_id
             WHERE pa.project_id = :pid'
        );
        $stmt->execute(['pid' => $projectId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
