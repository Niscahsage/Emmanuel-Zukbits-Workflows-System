<?php
// app/controllers/ApprovalController.php
// ApprovalController manages approval workflows for project completion and other approval-requiring actions.

namespace App\controllers;

use App\core\Auth;
use App\core\Controller;
use App\core\Helpers;
use App\core\Middleware;
use App\services\ApprovalService;

class ApprovalController extends Controller
{
    private ApprovalService $service;

    public function __construct()
    {
        $this->service = new ApprovalService();
    }

    // List approvals visible to the user
    public function index(): void
    {
        Middleware::requireAuth();

        $user      = Auth::user();
        $approvals = $this->service->listForUser($user);

        $this->view('approvals/index', [
            'approvals' => $approvals,
        ]);
    }

    // Pending approvals inbox (for decision makers)
    public function inbox(): void
    {
        Middleware::requireAuth();

        $approvals = $this->service->pendingInbox();

        $this->view('approvals/inbox', [
            'approvals' => $approvals,
        ]);
    }

    // Show a specific approval request and its decisions
    public function show(): void
    {
        Middleware::requireAuth();

        $id = (int) ($_GET['id'] ?? 0);
        if ($id <= 0) {
            Helpers::flash('Invalid approval id.');
            $this->redirect('/approvals');
        }

        $bundle = $this->service->getApproval($id);
        if (!$bundle) {
            Helpers::flash('Approval request not found.');
            $this->redirect('/approvals');
        }

        $this->view('approvals/show', [
            'approval'  => $bundle['approval'],
            'decisions' => $bundle['decisions'],
        ]);
    }

    // Request approval for a project completion
    public function requestProjectApproval(): void
    {
        Middleware::requireAuth();

        $projectId = (int) ($_POST['project_id'] ?? 0);
        if ($projectId <= 0) {
            Helpers::flash('Invalid project id.');
            $this->redirect('/projects');
        }

        $user = Auth::user();

        $id = $this->service->requestProjectCompletion($user, $projectId);
        if ($id === null) {
            Helpers::flash('There is already a pending approval request for this project.');
        } else {
            Helpers::flash('Approval request submitted.');
        }

        $this->redirect('/projects/show?id=' . $projectId);
    }

    // Approver decision for an approval request
    public function decide(): void
    {
        Middleware::requireAuth();

        $id       = (int) ($_POST['approval_id'] ?? 0);
        $decision = trim($_POST['decision'] ?? '');
        $comment  = trim($_POST['comment'] ?? '');

        if ($id <= 0 || !in_array($decision, ['approved', 'rejected'], true)) {
            Helpers::flash('Invalid approval decision.');
            $this->redirect('/approvals');
        }

        $user = Auth::user();

        $ok = $this->service->decide(
            $id,
            (int) $user['id'],
            $decision,
            $comment !== '' ? $comment : null
        );

        if ($ok) {
            Helpers::flash('Decision recorded.');
        } else {
            Helpers::flash('Error saving decision.');
        }

        $this->redirect('/approvals/show?id=' . $id);
    }

    // POST /approvals/act
    // Thin alias for decide(), wired from routes.php
    public function act(): void
    {
        // Reuse the existing decision logic
        $this->decide();
    }
}
