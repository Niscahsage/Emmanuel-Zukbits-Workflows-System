<?php
// app/controllers/CredentialController.php
// CredentialController manages secure access to encrypted credentials and integration keys associated with projects.

namespace App\controllers;

use App\core\Auth;
use App\core\Controller;
use App\core\Helpers;
use App\core\Middleware;
use App\services\CredentialService;

class CredentialController extends Controller
{
    private CredentialService $service;

    public function __construct()
    {
        $this->service = new CredentialService();
    }

    // List credentials for accessible projects
    public function index(): void
    {
        Middleware::requireAuth();

        $user        = Auth::user();
        $credentials = $this->service->listForUser($user);

        $this->view('credentials/index', [
            'credentials' => $credentials,
        ]);
    }

    // Show create form
    public function create(): void
    {
        Middleware::requireAuth();

        $this->view('credentials/create', [
            'projects' => [], // you can later populate via a project service if needed
        ]);
    }

    // Store new credential (encrypted)
    public function store(): void
    {
        Middleware::requireAuth();

        $projectId   = (int) ($_POST['project_id'] ?? 0);
        $label       = trim($_POST['label'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $value       = trim($_POST['value'] ?? '');
        $allowed     = trim($_POST['allowed_roles'] ?? '');

        if ($projectId <= 0 || $label === '' || $value === '') {
            Helpers::flash('Project, label and value are required.');
            $this->redirect('/credentials/create');
        }

        $user = Auth::user();

        $data = [
            'project_id'    => $projectId,
            'label'         => $label,
            'description'   => $description !== '' ? $description : null,
            'value'         => $value,
            'allowed_roles' => $allowed !== '' ? $allowed : null,
        ];

        try {
            $this->service->create($data, (int) $user['id']);
            Helpers::flash('Credential stored successfully.');
        } catch (\Throwable $e) {
            Helpers::flash('Error storing credential: ' . $e->getMessage());
        }

        $this->redirect('/credentials');
    }

    // Show credential details (without auto-revealing secret)
    public function show(): void
    {
        Middleware::requireAuth();

        $id = (int) ($_GET['id'] ?? 0);
        if ($id <= 0) {
            Helpers::flash('Invalid credential id.');
            $this->redirect('/credentials');
        }

        $user = Auth::user();
        $cred = $this->service->getForUser($user, $id);

        if (!$cred) {
            Helpers::flash('Credential not found or not accessible.');
            $this->redirect('/credentials');
        }

        $this->view('credentials/show', [
            'credential' => $cred,
        ]);
    }

    // Reveal decrypted credential value
    public function reveal(): void
    {
        Middleware::requireAuth();

        $id = (int) ($_GET['id'] ?? 0);
        if ($id <= 0) {
            http_response_code(400);
            echo 'Invalid id';
            return;
        }

        $user  = Auth::user();
        $value = $this->service->revealValueForUser($user, $id);

        if ($value === null) {
            http_response_code(403);
            echo 'Forbidden';
            return;
        }

        header('Content-Type: text/plain; charset=utf-8');
        echo $value;
    }

    // Show edit form for existing credential
    public function edit(): void
    {
        Middleware::requireAuth();

        $id = (int) ($_GET['id'] ?? 0);
        if ($id <= 0) {
            Helpers::flash('Invalid credential id.');
            $this->redirect('/credentials');
        }

        $user = Auth::user();
        $cred = $this->service->getForUser($user, $id);

        if (!$cred) {
            Helpers::flash('Credential not found or not accessible.');
            $this->redirect('/credentials');
        }

        $this->view('credentials/edit', [
            'credential' => $cred,
        ]);
    }

    // Update existing credential (metadata + value)
    public function update(): void
    {
        Middleware::requireAuth();

        $id          = (int) ($_POST['id'] ?? 0);
        $projectId   = (int) ($_POST['project_id'] ?? 0);
        $label       = trim($_POST['label'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $value       = trim($_POST['value'] ?? '');
        $allowed     = trim($_POST['allowed_roles'] ?? '');

        if ($id <= 0 || $projectId <= 0 || $label === '') {
            Helpers::flash('Invalid credential data.');
            $this->redirect('/credentials');
        }

        $user = Auth::user();

        $data = [
            'project_id'    => $projectId,
            'label'         => $label,
            'description'   => $description !== '' ? $description : null,
            'value'         => $value !== '' ? $value : null,
            'allowed_roles' => $allowed !== '' ? $allowed : null,
        ];

        try {
            if (method_exists($this->service, 'updateForUser')) {
                $this->service->updateForUser($user, $id, $data);
                Helpers::flash('Credential updated successfully.');
            } else {
                // Fallback: no update method implemented yet on service
                Helpers::flash('Credential update is not supported yet.');
            }
        } catch (\Throwable $e) {
            Helpers::flash('Error updating credential: ' . $e->getMessage());
        }

        $this->redirect('/credentials');
    }
}
