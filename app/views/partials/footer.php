<?php
// app/views/partials/footer.php
// Shared footer section for authenticated pages.

$app = app_config();
$appName = $app['name'] ?? 'App';

$currentYear = date('Y');

/** @var ?array $footerLinks */ // optional: [['label'=>'Privacy','url'=>'/privacy'], ...]
$footerLinks = $footerLinks ?? [];
?>
<footer class="zb-footer w-100 border-top">
    <div class="container-fluid py-3 px-3 px-md-4">
        <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-2">
            <div class="small text-muted">
                &copy; <?= $currentYear ?> <?= htmlspecialchars($appName, ENT_QUOTES, 'UTF-8') ?>.
            </div>

            <?php if (!empty($footerLinks) && is_array($footerLinks)): ?>
                <div class="d-flex flex-wrap gap-3 small">
                    <?php foreach ($footerLinks as $l): ?>
                        <?php
                        $label = $l['label'] ?? '';
                        $url   = $l['url'] ?? '#';
                        if ($label === '') continue;
                        ?>
                        <a class="text-decoration-none" href="<?= htmlspecialchars($url, ENT_QUOTES, 'UTF-8') ?>">
                            <?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</footer>

<style>
    .zb-footer { background: var(--zb-surface, #0b1020); }
    body[data-theme="light"] .zb-footer { background: #ffffff; }
</style>
