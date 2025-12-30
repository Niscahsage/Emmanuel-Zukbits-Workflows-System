<?php
// app/services/ProjectService.php
// ProjectService contains business logic for creating, updating, and tracking projects.

namespace App\services;

use App\repositories\ProjectRepository;
use App\models\ProjectAssignment;
use DateTimeImmutable;

class ProjectService
{
    private ProjectRepository $projects;

    public function __construct(?ProjectRepository $projects = null)
    {
        // Allow passing a mock in tests, default to real repository in production
        $this->projects = $projects ?: new ProjectRepository();
    }

    // List projects based on role
    public function listForUser(array $user): array
    {
        $roleKey = $user['role_key'] ?? 'developer';

        if (in_array($roleKey, ['super_admin', 'director', 'system_admin'], true)) {
            return $this->projects->all();
        }

        return $this->projects->forUser((int) $user['id']);
    }

    // Get single project
    public function getProject(int $id): ?array
    {
        return $this->projects->find($id);
    }

    // Create project with creator id, defaulting status to "draft" if not provided
    public function createProject(array $data, int $creatorId): int
    {
        $data['created_by'] = $creatorId;

        if (empty($data['status'])) {
            $data['status'] = 'draft'; // FR-11 default lifecycle start
        }

        return $this->projects->create($data);
    }

    // Update project (no business rule checks here – controller/service can enforce)
    public function updateProject(int $id, array $data): bool
    {
        return $this->projects->update($id, $data);
    }

    // Assign user to project (developer/marketer/etc.)
    public function assignUser(int $projectId, int $userId, string $roleType): bool
    {
        // roleType might be "developer" or "marketer" or similar
        return ProjectAssignment::assign($projectId, $userId, $roleType);
    }

    // Get project assignments
    public function getAssignments(int $projectId): array
    {
        return ProjectAssignment::forProject($projectId);
    }

    // Change project status with simple lifecycle & role rules
    // Draft -> Ongoing -> Pending Approval -> Approved -> Archived
    public function changeStatusWithRules(int $projectId, string $newStatus, array $actor): bool
    {
        $project = $this->projects->find($projectId);

        if (!$project) {
            return false;
        }

        $currentStatus = $this->normalizeStatus($project['status'] ?? 'draft');
        $targetStatus  = $this->normalizeStatus($newStatus);
        $roleKey       = $actor['role_key'] ?? 'developer';

        if (!$this->isValidStatusTransition($currentStatus, $targetStatus, $roleKey)) {
            return false;
        }

        // For now we only update status; extra fields (approved_by, etc.) can be added later
        return $this->projects->update($projectId, [
            'status' => $targetStatus,
        ]);
    }

    // Count stats for dashboard (role-based)
    public function statsForDashboard(array $user): array
    {
        $roleKey = $user['role_key'] ?? 'developer';
        $userId  = (int) ($user['id'] ?? 0);

        // Management views (Super Admin, Director, System Admin) see ALL projects
        if (in_array($roleKey, ['super_admin', 'director', 'system_admin'], true)) {
            $allProjects = $this->projects->all();
            $statsAll    = $this->computeProjectStats($allProjects);

            $projectsAtRisk = $statsAll['projects_overdue'] + $statsAll['projects_pending_approval'];

            // Keep backward-compatible keys and add richer metrics for the dashboards.
            return [
                // Backwards compatible
                'projects_total'             => $statsAll['projects_total'],
                'projects_ongoing'           => $statsAll['projects_ongoing'],

                // Richer metrics
                'projects_draft'             => $statsAll['projects_draft'],
                'projects_pending_approval'  => $statsAll['projects_pending_approval'],
                'projects_approved'          => $statsAll['projects_approved'],
                'projects_archived'          => $statsAll['projects_archived'],
                'projects_completed'         => $statsAll['projects_completed'],
                'projects_overdue'           => $statsAll['projects_overdue'],
                'projects_next_deadline'     => $statsAll['projects_next_deadline'],
                'projects_at_risk'           => $projectsAtRisk,

                // System-admin health placeholders (can be refined using other repos later)
                'projects_missing_docs'         => 0,
                'projects_missing_credentials'  => 0,
                'projects_without_developer'    => 0,
                'projects_without_recent_updates' => 0,
            ];
        }

        // Developer / Marketer dashboards – only their assigned projects
        $assigned = $this->projects->forUser($userId);
        $statsMy  = $this->computeProjectStats($assigned);

        return [
            // Backwards compatible keys so existing views keep working
            'projects_total'                => $statsMy['projects_total'],
            'projects_ongoing'              => $statsMy['projects_ongoing'],

            // Role-focused keys
            'my_projects_total'             => $statsMy['projects_total'],
            'my_projects_ongoing'           => $statsMy['projects_ongoing'],
            'my_projects_completed'         => $statsMy['projects_completed'],
            'my_projects_pending_approval'  => $statsMy['projects_pending_approval'],
            'my_projects_overdue'           => $statsMy['projects_overdue'],
            'my_next_deadline'              => $statsMy['projects_next_deadline'],

            // Aliases used by dashboards
            'projects_overdue_assigned'     => $statsMy['projects_overdue'],
            'next_deadline_date'            => $statsMy['projects_next_deadline'],
            'next_campaign_deadline'        => $statsMy['projects_next_deadline'],
        ];
    }

