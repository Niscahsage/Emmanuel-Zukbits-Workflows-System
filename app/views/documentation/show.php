<?php
// show.php displays the full content of a documentation entry.

/** @var array $document */

use App\core\View;

$id        = (int)($document['id'] ?? 0);
$title     = (string)($document['title'] ?? '');
$project   = $document['project_name'] ?? ($document['project_id'] ?? '');
$type      = (string)($document['type'] ?? '');
$tagsCsv   = (string)($document['tags'] ?? '');
$body      = (string)($document['body'] ?? '');
$createdBy = (string)($document['created_by_name'] ?? ($document['created_by'] ?? ''));
$createdAt = (string)($document['created_at'] ?? '');
$updatedAt = (string)($document['updated_at'] ?? '');

$tagArray = $tagsCsv ? array_filter(array_map('trim', explode(',', $tagsCsv))) : [];
?>

<style>
    .zb-card{background:var(--color-surface);border:1px solid var(--color-border);border-radius:16px;}
    .zb-card-head{background:var(--color-surface-alt);border-bottom:1px solid var(--color-border);border-top-left-radius:16px;border-top-right-radius:16px;}
    .zb-chip{background:var(--color-surface-alt);border:1px solid var(--color-border);padding:.35rem .75rem;border-radius:999px;color:var(--color-text-muted);font-size:.85rem;}
    .zb-tag{background:rgba(255,200,87,.10);border:1px solid rgba(255,200,87,.20);color:var(--color-accent);padding:.35rem .75rem;border-radius:999px;font-size:.85rem;}
    .zb-content{line-height:1.8;font-size:1.02rem;}
    .zb-content pre{background:var(--color-bg);border:1px solid var(--color-border);border-radius:12px;padding:1rem;overflow:auto;white-space:pre;}
    .zb-content code{background:var(--color-bg);border:1px solid var(--color-border);padding:.1rem .35rem;border-radius:6px;}
    .zb-toast{position:fixed;bottom:1.25rem;right:1.25rem;z-index:9999;background:var(--color-surface);border:1px solid var(--color-border);border-radius:12px;padding:.75rem 1rem;box-shadow:0 8px 32px rgba(0,0,0,.35);display:flex;gap:.6rem;align-items:center;opacity:0;transform:translateY(20px);transition:.2s;}
    .zb-toast.show{opacity:1;transform:translateY(0);}
</style>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
    <div>
        <h1 class="h4 mb-1 fw-bold"><i class="bi bi-file-text me-2 text-muted"></i><?= View::e($title) ?></h1>
        <div class="text-muted"><span class="zb-chip"><i class="bi bi-hash me-1"></i>#<?= View::e((string)$id) ?></span></div>
    </div>

    <div class="d-flex gap-2 flex-wrap">
        <a href="/documentation" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Back to List
        </a>
        <a href="/documentation/edit?id=<?= View::e((string)$id) ?>" class="btn btn-primary">
            <i class="bi bi-pencil me-2"></i>Edit
        </a>
    </div>
</div>

<div class="card zb-card mb-4">
    <div class="card-header zb-card-head py-3 px-4">
        <div class="fw-semibold"><i class="bi bi-info-circle me-2 text-muted"></i>Metadata</div>
    </div>
    <div class="card-body p-4">
        <div class="row g-3">
            <div class="col-md-6">
                <div class="text-muted small fw-semibold text-uppercase mb-1"><i class="bi bi-folder me-2"></i>Project</div>
                <?php if ($project !== '' && $project !== null): ?>
                    <span class="badge" style="background:rgba(56,189,248,.10);border:1px solid rgba(56,189,248,.20);color:var(--color-accent-blue);">
                        <?= View::e((string)$project) ?>
                    </span>
                <?php else: ?>
                    <span class="text-muted"><i class="bi bi-globe me-1"></i>General Documentation</span>
                <?php endif; ?>
            </div>

            <div class="col-md-6">
                <div class="text-muted small fw-semibold text-uppercase mb-1"><i class="bi bi-tag me-2"></i>Type</div>
                <?php if ($type !== ''): ?>
                    <span class="badge" style="background:rgba(168,85,247,.10);border:1px solid rgba(168,85,247,.20);color:var(--color-accent-purple);">
                        <?= View::e($type) ?>
                    </span>
                <?php else: ?>
                    <span class="text-muted">Not specified</span>
                <?php endif; ?>
            </div>

            <div class="col-12">
                <div class="text-muted small fw-semibold text-uppercase mb-1"><i class="bi bi-tags me-2"></i>Tags</div>
                <?php if (!empty($tagArray)): ?>
                    <div class="d-flex flex-wrap gap-2">
                        <?php foreach ($tagArray as $t): ?>
                            <span class="zb-tag tag-chip" data-tag="<?= htmlspecialchars(strtolower($t), ENT_QUOTES) ?>">
                                <i class="bi bi-tag-fill me-1"></i><?= View::e($t) ?>
                            </span>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <span class="text-muted">No tags</span>
                <?php endif; ?>
            </div>

            <div class="col-md-6">
                <div class="text-muted small fw-semibold text-uppercase mb-1"><i class="bi bi-person-plus me-2"></i>Created By</div>
                <div class="text-muted"><i class="bi bi-person-circle me-2"></i><?= View::e($createdBy) ?><?php if ($createdAt): ?> <small>(<?= View::e($createdAt) ?>)</small><?php endif; ?></div>
            </div>

            <div class="col-md-6">
                <div class="text-muted small fw-semibold text-uppercase mb-1"><i class="bi bi-clock-history me-2"></i>Last Updated</div>
                <div class="text-muted"><?= $updatedAt ? View::e($updatedAt) : 'Never updated' ?></div>
            </div>
        </div>
    </div>
