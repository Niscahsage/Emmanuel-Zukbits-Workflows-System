<?php
// app/models/WeeklySchedule.php
// WeeklySchedule model represents a weekly plan of tasks and targets for a user.
namespace App\models;

use App\core\Database;
use PDO;

class WeeklySchedule
{
    // Find by id
    public static function find(int $id): ?array
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare(
            'SELECT ws.*, u.name AS user_name
             FROM weekly_schedules ws
             JOIN users u ON u.id = ws.user_id
             WHERE ws.id = :id'
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    // Get schedule for user and week
    public static function forUserAndWeek(int $userId, string $weekStart): ?array
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare(
            'SELECT *
             FROM weekly_schedules
             WHERE user_id = :uid AND week_start_date = :week_start'
        );
        $stmt->execute([
            'uid'        => $userId,
            'week_start' => $weekStart,
        ]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    // All schedules for user
    public static function forUser(int $userId): array
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare(
            'SELECT *
             FROM weekly_schedules
             WHERE user_id = :uid
             ORDER BY week_start_date DESC'
        );
        $stmt->execute(['uid' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
