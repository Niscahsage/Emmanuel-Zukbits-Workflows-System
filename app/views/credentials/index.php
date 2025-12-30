<?php
// app/views/credentials/index.php
use App\core\View;

/** @var array $credentials */

$credentials = is_array($credentials) ? $credentials : [];
$total = count($credentials);

$uniqueProjects = [];
$uniqueCreators = [];
foreach ($credentials as $c) {
    $p = (string)($c['project_name'] ?? '');
    $u = (string)($c['created_by'] ?? '');
    if ($p !== '') $uniqueProjects[$p] = true;
    if ($u !== '') $uniqueCreators[$u] = true;
}
?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
    <div>
        <h1 class="h4 mb-1 fw-bold">
            <i class="bi bi-key me-2 text-muted"></i>Credentials
        </h1>
        <div class="text-muted">Manage and access project credentials securely</div>
    </div>

    <div class="d-flex gap-2">
        <a href="/credentials/create" class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i>Add Credential
        </a>
    </div>
</div>

<?php if ($total > 0): ?>
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card h-100" style="background:var(--color-surface);border:1px solid var(--color-border);">
                <div class="card-body">
                    <div class="text-muted small fw-semibold text-uppercase">Total</div>
                    <div class="fs-2 fw-bold"><?= View::e((string)$total) ?></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100" style="background:var(--color-surface);border:1px solid var(--color-border);">
                <div class="card-body">
                    <div class="text-muted small fw-semibold text-uppercase">Projects</div>
                    <div class="fs-2 fw-bold"><?= View::e((string)count($uniqueProjects)) ?></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100" style="background:var(--color-surface);border:1px solid var(--color-border);">
                <div class="card-body">
                    <div class="text-muted small fw-semibold text-uppercase">Creators</div>
                    <div class="fs-2 fw-bold"><?= View::e((string)count($uniqueCreators)) ?></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100" style="background:var(--color-surface);border:1px solid var(--color-border);">
                <div class="card-body">
                    <div class="text-muted small fw-semibold text-uppercase">Encrypted</div>
                    <div class="fs-2 fw-bold">100%</div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php if ($total === 0): ?>
    <div class="alert alert-info d-flex align-items-center">
        <i class="bi bi-info-circle-fill me-3" style="font-size:1.25rem;"></i>
        <div>
            <div class="fw-semibold mb-1">No credentials available</div>
            <div class="mb-0">You don’t have access to any credentials yet.</div>
        </div>
    </div>
<?php else: ?>
    <div class="card" style="background:var(--color-surface);border:1px solid var(--color-border);">
        <div class="table-responsive">
            <table class="table table-hover mb-0" style="color:inherit;">
                <thead style="background:var(--color-surface-alt);">
                <tr>
                    <th class="ps-4">ID</th>
                    <th>Label</th>
                    <th>Project</th>
                    <th>Created By</th>
                    <th>Created At</th>
                    <th class="text-end pe-4">Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($credentials as $cred): ?>
                    <?php
                    $id        = (int)($cred['id'] ?? 0);
                    $label     = (string)($cred['label'] ?? '');
                    $project   = (string)($cred['project_name'] ?? '');
                    $createdBy = (string)($cred['created_by'] ?? '');
                    $createdAt = (string)($cred['created_at'] ?? '');
                    ?>
                    <tr>
                        <td class="ps-4 fw-semibold">#<?= View::e((string)$id) ?></td>
                        <td><i class="bi bi-key me-2 text-muted"></i><?= View::e($label) ?></td>
                        <td>
                            <span class="badge" style="background:rgba(56,189,248,.10);border:1px solid rgba(56,189,248,.20);color:var(--color-accent-blue);">
                                <?= View::e($project) ?>
                            </span>
                        </td>
                        <td><i class="bi bi-person-circle me-2 text-muted"></i><?= View::e($createdBy) ?></td>
                        <td><i class="bi bi-calendar3 me-2 text-muted"></i><small><?= View::e($createdAt) ?></small></td>
                        <td class="text-end pe-4">
                            <a class="btn btn-outline-secondary btn-sm" href="/credentials/show?id=<?= View::e((string)$id) ?>">
                                <i class="bi bi-eye me-1"></i>View
                            </a>
                            <a class="btn btn-outline-secondary btn-sm" href="/credentials/edit?id=<?= View::e((string)$id) ?>">
                                <i class="bi bi-pencil me-1"></i>Edit
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>
