<?php
// app/core/Helpers.php
// Helpers provide utility methods for URLs, redirects, and flash messages.

namespace App\core;

class Helpers
{
    // Redirects to a given path or absolute URL and exits
    public static function redirect(string $url): void
    {
        if (!preg_match('#^https?://#i', $url)) {
            $url = self::url($url);
        }
        header('Location: ' . $url);
        exit;
    }

    // Returns absolute URL for a path
    public static function url(string $path = '/'): string
    {
        $path = '/' . ltrim($path, '/');

        $appUrl = $_ENV['APP_URL'] ?? '';
        if ($appUrl !== '') {
            return rtrim($appUrl, '/') . $path;
        }

        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host   = $_SERVER['HTTP_HOST'] ?? 'localhost';
        return $scheme . '://' . $host . $path;
    }

    // Sets a flash message in session
    public static function setFlash(string $message): void
    {
        $_SESSION['flash'] = $message;
    }

    // Retrieves and clears a flash message
    public static function getFlash(): ?string
    {
        if (!isset($_SESSION['flash'])) {
            return null;
        }
        $msg = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $msg;
    }

    // Generates or returns current CSRF token
    public static function csrfToken(): string
    {
        if (empty($_SESSION['_csrf_token'])) {
            $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['_csrf_token'];
    }

    // Validates a CSRF token value
    public static function verifyCsrf(?string $token): bool
    {
        if (!$token || empty($_SESSION['_csrf_token'])) {
            return false;
        }
        return hash_equals($_SESSION['_csrf_token'], $token);
    }
}
