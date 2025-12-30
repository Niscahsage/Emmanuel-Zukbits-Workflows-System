<?php
// app/views/credentials/show.php
use App\core\View;

/** @var array $credential */

$id          = (int)($credential['id'] ?? 0);
$label       = (string)($credential['label'] ?? '');
$project     = (string)($credential['project_name'] ?? '');
$description = (string)($credential['description'] ?? '');
$allowed     = (string)($credential['allowed_roles'] ?? '');
$createdBy   = (string)($credential['created_by'] ?? '');
$createdAt   = (string)($credential['created_at'] ?? '');
?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
    <div>
        <h1 class="h4 mb-1 fw-bold">
            <i class="bi bi-key me-2 text-muted"></i><?= View::e($label) ?>
        </h1>
        <div class="text-muted">Credential details and secret access</div>
    </div>

    <div class="d-flex gap-2">
        <a href="/credentials" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Back
        </a>
        <a href="/credentials/edit?id=<?= View::e((string)$id) ?>" class="btn btn-primary">
            <i class="bi bi-pencil me-2"></i>Edit
        </a>
    </div>
</div>

<div class="card mb-4" style="background:var(--color-surface);border:1px solid var(--color-border);">
    <div class="card-header" style="background:var(--color-surface-alt);border-bottom:1px solid var(--color-border);">
        <div class="fw-semibold"><i class="bi bi-info-circle me-2 text-muted"></i>Details</div>
    </div>
    <div class="card-body">
        <div class="row g-4">
            <div class="col-md-6">
                <div class="text-muted small fw-semibold text-uppercase">Project</div>
                <div><?= View::e($project) ?></div>
            </div>

            <div class="col-md-6">
                <div class="text-muted small fw-semibold text-uppercase">Allowed Roles</div>
                <div>
                    <?php if (trim($allowed) !== ''): ?>
                        <?php foreach (explode(',', $allowed) as $r): ?>
                            <span class="badge me-1" style="background:rgba(56,189,248,.10);border:1px solid rgba(56,189,248,.20);color:var(--color-accent-blue);">
                                <?= View::e(trim($r)) ?>
                            </span>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <span class="text-muted">Project assignees only</span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="col-12">
                <div class="text-muted small fw-semibold text-uppercase">Description</div>
                <div class="<?= $description === '' ? 'text-muted' : '' ?>">
                    <?= $description !== '' ? nl2br(View::e($description)) : 'No description provided.' ?>
                </div>
            </div>

            <div class="col-md-6">
                <div class="text-muted small fw-semibold text-uppercase">Created By</div>
                <div><?= View::e($createdBy) ?></div>
            </div>

            <div class="col-md-6">
                <div class="text-muted small fw-semibold text-uppercase">Created At</div>
                <div><small><?= View::e($createdAt) ?></small></div>
            </div>
        </div>
    </div>
</div>

<div class="card" style="background:var(--color-surface);border:1px solid var(--color-border);">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2"
         style="background:var(--color-surface-alt);border-bottom:1px solid var(--color-border);">
        <div class="fw-semibold">
            <i class="bi bi-shield-lock me-2 text-info"></i>Secret Value
        </div>
        <small class="text-muted">Auto-hides after 60 seconds</small>
    </div>

    <div class="card-body">
        <p class="text-muted mb-3">Reveal only if authorized. Backend should enforce access + log the action.</p>

        <button type="button" class="btn btn-primary" id="revealBtn">
            <i class="bi bi-eye me-2"></i>Reveal Secret
        </button>

        <div class="mt-3 text-muted" id="revealHint">
            <i class="bi bi-lock me-1"></i>Secret is hidden.
        </div>

        <pre class="mt-3 p-3 rounded-3 d-none" id="secretBox"
             style="background:var(--color-bg);border:1px solid var(--color-border);white-space:pre-wrap;word-break:break-all;"></pre>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const btn = document.getElementById('revealBtn');
    const box = document.getElementById('secretBox');
    const hint = document.getElementById('revealHint');
    let hideTimer = null;

    async function reveal() {
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Revealing...';

        try {
            const res = await fetch('/credentials/reveal?id=<?= View::e((string)$id) ?>', { credentials: 'same-origin' });

            hint.classList.add('d-none');
            box.classList.remove('d-none');

            if (!res.ok) {
                box.textContent = 'Not authorized or unable to reveal.';
            } else {
                const text = (await res.text()).trim();
                box.textContent = text !== '' ? text : '(empty secret)';

                if (hideTimer) clearTimeout(hideTimer);
                hideTimer = setTimeout(() => {
                    box.textContent = '';
                    box.classList.add('d-none');
                    hint.classList.remove('d-none');
                    btn.disabled = false;
                    btn.innerHTML = '<i class="bi bi-eye me-2"></i>Reveal Secret';
                }, 60000);

                return;
            }
        } catch (e) {
            hint.classList.add('d-none');
            box.classList.remove('d-none');
            box.textContent = 'Error fetching secret.';
        }

        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-eye me-2"></i>Reveal Secret';
    }

    btn.addEventListener('click', reveal);
});
</script>
