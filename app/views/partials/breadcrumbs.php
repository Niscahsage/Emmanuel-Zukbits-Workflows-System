<?php
// app/views/partials/breadcrumbs.php
// Renders navigational breadcrumbs for deeper pages.

use App\core\View;

/** @var array $breadcrumbs */
if (empty($breadcrumbs) || !is_array($breadcrumbs)) {
    return;
}
?>
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb mb-0">
        <?php foreach ($breadcrumbs as $i => $b): ?>
            <?php
            $label  = View::e($b['label'] ?? '');
            $url    = $b['url'] ?? null;
            $icon   = $b['icon'] ?? null;
            $isLast = ($i === array_key_last($breadcrumbs));
            ?>
            <?php if ($isLast || empty($url)): ?>
                <li class="breadcrumb-item active" aria-current="page">
                    <?php if ($icon): ?><i class="bi <?= View::e($icon) ?> me-1"></i><?php endif; ?>
                    <?= $label ?>
                </li>
            <?php else: ?>
                <li class="breadcrumb-item">
                    <a class="text-decoration-none" href="<?= View::e($url) ?>">
                        <?php if ($icon): ?><i class="bi <?= View::e($icon) ?> me-1"></i><?php endif; ?>
                        <?= $label ?>
                    </a>
                </li>
            <?php endif; ?>
        <?php endforeach; ?>
    </ol>
</nav>

<style>
    .breadcrumb a:hover { text-decoration: underline; }
</style>
