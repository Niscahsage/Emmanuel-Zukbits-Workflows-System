<?php
// app/models/ProjectAssignment.php
// ProjectAssignment model links users to projects with specific responsibilities such as developer or marketer.

namespace App\models;

use App\core\Database;
use PDO;

// This model represents rows in project_assignments table.
// It handles assigning users to projects and querying assignments.

class ProjectAssignment
{
    // Get all assignments for a project
    public static function forProject(int $projectId): array
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare(
            'SELECT pa.*, u.name AS user_name, u.email AS user_email, u.id AS user_id
             FROM project_assignments pa
             JOIN users u ON u.id = pa.user_id
             WHERE pa.project_id = :pid
             ORDER BY u.name ASC'
        );
        $stmt->execute(['pid' => $projectId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get all assignments for a user
    public static function forUser(int $userId): array
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare(
            'SELECT pa.*, p.name AS project_name, p.code AS project_code
             FROM project_assignments pa
             JOIN projects p ON p.id = pa.project_id
             WHERE pa.user_id = :uid
             ORDER BY p.created_at DESC'
        );
        $stmt->execute(['uid' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Assign a user to a project with a given role_type (e.g. developer, marketer)
    public static function assign(int $projectId, int $userId, string $roleType): bool
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

    // Remove a user from a project
    public static function remove(int $projectId, int $userId): bool
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare(
            'DELETE FROM project_assignments
             WHERE project_id = :project_id AND user_id = :user_id'
        );

        return $stmt->execute([
            'project_id' => $projectId,
            'user_id'    => $userId,
        ]);
    }

    // Check if user is assigned to project
    public static function isUserAssigned(int $projectId, int $userId): bool
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare(
            'SELECT 1
             FROM project_assignments
             WHERE project_id = :project_id AND user_id = :user_id'
        );
        $stmt->execute([
            'project_id' => $projectId,
            'user_id'    => $userId,
        ]);

        return (bool) $stmt->fetchColumn();
    }

    // Change role type of an assignment
    public static function updateRoleType(int $projectId, int $userId, string $roleType): bool
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare(
            'UPDATE project_assignments
             SET role_type = :role_type
             WHERE project_id = :project_id AND user_id = :user_id'
        );

        return $stmt->execute([
            'role_type'  => $roleType,
            'project_id' => $projectId,
            'user_id'    => $userId,
        ]);
    }
}
