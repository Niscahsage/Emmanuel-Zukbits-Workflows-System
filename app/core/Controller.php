<?php
// app/core/Controller.php
// Base Controller providing common helpers for all controllers.

namespace App\core;

abstract class Controller
{
    // Renders a view inside a layout
    protected function render(string $view, array $params = [], string $layout = 'main'): void
    {
        View::render($view, $params, $layout);
    }

    // Convenience wrapper so controllers can call $this->view(...)
    protected function view(string $view, array $params = [], string $layout = 'main'): void
    {
        $this->render($view, $params, $layout);
    }

    // Redirects to a given URL and exits
    protected function redirect(string $url): void
    {
        Helpers::redirect($url);
    }

    // Requires an authenticated user
    protected function requireAuth(): void
    {
        if (!Auth::check()) {
            Helpers::setFlash('Please log in to continue.');
            Helpers::redirect('/login');
        }
    }

    // Requires a specific role or any in a list of roles
    protected function requireRole($roles): void
    {
        $this->requireAuth();

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

    // Reads input from POST safely
    protected function input(string $key, $default = null)
    {
        return $_POST[$key] ?? $default;
    }

    // Reads query parameter safely
    protected function query(string $key, $default = null)
    {
        return $_GET[$key] ?? $default;
    }
}
