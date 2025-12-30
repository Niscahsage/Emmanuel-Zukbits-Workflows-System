<?php
// app/controllers/SettingsController.php
// SettingsController manages system-level and account-level configuration screens.

namespace App\controllers;

use App\core\Auth;
use App\core\Controller;
use App\core\Helpers;
use App\core\Middleware;
use App\services\AuthService;
use App\services\UserService;

class SettingsController extends Controller
{
    private UserService $users;
    private AuthService $auth;

    public function __construct()
    {
        $this->users = new UserService();
        $this->auth  = new AuthService();
    }

    // Show account settings screen (profile info)
    public function account(): void
    {
        Middleware::requireAuth();

        $user = Auth::user();

        $fresh = $this->users->getUser((int) $user['id']);
        if ($fresh) {
            $_SESSION['user'] = $fresh;
            $user = $fresh;
        }

        $this->view('settings/account', [
            'user' => $user,
        ]);
    }

    // Handle update of name and email
    public function updateAccount(): void
    {
        Middleware::requireAuth();

        $user = Auth::user();

        $name  = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');

        if ($name === '' || $email === '') {
            Helpers::flash('Name and email are required.');
            $this->redirect('/settings/account');
        }

        $roleId   = (int) ($user['role_id'] ?? 0);
        $isActive = isset($user['is_active']) ? (bool) $user['is_active'] : true;

        try {
            $this->users->updateUser(
                (int) $user['id'],
                $name,
                $email,
                $roleId,
                $isActive,
                null
            );

            $fresh = $this->users->getUser((int) $user['id']);
            if ($fresh) {
                $_SESSION['user'] = $fresh;
            }

            Helpers::flash('Account details updated.');
        } catch (\Throwable $e) {
            Helpers::flash('Error updating account: ' . $e->getMessage());
        }

        $this->redirect('/settings/account');
    }

    // Show security settings screen (change password)
    public function security(): void
    {
        Middleware::requireAuth();

        $this->view('settings/security', []);
    }

    // Handle password change using AuthService (from /settings/security)
    public function changePassword(): void
    {
        Middleware::requireAuth();

        $user        = Auth::user();
        $oldPassword = trim($_POST['old_password'] ?? '');
        $newPassword = trim($_POST['new_password'] ?? '');
        $confirm     = trim($_POST['new_password_confirmation'] ?? '');

        if ($oldPassword === '' || $newPassword === '' || $confirm === '') {
            Helpers::flash('All password fields are required.');
            $this->redirect('/settings/security');
        }

        if ($newPassword !== $confirm) {
            Helpers::flash('New password and confirmation do not match.');
            $this->redirect('/settings/security');
        }

        $ok = $this->auth->changePassword((int) $user['id'], $oldPassword, $newPassword);

        if (!$ok) {
            Helpers::flash('Old password is incorrect or update failed.');
            $this->redirect('/settings/security');
        }

        Helpers::flash('Password changed successfully.');
        $this->redirect('/settings/security');
    }

    // Handle password change when posted from /settings/account (same logic, different redirect)
    public function updatePassword(): void
    {
        Middleware::requireAuth();

        $user        = Auth::user();
        $oldPassword = trim($_POST['old_password'] ?? '');
        $newPassword = trim($_POST['new_password'] ?? '');
        $confirm     = trim($_POST['new_password_confirmation'] ?? '');

        if ($oldPassword === '' || $newPassword === '' || $confirm === '') {
            Helpers::flash('All password fields are required.');
            $this->redirect('/settings/account');
        }

        if ($newPassword !== $confirm) {
            Helpers::flash('New password and confirmation do not match.');
            $this->redirect('/settings/account');
        }

        $ok = $this->auth->changePassword((int) $user['id'], $oldPassword, $newPassword);

        if (!$ok) {
            Helpers::flash('Old password is incorrect or update failed.');
            $this->redirect('/settings/account');
        }

        Helpers::flash('Password changed successfully.');
        $this->redirect('/settings/account');
    }

    // Update notification preferences (for now, just accept POST and flash)
    public function updateNotifications(): void
    {
        Middleware::requireAuth();

        // You can later persist preferences once you add a settings table.
        Helpers::flash('Notification preferences updated.');
        $this->redirect('/settings/account');
    }

    // System settings screen for super_admin / system_admin
    public function system(): void
    {
        Middleware::requireAuth();

        $user = Auth::user();
        $roleKey = $user['role_key'] ?? 'developer';

        if (!in_array($roleKey, ['super_admin', 'system_admin'], true)) {
            http_response_code(403);
            echo 'Forbidden';
            return;
        }

        // For now, no persisted system settings table – just render the view.
        $this->view('settings/system', [
            'user' => $user,
        ]);
    }

    // System app-level config (placeholder; no DB yet)
    public function updateSystemApp(): void
    {
        Middleware::requireAuth();

        $user = Auth::user();
        $roleKey = $user['role_key'] ?? 'developer';

        if (!in_array($roleKey, ['super_admin', 'system_admin'], true)) {
            http_response_code(403);
            echo 'Forbidden';
            return;
        }

        // TODO: persist settings when you add a system_settings table.
        Helpers::flash('Application settings updated.');
        $this->redirect('/settings/system');
    }

    // System performance config (placeholder)
    public function updateSystemPerformance(): void
    {
        Middleware::requireAuth();

        $user = Auth::user();
        $roleKey = $user['role_key'] ?? 'developer';

        if (!in_array($roleKey, ['super_admin', 'system_admin'], true)) {
            http_response_code(403);
            echo 'Forbidden';
            return;
        }

        Helpers::flash('Performance settings updated.');
        $this->redirect('/settings/system');
    }

    // Supabase / DB config (placeholder)
    public function updateSystemSupabase(): void
    {
        Middleware::requireAuth();

        $user = Auth::user();
        $roleKey = $user['role_key'] ?? 'developer';

        if (!in_array($roleKey, ['super_admin', 'system_admin'], true)) {
            http_response_code(403);
            echo 'Forbidden';
            return;
        }

        Helpers::flash('Supabase settings updated.');
        $this->redirect('/settings/system');
    }
}
