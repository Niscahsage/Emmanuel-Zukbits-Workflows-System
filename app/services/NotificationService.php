<?php
// app/services/NotificationService.php
// NotificationService generates and manages notifications triggered by system events.

namespace App\services;

use App\repositories\NotificationRepository;

class NotificationService
{
    private NotificationRepository $repo;

    public function __construct(?NotificationRepository $repo = null)
    {
        $this->repo = $repo ?: new NotificationRepository();
    }

    // Get all notifications for a user (typically paginated at repository level)
    public function listForUser(int $userId): array
    {
        return $this->repo->forUser($userId);
    }

    // Get only the latest N notifications for dashboards / dropdown bell
    public function latestForUser(int $userId, int $limit = 10): array
    {
        $all = $this->repo->forUser($userId);

        // Assume repository returns newest first; if not, we still just slice
        return array_slice($all, 0, $limit);
    }

    // Get unread count for bell badge
    public function unreadCount(int $userId): int
    {
        return $this->repo->unreadCount($userId);
    }

    // Create a generic notification
    // $type examples: project_assigned, project_status_changed, approval_request, weekly_reminder, etc.
    public function notifyUser(int $userId, string $type, string $message, ?string $link = null): int
    {
        $type = trim($type);
        if ($type === '') {
            $type = 'general';
        }

        return $this->repo->create($userId, $type, $message, $link);
    }

    // Convenience: notify user they were assigned to a project
    public function notifyProjectAssigned(int $userId, int $projectId, string $projectName): int
    {
        $message = sprintf('You have been assigned to project: %s', $projectName);
        $link    = '/projects/' . $projectId;

        return $this->notifyUser($userId, 'project_assigned', $message, $link);
    }

    // Convenience: notify user a project status changed
    public function notifyProjectStatusChanged(
        int $userId,
        int $projectId,
        string $projectName,
        string $oldStatus,
        string $newStatus
    ): int {
        $message = sprintf(
            'Status for project "%s" changed from %s to %s.',
            $projectName,
            ucfirst($oldStatus),
            ucfirst($newStatus)
        );
        $link = '/projects/' . $projectId;

        return $this->notifyUser($userId, 'project_status_changed', $message, $link);
    }

    // Convenience: notify approver about an approval request
    public function notifyApprovalRequest(
        int $approverId,
        int $projectId,
        string $projectName
    ): int {
        $message = sprintf('Approval requested for project: %s', $projectName);
        $link    = '/projects/' . $projectId . '#approvals';

        return $this->notifyUser($approverId, 'approval_request', $message, $link);
    }

    // Convenience: weekly reminder (schedule or report)
    public function notifyWeeklyReminder(int $userId, string $reminderType = 'schedule'): int
    {
        if ($reminderType === 'report') {
            $message = 'Reminder: Please submit your weekly report.';
            $link    = '/reports';
            $type    = 'weekly_report_reminder';
        } else {
            $message = 'Reminder: Please update your weekly schedule.';
            $link    = '/weekly';
            $type    = 'weekly_schedule_reminder';
        }

        return $this->notifyUser($userId, $type, $message, $link);
    }

    // Mark single notification as read
    public function markRead(int $userId, int $notificationId): bool
    {
        return $this->repo->markRead($userId, $notificationId);
    }

    // Mark all notifications for this user as read
    public function markAllRead(int $userId): bool
    {
        return $this->repo->markAllRead($userId);
    }
}
