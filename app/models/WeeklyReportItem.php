<?php
// app/models/WeeklyReportItem.php
// WeeklyReportItem model links reported outcomes to specific planned schedule items.

namespace App\models;

use App\core\Database;
use PDO;

class WeeklyReportItem
{
    // Items for a report
    public static function forReport(int $reportId): array
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare(
            'SELECT wri.*, wsi.description AS planned_description
             FROM weekly_report_items wri
             LEFT JOIN weekly_schedule_items wsi ON wsi.id = wri.schedule_item_id
             WHERE wri.report_id = :rid
             ORDER BY wri.id ASC'
        );
        $stmt->execute(['rid' => $reportId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
