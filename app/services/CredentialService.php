<?php
// app/services/CredentialService.php
// CredentialService handles secure creation, encryption, decryption, and retrieval of credentials.

namespace App\services;

use App\repositories\CredentialRepository;
use App\models\ProjectAssignment;
use App\models\Permission;

class CredentialService
{
    private CredentialRepository $creds;
    private EncryptionService $crypto;

    public function __construct(
        ?CredentialRepository $creds = null,
        ?EncryptionService $crypto = null
    ) {
        $this->creds  = $creds ?: new CredentialRepository();
        $this->crypto = $crypto ?: new EncryptionService();
    }

    // List credentials a user can see (metadata only)
    public function listForUser(array $user): array
    {
        $roleKey = $user['role_key'] ?? 'developer';

        if (in_array($roleKey, ['super_admin', 'system_admin'], true)) {
            return $this->creds->all();
        }

        $all = $this->creds->all();
        $filtered = [];

        foreach ($all as $cred) {
            if (!$this->canAccessCredential($user, $cred)) {
                continue;
            }
            $filtered[] = $cred;
        }

        return $filtered;
    }

    // Get a credential record for user (without decrypting it yet)
    public function getForUser(array $user, int $id): ?array
    {
        $cred = $this->creds->find($id);
        if (!$cred) {
            return null;
        }

        if (!$this->canAccessCredential($user, $cred)) {
            return null;
        }

        return $cred;
    }

    // Create new credential with encryption
    public function create(array $data, int $userId): int
    {
        $cipher = $this->crypto->encrypt($data['value']);

        $payload = [
            'project_id'      => $data['project_id'],
            'label'           => $data['label'],
            'description'     => $data['description'] ?? null,
            'encrypted_value' => $cipher,
            'allowed_roles'   => $data['allowed_roles'] ?? null,
            'created_by'      => $userId,
        ];

        return $this->creds->create($payload);
    }

    // Reveal decrypted value if user allowed
    public function revealValueForUser(array $user, int $id): ?string
    {
        $cred = $this->creds->find($id);
        if (!$cred) {
            return null;
        }

        if (!$this->canAccessCredential($user, $cred)) {
            return null;
        }

        return $this->crypto->decrypt($cred['encrypted_value']);
    }

    // Check if a user can access a credential
    private function canAccessCredential(array $user, array $cred): bool
    {
        $roleKey = $user['role_key'] ?? 'developer';

        if (in_array($roleKey, ['super_admin', 'system_admin'], true)) {
            return true;
        }

        if (!empty($cred['allowed_roles'])) {
            $allowed = array_filter(array_map('trim', explode(',', $cred['allowed_roles'])));
            if (!in_array($roleKey, $allowed, true)) {
                return false;
            }
        }

        $projectId = (int) $cred['project_id'];
        return ProjectAssignment::isUserAssigned($projectId, (int) $user['id']);
    }
}
