<?php
// app/models/Notification.php
// Notification model represents an in-system notification targeted at a specific user.
namespace App\models;

use App\core\Database;
use PDO;

class Notification
{
    // All notifications for user
    public static function forUser(int $userId): array
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

    // Unread notifications count
    public static function unreadCount(int $userId): int
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

    // Mark one as read
    public static function markRead(int $id, int $userId): bool
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare(
            'UPDATE notifications
             SET is_read = TRUE
             WHERE id = :id AND user_id = :uid'
        );
        return $stmt->execute([
            'id'  => $id,
            'uid' => $userId,
        ]);
    }

    // Mark all as read
    public static function markAllRead(int $userId): bool
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare(
            'UPDATE notifications
             SET is_read = TRUE
             WHERE user_id = :uid AND is_read = FALSE'
        );
        return $stmt->execute(['uid' => $userId]);
    }

    // Create a new notification
    public static function create(int $userId, string $type, string $message, ?string $link = null): bool
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare(
            'INSERT INTO notifications (user_id, type, message, link)
             VALUES (:uid, :type, :message, :link)'
        );
        return $stmt->execute([
            'uid'     => $userId,
            'type'    => $type,
            'message' => $message,
            'link'    => $link,
        ]);
    }
}
