<?php
// app/controllers/DashboardController.php

namespace App\controllers;

use App\core\Auth;
use App\core\Controller;
use App\core\Middleware;
use App\services\ProjectService;
use App\services\NotificationService;
use App\services\ScheduleService;
use App\services\ReportService;
use App\services\ApprovalService;

class DashboardController extends Controller
{
    private ProjectService $projects;
    private NotificationService $notifications;
    private ScheduleService $schedules;
    private ReportService $reports;
    private ApprovalService $approvals;

    public function __construct()
    {
        // Ensure only authenticated users can access any dashboard action
        Middleware::requireAuth();

        // Create service instances for this controller
        $this->projects      = new ProjectService();
        $this->notifications = new NotificationService();
        $this->schedules     = new ScheduleService();
        $this->reports       = new ReportService();
        $this->approvals     = new ApprovalService();
    }

    // Main dashboard route, picks role-specific view
    public function index(): void
    {
        // Get authenticated user
        $user = Auth::user();

        // Safety: if for some reason there's no user, redirect to login
        if (!$user || !isset($user['id'])) {
            $this->redirect('/login');
            return;
        }

        // Determine role key; default to developer
        $roleKey = $user['role_key'] ?? 'developer';

        // Collect stats from all relevant services
        $projectStats  = $this->projects->statsForDashboard($user);
        $scheduleStats = $this->schedules->summaryForDashboard($user);
        $reportStats   = $this->reports->summaryForDashboard($user);
        $approvalStats = $this->approvals->summaryForDashboard($user);

        // Merge into one stats array for the view
        $stats = array_merge(
            $projectStats,
            $scheduleStats,
            $reportStats,
            $approvalStats
        );

        // Unread notifications for the bell/badge
        $unreadCount = $this->notifications->unreadCount((int) $user['id']);

        // Choose which dashboard view to render based on role
        switch ($roleKey) {
            case 'super_admin':
                $view = 'dashboard/superadmin';
                break;
            case 'director':
                $view = 'dashboard/director';
                break;
            case 'system_admin':
                $view = 'dashboard/systemadmin';
                break;
            case 'marketer':
                $view = 'dashboard/marketer';
                break;
            default:
                $view = 'dashboard/developer';
        }

        // Render the selected view with all required data
        $this->view($view, [
            'user'         => $user,
            'stats'        => $stats,
            'unread_count' => $unreadCount,
        ]);
    }
}
