// Dashboard behaviour: check-ins, +1 progress, timers and the quick mood
// rating. Everything updates in place from the JSON the server returns.
import { renderCounter } from './counter.js';
import { setChecked } from './spring-check.js';
import { toast } from './toast.js';

const csrf = () => document.querySelector('meta[name="csrf-token"]')?.content;

async function post(url, data) {
    const res = await fetch(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', Accept: 'application/json', 'X-CSRF-TOKEN': csrf() },
        body: JSON.stringify(data),
    });
    if (!res.ok) throw new Error(`Request failed (${res.status})`);
    return res.json();
}

const stat = (name) => document.querySelector(`[data-stat="${name}"]`);

function applyResult(row, res) {
    const target = Number(row.dataset.target);
    const unit = row.dataset.unit;
    const text = row.querySelector('[data-progress-text]');
    if (text) text.textContent = target > 1 ? `${res.value} / ${target} ${unit}` : res.completed ? 'Done' : 'Not yet';
    const bar = row.querySelector('[data-progress-bar]');
    if (bar) bar.style.width = `${Math.min(100, (res.value / target) * 100)}%`;
    row.dataset.done = res.completed ? '1' : '0';
    row.dispatchEvent(new CustomEvent('today:refresh', { bubbles: true }));
    const check = row.querySelector('[data-spring-check]');
    if (check && (check.getAttribute('aria-checked') === 'true') !== res.completed) setChecked(check, res.completed, { silent: true });
    const streak = row.querySelector('[data-streak]');
    if (streak) {
        renderCounter(streak, res.habit_streak);
        streak.closest('[data-streak-wrap]')?.toggleAttribute('hidden', res.habit_streak === 0);
    }

    const s = stat('streak');
    if (s) renderCounter(s, res.streak.current);
    const b = stat('best');
    if (b) renderCounter(b, res.streak.best);
    const d = stat('done');
    if (d) renderCounter(d, res.done);
    const t = stat('total');
    if (t) renderCounter(t, res.total);

    const cell = document.querySelector(`[data-cal-date="${res.date}"]`);
    if (cell && res.total > 0) {
        const ratio = Math.min(1, res.done / res.total);
        cell.style.setProperty('--r', ratio);
        cell.toggleAttribute('data-high', ratio > 0.55);
    }

    res.earned.forEach((a) => toast({ title: `Achievement unlocked: ${a.name}`, body: a.description ?? '', icon: 'trophy', duration: 6000 }));
}

async function send(row, data) {
    row.setAttribute('aria-busy', 'true');
    try {
        applyResult(row, await post(row.dataset.url, { date: row.dataset.date, ...data }));
    } catch (e) {
        toast({ title: 'Could not save', body: 'Check your connection and try again.', icon: 'alert' });
        row.querySelector('[data-spring-check]') && setChecked(row.querySelector('[data-spring-check]'), row.dataset.wasDone === '1', { silent: true });
    } finally {
        row.removeAttribute('aria-busy');
    }
}

const fmt = (secs) => `${String(Math.floor(secs / 60)).padStart(2, '0')}:${String(secs % 60).padStart(2, '0')}`;

export function initTracker() {
    document.addEventListener('springcheck:change', (e) => {
        const row = e.target.closest('[data-habit-row]');
        if (!row) return;
        row.dataset.wasDone = e.detail.checked ? '0' : '1';
        send(row, { mode: 'set', value: e.detail.checked ? Number(row.dataset.target) : 0 });
    });

    document.addEventListener('click', (e) => {
        const add = e.target.closest('[data-add]');
        if (add) send(add.closest('[data-habit-row]'), { mode: 'add', value: 1 });

        const start = e.target.closest('[data-timer-start]');
        if (start) {
            const row = start.closest('[data-habit-row]');
            const panel = row.querySelector('[data-timer-panel]');
            const display = row.querySelector('[data-timer-display]');
            let secs = 0;
            renderCounter(display, fmt(0));
            panel.hidden = false;
            start.hidden = true;
            row.timer = setInterval(() => renderCounter(display, fmt(++secs)), 1000);
            row.timerSecs = () => secs;
        }

        const stop = e.target.closest('[data-timer-stop]');
        if (stop) {
            const row = stop.closest('[data-habit-row]');
            clearInterval(row.timer);
            const minutes = Math.max(1, Math.round(row.timerSecs() / 60));
            row.querySelector('[data-timer-panel]').hidden = true;
            row.querySelector('[data-timer-start]').hidden = false;
            send(row, { mode: 'add', value: minutes });
        }
        const cancel = e.target.closest('[data-timer-cancel]');
        if (cancel) {
            const row = cancel.closest('[data-habit-row]');
            clearInterval(row.timer);
            row.querySelector('[data-timer-panel]').hidden = true;
            row.querySelector('[data-timer-start]').hidden = false;
        }
    });

    // Quick mood rating on the dashboard
    document.addEventListener('peek:change', async (e) => {
        const box = e.target.closest('[data-mood-quick]');
        if (!box) return;
        const out = box.querySelector('[data-mood-label]');
        try {
            const res = await post(box.dataset.url, { entry_date: box.dataset.date, mood_level: e.detail.value });
            if (out) out.textContent = `Saved: ${res.label}`;
            res.earned.forEach((a) => toast({ title: `Achievement unlocked: ${a.name}`, body: a.description ?? '', icon: 'trophy', duration: 6000 }));
        } catch {
            if (out) out.textContent = 'Could not save, try again.';
        }
    });
}
