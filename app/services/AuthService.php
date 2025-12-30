<?php
// app/services/AuthService.php
// AuthService contains business logic related to authentication and session management.
namespace App\services;

use App\repositories\UserRepository;

class AuthService
{
    private UserRepository $users;

    public function __construct(?UserRepository $users = null)
    {
        $this->users = $users ?: new UserRepository();
    }

    // Attempt login, return user array or null
    public function attemptLogin(string $email, string $password): ?array
    {
        $user = $this->users->findActiveByEmail($email);
        if (!$user) {
            return null;
        }

        if (!password_verify($password, $user['password_hash'])) {
            return null;
        }

        return $user;
    }

    // Hash a password
    public function hashPassword(string $plaintext): string
    {
        return password_hash($plaintext, PASSWORD_BCRYPT);
    }

    // Change user password if old password matches
    public function changePassword(int $userId, string $oldPassword, string $newPassword): bool
    {
        $user = $this->users->find($userId);
        if (!$user) {
            return false;
        }

        if (!password_verify($oldPassword, $user['password_hash'])) {
            return false;
        }

        $hash = $this->hashPassword($newPassword);
        return $this->users->update(
            $userId,
            $user['name'],
            $user['email'],
            (int) $user['role_id'],
            (bool) $user['is_active'],
            $hash
        );
    }
}
