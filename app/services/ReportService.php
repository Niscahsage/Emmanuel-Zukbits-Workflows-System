<?php
// app/services/ReportService.php
// ReportService contains business logic for weekly reports and their linkage to schedules.

namespace App\services;

use App\repositories\WeeklyReportRepository;
use App\repositories\WeeklyScheduleRepository;
use DateTimeImmutable;

class ReportService
{
    private WeeklyReportRepository $reports;
    private WeeklyScheduleRepository $schedules;

    public function __construct(
        ?WeeklyReportRepository $reports = null,
        ?WeeklyScheduleRepository $schedules = null
    ) {
        $this->reports   = $reports ?: new WeeklyReportRepository();
        $this->schedules = $schedules ?: new WeeklyScheduleRepository();
    }

    // List reports based on role
    public function listForUser(array $user): array
    {
        $roleKey = $user['role_key'] ?? 'developer';

        if (in_array($roleKey, ['super_admin', 'director', 'system_admin'], true)) {
            return $this->reports->all();
        }

        return $this->reports->forUser((int) $user['id']);
    }

    // Get report with items for user, enforcing visibility
    public function getForUser(array $user, int $id): ?array
    {
        $report = $this->reports->find($id);
        if (!$report) {
            return null;
        }

        $roleKey = $user['role_key'] ?? 'developer';

        if ($report['user_id'] !== $user['id']
            && !in_array($roleKey, ['super_admin', 'director', 'system_admin'], true)
        ) {
            return null;
        }

        $items = $this->reports->items($id);

        return [
            'report' => $report,
            'items'  => $items,
        ];
    }

    // Prepare data for creating a report for a specific week
    public function prepareCreate(array $user, string $weekStart): ?array
    {
        $schedule = $this->schedules->forUserAndWeek((int) $user['id'], $weekStart);
        if (!$schedule) {
            return null;
        }

        $existingId = $this->reports->existsForScheduleAndUser((int) $schedule['id'], (int) $user['id']);
        if ($existingId) {
            return [
                'exists'    => true,
                'report_id' => $existingId,
            ];
        }

        $items = $this->schedules->items((int) $schedule['id']);

        return [
            'exists'   => false,
            'schedule' => $schedule,
            'items'    => $items,
        ];
    }

    // Create weekly report with items
    public function createReport(
        array $user,
        int $scheduleId,
        ?string $overall,
        ?string $challenges,
        ?string $supportNeeded,
        array $items
    ): int {
        return $this->reports->createWithItems(
            $scheduleId,
            (int) $user['id'],
            $overall !== '' ? $overall : null,
            $challenges !== '' ? $challenges : null,
            $supportNeeded !== '' ? $supportNeeded : null,
            $items
        );
    }

    // -------- Dashboard summary --------
    public function summaryForDashboard(array $user): array
    {
        $roleKey = $user['role_key'] ?? 'developer';
        $userId  = (int) ($user['id'] ?? 0);

        // Current week start (Monday) as YYYY-MM-DD
        $today     = new DateTimeImmutable('today');
        $weekStart = $today->modify('monday this week')->format('Y-m-d');

        // For personal flag: has this user submitted a report this week?
        $userReports = $this->reports->forUser($userId);
        $hasReportThisWeek = false;

        foreach ($userReports as $r) {
            $submittedAt = $r['submitted_at'] ?? null;
            if (!$submittedAt) {
                continue;
            }

            try {
                $date = new DateTimeImmutable((string) $submittedAt);
            } catch (\Exception $e) {
                continue;
            }

            $userWeekStart = $date->modify('monday this week')->format('Y-m-d');
            if ($userWeekStart === $weekStart) {
                $hasReportThisWeek = true;
                break;
            }
        }

        // Management-level metrics
        if (in_array($roleKey, ['super_admin', 'director', 'system_admin'], true)) {
            $allSchedules = $this->schedules->all();
            $weekSchedules = [];
            foreach ($allSchedules as $s) {
                if (($s['week_start_date'] ?? null) === $weekStart) {
                    $weekSchedules[] = $s;
                }
            }

            $expected = count($weekSchedules);

            $weekScheduleIds = array_map(
                static fn(array $s) => (int) ($s['id'] ?? 0),
                $weekSchedules
            );
            $weekScheduleIds = array_filter($weekScheduleIds);

            $allReports      = $this->reports->all();
            $reportsThisWeek = [];

            if (!empty($weekScheduleIds)) {
                $idSet = array_flip($weekScheduleIds);
                foreach ($allReports as $r) {
                    $sid = (int) ($r['schedule_id'] ?? 0);
                    if (isset($idSet[$sid])) {
                        $reportsThisWeek[] = $r;
                    }
                }
            }

            $submitted = count($reportsThisWeek);

            return [
                'has_report_this_week'              => $hasReportThisWeek,
                'team_reports_expected_this_week'   => $expected,
                'team_reports_submitted_this_week'  => $submitted,
                'reports_this_week'                 => $submitted,
            ];
        }

        // For non-management roles, only the personal flag is needed
        return [
            'has_report_this_week'              => $hasReportThisWeek,
            'team_reports_expected_this_week'   => 0,
            'team_reports_submitted_this_week'  => 0,
            'reports_this_week'                 => 0,
        ];
    }
}
