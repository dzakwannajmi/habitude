# P04: Dashboard, Calendar, and Streaks

| | |
|---|---|
| **Status** | ✅ Done |
| **Started** | 2026-10-28 |
| **Completed** | 2026-10-28 |
| **Branch** | `feature/crud-ui` |
| **Module** | [Laravel 5: CRUD Periode dengan Template AdminLTE](https://mirzayogy.github.io/blog/2026-10-13-laravel-simtikum-5/) |

## Goal
A home page where the day is tracked: check-ins, timers, a calendar, and streaks.

## Jobs

| Code | Job | Status | Completed | Proof |
|---|---|---|---|---|
| J1 | Daily check-in (spring check, +1, timer) | ✅ Done | 2026-10-28 | [`HabitLogController`](https://github.com/dzakwannajmi/PBO2_LARAVEL_5C/tree/feature/crud-ui/app/Http/Controllers/HabitLogController.php), [`tracker.js`](https://github.com/dzakwannajmi/PBO2_LARAVEL_5C/tree/feature/crud-ui/resources/js/ui/tracker.js) |
| J2 | Streak engine (per habit and overall) | ✅ Done | 2026-10-28 | [`StreakService`](https://github.com/dzakwannajmi/PBO2_LARAVEL_5C/tree/feature/crud-ui/app/Services/StreakService.php) |
| J3 | Month calendar with completion heat | ✅ Done | 2026-10-28 | [`CalendarService`](https://github.com/dzakwannajmi/PBO2_LARAVEL_5C/tree/feature/crud-ui/app/Services/CalendarService.php) |
| J4 | Dashboard stats and category progress | ✅ Done | 2026-10-28 | [`DashboardController`](https://github.com/dzakwannajmi/PBO2_LARAVEL_5C/tree/feature/crud-ui/app/Http/Controllers/DashboardController.php), [`dashboard.blade.php`](https://github.com/dzakwannajmi/PBO2_LARAVEL_5C/tree/feature/crud-ui/resources/views/dashboard.blade.php) |

**Status legend:** ⏳ Planned · 🚧 In progress · ✅ Done · ⛔ Blocked

---

### J1: Daily check-in
- **What:** One JSON endpoint (`mode` = `toggle`, `add`, or `set`) updates the day's log, and the page updates in place. Habits measured in minutes get a timer whose digits roll like an odometer. Past days can be logged, future days are rejected.
- **Verified:** `TrackingTest` and the browser: check, uncheck, +1, timer start/stop.

### J2: Streaks
- A day counts for a habit when the logged value reaches the target. The overall streak counts days with at least one completed habit; a still-open today does not break it. Best streak is computed from the same dates.

### J3: Calendar
- Monday-first month grid. Each day is shaded by completed / total habits (ink shades only), shows today and the selected day, and links to that day. Back-filled days count even for habits created later.

### J4: Stats and category progress
- Four animated stat tiles. **By category** uses the Has-Many-Through relation `Category → Habit → HabitLog`, limited to the user and month.
- **Bug found by tests:** a hand-edited `?date=garbage` caused a 500; invalid dates now fall back to today.

## Next
P05: mood and journal.
