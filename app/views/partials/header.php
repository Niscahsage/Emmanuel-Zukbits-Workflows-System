<?php
// app/views/partials/header.php
// Contains page title, breadcrumbs, and high-level context for a screen.

use App\core\View;

/** @var ?string $pageTitle */
/** @var ?string $subtitle */
/** @var ?array  $breadcrumbs */
/** @var ?array  $actions */
?>
<div class="zb-page-header mb-3">
    <?php if (!empty($breadcrumbs)): ?>
        <?php require __DIR__ . '/breadcrumbs.php'; ?>
    <?php endif; ?>

    <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3">
        <div class="min-w-0">
            <?php if (!empty($pageTitle)): ?>
                <h1 class="h4 mb-1 text-truncate"><?= View::e($pageTitle) ?></h1>
            <?php endif; ?>
            <?php if (!empty($subtitle)): ?>
                <div class="text-muted small"><?= View::e($subtitle) ?></div>
            <?php endif; ?>
        </div>

        <?php if (!empty($actions) && is_array($actions)): ?>
            <div class="d-flex flex-wrap gap-2">
                <?php foreach ($actions as $a): ?>
                    <?php
                    $label = $a['label'] ?? '';
                    $url   = $a['url'] ?? '#';
                    $type  = $a['type'] ?? 'secondary'; // primary|secondary|danger
                    $icon  = $a['icon'] ?? null;
                    $id    = $a['id'] ?? null;
                    if ($label === '') continue;

                    $btn = 'btn-outline-secondary';
                    if ($type === 'primary') $btn = 'btn-primary';
                    if ($type === 'danger')  $btn = 'btn-outline-danger';
                    ?>
                    <a href="<?= View::e($url) ?>"
                       class="btn <?= View::e($btn) ?> btn-sm"
                       <?= $id ? 'id="' . View::e($id) . '"' : '' ?>>
                        <?php if ($icon): ?><i class="bi <?= View::e($icon) ?> me-1"></i><?php endif; ?>
                        <?= View::e($label) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
    .zb-page-header {
        padding: 1rem;
        border: 1px solid rgba(255,255,255,.08);
        border-radius: .75rem;
        background: var(--zb-surface, #0b1020);
    }
    body[data-theme="light"] .zb-page-header {
        border-color: rgba(0,0,0,.08);
        background: #ffffff;
    }
</style>
