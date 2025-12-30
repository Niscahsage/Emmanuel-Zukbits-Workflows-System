<?php
// app/services/ApprovalService.php
// ApprovalService contains business logic for approval workflows and decision tracking.

namespace App\services;

use App\repositories\ApprovalRepository;
use App\repositories\ProjectRepository;
use App\repositories\NotificationRepository;

class ApprovalService
{
    private ApprovalRepository $approvals;
    private ProjectRepository $projects;
    private NotificationRepository $notifications;

    public function __construct(
        ?ApprovalRepository $approvals = null,
        ?ProjectRepository $projects = null,
        ?NotificationRepository $notifications = null
    ) {
        $this->approvals     = $approvals ?: new ApprovalRepository();
        $this->projects      = $projects ?: new ProjectRepository();
        $this->notifications = $notifications ?: new NotificationRepository();
    }

    // List approvals based on role
    public function listForUser(array $user): array
    {
        $roleKey = $user['role_key'] ?? 'developer';

        if (in_array($roleKey, ['super_admin', 'director', 'system_admin'], true)) {
            return $this->approvals->all();
        }

        return $this->approvals->forUser((int) $user['id']);
    }

    // Pending approvals (for inbox)
    public function pendingInbox(): array
    {
        return $this->approvals->pending();
    }

    // Get approval with decisions
    public function getApproval(int $id): ?array
    {
        $approval = $this->approvals->find($id);
        if (!$approval) {
            return null;
        }

        $decisions = $this->approvals->decisions($id);

        return [
            'approval'  => $approval,
            'decisions' => $decisions,
        ];
    }

    // Request project completion approval
    public function requestProjectCompletion(array $user, int $projectId): ?int
    {
        if ($this->approvals->hasPendingProjectCompletion($projectId)) {
            return null;
        }

        $approvalId = $this->approvals->createProjectCompletionRequest(
            $projectId,
            (int) $user['id']
        );

        $project = $this->projects->find($projectId);
        if ($project) {
            $message = 'Approval requested for project: ' . ($project['name'] ?? ('#' . $projectId));
            // TODO: use NotificationService here to notify directors / admins.
            // $this->notifications->create(...);
        }

        return $approvalId;
    }

    // Record decision and optionally update project status
    public function decide(
        int $approvalId,
        int $approverId,
        string $decision,
        ?string $comment = null
    ): bool {
        $dec = strtolower($decision);
        if (!in_array($dec, ['approved', 'rejected'], true)) {
            return false;
        }

        $ok = $this->approvals->addDecision($approvalId, $approverId, $dec, $comment);

        if (!$ok) {
            return false;
        }

        $bundle = $this->getApproval($approvalId);
        if (!$bundle) {
            return true;
        }

        $approval = $bundle['approval'];

        if (($approval['approval_type'] ?? '') === 'project_completion' && $dec === 'approved') {
            $projectId = (int) $approval['target_id'];
            $project   = $this->projects->find($projectId);

            if ($project) {
                $data          = $project;
                $data['status'] = 'approved';
                $this->projects->update($projectId, $data);
            }
        }

        return true;
    }

    // -------- Dashboard summary --------
    // Returns approval statistics used in dashboards.
    public function summaryForDashboard(array $user): array
    {
        $roleKey = $user['role_key'] ?? 'developer';
        $userId  = (int) ($user['id'] ?? 0);

        if (in_array($roleKey, ['super_admin', 'director', 'system_admin'], true)) {
            $records = $this->approvals->all();
        } else {
            $records = $this->approvals->forUser($userId);
        }

        $total        = 0;
        $pending      = 0;
        $approved     = 0;
        $rejected     = 0;
        $minePending  = 0;

        foreach ($records as $row) {
            $total++;
            $status = strtolower($row['status'] ?? 'pending');

            if ($status === 'pending') {
                $pending++;
            } elseif ($status === 'approved') {
                $approved++;
            } elseif ($status === 'rejected') {
                $rejected++;
            }

            if ($status === 'pending' && (int) ($row['requested_by'] ?? 0) === $userId) {
                $minePending++;
            }
        }

        return [
            'approvals_total'         => $total,
            'approvals_pending'       => $pending,
            'approvals_approved'      => $approved,
            'approvals_rejected'      => $rejected,
            'approvals_mine_pending'  => $minePending,
        ];
    }
}
