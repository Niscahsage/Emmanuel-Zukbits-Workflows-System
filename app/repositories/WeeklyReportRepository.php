<?php
// app/repositories/WeeklyReportRepository.php
// WeeklyReportRepository encapsulates database operations for weekly reports and items.

namespace App\repositories;

use App\core\Database;
use PDO;

class WeeklyReportRepository
{
    // All reports (admin)
    public function all(): array
    {
        $pdo = Database::connection();
        $stmt = $pdo->query(
            'SELECT wr.*, u.name AS user_name, ws.week_start_date
             FROM weekly_reports wr
             JOIN users u ON u.id = wr.user_id
             JOIN weekly_schedules ws ON ws.id = wr.schedule_id
             ORDER BY wr.submitted_at DESC'
        );
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Reports for user
    public function forUser(int $userId): array
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

    // Find report by id
    public function find(int $id): ?array
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

    // Items for a report
    public function items(int $reportId): array
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

    // Check if report exists for schedule and user
    public function existsForScheduleAndUser(int $scheduleId, int $userId): ?int
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare(
            'SELECT id
             FROM weekly_reports
             WHERE schedule_id = :sid AND user_id = :uid'
        );
        $stmt->execute([
            'sid' => $scheduleId,
            'uid' => $userId,
        ]);
        $id = $stmt->fetchColumn();
        return $id ? (int) $id : null;
    }

    // Create report with items
    public function createWithItems(
        int $scheduleId,
        int $userId,
        ?string $overall,
        ?string $challenges,
        ?string $supportNeeded,
        array $items
    ): int {
        $pdo = Database::connection();
        $pdo->beginTransaction();

        try {
            $ins = $pdo->prepare(
                'INSERT INTO weekly_reports
                    (schedule_id, user_id, overall_summary, challenges, support_needed)
                 VALUES
                    (:schedule_id, :user_id, :overall_summary, :challenges, :support_needed)
                 RETURNING id'
            );
            $ins->execute([
                'schedule_id'    => $scheduleId,
                'user_id'        => $userId,
                'overall_summary'=> $overall,
                'challenges'     => $challenges,
                'support_needed' => $supportNeeded,
            ]);
            $reportId = (int) $ins->fetchColumn();

            if (!empty($items)) {
                $itemStmt = $pdo->prepare(
                    'INSERT INTO weekly_report_items
                        (report_id, schedule_item_id, status, comment)
                     VALUES
                        (:report_id, :schedule_item_id, :status, :comment)'
                );

                foreach ($items as $item) {
                    $scheduleItemId = isset($item['schedule_item_id']) ? (int) $item['schedule_item_id'] : 0;
                    $status         = trim($item['status'] ?? '');
                    $comment        = trim($item['comment'] ?? '');

                    if ($scheduleItemId <= 0 || $status === '') {
                        continue;
                    }

                    $itemStmt->execute([
                        'report_id'        => $reportId,
                        'schedule_item_id' => $scheduleItemId,
                        'status'           => $status,
                        'comment'          => $comment !== '' ? $comment : null,
                    ]);
                }
            }

            $pdo->commit();
            return $reportId;
        } catch (\Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }
}
