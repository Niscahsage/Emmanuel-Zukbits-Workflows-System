<?php
// app/models/Permission.php
// Permission model represents granular permissions that can be linked to roles.
namespace App\models;

use App\core\Auth;

// This model wraps permissions defined in app/config/permissions.php
// It does not use the database; it reads the PHP config file.

class Permission
{
    // Get the full permission map: role_key => [permissions...]
    public static function all(): array
    {
        $map = require __DIR__ . '/../config/permissions.php';
        return is_array($map) ? $map : [];
    }

    // Get all permissions for a specific role key
    public static function forRole(string $roleKey): array
    {
        $map = self::all();
        return $map[$roleKey] ?? [];
    }

    // Check if a role has a specific permission
    public static function roleHas(string $roleKey, string $permission): bool
    {
        $perms = self::forRole($roleKey);
        return in_array($permission, $perms, true);
    }

    // Check if the current logged in user has a permission
    public static function userHas(string $permission): bool
    {
        $user = Auth::user();
        if (!$user) {
            return false;
        }

        $roleKey = $user['role_key'] ?? null;
        if (!$roleKey) {
            return false;
        }

        return self::roleHas($roleKey, $permission);
    }

    // List all distinct permission strings used in the system
    public static function listAllPermissions(): array
    {
        $map = self::all();
        $all = [];

        foreach ($map as $rolePerms) {
            foreach ($rolePerms as $perm) {
                $all[$perm] = true;
            }
        }

        return array_keys($all);
    }
}
