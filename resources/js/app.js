

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-account-menu]').forEach((menu) => {
        const toggle = menu.querySelector('[data-account-toggle]');
        const panel = menu.querySelector('[data-account-panel]');
        const close = () => {
            panel.dataset.open = 'false';
            window.setTimeout(() => { if (panel.dataset.open === 'false') panel.hidden = true; }, 160);
            toggle.setAttribute('aria-expanded', 'false');
        };

        toggle.addEventListener('click', () => {
            const opening = panel.hidden;
            document.querySelectorAll('[data-account-panel]').forEach((other) => {
                other.dataset.open = 'false';
                window.setTimeout(() => { if (other.dataset.open === 'false') other.hidden = true; }, 160);
            });
            document.querySelectorAll('[data-account-toggle]').forEach((other) => other.setAttribute('aria-expanded', 'false'));
            panel.hidden = false;
            requestAnimationFrame(() => { panel.dataset.open = String(opening); });
            if (!opening) window.setTimeout(() => { if (panel.dataset.open === 'false') panel.hidden = true; }, 160);
            toggle.setAttribute('aria-expanded', String(opening));
            if (opening) window.lucide?.createIcons();
        });

        document.addEventListener('click', (event) => { if (!menu.contains(event.target)) close(); });
        document.addEventListener('keydown', (event) => { if (event.key === 'Escape') close(); });
    });

    const dialog = document.getElementById('delete-confirmation-modal');
    if (!dialog) return;

    let pendingForm = null;
    const cancel = () => { pendingForm = null; dialog.close(); };
    document.querySelectorAll('[data-delete-trigger]').forEach((button) => {
        button.addEventListener('click', () => {
            pendingForm = button.form || button.closest('form');
            dialog.querySelector('[data-delete-name]').textContent = button.dataset.deleteName || 'data ini';
            dialog.querySelector('[data-delete-error]').hidden = true;
            dialog.showModal();
            window.lucide?.createIcons();
        });
    });
    dialog.querySelector('[data-delete-cancel]').addEventListener('click', cancel);
    dialog.addEventListener('click', (event) => { if (event.target === dialog) cancel(); });
    dialog.addEventListener('cancel', () => { pendingForm = null; });
    dialog.querySelector('[data-delete-confirm]').addEventListener('click', () => {
        if (!pendingForm) return;
        const form = pendingForm;
        pendingForm = null;
        dialog.close();
        HTMLFormElement.prototype.submit.call(form);
    });
});
