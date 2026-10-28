import { initBellToggles } from './ui/bell-toggle.js';
import { initBranchedMenus } from './ui/branched-menu.js';
import { initCounters, renderCounter } from './ui/counter.js';
import { initDocks } from './ui/dock.js';
import { initGlideSelects } from './ui/glide-select.js';
import { initPeekRatings } from './ui/peek-rating.js';
import { initRubberSegments } from './ui/rubber-segment.js';
import { initSkylines } from './ui/skyline.js';
import { initSpringChecks } from './ui/spring-check.js';
import { initSteppers } from './ui/stepper.js';
import { initSwipeRows } from './ui/swipe-row.js';
import { flashToToasts, toast } from './ui/toast.js';
import { initTodayLists } from './ui/today-list.js';
import { initTracker } from './ui/tracker.js';

window.Habitude = { toast, counter: renderCounter };

// Sidebar open/close (shadcn sidebar-08 "inset" behaviour), remembered per browser.
const root = document.documentElement;
const KEY = 'habitude.sidebar';
const read = () => { try { return localStorage.getItem(KEY); } catch { return null; } };
const write = (value) => { try { localStorage.setItem(KEY, value); } catch { /* storage unavailable */ } };
const desktop = window.matchMedia('(min-width: 1024px)');
root.dataset.sidebar = desktop.matches && read() !== 'closed' ? 'open' : 'closed';

document.addEventListener('click', (event) => {
    if (event.target.closest('[data-sidebar-toggle]')) {
        const next = root.dataset.sidebar === 'open' ? 'closed' : 'open';
        root.dataset.sidebar = next;
        if (desktop.matches) write(next);
    }
    if (event.target.closest('[data-sidebar-overlay]')) root.dataset.sidebar = 'closed';

    // Elements that are in the design but not built yet (magic link, SSO, legal pages)
    const soon = event.target.closest('[data-soon]');
    if (soon) {
        event.preventDefault();
        toast({ title: soon.dataset.soon || 'Coming soon', body: 'This is not available in the beta yet.', icon: 'info' });
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

// Submit the surrounding form when an [data-autosubmit] field changes (group-by select)
document.addEventListener('change', (e) => {
    const field = e.target.closest('[data-autosubmit]') ?? e.target.closest('[data-glide-select][data-autosubmit]');
    if (field) field.closest('form')?.requestSubmit();
});

document.addEventListener('pointerdown', (e) => {
    document.querySelectorAll('details[data-menu][open]').forEach((d) => { if (!d.contains(e.target)) d.removeAttribute('open'); });
});

initBranchedMenus();
initGlideSelects();
initSpringChecks();
initPeekRatings();
initCounters();
initSteppers();
initSwipeRows();
initDocks();
initRubberSegments();
initBellToggles();
initSkylines();
flashToToasts();
initTodayLists();
initTracker();
