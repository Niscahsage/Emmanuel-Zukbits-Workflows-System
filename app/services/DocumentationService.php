<?php
// app/services/DocumentationService.php
// DocumentationService contains business logic related to project documentation records.
namespace App\services;

use App\repositories\DocumentRepository;
use App\models\ProjectAssignment;

class DocumentationService
{
    private DocumentRepository $docs;

    public function __construct(?DocumentRepository $docs = null)
    {
        $this->docs = $docs ?: new DocumentRepository();
    }

    // List documents depending on role and optional project
    public function listForUser(array $user, ?int $projectId = null): array
    {
        $roleKey = $user['role_key'] ?? 'developer';

        if ($projectId !== null && $projectId > 0) {
            return $this->docs->forProject($projectId);
        }

        if (in_array($roleKey, ['super_admin', 'director', 'system_admin'], true)) {
            return $this->docs->all();
        }

        return $this->docs->forUserProjects((int) $user['id']);
    }

    // Get a single document and ensure user is allowed by assignments
    public function getForUser(array $user, int $docId): ?array
    {
        $doc = $this->docs->find($docId);
        if (!$doc) {
            return null;
        }

        $roleKey = $user['role_key'] ?? 'developer';

        if (!in_array($roleKey, ['super_admin', 'director', 'system_admin'], true)
            && !empty($doc['project_id'])
        ) {
            $projectId = (int) $doc['project_id'];
            if (!ProjectAssignment::isUserAssigned($projectId, (int) $user['id'])) {
                return null;
            }
        }

        return $doc;
    }

    // Create documentation entry
    public function create(array $data, int $userId): int
    {
        $payload = [
            'project_id' => $data['project_id'] ?? null,
            'title'      => $data['title'],
            'type'       => $data['type'] ?? null,
            'body'       => $data['body'] ?? null,
            'file_path'  => $data['file_path'] ?? null,
            'tags'       => $data['tags'] ?? null,
            'created_by' => $userId,
        ];

        return $this->docs->create($payload);
    }

    // Update documentation entry
    public function update(int $id, array $data): bool
    {
        $payload = [
            'project_id' => $data['project_id'] ?? null,
            'title'      => $data['title'],
            'type'       => $data['type'] ?? null,
            'body'       => $data['body'] ?? null,
            'tags'       => $data['tags'] ?? null,
        ];

        return $this->docs->update($id, $payload);
    }
}