    // ---------- INTERNAL HELPERS ----------

    // Normalize status string to a small set of internal labels
    private function normalizeStatus(string $status): string
    {
        $s = strtolower(trim($status));

        // Accept a few common variants and map them
        if ($s === 'completed - pending approval' || $s === 'completed_pending_approval') {
            return 'pending_approval';
        }

        // Keep consistent set: draft, ongoing, pending_approval, approved, archived
        $allowed = ['draft', 'ongoing', 'pending_approval', 'approved', 'archived'];

        if (!in_array($s, $allowed, true)) {
            return 'draft';
        }

        return $s;
    }

    // Validate lifecycle transitions + role rules
    private function isValidStatusTransition(string $from, string $to, string $roleKey): bool
    {
        if ($from === $to) {
            return true;
        }

        // Basic lifecycle chain
        $allowedTransitions = [
            'draft'            => ['ongoing'],                     // start work
            'ongoing'          => ['pending_approval', 'archived'],// dev/system admin
            'pending_approval' => ['approved', 'ongoing'],         // approved or sent back
            'approved'         => ['archived'],                    // cleanup
            'archived'         => [],                              // end state
        ];

        if (!isset($allowedTransitions[$from])) {
            return false;
        }

        if (!in_array($to, $allowedTransitions[$from], true)) {
            return false;
        }

        // Role-specific restrictions:
        // - Only Director / Super Admin approve completion.
        if ($from === 'pending_approval' && $to === 'approved') {
            if (!in_array($roleKey, ['director', 'super_admin'], true)) {
                return false;
            }
        }

        return true;
    }

    // Compute rich stats from a list of projects (for any role)
    private function computeProjectStats(array $projects): array
    {
        $total       = count($projects);
        $now         = new DateTimeImmutable('today');
        $nextDeadline = null;

        $draft           = 0;
        $ongoing         = 0;
        $pendingApproval = 0;
        $approved        = 0;
        $archived        = 0;
        $overdue         = 0;

        foreach ($projects as $project) {
            $status = $this->normalizeStatus($project['status'] ?? 'draft');

            switch ($status) {
                case 'draft':
                    $draft++;
                    break;
                case 'ongoing':
                    $ongoing++;
                    break;
                case 'pending_approval':
                    $pendingApproval++;
                    break;
                case 'approved':
                    $approved++;
                    break;
                case 'archived':
                    $archived++;
                    break;
            }

            // Deadline handling (target_end_date per spec)
            $endDateRaw = $project['target_end_date'] ?? $project['end_date'] ?? null;

            if ($endDateRaw) {
                try {
                    $endDate = new DateTimeImmutable((string) $endDateRaw);

                    // Overdue = past date and not fully done
                    $isTerminal = in_array($status, ['approved', 'archived'], true);

                    if ($endDate < $now && !$isTerminal) {
                        $overdue++;
                    }

                    // Next upcoming deadline
                    if ($endDate >= $now) {
                        if ($nextDeadline === null || $endDate < $nextDeadline) {
                            $nextDeadline = $endDate;
                        }
                    }
                } catch (\Exception $e) {
                    // Ignore invalid date formats, but don't break stats
                }
            }
        }

        $completed = $approved + $archived;

        return [
            'projects_total'             => $total,
            'projects_draft'             => $draft,
            'projects_ongoing'           => $ongoing,
            'projects_pending_approval'  => $pendingApproval,
            'projects_approved'          => $approved,
            'projects_archived'          => $archived,
            'projects_completed'         => $completed,
            'projects_overdue'           => $overdue,
            'projects_next_deadline'     => $nextDeadline ? $nextDeadline->format('Y-m-d') : null,
        ];
    }
}
