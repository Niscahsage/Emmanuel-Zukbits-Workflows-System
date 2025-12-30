<?php
// app/models/WeeklyScheduleItem.php
// WeeklyScheduleItem model stores individual planned tasks inside a weekly schedule.
namespace App\models;

use App\core\Database;
use PDO;

class WeeklyScheduleItem
{
    // Items for a schedule
    public static function forSchedule(int $scheduleId): array
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare(
            'SELECT wsi.*, p.name AS project_name
             FROM weekly_schedule_items wsi
             LEFT JOIN projects p ON p.id = wsi.project_id
             WHERE wsi.schedule_id = :sid
             ORDER BY wsi.id ASC'
        );
        $stmt->execute(['sid' => $scheduleId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
