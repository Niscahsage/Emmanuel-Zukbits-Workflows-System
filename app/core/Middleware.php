<?php
// app/core/Middleware.php
// Middleware helpers for auth and role checks.

namespace App\core;

class Middleware
{
    // Direct helper: require an authenticated user (used in some controllers)
    public static function requireAuth(): void
    {
        if (!Auth::check()) {
            Helpers::setFlash('Please log in to continue.');
            Helpers::redirect('/login');
        }
    }

    // Direct helper: require guest (not logged in)
    public static function requireGuest(): void
    {
        if (Auth::check()) {
            Helpers::redirect('/');
        }
    }

    // Direct helper: require specific role or roles
    public static function requireRole($roles): void
    {
        self::requireAuth();

        $roles = is_array($roles) ? $roles : [$roles];
        $user  = Auth::user();
        $roleKey = $user['role_key'] ?? null;

        if ($roleKey === null || !in_array($roleKey, $roles, true)) {
            http_response_code(403);
            View::render('errors/403', [
                'message' => 'You are not allowed to access this resource.',
            ], 'error');
            exit;
        }
    }

    // Router middleware: requires authentication
    public static function auth(): callable
    {
        return function (): void {
            self::requireAuth();
        };
    }

    // Router middleware: requires guest
    public static function guest(): callable
    {
        return function (): void {
            self::requireGuest();
        };
    }

    // Router middleware: requires any of the given roles
    public static function role(array $roles): callable
    {
        return function () use ($roles): void {
            self::requireRole($roles);
        };
    }
}
