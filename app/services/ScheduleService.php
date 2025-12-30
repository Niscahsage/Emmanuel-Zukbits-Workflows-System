<?php
// app/services/ScheduleService.php
// ScheduleService contains business logic for weekly schedules and planned tasks.

namespace App\services;

use App\repositories\WeeklyScheduleRepository;
use DateTimeImmutable;

class ScheduleService
{
    private WeeklyScheduleRepository $repo;

    public function __construct(?WeeklyScheduleRepository $repo = null)
    {
        $this->repo = $repo ?: new WeeklyScheduleRepository();
    }

    // List schedules based on role
    public function listForUser(array $user): array
    {
        $roleKey = $user['role_key'] ?? 'developer';

        if (in_array($roleKey, ['super_admin', 'director', 'system_admin'], true)) {
            return $this->repo->all();
        }

        return $this->repo->forUser((int) $user['id']);
    }

    // Get schedule and items for user and week
    public function getForUserAndWeek(array $user, string $weekStart): ?array
    {
        $schedule = $this->repo->forUserAndWeek((int) $user['id'], $weekStart);
        if (!$schedule) {
            return null;
        }

        $items = $this->repo->items((int) $schedule['id']);

        return [
            'schedule' => $schedule,
            'items'    => $items,
        ];
    }

    // Save or update schedule with items
    public function saveForUser(
        array $user,
        string $weekStart,
        ?string $summary,
        array $items
    ): int {
        return $this->repo->upsertWithItems(
            (int) $user['id'],
            $weekStart,
            $summary !== '' ? $summary : null,
            $items
        );
    }

    // Get schedule with items by id if allowed
    public function getByIdForUser(array $user, int $id): ?array
    {
        $schedule = $this->repo->find($id);
        if (!$schedule) {
            return null;
        }

        $roleKey = $user['role_key'] ?? 'developer';

        if ($schedule['user_id'] !== $user['id']
            && !in_array($roleKey, ['super_admin', 'director', 'system_admin'], true)
        ) {
            return null;
        }

        $items = $this->repo->items($id);

        return [
            'schedule' => $schedule,
            'items'    => $items,
        ];
    }

    // -------- Dashboard summary --------
    public function summaryForDashboard(array $user): array
    {
        $roleKey = $user['role_key'] ?? 'developer';
        $userId  = (int) ($user['id'] ?? 0);

        $today     = new DateTimeImmutable('today');
        $weekStart = $today->modify('monday this week')->format('Y-m-d');

        // Personal flag: do I have a schedule for this week?
        $mine = $this->repo->forUserAndWeek($userId, $weekStart);
        $hasSchedule = $mine !== null;

        // Management metrics: how many schedules exist this week?
        if (in_array($roleKey, ['super_admin', 'director', 'system_admin'], true)) {
            $all         = $this->repo->all();
            $thisWeek    = 0;

            foreach ($all as $s) {
                if (($s['week_start_date'] ?? null) === $weekStart) {
                    $thisWeek++;
                }
            }

            return [
                'has_schedule_this_week' => $hasSchedule,
                'schedules_this_week'    => $thisWeek,
            ];
        }

        return [
            'has_schedule_this_week' => $hasSchedule,
            'schedules_this_week'    => 0,
        ];
    }
}
