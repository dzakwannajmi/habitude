// Sidebar open/close (shadcn sidebar-08 "inset" behaviour), remembered per browser.
const root = document.documentElement;
const KEY = 'habitude.sidebar';

const read = () => {
    try { return localStorage.getItem(KEY); } catch { return null; }
};
const write = (value) => {
    try { localStorage.setItem(KEY, value); } catch { /* storage unavailable */ }
};

const desktop = window.matchMedia('(min-width: 1024px)');
root.dataset.sidebar = desktop.matches && read() !== 'closed' ? 'open' : 'closed';

document.addEventListener('click', (event) => {
    if (event.target.closest('[data-sidebar-toggle]')) {
        const next = root.dataset.sidebar === 'open' ? 'closed' : 'open';
        root.dataset.sidebar = next;
        if (desktop.matches) write(next);
    }
    if (event.target.closest('[data-sidebar-overlay]')) {
        root.dataset.sidebar = 'closed';
    }

    // Delete confirmation modal (native <dialog>)
    const opener = event.target.closest('[data-modal-open]');
    if (opener) document.getElementById(opener.dataset.modalOpen)?.showModal();
    const closer = event.target.closest('[data-modal-close]');
    if (closer) closer.closest('dialog')?.close();
    if (event.target instanceof HTMLDialogElement) event.target.close(); // click on backdrop
});

// Color input mirrors its hex value next to it
document.querySelectorAll('[data-color-input]').forEach((input) => {
    const out = document.querySelector(input.dataset.colorInput);
    const sync = () => { if (out) out.textContent = input.value; };
    input.addEventListener('input', sync);
    sync();
});
