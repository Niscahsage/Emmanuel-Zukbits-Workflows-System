<?php
// app/repositories/NotificationRepository.php
// NotificationRepository encapsulates database operations for notifications.

namespace App\repositories;

use App\core\Database;
use PDO;

class NotificationRepository
{
    // All notifications for user
    public function forUser(int $userId): array
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare(
            'SELECT *
             FROM notifications
             WHERE user_id = :uid
             ORDER BY created_at DESC'
        );
        $stmt->execute(['uid' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Unread count
    public function unreadCount(int $userId): int
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare(
            'SELECT COUNT(*)
             FROM notifications
             WHERE user_id = :uid AND is_read = FALSE'
        );
        $stmt->execute(['uid' => $userId]);
        return (int) $stmt->fetchColumn();
    }

    // Create notification
    public function create(int $userId, string $type, string $message, ?string $link = null): int
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare(
            'INSERT INTO notifications (user_id, type, message, link)
             VALUES (:uid, :type, :message, :link)
             RETURNING id'
        );
        $stmt->execute([
            'uid'     => $userId,
            'type'    => $type,
            'message' => $message,
            'link'    => $link,
        ]);
        return (int) $stmt->fetchColumn();
    }

    // Mark one as read
    public function markRead(int $userId, int $notificationId): bool
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare(
            'UPDATE notifications
             SET is_read = TRUE
             WHERE id = :id AND user_id = :uid'
        );
        return $stmt->execute([
            'id'  => $notificationId,
            'uid' => $userId,
        ]);
    }

    // Mark all as read
    public function markAllRead(int $userId): bool
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare(
            'UPDATE notifications
             SET is_read = TRUE
             WHERE user_id = :uid AND is_read = FALSE'
        );
        return $stmt->execute(['uid' => $userId]);
    }
}
