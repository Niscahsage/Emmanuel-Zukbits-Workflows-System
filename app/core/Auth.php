<?php
// app/core/Auth.php
// Auth manages authentication state using the session.

namespace App\core;

class Auth
{
    // Returns the currently authenticated user, or null
    public static function user(): ?array
    {
        return $_SESSION['user'] ?? null;
    }

    // Returns the authenticated user ID or null
    public static function id(): ?int
    {
        $user = self::user();
        if (!$user || !isset($user['id'])) {
            return null;
        }
        return (int)$user['id'];
    }

    // Returns true if a user is logged in
    public static function check(): bool
    {
        return self::user() !== null;
    }

    // Logs a user in by storing their data in the session
    public static function login(array $user): void
    {
        $_SESSION['user']     = $user;
        $_SESSION['user_id']  = $user['id'] ?? null;
        $_SESSION['role_key'] = $user['role_key'] ?? null;
    }

    // Logs the current user out
    public static function logout(): void
    {
        unset($_SESSION['user'], $_SESSION['user_id'], $_SESSION['role_key']);
        // Optionally clear all session
        // session_destroy(); // only if you want to clear everything
    }

    // Checks if the user has a given role
    public static function hasRole(string $roleKey): bool
    {
        $user = self::user();
        if (!$user) {
            return false;
        }
        return ($user['role_key'] ?? null) === $roleKey;
    }

    // Checks if the user has any of the given roles
    public static function hasAnyRole(array $roles): bool
    {
        $user = self::user();
        if (!$user) {
            return false;
        }
        $roleKey = $user['role_key'] ?? null;
        return $roleKey !== null && in_array($roleKey, $roles, true);
    }
}
