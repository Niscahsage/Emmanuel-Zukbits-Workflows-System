<?php
// app/repositories/CredentialRepository.php
// CredentialRepository encapsulates database operations for stored credentials.

namespace App\repositories;

use App\core\Database;
use PDO;

class CredentialRepository
{
    // All credentials (for system_admin / super_admin)
    public function all(): array
    {
        $pdo = Database::connection();
        $stmt = $pdo->query(
            'SELECT c.*, p.name AS project_name
             FROM credentials c
             JOIN projects p ON p.id = c.project_id
             ORDER BY c.created_at DESC'
        );
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Credentials for a project
    public function forProject(int $projectId): array
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare(
            'SELECT c.*, p.name AS project_name
             FROM credentials c
             JOIN projects p ON p.id = c.project_id
             WHERE c.project_id = :pid
             ORDER BY c.created_at DESC'
        );
        $stmt->execute(['pid' => $projectId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Find credential by id
    public function find(int $id): ?array
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare(
            'SELECT c.*, p.name AS project_name
             FROM credentials c
             JOIN projects p ON p.id = c.project_id
             WHERE c.id = :id'
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    // Create new credential (value should already be encrypted)
    public function create(array $data): int
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare(
            'INSERT INTO credentials
                (project_id, label, description, encrypted_value, allowed_roles, created_by)
             VALUES
                (:project_id, :label, :description, :encrypted_value, :allowed_roles, :created_by)
             RETURNING id'
        );
        $stmt->execute([
            'project_id'      => $data['project_id'],
            'label'           => $data['label'],
            'description'     => $data['description'] ?? null,
            'encrypted_value' => $data['encrypted_value'],
            'allowed_roles'   => $data['allowed_roles'] ?? null,
            'created_by'      => $data['created_by'] ?? null,
        ]);
        return (int) $stmt->fetchColumn();
    }
}
