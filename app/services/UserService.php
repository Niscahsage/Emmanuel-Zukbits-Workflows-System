<?php
// app/services/UserService.php
// UserService contains business logic for managing users, roles, and profiles.
namespace App\services;

use App\repositories\UserRepository;
use App\repositories\RoleRepository;

class UserService
{
    private UserRepository $users;
    private RoleRepository $roles;
    private AuthService $auth;

    public function __construct(
        ?UserRepository $users = null,
        ?RoleRepository $roles = null,
        ?AuthService $auth = null
    ) {
        $this->users = $users ?: new UserRepository();
        $this->roles = $roles ?: new RoleRepository();
        $this->auth  = $auth ?: new AuthService($this->users);
    }

    // List users with role info
    public function listUsers(): array
    {
        return $this->users->all();
    }

    // Create a new user
    public function createUser(
        string $name,
        string $email,
        string $password,
        int $roleId,
        bool $active = true
    ): int {
        $hash = $this->auth->hashPassword($password);
        return $this->users->create($name, $email, $hash, $roleId, $active);
    }

    // Update user basic data, optional new password
    public function updateUser(
        int $id,
        string $name,
        string $email,
        int $roleId,
        bool $active,
        ?string $newPassword = null
    ): bool {
        $hash = null;
        if ($newPassword !== null && $newPassword !== '') {
            $hash = $this->auth->hashPassword($newPassword);
        }
        return $this->users->update($id, $name, $email, $roleId, $active, $hash);
    }

    // Toggle active flag
    public function setActive(int $id, bool $active): bool
    {
        return $this->users->setActive($id, $active);
    }

    // Get single user
    public function getUser(int $id): ?array
    {
        return $this->users->find($id);
    }

    // Get all roles
    public function listRoles(): array
    {
        return $this->roles->all();
    }
}
