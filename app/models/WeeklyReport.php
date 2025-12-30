<?php
// app/models/WeeklyReport.php
// WeeklyReport model represents a submitted summary of what a user achieved during a given week.

namespace App\models;

use App\core\Database;
use PDO;

class WeeklyReport
{
    // Find report by id
    public static function find(int $id): ?array
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare(
            'SELECT wr.*, u.name AS user_name, ws.week_start_date
             FROM weekly_reports wr
             JOIN users u ON u.id = wr.user_id
             JOIN weekly_schedules ws ON ws.id = wr.schedule_id
             WHERE wr.id = :id'
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    // All reports for user
    public static function forUser(int $userId): array
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare(
            'SELECT wr.*, ws.week_start_date
             FROM weekly_reports wr
             JOIN weekly_schedules ws ON ws.id = wr.schedule_id
             WHERE wr.user_id = :uid
             ORDER BY wr.submitted_at DESC'
        );
        $stmt->execute(['uid' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
