<?php
// app/repositories/ApprovalRepository.php
// ApprovalRepository encapsulates database operations for approvals and decisions.

namespace App\repositories;

use App\core\Database;
use PDO;

class ApprovalRepository
{
    // All approvals
    public function all(): array
    {
        $pdo = Database::connection();
        $stmt = $pdo->query(
            'SELECT a.*, u.name AS requester_name
             FROM approvals a
             LEFT JOIN users u ON u.id = a.requested_by
             ORDER BY a.created_at DESC'
        );
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Pending approvals
    public function pending(): array
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare(
            'SELECT a.*, u.name AS requester_name
             FROM approvals a
             LEFT JOIN users u ON u.id = a.requested_by
             WHERE a.status = :status
             ORDER BY a.created_at DESC'
        );
        $stmt->execute(['status' => 'pending']);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Approvals related to a user (requested by or decided by)
    public function forUser(int $userId): array
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare(
            'SELECT DISTINCT a.*, u.name AS requester_name
             FROM approvals a
             LEFT JOIN users u ON u.id = a.requested_by
             LEFT JOIN approval_decisions ad ON ad.approval_id = a.id
             WHERE a.requested_by = :uid OR ad.approver_id = :uid
             ORDER BY a.created_at DESC'
        );
        $stmt->execute(['uid' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Find single approval
    public function find(int $id): ?array
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare(
            'SELECT a.*, u.name AS requester_name
             FROM approvals a
             LEFT JOIN users u ON u.id = a.requested_by
             WHERE a.id = :id'
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    // Decisions for an approval
    public function decisions(int $approvalId): array
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare(
            'SELECT ad.*, u.name AS approver_name
             FROM approval_decisions ad
             JOIN users u ON u.id = ad.approver_id
             WHERE ad.approval_id = :aid
             ORDER BY ad.decided_at DESC'
        );
        $stmt->execute(['aid' => $approvalId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Create project completion approval request
    public function createProjectCompletionRequest(int $projectId, int $requestedBy): int
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare(
            "INSERT INTO approvals
                (approval_type, target_id, requested_by, status)
             VALUES
                ('project_completion', :target_id, :requested_by, 'pending')
             RETURNING id"
        );
        $stmt->execute([
            'target_id'    => $projectId,
            'requested_by' => $requestedBy,
        ]);
        return (int) $stmt->fetchColumn();
    }

    // Check existing pending project_completion approval
    public function hasPendingProjectCompletion(int $projectId): bool
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare(
            "SELECT 1
             FROM approvals
             WHERE approval_type = 'project_completion'
               AND target_id = :pid
               AND status = 'pending'
             LIMIT 1"
        );
        $stmt->execute(['pid' => $projectId]);
        return (bool) $stmt->fetchColumn();
    }

    // Add decision and update approval status
    public function addDecision(int $approvalId, int $approverId, string $decision, ?string $comment = null): bool
    {
        $pdo = Database::connection();
        $pdo->beginTransaction();

        try {
            $ins = $pdo->prepare(
                'INSERT INTO approval_decisions
                    (approval_id, approver_id, decision, comment)
                 VALUES
                    (:approval_id, :approver_id, :decision, :comment)'
            );
            $ins->execute([
                'approval_id' => $approvalId,
                'approver_id' => $approverId,
                'decision'    => $decision,
                'comment'     => $comment,
            ]);

            $upd = $pdo->prepare(
                'UPDATE approvals
                 SET status = :status,
                     updated_at = NOW()
                 WHERE id = :id'
            );
            $upd->execute([
                'status' => $decision,
                'id'     => $approvalId,
            ]);

            $pdo->commit();
            return true;
        } catch (\Throwable $e) {
            $pdo->rollBack();
            return false;
        }
    }
}
