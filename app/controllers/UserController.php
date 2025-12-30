<?php
// app/controllers/UserController.php
// UserController manages user accounts, profiles, and role assignments within the system.

namespace App\controllers;

use App\core\Controller;
use App\core\Helpers;
use App\core\Middleware;
use App\services\UserService;

class UserController extends Controller
{
    private UserService $users;

    public function __construct()
    {
        $this->users = new UserService();
    }

    // List all users (admin)
    public function index(): void
    {
        Middleware::requirePermission('manage_users');

        $users = $this->users->listUsers();

        $this->view('users/index', [
            'users' => $users,
        ]);
    }

    // Show a single user (admin / director view)
    public function show(): void
    {
        Middleware::requirePermission('manage_users');

        $id = (int) ($_GET['id'] ?? 0);
        if ($id <= 0) {
            Helpers::flash('Invalid user id.');
            $this->redirect('/users');
        }

        $user = $this->users->getUser($id);
        if (!$user) {
            Helpers::flash('User not found.');
            $this->redirect('/users');
        }

        $this->view('users/show', [
            'user' => $user,
        ]);
    }

    // Show create user form
    public function create(): void
    {
        Middleware::requirePermission('manage_users');

        $roles = $this->users->listRoles();

        $this->view('users/create', [
            'roles' => $roles,
        ]);
    }

    // Store new user
    public function store(): void
    {
        Middleware::requirePermission('manage_users');

        $name     = trim($_POST['name'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $roleId   = (int) ($_POST['role_id'] ?? 0);
        $active   = isset($_POST['is_active']) ? true : false;

        if ($name === '' || $email === '' || $password === '' || $roleId <= 0) {
            Helpers::flash('Name, email, password and role are required.');
            $this->redirect('/users/create');
        }

        try {
            $this->users->createUser($name, $email, $password, $roleId, $active);
            Helpers::flash('User created successfully.');
        } catch (\Throwable $e) {
            Helpers::flash('Error creating user: ' . $e->getMessage());
        }

        $this->redirect('/users');
    }

    // Show edit form
    public function edit(): void
    {
        Middleware::requirePermission('manage_users');

        $id = (int) ($_GET['id'] ?? 0);
        if ($id <= 0) {
            Helpers::flash('Invalid user id.');
            $this->redirect('/users');
        }

        $user  = $this->users->getUser($id);
        $roles = $this->users->listRoles();

        if (!$user) {
            Helpers::flash('User not found.');
            $this->redirect('/users');
        }

        $this->view('users/edit', [
            'user'  => $user,
            'roles' => $roles,
        ]);
    }

    // Update user (admin)
    public function update(): void
    {
        Middleware::requirePermission('manage_users');

        $id       = (int) ($_POST['id'] ?? 0);
        $name     = trim($_POST['name'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $roleId   = (int) ($_POST['role_id'] ?? 0);
        $active   = isset($_POST['is_active']) ? true : false;

        if ($id <= 0 || $name === '' || $email === '' || $roleId <= 0) {
            Helpers::flash('Name, email and role are required.');
            $this->redirect('/users');
        }

        try {
            $this->users->updateUser(
                $id,
                $name,
                $email,
                $roleId,
                $active,
                $password !== '' ? $password : null
            );

            Helpers::flash('User updated successfully.');
        } catch (\Throwable $e) {
            Helpers::flash('Error updating user: ' . $e->getMessage());
        }

        $this->redirect('/users');
    }

    // Toggle active flag
    public function toggleActive(): void
    {
        Middleware::requirePermission('manage_users');

        $id     = (int) ($_POST['id'] ?? 0);
        $active = isset($_POST['is_active']) ? true : false;

        if ($id <= 0) {
            Helpers::flash('Invalid user id.');
            $this->redirect('/users');
        }

        try {
            $this->users->setActive($id, $active);
            Helpers::flash('User status updated.');
        } catch (\Throwable $e) {
            Helpers::flash('Error updating user status: ' . $e->getMessage());
        }

        $this->redirect('/users');
    }

    // Backwards-compatible alias for toggleActive() used in routes.php (/users/toggle-status)
    public function toggleStatus(): void
    {
        $this->toggleActive();
    }

    // Admin-triggered "reset password" helper (stub until full email/token flow exists)
    public function sendReset(): void
    {
        Middleware::requirePermission('manage_users');

        $id = (int) ($_POST['id'] ?? 0);
        if ($id <= 0) {
            Helpers::flash('Invalid user id.');
            $this->redirect('/users');
        }

        $user = $this->users->getUser($id);
        if (!$user) {
            Helpers::flash('User not found.');
            $this->redirect('/users');
        }

        // In a full implementation you would:
        // - generate a secure reset token
        // - store it in a password_resets table
        // - email a /reset-password?token=... link to $user['email']
        // For now we just acknowledge the action.
        Helpers::flash('Password reset instructions would be sent to: ' . $user['email']);
        $this->redirect('/users');
    }
}
