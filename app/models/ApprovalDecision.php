<?php
// app/models/ApprovalDecision.php
// ApprovalDecision model stores an approver's decision, comment, and timestamp for an approval request.

namespace App\models;

use App\core\Database;
use PDO;

class ApprovalDecision
{
    // Decisions for an approval
    public static function forApproval(int $approvalId): array
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
}
