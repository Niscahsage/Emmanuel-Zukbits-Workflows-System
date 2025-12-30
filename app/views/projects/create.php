<?php
// app/views/projects/create.php
// create.php displays the form to create a new project or campaign.

use App\core\View;
?>
<div class="zb-project-create">
    <div class="page-header">
        <div class="header-content">
            <?php require __DIR__ . '/../partials/breadcrumbs.php'; ?>
            <div class="title-section">
                <h1 class="page-title">Create New Project</h1>
                <p class="page-subtitle">Set up a new project or campaign with all necessary details</p>
            </div>
        </div>
        <div class="header-actions">
            <a href="/projects" class="btn-action btn-secondary"><i class="bi bi-arrow-left"></i> Back to Projects</a>
        </div>
    </div>

    <div class="form-wizard">
        <div class="wizard-steps" id="wizardSteps">
            <div class="wizard-step active" data-step="1"><div class="step-number">1</div><div class="step-label">Basic Info</div></div>
            <div class="wizard-step" data-step="2"><div class="step-number">2</div><div class="step-label">Details</div></div>
            <div class="wizard-step" data-step="3"><div class="step-number">3</div><div class="step-label">Objectives</div></div>
            <div class="wizard-step" data-step="4"><div class="step-number">4</div><div class="step-label">Review</div></div>
        </div>
    </div>

    <div class="form-container">
        <form action="/projects/store" method="post" id="projectForm" class="form-card" novalidate>
            <!-- Keep your existing sections markup (1..4) as-is -->
            <!-- Only change: remove inline onclick handlers and let JS attach events -->
            <?= View::e('') ?>
        </form>

        <!-- Keep your sidebar markup as-is -->
    </div>
</div>

<script>
(() => {
    // NOTE: Your original create.php is huge; main fixes:
    // 1) Remove the simulated submit (setTimeout) and actually submit after validation.
    // 2) Don’t reference PHP $id in success redirect (there is no ID yet).
    // 3) Don’t rely on global `event` in onclick handlers; use addEventListener.

    const form = document.getElementById('projectForm');
    if (!form) return;

    let currentSection = 1;
    const totalSections = 4;

    function setWizard(step) {
        document.querySelectorAll('.wizard-step').forEach(s => s.classList.toggle('active', Number(s.dataset.step) === step));
    }

    function showSection(step) {
        document.querySelectorAll('.form-section').forEach(sec => sec.classList.remove('active'));
        const el = document.getElementById(`section${step}`);
        if (el) el.classList.add('active');
        currentSection = step;
        setWizard(step);
    }

    function clearError(input) {
        input.style.borderColor = '';
        input.style.boxShadow = '';
        const err = input.parentElement.querySelector('.error-message');
        if (err) err.remove();
    }

    function showError(input, message) {
        clearError(input);
        input.style.borderColor = '#ef4444';
        input.style.boxShadow = '0 0 0 3px rgba(239, 68, 68, 0.15)';
        const err = document.createElement('div');
        err.className = 'error-message';
        err.style.color = '#ef4444';
        err.style.fontSize = '0.8rem';
        err.style.marginTop = '0.5rem';
        err.textContent = message;
        input.parentElement.appendChild(err);
        input.focus();
    }

    function validateSection(step) {
        const sectionEl = document.getElementById(`section${step}`);
        if (!sectionEl) return true;

        const inputs = sectionEl.querySelectorAll('[data-validation], [required]');
        for (const input of inputs) {
            const value = (input.value || '').trim();

            if (input.hasAttribute('required') && !value) {
                showError(input, 'This field is required');
                return false;
            }

            const rule = input.dataset.validation || '';
            if (rule === 'alphanumeric' && value && !/^[a-zA-Z0-9\-\s]+$/.test(value)) {
                showError(input, 'Only letters, numbers, and hyphens allowed');
                return false;
            }

            if (rule === 'date' && value && isNaN(Date.parse(value))) {
                showError(input, 'Please enter a valid date');
                return false;
            }

            clearError(input);
        }

        return true;
    }

    function collectRequirements() {
        const inputs = Array.from(document.querySelectorAll('.requirement-input'));
        const list = inputs.map(i => i.value.trim()).filter(Boolean);
        const hidden = document.getElementById('requirementsInput');
        if (hidden) hidden.value = JSON.stringify(list);
        return list;
    }

    function updateReview() {
        // Fill review fields if you kept them
        const get = (name, fallback = 'Not specified') => {
            const el = form.querySelector(`[name="${name}"]`);
            return (el && el.value.trim()) ? el.value.trim() : fallback;
        };

        const setText = (id, text) => {
            const el = document.getElementById(id);
            if (el) el.textContent = text;
        };

        setText('reviewName', get('name'));
        setText('reviewCode', get('code'));
        setText('reviewCategory', get('category'));
        setText('reviewClient', get('client_name'));
        setText('reviewStatus', get('status').replaceAll('_',' '));
        setText('reviewPriority', get('priority'));
        setText('reviewStart', get('start_date'));
        setText('reviewEnd', get('target_end_date'));
        setText('reviewDescription', get('description'));
        setText('reviewObjectives', get('objectives'));

        const reqs = collectRequirements();
        const ul = document.getElementById('reviewRequirements');
        if (ul) {
            ul.innerHTML = '';
            reqs.forEach(r => {
                const li = document.createElement('li');
                li.textContent = r;
                ul.appendChild(li);
            });
        }
    }

    // Wire next/prev buttons by data attributes (add these to your buttons: data-next / data-prev)
    document.addEventListener('click', (e) => {
        const nextBtn = e.target.closest('[data-next]');
        const prevBtn = e.target.closest('[data-prev]');
        const addReqBtn = e.target.closest('[data-add-req]');
        const removeReqBtn = e.target.closest('[data-remove-req]');

        if (nextBtn) {
            if (!validateSection(currentSection)) return;
            const next = Math.min(totalSections, currentSection + 1);
            showSection(next);
            if (next === 4) updateReview();
        }

        if (prevBtn) {
            const prev = Math.max(1, currentSection - 1);
            showSection(prev);
        }

        if (addReqBtn) {
            const list = document.getElementById('requirementsList');
            if (!list) return;
            const item = document.createElement('div');
            item.className = 'requirement-item';
            item.innerHTML = `
                <input type="text" class="form-control requirement-input" placeholder="Add a key requirement">
                <button type="button" class="btn-action-sm btn-secondary" data-remove-req="1"><i class="bi bi-x"></i></button>
            `;
            list.appendChild(item);
            item.querySelector('input')?.focus();
        }

        if (removeReqBtn) {
            const item = removeReqBtn.closest('.requirement-item');
            if (!item) return;
            const all = document.querySelectorAll('.requirement-item');
            if (all.length > 1) item.remove();
            else item.querySelector('input') && (item.querySelector('input').value = '');
        }
    });

    form.addEventListener('submit', (e) => {
        // Validate current section; if you want, validate all sections before submit
        if (!validateSection(currentSection)) {
            e.preventDefault();
            return;
        }
        collectRequirements();
        // Allow real submit to /projects/store (remove the simulated setTimeout)
    });

    // Start at section 1
    showSection(1);
})();
</script>
