<?php
// app/repositories/DocumentRepository.php
// DocumentRepository encapsulates database operations for documentation records.
namespace App\repositories;

use App\core\Database;
use PDO;

class DocumentRepository
{
    // All documents (for admin)
    public function all(): array
    {
        $pdo = Database::connection();
        $stmt = $pdo->query(
            'SELECT d.*, p.name AS project_name
             FROM documents d
             LEFT JOIN projects p ON p.id = d.project_id
             ORDER BY d.created_at DESC'
        );
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Documents for a project
    public function forProject(int $projectId): array
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare(
            'SELECT d.*, p.name AS project_name
             FROM documents d
             LEFT JOIN projects p ON p.id = d.project_id
             WHERE d.project_id = :pid
             ORDER BY d.created_at DESC'
        );
        $stmt->execute(['pid' => $projectId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Documents visible to user via project assignments
    public function forUserProjects(int $userId): array
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare(
            'SELECT d.*, p.name AS project_name
             FROM documents d
             JOIN projects p ON p.id = d.project_id
             JOIN project_assignments pa ON pa.project_id = p.id
             WHERE pa.user_id = :uid
             ORDER BY d.created_at DESC'
        );
        $stmt->execute(['uid' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Find one document
    public function find(int $id): ?array
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare(
            'SELECT d.*, p.name AS project_name
             FROM documents d
             LEFT JOIN projects p ON p.id = d.project_id
             WHERE d.id = :id'
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    // Create a new document
    public function create(array $data): int
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare(
            'INSERT INTO documents
                (project_id, title, type, body, file_path, tags, created_by)
             VALUES
                (:project_id, :title, :type, :body, :file_path, :tags, :created_by)
             RETURNING id'
        );
        $stmt->execute([
            'project_id' => $data['project_id'] ?? null,
            'title'      => $data['title'],
            'type'       => $data['type'] ?? null,
            'body'       => $data['body'] ?? null,
            'file_path'  => $data['file_path'] ?? null,
            'tags'       => $data['tags'] ?? null,
            'created_by' => $data['created_by'] ?? null,
        ]);
        return (int) $stmt->fetchColumn();
    }

    // Update document
    public function update(int $id, array $data): bool
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare(
            'UPDATE documents
             SET project_id = :project_id,
                 title = :title,
                 type = :type,
                 body = :body,
                 tags = :tags,
                 updated_at = NOW()
             WHERE id = :id'
        );
        return $stmt->execute([
            'project_id' => $data['project_id'] ?? null,
            'title'      => $data['title'],
            'type'       => $data['type'] ?? null,
            'body'       => $data['body'] ?? null,
            'tags'       => $data['tags'] ?? null,
            'id'         => $id,
        ]);
    }
}
