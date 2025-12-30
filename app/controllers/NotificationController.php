<?php
// app/controllers/NotificationController.php
// NotificationController lists and manages in-system notifications for each user.
namespace App\controllers;

use App\core\Auth;
use App\core\Controller;
use App\core\Helpers;
use App\core\Middleware;
use App\services\NotificationService;

class NotificationController extends Controller
{
    private NotificationService $service;

    public function __construct()
    {
        $this->service = new NotificationService();
    }

    // List notifications for current user
    public function index(): void
    {
        Middleware::requireAuth();

        $user           = Auth::user();
        $notifications  = $this->service->listForUser((int) $user['id']);

        $this->view('notifications/index', [
            'notifications' => $notifications,
        ]);
    }

    // Mark a single notification as read
    public function markRead(): void
    {
        Middleware::requireAuth();

        $id = (int) ($_POST['id'] ?? 0);
        if ($id <= 0) {
            Helpers::flash('Invalid notification id.');
            $this->redirect('/notifications');
        }

        $user = Auth::user();
        $this->service->markRead((int) $user['id'], $id);

        $this->redirect('/notifications');
    }

    // Mark all notifications as read
    public function markAllRead(): void
    {
        Middleware::requireAuth();

        $user = Auth::user();
        $this->service->markAllRead((int) $user['id']);

        Helpers::flash('All notifications marked as read.');
        $this->redirect('/notifications');
    }
}
