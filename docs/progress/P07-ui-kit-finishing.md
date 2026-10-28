# P07: UI Kit and Finishing

| | |
|---|---|
| **Status** | 🚧 In progress (J7 waits for a MySQL run) |
| **Started** | 2026-10-28 |
| **Completed** | - |
| **Branch** | `feature/crud-ui` |
| **Module** | [Laravel 5: CRUD Periode dengan Template AdminLTE](https://mirzayogy.github.io/blog/2026-10-13-laravel-simtikum-5/) |

## Goal
Apply the design system and the React Bits micro-interactions across the app, then verify everything.

## Jobs

| Code | Job | Status | Completed | Proof |
|---|---|---|---|---|
| J1 | Branched menu sidebar with icons | ✅ Done | 2026-10-28 | [`branched-menu.js`](https://github.com/dzakwannajmi/PBO2_LARAVEL_5C/tree/feature/crud-ui/resources/js/ui/branched-menu.js), [`nav-tree`](https://github.com/dzakwannajmi/PBO2_LARAVEL_5C/tree/feature/crud-ui/resources/views/components/nav-tree.blade.php) |
| J2 | Glide select for every dropdown | ✅ Done | 2026-10-28 | [`glide-select.js`](https://github.com/dzakwannajmi/PBO2_LARAVEL_5C/tree/feature/crud-ui/resources/js/ui/glide-select.js) |
| J3 | Swipe row and list/grid grouping | ✅ Done | 2026-10-28 | [`swipe-row.js`](https://github.com/dzakwannajmi/PBO2_LARAVEL_5C/tree/feature/crud-ui/resources/js/ui/swipe-row.js), [`habits/index`](https://github.com/dzakwannajmi/PBO2_LARAVEL_5C/tree/feature/crud-ui/resources/views/habits/index.blade.php) |
| J4 | Spring check, peek rating, counter | ✅ Done | 2026-10-28 | [`resources/js/ui`](https://github.com/dzakwannajmi/PBO2_LARAVEL_5C/tree/feature/crud-ui/resources/js/ui) |
| J5 | Stepper (onboarding) and dock (mobile) | ✅ Done | 2026-10-28 | [`stepper.js`](https://github.com/dzakwannajmi/PBO2_LARAVEL_5C/tree/feature/crud-ui/resources/js/ui/stepper.js), [`dock.js`](https://github.com/dzakwannajmi/PBO2_LARAVEL_5C/tree/feature/crud-ui/resources/js/ui/dock.js) |
| J6 | Tests and browser verification | ✅ Done | 2026-10-28 | [`tests/Feature`](https://github.com/dzakwannajmi/PBO2_LARAVEL_5C/tree/feature/crud-ui/tests/Feature) |
| J7 | MySQL run on the lecturer's setup | ⏳ Planned | - | needs a MySQL server (see README) |

**Status legend:** ⏳ Planned · 🚧 In progress · ✅ Done · ⛔ Blocked

---

### J1-J5: React Bits micro-interactions
React Bits is written for React, so each effect is re-implemented in plain JavaScript and CSS (Blade + Tailwind project). Colors stay inside the Brex tokens: ink, paper, fog, mist, with Ember only on the primary button.

| Component | Used for |
|---|---|
| Branched Menu | Sidebar tree with branch lines, a gliding marker, and icons on the children |
| Glide Select | Every dropdown: category, unit, timezone, goal type, group-by |
| Swipe Row | Habit, mood, and journal rows: swipe or press the dots for edit and delete; grouped list or grid |
| Spring Check | Habit check-in, Active switch, onboarding picks, Remember me |
| Peek Rating | Mood hearts with a label tooltip |
| Counter | Odometer digits for streaks, stats, and the habit timer |
| Stepper | First-run onboarding |
| Dock | Bottom navigation on mobile, magnifies under the finger |

### J6: Verification
- `php artisan test`: 47 tests pass (SQLite in memory).
- Browser run with Chrome: login, check-in and uncheck, +1, timer, mood rating, menu fold, swipe, delete dialog, select with auto-submit, onboarding, mobile dock, with no console errors.
- **Bugs found and fixed while verifying:** invalid dates caused a 500; the mobile dock showed on desktop because custom CSS outranked `lg:hidden` (UI CSS now lives in the components layer); the calendar and monthly percentage ignored habits whose history predates their creation date.

### J7: MySQL
- Everything was developed on SQLite. The migrations use standard column types, but they have not been run against MySQL yet. Run `php artisan migrate:fresh --seed` on MySQL and check the tables in phpMyAdmin.

## Credits
Interaction designs are ports of [React Bits](https://reactbits.dev) micro-interactions. Check its license terms before publishing the project publicly.
