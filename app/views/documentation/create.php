<?php
// create.php displays a form to add a new documentation entry.

/** @var int $project_id */

use App\core\View;

$projectId = (int)($project_id ?? 0);
?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
    <div>
        <h1 class="h4 mb-1 fw-bold"><i class="bi bi-plus-circle me-2 text-muted"></i>Add Documentation</h1>
        <div class="text-muted">Create new documentation entry for knowledge sharing</div>
    </div>

    <a href="/documentation<?= $projectId > 0 ? '?project_id=' . View::e((string)$projectId) : '' ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-2"></i>Cancel
    </a>
</div>

<style>
    .zb-card{background:var(--color-surface);border:1px solid var(--color-border);border-radius:16px;}
    .zb-card-head{background:var(--color-surface-alt);border-bottom:1px solid var(--color-border);border-top-left-radius:16px;border-top-right-radius:16px;}
    .tags-input-container{background:var(--color-surface-alt);border:1px solid var(--color-border);border-radius:12px;padding:.75rem 1rem;min-height:50px;display:flex;flex-wrap:wrap;gap:.5rem;align-items:center;}
    .tag-item{background:rgba(255,200,87,.10);border:1px solid rgba(255,200,87,.20);color:var(--color-accent);padding:.25rem .75rem;border-radius:20px;font-size:.85rem;display:flex;align-items:center;gap:.5rem;}
    .tag-remove{background:none;border:none;color:var(--color-accent);cursor:pointer;padding:0;font-size:1rem;line-height:1;}
    .tags-input{flex-grow:1;background:transparent;border:none;color:var(--color-text);font-size:.95rem;outline:none;min-width:150px;}
    .type-suggestions{display:flex;flex-wrap:wrap;gap:.5rem;margin-top:.5rem;}
    .type-suggestion{background:var(--color-surface-alt);border:1px solid var(--color-border);color:var(--color-text);padding:.25rem .75rem;border-radius:20px;font-size:.85rem;cursor:pointer;}
    .type-suggestion:hover{background:var(--color-surface-soft);border-color:var(--color-accent-blue);}
    .char-counter{color:var(--color-text-muted);font-size:.85rem;text-align:right;margin-top:.5rem;}
    .char-counter.warning{color:var(--color-accent-strong);}
    .char-counter.danger{color:#ef4444;}
    textarea#body{min-height:300px;resize:vertical;font-family:ui-monospace,SFMono-Regular,Menlo,Monaco,Consolas,"Liberation Mono","Courier New",monospace;line-height:1.6;}
</style>

<div class="card zb-card">
    <div class="card-header zb-card-head py-3 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div class="fw-semibold"><i class="bi bi-file-earmark-text me-2 text-muted"></i>Documentation Details</div>
        <small class="text-muted">Markdown supported</small>
    </div>

    <div class="card-body p-4">
        <form action="/documentation/store" method="post" id="docForm" class="needs-validation" novalidate>
            <div class="row g-4">
                <div class="col-12">
                    <div class="fw-semibold text-muted small text-uppercase mb-2"><i class="bi bi-folder me-2"></i>Project Association</div>

                    <?php if ($projectId > 0): ?>
                        <input type="hidden" name="project_id" value="<?= View::e((string)$projectId) ?>">
                        <div class="p-3 rounded-3"
                             style="background:rgba(56,189,248,.08);border:1px solid rgba(56,189,248,.18);border-left:4px solid var(--color-accent-blue);">
                            <div class="fw-semibold text-info mb-1">This documentation will be attached to:</div>
                            <div class="text-muted">Project ID <?= View::e((string)$projectId) ?></div>
                        </div>
                    <?php else: ?>
                        <div>
                            <label for="project_id" class="form-label fw-semibold">
                                <i class="bi bi-folder me-2"></i>Project (Optional)
                            </label>
                            <input type="number" name="project_id" id="project_id" class="form-control" min="0"
                                   placeholder="Enter project ID (leave blank for general documentation)">
                            <div class="form-text"><i class="bi bi-info-circle me-1"></i>Set a project ID to link this entry to a specific project.</div>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="col-12">
                    <div class="fw-semibold text-muted small text-uppercase mb-2"><i class="bi bi-info-circle me-2"></i>Basic Information</div>
                </div>

                <div class="col-12">
                    <label for="title" class="form-label fw-semibold">Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" id="title" class="form-control" required maxlength="200"
                           placeholder="Enter a clear and descriptive title">
                    <div class="invalid-feedback">Title is required.</div>
                    <div class="char-counter" id="titleCounter">0/200</div>
                </div>

                <div class="col-12">
                    <label for="type" class="form-label fw-semibold">Document Type</label>
                    <input type="text" name="type" id="type" class="form-control" maxlength="50"
                           placeholder="e.g. SOP, Architecture, Onboarding, Checklist, API Documentation">
                    <div class="form-text">
                        <i class="bi bi-lightbulb me-1"></i>Common types:
                        <div class="type-suggestions">
                            <span class="type-suggestion" data-type="SOP">SOP</span>
                            <span class="type-suggestion" data-type="Architecture">Architecture</span>
                            <span class="type-suggestion" data-type="Onboarding">Onboarding</span>
                            <span class="type-suggestion" data-type="Checklist">Checklist</span>
                            <span class="type-suggestion" data-type="API Documentation">API Documentation</span>
                            <span class="type-suggestion" data-type="Troubleshooting">Troubleshooting</span>
                        </div>
                    </div>
                    <div class="char-counter" id="typeCounter">0/50</div>
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold">Tags (Optional)</label>
                    <div class="tags-input-container" id="tagsContainer">
                        <input type="text" id="tagInput" class="tags-input" placeholder="Type a tag and press Enter or comma">
                    </div>
                    <input type="hidden" name="tags" id="tagsHidden" value="">
                    <div class="form-text"><i class="bi bi-info-circle me-1"></i>Press Enter or comma to add tags. Click X to remove.</div>
                </div>

                <div class="col-12">
                    <div class="fw-semibold text-muted small text-uppercase mb-2"><i class="bi bi-file-text me-2"></i>Content</div>
                    <label for="body" class="form-label fw-semibold">Content <span class="text-danger">*</span></label>
                    <textarea name="body" id="body" rows="12" class="form-control" required maxlength="10000"
                              placeholder="Write your documentation here. You can use markdown formatting."></textarea>
                    <div class="invalid-feedback">Content is required.</div>
                    <div class="char-counter" id="bodyCounter">0/10000</div>

                    <div class="mt-3 p-3 rounded-3" style="background:var(--color-surface-alt);border:1px solid var(--color-border);">
                        <div class="fw-semibold mb-2" style="color:var(--color-accent);"><i class="bi bi-markdown me-2"></i>Markdown Help</div>
                        <div class="row g-2 small text-muted">
                            <div class="col-md-3"><code># H1</code>, <code>## H2</code>, <code>### H3</code></div>
                            <div class="col-md-3"><code>- Item</code>, <code>1. Item</code></div>
                            <div class="col-md-3"><code>`inline code`</code></div>
                            <div class="col-md-3"><code>[Text](URL)</code></div>
                        </div>
                    </div>
                </div>

                <div class="col-12 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="confirmValid" required>
                        <label class="form-check-label" for="confirmValid">I confirm this information is accurate</label>
                        <div class="invalid-feedback">You must confirm.</div>
                    </div>

                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <i class="bi bi-save me-2"></i>Save Documentation
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('docForm');
    const submitBtn = document.getElementById('submitBtn');

    const tags = [];
    const tagsContainer = document.getElementById('tagsContainer');
    const tagInput = document.getElementById('tagInput');
    const tagsHidden = document.getElementById('tagsHidden');

    const title = document.getElementById('title');
    const type = document.getElementById('type');
    const body = document.getElementById('body');

    function setCounter(el, counterId, max) {
        const c = document.getElementById(counterId);
        const len = el.value.length;
        c.textContent = `${len}/${max}`;
        c.className = 'char-counter';
        if (len > max) c.classList.add('danger');
        else if (len > max * 0.9) c.classList.add('warning');
    }

    function renderTags() {
        tagsContainer.querySelectorAll('.tag-item').forEach(n => n.remove());

        tags.forEach((t, idx) => {
            const tagEl = document.createElement('div');
            tagEl.className = 'tag-item';
            tagEl.innerHTML = `${t}
                <button type="button" class="tag-remove" aria-label="Remove tag">
                    <i class="bi bi-x"></i>
                </button>`;
            tagEl.querySelector('button').addEventListener('click', () => {
                tags.splice(idx, 1);
                renderTags();
            });
            tagsContainer.insertBefore(tagEl, tagInput);
        });

        tagsHidden.value = tags.join(',');
    }

    tagInput.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' || e.key === ',') {
            e.preventDefault();
            const v = tagInput.value.trim();
            if (!v) return;
            const low = v.toLowerCase();
            const exists = tags.some(x => x.toLowerCase() === low);
            if (!exists) tags.push(v);
            tagInput.value = '';
            renderTags();
        }
    });

    document.querySelectorAll('.type-suggestion').forEach(el => {
        el.addEventListener('click', () => {
            type.value = el.getAttribute('data-type') || '';
            setCounter(type, 'typeCounter', 50);
        });
    });

    title.addEventListener('input', () => setCounter(title, 'titleCounter', 200));
    type.addEventListener('input', () => setCounter(type, 'typeCounter', 50));
    body.addEventListener('input', () => setCounter(body, 'bodyCounter', 10000));

    setCounter(title, 'titleCounter', 200);
    setCounter(type, 'typeCounter', 50);
    setCounter(body, 'bodyCounter', 10000);
    title.focus();

    form.addEventListener('submit', (e) => {
        if (!form.checkValidity()) {
            e.preventDefault();
            e.stopPropagation();
            form.classList.add('was-validated');
            return;
        }

        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Saving...';
    });
});
</script>