</div>

<div class="card zb-card">
    <div class="card-header zb-card-head py-3 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div class="fw-semibold"><i class="bi bi-file-text me-2 text-muted"></i>Documentation Content</div>
        <div class="d-flex gap-2">
            <button class="btn btn-sm btn-outline-secondary" type="button" id="copyBtn">
                <i class="bi bi-clipboard me-1"></i>Copy
            </button>
            <button class="btn btn-sm btn-outline-secondary" type="button" id="printBtn">
                <i class="bi bi-printer me-1"></i>Print
            </button>
        </div>
    </div>
    <div class="card-body p-4">
        <?php if ($body !== ''): ?>
            <div class="zb-content" id="documentContent"><?= nl2br(View::e($body)) ?></div>
        <?php else: ?>
            <div class="text-center py-5 text-muted">
                <div class="display-6 mb-3"><i class="bi bi-file-earmark-text"></i></div>
                <div class="fw-semibold mb-2">No Content Available</div>
                <div class="mb-3">This documentation entry doesn't have any content yet.</div>
                <a href="/documentation/edit?id=<?= View::e((string)$id) ?>" class="btn btn-primary">
                    <i class="bi bi-pencil me-2"></i>Add Content
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="zb-toast" id="toast">
    <i class="bi bi-check-circle-fill" style="color:var(--color-accent-green);"></i>
    <span id="toastMsg">Copied!</span>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const toast = document.getElementById('toast');
    const toastMsg = document.getElementById('toastMsg');
    const contentEl = document.getElementById('documentContent');
    const copyBtn = document.getElementById('copyBtn');
    const printBtn = document.getElementById('printBtn');

    function showToast(msg) {
        toastMsg.textContent = msg;
        toast.classList.add('show');
        setTimeout(() => toast.classList.remove('show'), 2500);
    }

    function formatContent() {
        if (!contentEl) return;
        let html = contentEl.innerHTML;

        html = html.replace(/^### (.*?)$/gm, '<h3>$1</h3>');
        html = html.replace(/^## (.*?)$/gm, '<h2>$1</h2>');
        html = html.replace(/^# (.*?)$/gm, '<h1>$1</h1>');
        html = html.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
        html = html.replace(/\*(.*?)\*/g, '<em>$1</em>');
        html = html.replace(/```([\s\S]*?)```/g, '<pre><code>$1</code></pre>');
        html = html.replace(/`([^`]+)`/g, '<code>$1</code>');
        html = html.replace(/^> (.*?)$/gm, '<blockquote>$1</blockquote>');
        html = html.replace(/\[([^\]]+)\]\(([^)]+)\)/g, '<a href="$2" target="_blank" rel="noopener noreferrer">$1</a>');

        contentEl.innerHTML = html;
    }

    async function copyToClipboard() {
        if (!contentEl) return;
        try {
            await navigator.clipboard.writeText(contentEl.textContent || '');
            showToast('Copied to clipboard');
        } catch (e) {
            showToast('Copy failed');
        }
    }

    function printDocument() {
        const html = `
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title><?= View::e($title) ?> - Documentation</title>
<style>
body{font-family:Arial,sans-serif;line-height:1.6;padding:2rem;}
h1{margin:0 0 .5rem 0;}
.meta{background:#f5f5f5;padding:1rem;border-radius:6px;margin:1rem 0 2rem 0;}
pre{background:#eee;padding:1rem;border-radius:6px;overflow:auto;}
code{background:#eee;padding:.1rem .25rem;border-radius:4px;}
</style>
</head>
<body>
<h1><?= View::e($title) ?></h1>
<div class="meta">
<div><strong>ID:</strong> #<?= View::e((string)$id) ?></div>
<div><strong>Project:</strong> <?= ($project !== '' && $project !== null) ? View::e((string)$project) : 'General' ?></div>
<div><strong>Type:</strong> <?= $type !== '' ? View::e($type) : 'Not specified' ?></div>
<div><strong>Created by:</strong> <?= View::e((string)$createdBy) ?><?= $createdAt ? ' on ' . View::e($createdAt) : '' ?></div>
</div>
<div><?= nl2br(View::e($body)) ?></div>
</body>
</html>`;
        const w = window.open('', '_blank');
        w.document.write(html);
        w.document.close();
        w.focus();
        setTimeout(() => { w.print(); w.close(); }, 200);
    }

    formatContent();

    copyBtn && copyBtn.addEventListener('click', copyToClipboard);
    printBtn && printBtn.addEventListener('click', printDocument);

    document.querySelectorAll('.tag-chip').forEach(tag => {
        tag.style.cursor = 'pointer';
        tag.addEventListener('click', () => {
            const t = tag.getAttribute('data-tag') || '';
            showToast(`Searching for "${t}"`);
            setTimeout(() => {
                window.location.href = `/documentation?search=${encodeURIComponent(t)}`;
            }, 600);
        });
    });
});
</script>
