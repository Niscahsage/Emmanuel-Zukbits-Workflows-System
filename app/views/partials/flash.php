<?php
// app/views/partials/flash.php
// Displays flash messages stored in session.

use App\core\Helpers;

/** @var ?string $flashType */ // optional: success|warning|danger|info
$flash = Helpers::getFlash();
if (!$flash) {
    return;
}

$type = $flashType ?? 'info';
$bsType = in_array($type, ['success','warning','danger','info'], true) ? $type : 'info';
?>
<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1080;">
    <div id="zbToast" class="toast show border-0 shadow" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
            <span class="badge rounded-pill me-2 bg-<?= htmlspecialchars($bsType, ENT_QUOTES, 'UTF-8') ?>">&nbsp;</span>
            <strong class="me-auto">Notice</strong>
            <small class="text-muted">now</small>
            <button type="button" class="btn-close ms-2" data-zb-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body">
            <?= htmlspecialchars($flash, ENT_QUOTES, 'UTF-8') ?>
        </div>
    </div>
</div>

<style>
    /* Keeps toast readable in dark layouts */
    body[data-theme="dark"] .toast { background: #0b1020; color: #f7f7ff; }
    body[data-theme="dark"] .toast-header { background: #0b1020; color: #f7f7ff; border-bottom: 1px solid rgba(255,255,255,.08); }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const toast = document.getElementById('zbToast');
        if (!toast) return;

        // Auto hide after 5s
        const t = setTimeout(() => { toast.remove(); }, 5000);

        // Close button
        toast.querySelectorAll('[data-zb-dismiss="toast"]').forEach(btn => {
            btn.addEventListener('click', function () {
                clearTimeout(t);
                toast.remove();
            });
        });

        // Click anywhere to dismiss (optional, clean)
        toast.addEventListener('click', function (e) {
            if (e.target.closest('[data-zb-dismiss="toast"]')) return;
            clearTimeout(t);
            toast.remove();
        });
    });
</script>
