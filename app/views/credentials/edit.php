<?php
// app/views/credentials/edit.php
use App\core\View;

/** @var array $credential */

$id          = (int)($credential['id'] ?? 0);
$projectId   = (int)($credential['project_id'] ?? 0);
$label       = (string)($credential['label'] ?? '');
$description = (string)($credential['description'] ?? '');
$allowed     = (string)($credential['allowed_roles'] ?? '');
?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
    <div>
        <h1 class="h4 mb-1 fw-bold"><i class="bi bi-pencil-square me-2 text-muted"></i>Edit Credential</h1>
        <div class="text-muted">Update label, description, and access roles</div>
    </div>

    <a href="/credentials/show?id=<?= View::e((string)$id) ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-2"></i>Back
    </a>
</div>

<div class="card" style="background:var(--color-surface);border:1px solid var(--color-border);">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2"
         style="background:var(--color-surface-alt);border-bottom:1px solid var(--color-border);">
        <div class="fw-semibold"><i class="bi bi-hash me-2 text-muted"></i>Credential #<?= View::e((string)$id) ?></div>
        <small class="text-muted">Secret value cannot be edited</small>
    </div>

    <div class="card-body">
        <form action="/credentials/update" method="post" id="editForm" class="needs-validation" novalidate>
            <input type="hidden" name="id" value="<?= View::e((string)$id) ?>">

            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold" for="project_id">Project ID</label>
                    <input class="form-control" id="project_id" value="<?= View::e((string)$projectId) ?>" readonly
                           style="background:var(--color-surface);border-color:var(--color-border);color:var(--color-text-muted);">
                </div>

                <div class="col-md-8">
                    <label class="form-label fw-semibold" for="label">Label <span class="text-danger">*</span></label>
                    <input
                        type="text"
                        class="form-control"
                        name="label"
                        id="label"
                        value="<?= View::e($label) ?>"
                        required
                        maxlength="100"
                    >
                    <div class="invalid-feedback">Label is required.</div>
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold" for="description">Description</label>
                    <textarea class="form-control" name="description" id="description" rows="3" maxlength="500"><?= View::e($description) ?></textarea>
                    <div class="form-text">Optional — max 500 characters.</div>
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold" for="allowed_roles">Allowed Roles</label>
                    <input
                        type="text"
                        class="form-control"
                        name="allowed_roles"
                        id="allowed_roles"
                        value="<?= View::e($allowed) ?>"
                        placeholder="developer,system_admin,director"
                    >
                    <div class="invalid-feedback">Use only letters/numbers/underscore/hyphen in role keys.</div>
                    <div class="form-text">Comma-separated role keys. Leave empty for project-assignees only.</div>
                </div>

                <div class="col-12">
                    <div class="rounded-3 p-3"
                         style="background:rgba(251,191,36,.08);border:1px solid rgba(251,191,36,.20);border-left:4px solid var(--color-accent-strong);">
                        <div class="fw-semibold text-warning mb-1"><i class="bi bi-exclamation-triangle me-2"></i>Security</div>
                        <div class="text-muted">To change the secret value, delete this credential and create a new one.</div>
                    </div>
                </div>

                <div class="col-12 d-flex justify-content-between align-items-center flex-wrap gap-2 mt-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="confirmChanges" required>
                        <label class="form-check-label" for="confirmChanges">I confirm these changes are correct</label>
                        <div class="invalid-feedback">You must confirm.</div>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-2"></i>Save Changes
                    </button>
                </div>
            </div>
        </form>

        <hr class="my-4" style="border-color:var(--color-border);">

        <form action="/credentials/delete" method="post"
              onsubmit="return confirm('Delete this credential? This cannot be undone.');">
            <input type="hidden" name="id" value="<?= View::e((string)$id) ?>">
            <button type="submit" class="btn btn-outline-danger">
                <i class="bi bi-trash me-2"></i>Delete Credential
            </button>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('editForm');
    const roles = document.getElementById('allowed_roles');

    function validateRoles() {
        const v = roles.value.trim();
        if (!v) { roles.classList.remove('is-invalid'); return true; }
        const bad = v.split(',').map(s => s.trim()).filter(s => s && !/^[a-zA-Z0-9_-]+$/.test(s));
        roles.classList.toggle('is-invalid', bad.length > 0);
        return bad.length === 0;
    }

    roles.addEventListener('input', validateRoles);

    form.addEventListener('submit', (e) => {
        const okRoles = validateRoles();
        if (!form.checkValidity() || !okRoles) {
            e.preventDefault();
            e.stopPropagation();
        }
        form.classList.add('was-validated');
    });

    validateRoles();
});
</script>
