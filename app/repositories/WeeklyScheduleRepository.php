<?php
// app/repositories/WeeklyScheduleRepository.php
// WeeklyScheduleRepository encapsulates database operations for weekly schedules and items.

namespace App\repositories;

use App\core\Database;
use PDO;

class WeeklyScheduleRepository
{
    // All schedules (admin)
    public function all(): array
    {
        $pdo = Database::connection();
        $stmt = $pdo->query(
            'SELECT ws.*, u.name AS user_name
             FROM weekly_schedules ws
             JOIN users u ON u.id = ws.user_id
             ORDER BY ws.week_start_date DESC, u.name ASC'
        );
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Schedules for a user
    public function forUser(int $userId): array
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

    // Find schedule by id
    public function find(int $id): ?array
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

    // Schedule for user and week_start_date
    public function forUserAndWeek(int $userId, string $weekStart): ?array
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

    // Items for schedule
    public function items(int $scheduleId): array
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

    // Upsert schedule and replace items
    public function upsertWithItems(
        int $userId,
        string $weekStart,
        ?string $summary,
        array $items
    ): int {
        $pdo = Database::connection();
        $pdo->beginTransaction();

        try {
            $stmt = $pdo->prepare(
                'SELECT id
                 FROM weekly_schedules
                 WHERE user_id = :uid AND week_start_date = :week_start'
            );
            $stmt->execute([
                'uid'        => $userId,
                'week_start' => $weekStart,
            ]);
            $scheduleId = $stmt->fetchColumn();

            if ($scheduleId) {
                $upd = $pdo->prepare(
                    'UPDATE weekly_schedules
                     SET summary_plan = :summary_plan,
                         updated_at = NOW()
                     WHERE id = :id'
                );
                $upd->execute([
                    'summary_plan' => $summary,
                    'id'           => $scheduleId,
                ]);

                $pdo->prepare(
                    'DELETE FROM weekly_schedule_items WHERE schedule_id = :sid'
                )->execute(['sid' => $scheduleId]);
            } else {
                $ins = $pdo->prepare(
                    'INSERT INTO weekly_schedules
                        (user_id, week_start_date, summary_plan)
                     VALUES
                        (:uid, :week_start, :summary_plan)
                     RETURNING id'
                );
                $ins->execute([
                    'uid'         => $userId,
                    'week_start'  => $weekStart,
                    'summary_plan'=> $summary,
                ]);
                $scheduleId = (int) $ins->fetchColumn();
            }

            if (!empty($items)) {
                $itemStmt = $pdo->prepare(
                    'INSERT INTO weekly_schedule_items
                        (schedule_id, description, estimated_hours, project_id)
                     VALUES
                        (:schedule_id, :description, :estimated_hours, :project_id)'
                );

                foreach ($items as $item) {
                    $desc   = trim($item['description'] ?? '');
                    $hours  = isset($item['estimated_hours']) ? (float) $item['estimated_hours'] : null;
                    $projId = isset($item['project_id']) ? (int) $item['project_id'] : 0;

                    if ($desc === '') {
                        continue;
                    }

                    $itemStmt->execute([
                        'schedule_id'    => $scheduleId,
                        'description'    => $desc,
                        'estimated_hours'=> $hours ?: null,
                        'project_id'     => $projId > 0 ? $projId : null,
                    ]);
                }
            }

            $pdo->commit();
            return (int) $scheduleId;
        } catch (\Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }
}
