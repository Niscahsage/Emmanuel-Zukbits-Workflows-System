<?php
// app/controllers/AuthController.php
// Handles authentication: login, logout, password reset.

namespace App\controllers;

use App\core\Controller;
use App\core\View;
use App\core\Auth;
use App\core\Helpers;
use App\core\Database;

class AuthController extends Controller
{
    // Shows the login form
    public function loginForm(): void
    {
        if (Auth::check()) {
            $this->redirect('/');
        }

        $this->render('auth/login', [], 'auth');
    }

    // Processes login submission
    public function login(): void
    {
        if (Auth::check()) {
            $this->redirect('/');
        }

        $email    = trim((string)($_POST['email'] ?? ''));
        $password = (string)($_POST['password'] ?? '');

        if ($email === '' || $password === '') {
            Helpers::setFlash('Email and password are required.');
            $this->redirect('/login');
        }

        $db = Database::getInstance();
        $sql = "select *
                from users
                where email = :email
                  and (is_active = true or is_active is null)
                limit 1";

        $stmt = $db->query($sql, ['email' => $email]);
        $user = $stmt->fetch();

        if (!$user) {
            Helpers::setFlash('Invalid credentials.');
            $this->redirect('/login');
        }

        // Try password_hash field first
        $hash = $user['password_hash'] ?? null;
        $plain = $user['password'] ?? null;

        $ok = false;
        if ($hash) {
            $ok = password_verify($password, $hash);
        } elseif ($plain !== null) {
            // fallback if demo data is stored in plain text
            $ok = hash_equals((string)$plain, $password);
        }

        if (!$ok) {
            Helpers::setFlash('Invalid credentials.');
            $this->redirect('/login');
        }

        // Normalise role fields so views can use role_key and role_name
        if (!isset($user['role_key']) && isset($user['role'])) {
            $user['role_key'] = $user['role'];
        }

        Auth::login($user);
        Helpers::setFlash('Welcome back.');
        $this->redirect('/');
    }

    // Logs the user out
    public function logout(): void
    {
        if (Auth::check()) {
            Auth::logout();
            Helpers::setFlash('You have been logged out.');
        }

        $this->redirect('/login');
    }

    // Shows the "forgot password" form
    public function forgotPasswordForm(): void
    {
        if (Auth::check()) {
            $this->redirect('/');
        }

        $this->render('auth/forgot_password', [], 'auth');
    }

    // Handles "forgot password" submission (simple stub)
    public function sendResetLink(): void
    {
        $email = trim((string)($_POST['email'] ?? ''));

        if ($email === '') {
            Helpers::setFlash('Please enter your email.');
            $this->redirect('/forgot-password');
        }

        // Here you would normally:
        // 1. Look up the user by email
        // 2. Create a password reset token
        // 3. Email the reset link
        // For now we just show a generic message.
        Helpers::setFlash('If that email exists, a reset link has been sent.');
        $this->redirect('/login');
    }

    // Shows the reset password form
    public function resetPasswordForm(): void
    {
        if (Auth::check()) {
            $this->redirect('/');
        }

        $token = $_GET['token'] ?? '';

        // In a real implementation you would validate the token
        $this->render('auth/reset_password', [
            'token' => $token,
        ], 'auth');
    }

    // Handles actual password reset (simple stub)
    public function resetPassword(): void
    {
        if (Auth::check()) {
            $this->redirect('/');
        }

        $token        = $_POST['token'] ?? '';
        $password     = (string)($_POST['password'] ?? '');
        $confirmation = (string)($_POST['password_confirmation'] ?? '');

        if ($password === '' || $confirmation === '') {
            Helpers::setFlash('Please enter and confirm the new password.');
            $this->redirect('/reset-password?token=' . urlencode($token));
        }

        if ($password !== $confirmation) {
            Helpers::setFlash('Passwords do not match.');
            $this->redirect('/reset-password?token=' . urlencode($token));
        }

        // Real implementation would:
        // - validate token
        // - find user
        // - update password_hash with password_hash($password, PASSWORD_DEFAULT)
        // For now, just pretend success.
        Helpers::setFlash('Password has been reset. You can now log in.');
        $this->redirect('/login');
    }
}
