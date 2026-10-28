export function toast({ title, body = '', duration = 4500 }) {
    const host = document.getElementById('toasts');
    if (!host) return;
    const el = document.createElement('div');
    el.className = 'toast';
    el.setAttribute('role', 'status');
    el.innerHTML = '<span class="mt-0.5 flex size-8 flex-none items-center justify-center rounded-md bg-ink text-paper"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"/><path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"/><path d="M4 22h16"/><path d="M18 2H6v7a6 6 0 0 0 12 0V2Z"/></svg></span><div class="min-w-0"><p class="text-body-sm font-semibold"></p><p class="text-caption text-graphite"></p></div>';
    el.querySelector('p.font-semibold').textContent = title;
    el.querySelector('p.text-graphite').textContent = body;
    host.append(el);
    setTimeout(() => {
        el.dataset.leaving = '';
        setTimeout(() => el.remove(), 220);
    }, duration);
}
