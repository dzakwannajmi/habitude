# P03: Authentication and Onboarding

| | |
|---|---|
| **Status** | ✅ Done |
| **Started** | 2026-10-28 |
| **Completed** | 2026-10-28 |
| **Branch** | `feature/crud-ui` |
| **Module** | [Laravel 5: CRUD Periode dengan Template AdminLTE](https://mirzayogy.github.io/blog/2026-10-13-laravel-simtikum-5/) |

## Goal
Replace the stand-in user with real accounts, and guide new users through a first-run stepper.

## Jobs

| Code | Job | Status | Completed | Proof |
|---|---|---|---|---|
| J1 | Register, login, logout with throttling | ✅ Done | 2026-10-28 | [`AuthController`](https://github.com/dzakwannajmi/PBO2_LARAVEL_5C/tree/feature/crud-ui/app/Http/Controllers/AuthController.php), [`views/auth`](https://github.com/dzakwannajmi/PBO2_LARAVEL_5C/tree/feature/crud-ui/resources/views/auth) |
| J2 | Profile page (name, email, bio, timezone, goal, password) | ✅ Done | 2026-10-28 | [`ProfileController`](https://github.com/dzakwannajmi/PBO2_LARAVEL_5C/tree/feature/crud-ui/app/Http/Controllers/ProfileController.php) |
| J3 | Onboarding stepper and starter habits | ✅ Done | 2026-10-28 | [`OnboardingController`](https://github.com/dzakwannajmi/PBO2_LARAVEL_5C/tree/feature/crud-ui/app/Http/Controllers/OnboardingController.php), [`onboarding.blade.php`](https://github.com/dzakwannajmi/PBO2_LARAVEL_5C/tree/feature/crud-ui/resources/views/onboarding.blade.php) |
| J4 | Data scoping: every user sees only their own rows | ✅ Done | 2026-10-28 | [`routes/web.php`](https://github.com/dzakwannajmi/PBO2_LARAVEL_5C/tree/feature/crud-ui/routes/web.php) |

**Status legend:** ⏳ Planned · 🚧 In progress · ✅ Done · ⛔ Blocked

---

### J1: Register, login, logout
- **What:** Custom Blade auth. Register creates the user and its one-to-one `Profile`. Login is limited to 5 attempts per email and IP. The temporary `CurrentUser` class was removed.
- **Verified:** `AuthTest`: guests are redirected, wrong credentials and throttling, duplicate email, registration creates a profile.

### J2: Profile page
- **What:** Edit name, email, bio, timezone, daily goal, and optionally the password. The timezone decides when a new day starts for streaks (`User::today()`).

### J3: Onboarding stepper
- **What:** New users are redirected by the `onboarded` middleware until they finish 4 steps: profile, starter habits, reminder time, finish. Picked starters become real habits (with a reminder when a time is set). Adds `profiles.onboarded_at` (new migration).
- **Verified:** `OnboardingTest`, plus a browser run: register, four steps, dashboard shows the chosen habits.

### J4: Data scoping
- Habits, logs, moods, journals, and reminders are checked against the signed-in user (404 otherwise). Categories, tags, and achievements are shared master data.

## Next
P04: dashboard, calendar, and streaks.
