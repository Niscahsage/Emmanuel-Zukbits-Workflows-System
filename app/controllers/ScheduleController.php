<?php
// app/controllers/ScheduleController.php
// ScheduleController handles weekly schedule creation, listing, and viewing for staff members.

namespace App\controllers;

use App\core\Auth;
use App\core\Controller;
use App\core\Helpers;
use App\core\Middleware;
use App\core\Database;
use App\services\ScheduleService;

class ScheduleController extends Controller
{
    private ScheduleService $schedules;

    public function __construct()
    {
        $this->schedules = new ScheduleService();
    }

    // List weekly schedules visible to the current user
    public function index(): void
    {
        Middleware::requireAuth();

        $user      = Auth::user();
        $schedules = $this->schedules->listForUser($user);

        $this->view('schedules/index', [
            'user'      => $user,
            'schedules' => $schedules,
        ]);
    }

    // Show a single schedule with items
    public function show(): void
    {
        Middleware::requireAuth();

        $id = (int) ($_GET['id'] ?? 0);
        if ($id <= 0) {
            Helpers::flash('Invalid schedule id.');
            $this->redirect('/schedules');
        }

        $user = Auth::user();
        $data = $this->schedules->getByIdForUser($user, $id);

        if (!$data) {
            Helpers::flash('Schedule not found or not accessible.');
            $this->redirect('/schedules');
        }

        $this->view('schedules/show', [
            'schedule' => $data['schedule'],
            'items'    => $data['items'],
        ]);
    }

    // Show create or edit form for a particular week_start
    public function create(): void
    {
        Middleware::requireAuth();

        $weekStart = trim($_GET['week_start'] ?? '');
        if ($weekStart === '') {
            Helpers::flash('Week start date is required.');
            $this->redirect('/schedules');
        }

        $user = Auth::user();
        $data = $this->schedules->getForUserAndWeek($user, $weekStart);

        if ($data === null) {
            $schedule = [
                'id'              => null,
                'week_start_date' => $weekStart,
                'summary_plan'    => '',
            ];
            $items = [];
        } else {
            $schedule = $data['schedule'];
            $items    = $data['items'];
        }

        $this->view('schedules/edit', [
            'schedule' => $schedule,
            'items'    => $items,
        ]);
    }

    // Store/update schedule for a week with items
    public function store(): void
    {
        Middleware::requireAuth();

        $weekStart = trim($_POST['week_start'] ?? '');
        $summary   = trim($_POST['summary_plan'] ?? '');

        $itemDescriptions = $_POST['item_description'] ?? [];
        $itemHours        = $_POST['item_hours'] ?? [];
        $itemProjectIds   = $_POST['item_project_id'] ?? [];

        if ($weekStart === '') {
            Helpers::flash('Week start date is required.');
            $this->redirect('/schedules');
        }

        if (!is_array($itemDescriptions) || !is_array($itemHours) || !is_array($itemProjectIds)) {
            Helpers::flash('Invalid schedule items.');
            $this->redirect('/schedules');
        }

        $items = [];
        $count = count($itemDescriptions);

        for ($i = 0; $i < $count; $i++) {
            $desc   = trim($itemDescriptions[$i] ?? '');
            $hours  = trim($itemHours[$i] ?? '');
            $projId = (int) ($itemProjectIds[$i] ?? 0);

            if ($desc === '') {
                continue;
            }

            $items[] = [
                'description'     => $desc,
                'estimated_hours' => $hours !== '' ? (float) $hours : null,
                'project_id'      => $projId > 0 ? $projId : null,
            ];
        }

        if (empty($items)) {
            Helpers::flash('Please add at least one planned task.');
            $this->redirect('/schedules/create?week_start=' . $weekStart);
        }

        $user = Auth::user();

        try {
            $scheduleId = $this->schedules->saveForUser(
                $user,
                $weekStart,
                $summary !== '' ? $summary : null,
                $items
            );

            Helpers::flash('Schedule saved successfully.');
            $this->redirect('/schedules/show?id=' . $scheduleId);
        } catch (\Throwable $e) {
            Helpers::flash('Error saving schedule: ' . $e->getMessage());
            $this->redirect('/schedules');
        }
    }

    // Alias update() to store(), since saveForUser handles both insert and update
    public function update(): void
    {
        $this->store();
    }

    // Weekly overview: show all schedules (own or team) grouped by week
    public function weeklyOverview(): void
    {
        Middleware::requireAuth();

        $user    = Auth::user();
        $roleKey = $user['role_key'] ?? 'developer';

        $pdo = Database::connection();

        // Managers see all schedules; staff see only their own
        if (in_array($roleKey, ['super_admin', 'director', 'system_admin'], true)) {
            $stmt = $pdo->query(
                'SELECT s.id,
                        s.user_id,
                        u.name AS user_name,
                        s.week_start_date,
                        s.summary_plan,
                        s.created_at,
                        s.updated_at
                 FROM weekly_schedules s
                 JOIN users u ON u.id = s.user_id
                 ORDER BY s.week_start_date DESC, u.name ASC'
            );
        } else {
            $stmt = $pdo->prepare(
                'SELECT s.id,
                        s.user_id,
                        u.name AS user_name,
                        s.week_start_date,
                        s.summary_plan,
                        s.created_at,
                        s.updated_at
                 FROM weekly_schedules s
                 JOIN users u ON u.id = s.user_id
                 WHERE s.user_id = :uid
                 ORDER BY s.week_start_date DESC'
            );
            $stmt->execute([':uid' => (int) $user['id']]);
        }

        $rows = $stmt->fetchAll();

        $this->view('schedules/weekly_overview', [
            'user'      => $user,
            'schedules' => $rows,
        ]);
    }
}
