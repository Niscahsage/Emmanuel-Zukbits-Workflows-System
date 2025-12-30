<?php
// app/models/Approval.php
// Approval model represents an approval request such as a project completion awaiting decision.
namespace App\models;

use App\core\Database;
use PDO;

class Approval
{
    // Find approval by id
    public static function find(int $id): ?array
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

    // Pending approvals
    public static function pending(): array
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
}
