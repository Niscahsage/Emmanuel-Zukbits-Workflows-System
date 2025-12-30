<?php
// app/controllers/DocumentationController.php
// DocumentationController manages project documentation including technical notes and knowledge base entries.
// app/controllers/DocumentationController.php

namespace App\controllers;

use App\core\Auth;
use App\core\Controller;
use App\core\Helpers;
use App\core\Middleware;
use App\services\DocumentationService;

class DocumentationController extends Controller
{
    private DocumentationService $docs;

    public function __construct()
    {
        $this->docs = new DocumentationService();
    }

    // List documentation for a project or all (by role)
    public function index(): void
    {
        Middleware::requireAuth();

        $user      = Auth::user();
        $projectId = isset($_GET['project_id']) ? (int) $_GET['project_id'] : 0;
        $docs      = $this->docs->listForUser($user, $projectId > 0 ? $projectId : null);

        $this->view('documentation/index', [
            'documents'  => $docs,
            'project_id' => $projectId,
        ]);
    }

    // Show a single documentation entry
    public function show(): void
    {
        Middleware::requireAuth();

        $id = (int) ($_GET['id'] ?? 0);
        if ($id <= 0) {
            Helpers::flash('Invalid document id.');
            $this->redirect('/documentation');
        }

        $user = Auth::user();
        $doc  = $this->docs->getForUser($user, $id);

        if (!$doc) {
            Helpers::flash('Document not found or not accessible.');
            $this->redirect('/documentation');
        }

        $this->view('documentation/show', [
            'document' => $doc,
        ]);
    }

    // Show form to create documentation
    public function create(): void
    {
        Middleware::requireAuth();

        $projectId = isset($_GET['project_id']) ? (int) $_GET['project_id'] : 0;

        $this->view('documentation/create', [
            'project_id' => $projectId,
        ]);
    }

    // Store new documentation entry
    public function store(): void
    {
        Middleware::requireAuth();

        $title      = trim($_POST['title'] ?? '');
        $projectId  = (int) ($_POST['project_id'] ?? 0);
        $type       = trim($_POST['type'] ?? '');
        $body       = trim($_POST['body'] ?? '');
        $tags       = trim($_POST['tags'] ?? '');

        if ($title === '') {
            Helpers::flash('Title is required.');
            $this->redirect('/documentation/create');
        }

        $user = Auth::user();

        $data = [
            'title'      => $title,
            'project_id' => $projectId > 0 ? $projectId : null,
            'type'       => $type !== '' ? $type : null,
            'body'       => $body !== '' ? $body : null,
            'tags'       => $tags !== '' ? $tags : null,
        ];

        try {
            $this->docs->create($data, (int) $user['id']);
            Helpers::flash('Document created successfully.');
        } catch (\Throwable $e) {
            Helpers::flash('Error creating document: ' . $e->getMessage());
        }

        $redirect = $projectId > 0 ? '/documentation?project_id=' . $projectId : '/documentation';
        $this->redirect($redirect);
    }

    // Show edit form
    public function edit(): void
    {
        Middleware::requireAuth();

        $id = (int) ($_GET['id'] ?? 0);
        if ($id <= 0) {
            Helpers::flash('Invalid document id.');
            $this->redirect('/documentation');
        }

        $user = Auth::user();
        $doc  = $this->docs->getForUser($user, $id);

        if (!$doc) {
            Helpers::flash('Document not found or not accessible.');
            $this->redirect('/documentation');
        }

        $this->view('documentation/edit', [
            'document' => $doc,
        ]);
    }

    // Update existing documentation
    public function update(): void
    {
        Middleware::requireAuth();

        $id        = (int) ($_POST['id'] ?? 0);
        $title     = trim($_POST['title'] ?? '');
        $type      = trim($_POST['type'] ?? '');
        $body      = trim($_POST['body'] ?? '');
        $tags      = trim($_POST['tags'] ?? '');
        $projectId = (int) ($_POST['project_id'] ?? 0);

        if ($id <= 0 || $title === '') {
            Helpers::flash('Title is required.');
            $this->redirect('/documentation');
        }

        $data = [
            'title'      => $title,
            'type'       => $type !== '' ? $type : null,
            'body'       => $body !== '' ? $body : null,
            'tags'       => $tags !== '' ? $tags : null,
            'project_id' => $projectId > 0 ? $projectId : null,
        ];

        try {
            $this->docs->update($id, $data);
            Helpers::flash('Document updated successfully.');
        } catch (\Throwable $e) {
            Helpers::flash('Error updating document: ' . $e->getMessage());
        }

        $redirect = $projectId > 0 ? '/documentation?project_id=' . $projectId : '/documentation';
        $this->redirect($redirect);
    }
}

