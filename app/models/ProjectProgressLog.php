<?php
// app/models/ProjectProgressLog.php
// ProjectProgressLog model stores time-stamped updates and comments about ongoing project work.

namespace App\models;

use App\core\Database;
use PDO;

// This model represents project_progress_logs table.
// It stores chronological updates on project progress.

class ProjectProgressLog
{
    // Create a new log entry
    public static function create(
        int $projectId,
        ?int $userId,
        string $comment,
        ?string $statusSnapshot = null
    ): bool {
        if ($comment === '') {
            return false;
        }

        $pdo = Database::connection();
        $stmt = $pdo->prepare(
            'INSERT INTO project_progress_logs
                (project_id, user_id, comment, status_snapshot)
             VALUES
                (:project_id, :user_id, :comment, :status_snapshot)'
        );

        return $stmt->execute([
            'project_id'      => $projectId,
            'user_id'         => $userId,
            'comment'         => $comment,
            'status_snapshot' => $statusSnapshot !== '' ? $statusSnapshot : null,
        ]);
    }

    // All logs for a project, newest first
    public static function forProject(int $projectId): array
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare(
            'SELECT l.*, u.name AS user_name
             FROM project_progress_logs l
             LEFT JOIN users u ON u.id = l.user_id
             WHERE l.project_id = :pid
             ORDER BY l.created_at DESC'
        );
        $stmt->execute(['pid' => $projectId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // All logs created by a given user
    public static function forUser(int $userId): array
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare(
            'SELECT l.*, p.name AS project_name, p.code AS project_code
             FROM project_progress_logs l
             JOIN projects p ON p.id = l.project_id
             WHERE l.user_id = :uid
             ORDER BY l.created_at DESC'
        );
        $stmt->execute(['uid' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Latest log for a project
    public static function latestForProject(int $projectId): ?array
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare(
            'SELECT l.*, u.name AS user_name
             FROM project_progress_logs l
             LEFT JOIN users u ON u.id = l.user_id
             WHERE l.project_id = :pid
             ORDER BY l.created_at DESC
             LIMIT 1'
        );
        $stmt->execute(['pid' => $projectId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }
}
