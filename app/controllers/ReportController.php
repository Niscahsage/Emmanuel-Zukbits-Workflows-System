<?php
// app/controllers/ReportController.php
// ReportController manages weekly reports, including submitted outcomes, challenges, and team overviews.

namespace App\controllers;

use App\core\Auth;
use App\core\Controller;
use App\core\Helpers;
use App\core\Middleware;
use App\core\Database;
use App\services\ReportService;

class ReportController extends Controller
{
    private ReportService $reports;

    public function __construct()
    {
        $this->reports = new ReportService();
    }

    // List weekly reports visible to current user (role-based)
    public function index(): void
    {
        Middleware::requireAuth();

        $user    = Auth::user();
        $reports = $this->reports->listForUser($user);

        $this->view('reports/index', [
            'user'    => $user,
            'reports' => $reports,
        ]);
    }

    // Show a single weekly report with its items
    public function show(): void
    {
        Middleware::requireAuth();

        $id = (int) ($_GET['id'] ?? 0);
        if ($id <= 0) {
            Helpers::flash('Invalid report id.');
            $this->redirect('/reports');
        }

        $user = Auth::user();
        $data = $this->reports->getForUser($user, $id);

        if (!$data) {
            Helpers::flash('Report not found or not accessible.');
            $this->redirect('/reports');
        }

        $this->view('reports/show', [
            'report' => $data['report'],
            'items'  => $data['items'],
        ]);
    }

    // Show create form for a specific week_start (YYYY-MM-DD)
    public function create(): void
    {
        Middleware::requireAuth();

        $weekStart = trim($_GET['week_start'] ?? '');
        if ($weekStart === '') {
            Helpers::flash('Week start date is required.');
            $this->redirect('/schedules');
        }

        $user = Auth::user();
        $prep = $this->reports->prepareCreate($user, $weekStart);

        if ($prep === null) {
            Helpers::flash('You have no schedule for that week. Please create a schedule first.');
            $this->redirect('/schedules');
        }

        if ($prep['exists'] === true) {
            Helpers::flash('You already submitted a report for this week.');
            $this->redirect('/reports/show?id=' . $prep['report_id']);
        }

        $this->view('reports/create', [
            'schedule' => $prep['schedule'],
            'items'    => $prep['items'],
        ]);
    }

    // Store submitted weekly report
    public function store(): void
    {
        Middleware::requireAuth();

        $scheduleId    = (int) ($_POST['schedule_id'] ?? 0);
        $overall       = trim($_POST['overall_summary'] ?? '');
        $challenges    = trim($_POST['challenges'] ?? '');
        $supportNeeded = trim($_POST['support_needed'] ?? '');

        $itemScheduleIds = $_POST['item_schedule_id'] ?? [];
        $itemStatuses    = $_POST['item_status'] ?? [];
        $itemComments    = $_POST['item_comment'] ?? [];

        if ($scheduleId <= 0) {
            Helpers::flash('Invalid schedule id.');
            $this->redirect('/schedules');
        }

        if (!is_array($itemScheduleIds) || !is_array($itemStatuses) || !is_array($itemComments)) {
            Helpers::flash('Invalid report items.');
            $this->redirect('/reports');
        }

        $items = [];
        $count = count($itemScheduleIds);

        for ($i = 0; $i < $count; $i++) {
            $sid     = (int) ($itemScheduleIds[$i] ?? 0);
            $status  = trim($itemStatuses[$i] ?? '');
            $comment = trim($itemComments[$i] ?? '');

            if ($sid <= 0 || $status === '') {
                continue;
            }

            $items[] = [
                'schedule_item_id' => $sid,
                'status'           => $status,
                'comment'          => $comment,
            ];
        }

        if (empty($items)) {
            Helpers::flash('Please update at least one scheduled item in your report.');
            $this->redirect('/reports/create?week_start=' . ($_POST['week_start'] ?? ''));
        }

        $user = Auth::user();

        try {
            $reportId = $this->reports->createReport(
                $user,
                $scheduleId,
                $overall,
                $challenges,
                $supportNeeded,
                $items
            );

            Helpers::flash('Weekly report submitted successfully.');
            $this->redirect('/reports/show?id=' . $reportId);
        } catch (\Throwable $e) {
            Helpers::flash('Error submitting report: ' . $e->getMessage());
            $this->redirect('/reports');
        }
    }

    // Team overview for managers (summary of reports per user)
    public function teamOverview(): void
    {
        Middleware::requireAuth();

        $user    = Auth::user();
        $roleKey = $user['role_key'] ?? 'developer';

        if (!in_array($roleKey, ['super_admin', 'director', 'system_admin'], true)) {
            http_response_code(403);
            echo 'Forbidden';
            return;
        }

        $pdo = Database::connection();

        $stmt = $pdo->query(
            'SELECT u.id   AS user_id,
                    u.name AS user_name,
                    u.email,
                    COUNT(r.id) AS reports_count,
                    MAX(r.submitted_at) AS last_submitted_at
             FROM weekly_reports r
             JOIN users u ON u.id = r.user_id
             GROUP BY u.id, u.name, u.email
             ORDER BY last_submitted_at DESC NULLS LAST'
        );

        $rows = $stmt->fetchAll();

        $this->view('reports/team_overview', [
            'user'     => $user,
            'overview' => $rows,
        ]);
    }
}
