<?php
// app/repositories/ProjectRepository.php
// ProjectRepository encapsulates database operations for projects and related queries.
namespace App\repositories;

use App\core\Database;
use PDO;

class ProjectRepository
{
    // Get all projects with creator info
    public function all(): array
    {
        $pdo = Database::connection();
        $stmt = $pdo->query(
            'SELECT p.*, u.name AS creator_name
             FROM projects p
             LEFT JOIN users u ON u.id = p.created_by
             ORDER BY p.created_at DESC'
        );
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Projects visible to a given user (via assignments)
    public function forUser(int $userId): array
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare(
            'SELECT p.*, u.name AS creator_name
             FROM projects p
             JOIN project_assignments pa ON pa.project_id = p.id
             LEFT JOIN users u ON u.id = p.created_by
             WHERE pa.user_id = :uid
             ORDER BY p.created_at DESC'
        );
        $stmt->execute(['uid' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Find project by id
    public function find(int $id): ?array
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare(
            'SELECT p.*, u.name AS creator_name
             FROM projects p
             LEFT JOIN users u ON u.id = p.created_by
             WHERE p.id = :id'
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    // Find project by code
    public function findByCode(string $code): ?array
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare(
            'SELECT p.*, u.name AS creator_name
             FROM projects p
             LEFT JOIN users u ON u.id = p.created_by
             WHERE p.code = :code'
        );
        $stmt->execute(['code' => $code]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    // Create a new project
    public function create(array $data): int
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare(
            'INSERT INTO projects
                (name, code, client_name, description, objectives, category, priority,
                 status, start_date, target_end_date, created_by)
             VALUES
                (:name, :code, :client_name, :description, :objectives, :category, :priority,
                 :status, :start_date, :target_end_date, :created_by)
             RETURNING id'
        );

        $stmt->execute([
            'name'            => $data['name'],
            'code'            => $data['code'] ?? null,
            'client_name'     => $data['client_name'] ?? null,
            'description'     => $data['description'] ?? null,
            'objectives'      => $data['objectives'] ?? null,
            'category'        => $data['category'] ?? null,
            'priority'        => $data['priority'] ?? null,
            'status'          => $data['status'] ?? 'draft',
            'start_date'      => $data['start_date'] ?? null,
            'target_end_date' => $data['target_end_date'] ?? null,
            'created_by'      => $data['created_by'] ?? null,
        ]);

        return (int) $stmt->fetchColumn();
    }

    // Update an existing project
    public function update(int $id, array $data): bool
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare(
            'UPDATE projects
             SET name = :name,
                 code = :code,
                 client_name = :client_name,
                 description = :description,
                 objectives = :objectives,
                 category = :category,
                 priority = :priority,
                 status = :status,
                 start_date = :start_date,
                 target_end_date = :target_end_date,
                 updated_at = NOW()
             WHERE id = :id'
        );

        return $stmt->execute([
            'name'            => $data['name'],
            'code'            => $data['code'] ?? null,
            'client_name'     => $data['client_name'] ?? null,
            'description'     => $data['description'] ?? null,
            'objectives'      => $data['objectives'] ?? null,
            'category'        => $data['category'] ?? null,
            'priority'        => $data['priority'] ?? null,
            'status'          => $data['status'] ?? 'draft',
            'start_date'      => $data['start_date'] ?? null,
            'target_end_date' => $data['target_end_date'] ?? null,
            'id'              => $id,
        ]);
    }

    // Count projects
    public function countAll(): int
    {
        $pdo = Database::connection();
        return (int) $pdo->query('SELECT COUNT(*) FROM projects')->fetchColumn();
    }

    // Count ongoing projects
    public function countOngoing(): int
    {
        $pdo = Database::connection();
        $stmt = $pdo->query("SELECT COUNT(*) FROM projects WHERE status = 'ongoing'");
        return (int) $stmt->fetchColumn();
    }
}
