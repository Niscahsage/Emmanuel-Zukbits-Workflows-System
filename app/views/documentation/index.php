<?php
// index.php lists documentation entries for a selected project or the entire system.

/** @var array $documents */
/** @var int   $project_id */

use App\core\View;

$documents = is_array($documents) ? $documents : [];
$projectId = (int)($project_id ?? 0);
?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
    <div>
        <h1 class="h4 mb-1 fw-bold"><i class="bi bi-file-text me-2 text-muted"></i>Documentation</h1>
        <div class="text-muted">
            <?php if ($projectId > 0): ?>
                Showing documentation for project ID <?= View::e((string)$projectId) ?>
                <span class="badge ms-2" style="background:rgba(56,189,248,.10);border:1px solid rgba(56,189,248,.20);color:var(--color-accent-blue);">
                    <i class="bi bi-filter me-1"></i>Project Filter
                </span>
            <?php else: ?>
                Showing documentation entries you are allowed to access
            <?php endif; ?>
        </div>
    </div>

    <a href="/documentation/create<?= $projectId > 0 ? '?project_id=' . View::e((string)$projectId) : '' ?>" class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i>Add Documentation
    </a>
</div>

<style>
    .zb-search{background:var(--color-surface);border:1px solid var(--color-border);border-radius:12px;padding:.75rem 1rem;display:flex;align-items:center;gap:.75rem;}
    .zb-search input{flex-grow:1;background:transparent;border:none;color:var(--color-text);outline:none;}
    .zb-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(340px,1fr));gap:1rem;margin-top:1rem;}
    .zb-doc{background:var(--color-surface);border:1px solid var(--color-border);border-radius:16px;padding:1.25rem;display:flex;flex-direction:column;}
    .zb-doc:hover{background:var(--color-surface-alt);}
    .zb-id{background:var(--color-surface-alt);border:1px solid var(--color-border);color:var(--color-text-muted);font-size:.75rem;padding:.25rem .75rem;border-radius:12px;font-family:ui-monospace,SFMono-Regular,Menlo,Monaco,Consolas,"Liberation Mono","Courier New",monospace;}
    .zb-title{font-size:1.1rem;font-weight:700;margin:.75rem 0 0 0;line-height:1.35;}
    .zb-preview{color:var(--color-text-muted);margin:.75rem 0 1rem 0;flex-grow:1;}
    .zb-tag{background:rgba(255,200,87,.10);border:1px solid rgba(255,200,87,.20);color:var(--color-accent);padding:.2rem .5rem;border-radius:10px;font-size:.75rem;}
</style>

<div class="zb-search mb-3">
    <i class="bi bi-search text-muted"></i>
    <input type="text" id="searchInput" placeholder="Search by title, tags, or type...">
    <button class="btn btn-sm btn-primary" type="button" id="searchBtn">
        <i class="bi bi-search me-1"></i>Search
    </button>
</div>

<?php if (empty($documents)): ?>
    <div class="card" style="background:var(--color-surface);border:1px solid var(--color-border);border-radius:16px;">
        <div class="card-body text-center p-5">
            <div class="display-6 text-muted mb-3"><i class="bi bi-file-earmark-text"></i></div>
            <h3 class="h5 mb-2">No documentation found</h3>
            <p class="text-muted mb-4">
                <?php if ($projectId > 0): ?>
                    No documentation entries found for this project. Create the first one!
                <?php else: ?>
                    No documentation entries available. Start by adding your first documentation.
                <?php endif; ?>
            </p>
            <a href="/documentation/create<?= $projectId > 0 ? '?project_id=' . View::e((string)$projectId) : '' ?>" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>Create First Documentation
            </a>
        </div>
    </div>
<?php else: ?>
    <div class="zb-grid" id="docsGrid">
        <?php foreach ($documents as $doc): ?>
            <?php
            $id        = (int)($doc['id'] ?? 0);
            $title     = (string)($doc['title'] ?? '');
            $type      = (string)($doc['type'] ?? '');
            $tagsCsv   = (string)($doc['tags'] ?? '');
            $createdBy = (string)($doc['created_by_name'] ?? ($doc['created_by'] ?? ''));
            $createdAt = (string)($doc['created_at'] ?? '');

            $rawBody = (string)($doc['body'] ?? '');
            $bodyPreview = trim(preg_replace('/\s+/', ' ', strip_tags($rawBody)));
            if (strlen($bodyPreview) > 160) $bodyPreview = substr($bodyPreview, 0, 160) . '...';
            ?>
            <div class="zb-doc"
                 data-title="<?= htmlspecialchars(strtolower($title), ENT_QUOTES) ?>"
                 data-tags="<?= htmlspecialchars(strtolower($tagsCsv), ENT_QUOTES) ?>"
                 data-type="<?= htmlspecialchars(strtolower($type), ENT_QUOTES) ?>">
                <div class="d-flex justify-content-between align-items-start gap-2">
                    <span class="zb-id">#<?= View::e((string)$id) ?></span>
                    <?php if ($type !== ''): ?>
                        <span class="badge" style="background:rgba(56,189,248,.10);border:1px solid rgba(56,189,248,.20);color:var(--color-accent-blue);">
                            <i class="bi bi-tag me-1"></i><?= View::e($type) ?>
                        </span>
                    <?php endif; ?>
                </div>

                <div class="zb-title"><?= View::e($title) ?></div>

                <?php if (trim($tagsCsv) !== ''): ?>
                    <div class="d-flex flex-wrap gap-2 mt-2">
                        <?php foreach (explode(',', $tagsCsv) as $t): ?>
                            <?php $t = trim($t); if ($t === '') continue; ?>
                            <span class="zb-tag tag-chip" data-tag="<?= htmlspecialchars(strtolower($t), ENT_QUOTES) ?>"><?= View::e($t) ?></span>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <div class="zb-preview"><?= View::e($bodyPreview) ?></div>

                <div class="d-flex justify-content-between align-items-center pt-3" style="border-top:1px solid var(--color-border);">
                    <div class="text-muted small"><i class="bi bi-person-circle me-2"></i><?= View::e($createdBy) ?></div>
                    <div class="d-flex align-items-center gap-2">
                        <small class="text-muted"><i class="bi bi-calendar3 me-1"></i><?= View::e($createdAt) ?></small>
                        <a href="/documentation/show?id=<?= View::e((string)$id) ?>" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-eye me-1"></i>View
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const input = document.getElementById('searchInput');
    const btn = document.getElementById('searchBtn');
    const cards = Array.from(document.querySelectorAll('#docsGrid .zb-doc'));

    function runSearch() {
        const q = (input.value || '').trim().toLowerCase();
        cards.forEach(card => {
            const title = card.getAttribute('data-title') || '';
            const tags = card.getAttribute('data-tags') || '';
            const type = card.getAttribute('data-type') || '';
            const ok = !q || title.includes(q) || tags.includes(q) || type.includes(q);
            card.style.display = ok ? 'flex' : 'none';
        });
    }

    btn && btn.addEventListener('click', runSearch);
    input && input.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') runSearch();
    });

    document.querySelectorAll('.tag-chip').forEach(tag => {
        tag.style.cursor = 'pointer';
        tag.addEventListener('click', () => {
            input.value = (tag.getAttribute('data-tag') || '').toLowerCase();
            runSearch();
        });
    });
});
</script>
