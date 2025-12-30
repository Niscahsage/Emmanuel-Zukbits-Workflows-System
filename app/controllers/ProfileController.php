<?php
// app/controllers/ProfileController.php
// ProfileController manages the current user's profile view and basic profile editing.

namespace App\controllers;

use App\core\Auth;
use App\core\Controller;
use App\core\Helpers;
use App\core\Middleware;
use App\services\UserService;

class ProfileController extends Controller
{
    private UserService $users;

    public function __construct()
    {
        $this->users = new UserService();
    }

    // Show current user's profile
    public function show(): void
    {
        Middleware::requireAuth();

        $user = Auth::user();

        $this->view('users/profile', [
            'user' => $user,
        ]);
    }

    // Update current user's profile using UserService
    public function update(): void
    {
        Middleware::requireAuth();

        $user = Auth::user();

        $name     = trim($_POST['name'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');

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
                $password !== '' ? $password : null
            );

            $fresh = $this->users->getUser((int) $user['id']);
            if ($fresh) {
                $_SESSION['user'] = $fresh;
            }

            Helpers::flash('Profile updated successfully.');
        } catch (\Throwable $e) {
            Helpers::flash('Error updating profile: ' . $e->getMessage());
        }

        $this->redirect('/settings/account');
    }

    // Route alias: direct /profile/password to security settings
    public function updatePassword(): void
    {
        Middleware::requireAuth();

        Helpers::flash('Please change your password from the Security settings page.');
        $this->redirect('/settings/security');
    }
}
