<?php
// app/controllers/ProjectController.php

namespace App\controllers;

use App\core\Auth;
use App\core\Controller;
use App\core\Helpers;
use App\core\Middleware;
use App\models\ProjectProgressLog;
use App\services\ProjectService;
use App\services\DocumentationService;

class ProjectController extends Controller
{
    private ProjectService $projects;
    private DocumentationService $docs;

    public function __construct()
    {
        $this->projects = new ProjectService();
        $this->docs     = new DocumentationService();
    }

    // List projects: all for managers, assigned for staff
    public function index(): void
    {
        Middleware::requireAuth();

        $user     = Auth::user();
        $projects = $this->projects->listForUser($user);

        $this->view('projects/index', [
            'user'     => $user,
            'projects' => $projects,
        ]);
    }

    // Show a single project with logs and documents
    public function show(): void
    {
        Middleware::requireAuth();

        $id = (int) ($_GET['id'] ?? 0);
        if ($id <= 0) {
            Helpers::flash('Invalid project id.');
            $this->redirect('/projects');
        }

        $user    = Auth::user();
        $project = $this->projects->getProject($id);

        if (!$project) {
            Helpers::flash('Project not found.');
            $this->redirect('/projects');
        }

        $roleKey = $user['role_key'] ?? 'developer';
        if (!in_array($roleKey, ['super_admin', 'director', 'system_admin'], true)) {
            $assignments = $this->projects->getAssignments($id);
            $allowed     = false;

            foreach ($assignments as $a) {
                if ((int) $a['user_id'] === (int) $user['id']) {
                    $allowed = true;
                    break;
                }
            }

            if (!$allowed) {
                http_response_code(403);
                echo 'Forbidden';
                return;
            }
        }

        $logs      = ProjectProgressLog::forProject($id);
        $documents = $this->docs->listForUser($user, $id);

        $this->view('projects/show', [
            'project'   => $project,
            'logs'      => $logs,
            'documents' => $documents,
        ]);
    }

    // Show create form
    public function create(): void
    {
        Middleware::requirePermission('manage_projects_basic');

        $this->view('projects/create', []);
    }

    // Store new project
    public function store(): void
    {
        Middleware::requirePermission('manage_projects_basic');

        $name        = trim($_POST['name'] ?? '');
        $code        = trim($_POST['code'] ?? '');
        $clientName  = trim($_POST['client_name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $objectives  = trim($_POST['objectives'] ?? '');
        $category    = trim($_POST['category'] ?? '');
        $priority    = trim($_POST['priority'] ?? '');
        $status      = trim($_POST['status'] ?? 'draft');
        $startDate   = trim($_POST['start_date'] ?? '');
        $targetEnd   = trim($_POST['target_end_date'] ?? '');

        if ($name === '') {
            Helpers::flash('Project name is required.');
            $this->redirect('/projects/create');
        }

        $user = Auth::user();

        $data = [
            'name'            => $name,
            'code'            => $code !== '' ? $code : null,
            'client_name'     => $clientName !== '' ? $clientName : null,
            'description'     => $description !== '' ? $description : null,
            'objectives'      => $objectives !== '' ? $objectives : null,
            'category'        => $category !== '' ? $category : null,
            'priority'        => $priority !== '' ? $priority : null,
            'status'          => $status !== '' ? $status : 'draft',
            'start_date'      => $startDate !== '' ? $startDate : null,
            'target_end_date' => $targetEnd !== '' ? $targetEnd : null,
        ];

        try {
            $this->projects->createProject($data, (int) $user['id']);
            Helpers::flash('Project created successfully.');
        } catch (\Throwable $e) {
            Helpers::flash('Error creating project: ' . $e->getMessage());
        }

        $this->redirect('/projects');
    }

    // Show edit form
    public function edit(): void
    {
        Middleware::requirePermission('manage_projects_basic');

        $id = (int) ($_GET['id'] ?? 0);
        if ($id <= 0) {
            Helpers::flash('Invalid project id.');
            $this->redirect('/projects');
        }

        $project = $this->projects->getProject($id);

        if (!$project) {
            Helpers::flash('Project not found.');
            $this->redirect('/projects');
        }

        $this->view('projects/edit', ['project' => $project]);
    }

    // Update project
    public function update(): void
    {
        Middleware::requirePermission('manage_projects_basic');

        $id          = (int) ($_POST['id'] ?? 0);
        $name        = trim($_POST['name'] ?? '');
        $code        = trim($_POST['code'] ?? '');
        $clientName  = trim($_POST['client_name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $objectives  = trim($_POST['objectives'] ?? '');
        $category    = trim($_POST['category'] ?? '');
        $priority    = trim($_POST['priority'] ?? '');
        $status      = trim($_POST['status'] ?? 'draft');
        $startDate   = trim($_POST['start_date'] ?? '');
        $targetEnd   = trim($_POST['target_end_date'] ?? '');

        if ($id <= 0 || $name === '') {
            Helpers::flash('Project name is required.');
            $this->redirect('/projects');
        }

        $data = [
            'name'            => $name,
            'code'            => $code !== '' ? $code : null,
            'client_name'     => $clientName !== '' ? $clientName : null,
            'description'     => $description !== '' ? $description : null,
            'objectives'      => $objectives !== '' ? $objectives : null,
            'category'        => $category !== '' ? $category : null,
            'priority'        => $priority !== '' ? $priority : null,
            'status'          => $status !== '' ? $status : 'draft',
            'start_date'      => $startDate !== '' ? $startDate : null,
            'target_end_date' => $targetEnd !== '' ? $targetEnd : null,
        ];

        try {
            $this->projects->updateProject($id, $data);
            Helpers::flash('Project updated successfully.');
        } catch (\Throwable $e) {
            Helpers::flash('Error updating project: ' . $e->getMessage());
        }

        $this->redirect('/projects');
    }

    // List archived projects (status = 'archived')
    public function archive(): void
    {
        Middleware::requireAuth();

        $user     = Auth::user();
        $projects = $this->projects->listForUser($user);

        $archived = array_filter($projects, function (array $p): bool {
            return strtolower($p['status'] ?? '') === 'archived';
        });

        $this->view('projects/archive', [
            'user'     => $user,
            'projects' => $archived,
        ]);
    }

    // List projects that are pending approval (status = 'pending_approval')
    public function approvals(): void
    {
        Middleware::requireAuth();

        $user     = Auth::user();
        $projects = $this->projects->listForUser($user);

        $pending = array_filter($projects, function (array $p): bool {
            return strtolower($p['status'] ?? '') === 'pending_approval';
        });

        $this->view('projects/approvals', [
            'user'     => $user,
            'projects' => $pending,
        ]);
    }
}
