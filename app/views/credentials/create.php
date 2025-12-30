<?php
// app/views/credentials/create.php
use App\core\View;

/** @var int|null $project_id */
$projectPreset = isset($project_id) ? (int)$project_id : (int)($_GET['project_id'] ?? 0);
?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
    <div>
        <h1 class="h4 mb-1 fw-bold"><i class="bi bi-plus-circle me-2 text-muted"></i>Add Credential</h1>
        <div class="text-muted">Create a new encrypted credential for a project</div>
    </div>
    <a href="/credentials" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-2"></i>Back
    </a>
</div>

<div class="card" style="background:var(--color-surface);border:1px solid var(--color-border);">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2"
         style="background:var(--color-surface-alt);border-bottom:1px solid var(--color-border);">
        <div class="fw-semibold"><i class="bi bi-shield-lock me-2 text-info"></i>Credential Details</div>
        <small class="text-muted">Values are encrypted at rest</small>
    </div>

    <div class="card-body">
        <form action="/credentials/store" method="post" id="credentialForm" class="needs-validation" novalidate>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold" for="project_id">Project ID <span class="text-danger">*</span></label>
                    <input
                        type="number"
                        class="form-control"
                        name="project_id"
                        id="project_id"
                        required
                        min="1"
                        value="<?= $projectPreset > 0 ? View::e((string)$projectPreset) : '' ?>"
                        placeholder="e.g. 12"
                    >
                    <div class="invalid-feedback">Enter a valid Project ID.</div>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold" for="label">Label <span class="text-danger">*</span></label>
                    <input
                        type="text"
                        class="form-control"
                        name="label"
                        id="label"
                        required
                        maxlength="100"
                        placeholder="e.g. Database Password, API Key"
                    >
                    <div class="invalid-feedback">Label is required.</div>
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold" for="description">Description</label>
                    <textarea
                        class="form-control"
                        name="description"
                        id="description"
                        rows="3"
                        maxlength="500"
                        placeholder="Optional notes (max 500 characters)"
                    ></textarea>
                    <div class="form-text">Optional — helps your team know what this secret is for.</div>
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold" for="value">Secret Value <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input
                            type="password"
                            class="form-control"
                            name="value"
                            id="value"
                            required
                            placeholder="Enter secret"
                            autocomplete="new-password"
                        >
                        <button class="btn btn-outline-secondary" type="button" id="toggleSecret" aria-label="Toggle secret">
                            <i class="bi bi-eye" id="toggleIcon"></i>
                        </button>
                        <div class="invalid-feedback">Secret value is required.</div>
                    </div>

                    <div class="mt-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <small class="text-muted">Strength</small>
                            <small class="text-muted" id="strengthText">—</small>
                        </div>
                        <div class="progress" style="height:6px;background:var(--color-border);">
                            <div class="progress-bar" id="strengthBar" style="width:0%;"></div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold" for="allowed_roles">Allowed Roles</label>
                    <input
                        type="text"
                        class="form-control"
                        name="allowed_roles"
                        id="allowed_roles"
                        placeholder="developer,system_admin,director"
                    >
                    <div class="form-text">Comma-separated role keys. Leave empty for project-assignees only.</div>
                </div>

                <div class="col-12">
                    <div class="rounded-3 p-3"
                         style="background:rgba(56,189,248,.08);border:1px solid rgba(56,189,248,.18);border-left:4px solid var(--color-accent-blue);">
                        <div class="fw-semibold text-info mb-1"><i class="bi bi-shield-check me-2"></i>Best practice</div>
                        <div class="text-muted">Use unique secrets, rotate regularly, and restrict roles to only what’s necessary.</div>
                    </div>
                </div>

                <div class="col-12 d-flex justify-content-between align-items-center flex-wrap gap-2 mt-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="confirmEncryption" required>
                        <label class="form-check-label" for="confirmEncryption">I understand this value will be encrypted</label>
                        <div class="invalid-feedback">You must confirm.</div>
                    </div>

                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <i class="bi bi-save me-2"></i>Save Credential
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('credentialForm');
    const secret = document.getElementById('value');
    const toggle = document.getElementById('toggleSecret');
    const icon = document.getElementById('toggleIcon');
    const bar = document.getElementById('strengthBar');
    const text = document.getElementById('strengthText');

    toggle.addEventListener('click', () => {
        const isPwd = secret.type === 'password';
        secret.type = isPwd ? 'text' : 'password';
        icon.className = isPwd ? 'bi bi-eye-slash' : 'bi bi-eye';
    });

    function strength(p) {
        let s = 0;
        if (p.length >= 8) s += 25;
        if (p.length >= 12) s += 15;
        if (/[A-Z]/.test(p)) s += 15;
        if (/[a-z]/.test(p)) s += 15;
        if (/\d/.test(p)) s += 15;
        if (/[^A-Za-z0-9]/.test(p)) s += 15;
        return Math.min(100, s);
    }

    function setStrength(p) {
        const s = strength(p);
        bar.style.width = s + '%';

        let label = 'Weak';
        let bg = 'var(--color-accent-red)';
        if (s >= 80) { label = 'Very strong'; bg = 'var(--color-accent-green)'; }
        else if (s >= 60) { label = 'Strong'; bg = 'var(--color-accent-green)'; }
        else if (s >= 40) { label = 'Good'; bg = 'var(--color-accent-strong)'; }
        else if (s >= 20) { label = 'Weak'; bg = 'var(--color-accent-strong)'; }

        bar.style.background = bg;
        text.textContent = p ? label : '—';
    }

    secret.addEventListener('input', () => setStrength(secret.value));
    setStrength(secret.value);

    form.addEventListener('submit', (e) => {
        if (!form.checkValidity()) {
            e.preventDefault();
            e.stopPropagation();
        }
        form.classList.add('was-validated');
    });
});
</script>
