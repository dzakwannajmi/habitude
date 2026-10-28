<?php

namespace Tests\Feature;

use App\Models\Achievement;
use App\Models\Category;
use App\Models\Habit;
use App\Models\JournalEntry;
use App\Models\MoodEntry;
use App\Models\Reminder;
use App\Models\User;
use App\Services\CalendarService;
use App\Services\StreakService;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TrackingTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        CarbonImmutable::setTestNow('2026-10-28 12:00:00');
        $this->user = $this->signIn();
    }

    protected function tearDown(): void
    {
        CarbonImmutable::setTestNow();
        parent::tearDown();
    }

    private function habit(array $attrs = []): Habit
    {
        return Habit::factory()->create($attrs + ['user_id' => $this->user->id, 'target_count' => 1, 'unit' => 'times', 'is_active' => true]);
    }

    private function logDays(Habit $habit, array $daysAgo, ?int $value = null): void
    {
        foreach ($daysAgo as $d) {
            $habit->logs()->create(['logged_date' => CarbonImmutable::now()->subDays($d)->toDateString(), 'value' => $value ?? $habit->target_count]);
        }
    }

    // ---- check-ins ----

    public function test_set_mode_completes_and_clears_a_day(): void
    {
        $habit = $this->habit(['target_count' => 8, 'unit' => 'glasses']);

        $this->postJson("/habits/{$habit->id}/log", ['mode' => 'set', 'value' => 8])
            ->assertOk()->assertJson(['value' => 8, 'completed' => true, 'done' => 1, 'total' => 1, 'habit_streak' => 1]);

        $this->assertDatabaseCount('habit_logs', 1);

        $this->postJson("/habits/{$habit->id}/log", ['mode' => 'set', 'value' => 0])
            ->assertOk()->assertJson(['value' => 0, 'completed' => false, 'done' => 0]);

        $this->assertDatabaseCount('habit_logs', 0);
    }

    public function test_add_mode_accumulates_progress_until_target(): void
    {
        $habit = $this->habit(['target_count' => 3]);

        $this->postJson("/habits/{$habit->id}/log", ['mode' => 'add', 'value' => 1])->assertJson(['value' => 1, 'completed' => false]);
        $this->postJson("/habits/{$habit->id}/log", ['mode' => 'add', 'value' => 2])->assertJson(['value' => 3, 'completed' => true]);

        $this->assertDatabaseCount('habit_logs', 1); // one row per habit per day
    }

    public function test_toggle_mode_flips_completion(): void
    {
        $habit = $this->habit();

        $this->postJson("/habits/{$habit->id}/log", ['mode' => 'toggle'])->assertJson(['completed' => true]);
        $this->postJson("/habits/{$habit->id}/log", ['mode' => 'toggle'])->assertJson(['completed' => false]);
    }

    public function test_past_days_can_be_logged_but_not_future_days(): void
    {
        $habit = $this->habit();

        $this->postJson("/habits/{$habit->id}/log", ['mode' => 'set', 'value' => 1, 'date' => '2026-10-20'])->assertOk()->assertJson(['date' => '2026-10-20']);
        $this->postJson("/habits/{$habit->id}/log", ['mode' => 'set', 'value' => 1, 'date' => '2026-10-29'])->assertStatus(422);
        $this->postJson("/habits/{$habit->id}/log", ['mode' => 'nope'])->assertStatus(422);
    }

    public function test_checking_in_another_users_habit_is_not_found(): void
    {
        $habit = Habit::factory()->create(['user_id' => User::factory()->create()->id]);

        $this->postJson("/habits/{$habit->id}/log", ['mode' => 'toggle'])->assertNotFound();
    }

    // ---- streaks ----

    public function test_overall_streak_counts_days_with_any_completed_habit(): void
    {
        $a = $this->habit();
        $b = $this->habit();
        $this->logDays($a, [0, 1, 2]);
        $this->logDays($b, [3, 4, 6]); // gap on day 5

        $streak = app(StreakService::class)->overall($this->user, $this->user->today());

        $this->assertSame(5, $streak['current']);
        $this->assertSame(5, $streak['best']);
    }

    public function test_streak_survives_an_open_today_but_breaks_after_a_missed_day(): void
    {
        $habit = $this->habit();
        $this->logDays($habit, [1, 2, 3]); // today still open

        $service = app(StreakService::class);
        $this->assertSame(3, $service->habitStreak($habit, $this->user->today()));

        $habit->logs()->delete();
        $this->logDays($habit, [2, 3]); // yesterday missed
        $this->assertSame(0, $service->habitStreak($habit, $this->user->today()));
        $this->assertSame(2, $service->overall($this->user, $this->user->today())['best']);
    }

    public function test_partial_progress_does_not_count_toward_a_streak(): void
    {
        $habit = $this->habit(['target_count' => 5]);
        $this->logDays($habit, [0, 1], 2);

        $this->assertSame(0, app(StreakService::class)->habitStreak($habit, $this->user->today()));
    }

    // ---- dashboard ----

    public function test_dashboard_clamps_future_dates_and_ignores_bad_input(): void
    {
        $this->habit();

        $this->get('/?date=2026-12-25')->assertOk()->assertSee('Today, 28 October 2026');
        $this->get('/?date=garbage&month=2026-99')->assertOk()->assertSee('Today, 28 October 2026');
        $this->get('/?date=2026-10-20')->assertOk()->assertSee('past day');
    }

    public function test_dashboard_shows_category_progress_through_habits(): void
    {
        $category = Category::factory()->create(['name' => 'Stay Hydrated']);
        $habit = $this->habit(['category_id' => $category->id]);
        $this->logDays($habit, [0, 1, 2]);

        $this->get('/')->assertOk()->assertSee('Stay Hydrated');
        $this->assertSame(3, $category->habitLogs()->where('habits.user_id', $this->user->id)->count());
    }

    public function test_paused_habits_are_hidden_from_the_dashboard(): void
    {
        $this->habit(['name' => 'Visible habit']);
        $this->habit(['name' => 'Paused habit', 'is_active' => false]);

        $this->get('/')->assertSee('Visible habit')->assertDontSee('Paused habit');
    }

    public function test_calendar_counts_back_filled_days_even_for_new_habits(): void
    {
        $habit = $this->habit(); // created "today" in the test clock
        $this->logDays($habit, [5]);

        $weeks = app(CalendarService::class)->month($this->user, CarbonImmutable::now()->startOfMonth(), $this->user->today());
        $day = collect($weeks)->flatten(1)->firstWhere('date', CarbonImmutable::now()->subDays(5)->toDateString());
        $unlogged = collect($weeks)->flatten(1)->firstWhere('date', CarbonImmutable::now()->subDays(6)->toDateString());

        $this->assertSame([1, 1, 1], [$day['total'], $day['done'], $day['ratio']]);
        $this->assertSame(0, $unlogged['total']); // the habit did not exist yet
    }

    // ---- achievements ----

    public function test_achievements_unlock_from_check_ins(): void
    {
        $unlock = Achievement::create(['name' => 'Three in a row', 'criteria' => 'streak:3']);
        $habit = $this->habit();
        $this->logDays($habit, [1, 2]);

        $this->postJson("/habits/{$habit->id}/log", ['mode' => 'toggle'])
            ->assertJsonPath('earned.0.name', 'Three in a row');

        $this->assertTrue($this->user->achievements()->whereKey($unlock->id)->exists());
        $this->assertNotNull($this->user->achievements()->first()->pivot->earned_at);

        // earned once, never twice
        $this->postJson("/habits/{$habit->id}/log", ['mode' => 'toggle']);
        $this->postJson("/habits/{$habit->id}/log", ['mode' => 'toggle'])->assertJsonPath('earned', []);
        $this->assertSame(1, $this->user->achievements()->count());
    }

    public function test_achievement_crud_composes_criteria(): void
    {
        $this->post('/achievements', ['name' => 'Page turner', 'criteria_type' => 'journals', 'criteria_threshold' => 5])->assertRedirect('/achievements');
        $a = Achievement::firstWhere('name', 'Page turner');
        $this->assertSame('journals:5', $a->criteria);

        $this->post('/achievements', ['name' => 'Page turner', 'criteria_type' => 'journals', 'criteria_threshold' => 5])->assertSessionHasErrors('name');
        $this->post('/achievements', ['name' => 'X', 'criteria_type' => 'bogus', 'criteria_threshold' => 0])->assertSessionHasErrors(['criteria_type', 'criteria_threshold']);

        $this->get("/achievements/{$a->id}/edit")->assertOk();
        $this->put("/achievements/{$a->id}", ['name' => 'Page turner', 'criteria_type' => 'logs', 'criteria_threshold' => 20])->assertRedirect('/achievements');
        $this->assertSame('logs:20', $a->fresh()->criteria);

        $this->user->achievements()->attach($a, ['earned_at' => now()]);
        $this->delete("/achievements/{$a->id}")->assertRedirect('/achievements');
        $this->assertDatabaseMissing('achievement_user', ['achievement_id' => $a->id]);
    }

    // ---- mood and journal ----

    public function test_mood_is_one_per_day_and_updates_in_place(): void
    {
        $this->postJson('/mood', ['mood_level' => 3])->assertOk()->assertJson(['label' => 'Okay']);
        $this->postJson('/mood', ['mood_level' => 5, 'note' => 'Better'])->assertJson(['label' => 'Great']);

        $this->assertDatabaseCount('mood_entries', 1);
        $this->assertSame(5, MoodEntry::first()->mood_level);

        $this->postJson('/mood', ['mood_level' => 9])->assertStatus(422);
        $this->postJson('/mood', ['mood_level' => 3, 'entry_date' => '2026-10-29'])->assertStatus(422);
    }

    public function test_journal_links_to_the_mood_of_the_same_day_only_once(): void
    {
        $mood = $this->user->moodEntries()->create(['entry_date' => '2026-10-28', 'mood_level' => 4]);

        $this->post('/journal', ['entry_date' => '2026-10-28', 'title' => 'First', 'content' => 'Hello'])->assertRedirect('/journal');
        $this->post('/journal', ['entry_date' => '2026-10-28', 'title' => 'Second', 'content' => 'Again']);
        $this->post('/journal', ['entry_date' => '2026-10-27', 'title' => 'Other day', 'content' => 'No mood']);

        $this->assertSame($mood->id, JournalEntry::firstWhere('title', 'First')->mood_entry_id);
        $this->assertNull(JournalEntry::firstWhere('title', 'Second')->mood_entry_id); // one-to-one: mood already has a journal
        $this->assertNull(JournalEntry::firstWhere('title', 'Other day')->mood_entry_id);
        $this->assertSame('First', $mood->journalEntry->title);
    }

    public function test_mood_saved_later_picks_up_the_days_journal(): void
    {
        $journal = $this->user->journalEntries()->create(['entry_date' => '2026-10-28', 'title' => 'Early', 'content' => 'x']);

        $this->postJson('/mood', ['mood_level' => 2]);

        $this->assertNotNull($journal->fresh()->mood_entry_id);
    }

    public function test_deleting_a_mood_keeps_the_journal(): void
    {
        $mood = $this->user->moodEntries()->create(['entry_date' => '2026-10-28', 'mood_level' => 4]);
        $journal = $this->user->journalEntries()->create(['mood_entry_id' => $mood->id, 'entry_date' => '2026-10-28', 'title' => 'Keep me', 'content' => 'x']);

        $this->delete("/mood/{$mood->id}")->assertRedirect('/mood');

        $this->assertModelMissing($mood);
        $this->assertNull($journal->fresh()->mood_entry_id);
    }

    public function test_journal_validation_update_delete_and_ownership(): void
    {
        $this->post('/journal', [])->assertSessionHasErrors(['entry_date', 'title', 'content']);
        $this->post('/journal', ['entry_date' => '2026-11-30', 'title' => 't', 'content' => 'c'])->assertSessionHasErrors('entry_date');

        $entry = $this->user->journalEntries()->create(['entry_date' => '2026-10-28', 'title' => 'Mine', 'content' => 'x']);
        $this->get("/journal/{$entry->id}/edit")->assertOk();
        $this->put("/journal/{$entry->id}", ['entry_date' => '2026-10-28', 'title' => 'Changed', 'content' => 'y'])->assertRedirect('/journal');
        $this->assertSame('Changed', $entry->fresh()->title);

        $foreign = JournalEntry::create(['user_id' => User::factory()->create()->id, 'entry_date' => '2026-10-28', 'title' => 'Theirs', 'content' => 'x']);
        $this->get("/journal/{$foreign->id}/edit")->assertNotFound();
        $this->delete("/journal/{$foreign->id}")->assertNotFound();

        $this->delete("/journal/{$entry->id}")->assertRedirect('/journal');
        $this->assertModelMissing($entry);
    }

    public function test_journal_and_habit_views_switch_between_list_and_grid(): void
    {
        $this->habit(['name' => 'Walk']);
        $this->user->journalEntries()->create(['entry_date' => '2026-10-28', 'title' => 'Hello there', 'content' => 'x']);

        $this->get('/habits?view=grid&group=none')->assertOk()->assertSee('Walk')->assertSee('tile', false);
        $this->get('/habits?view=list&group=status')->assertOk()->assertSee('data-swipe-row', false);
        $this->get('/journal?view=grid')->assertOk()->assertSee('Hello there');
    }

    // ---- reminders ----

    public function test_reminders_can_be_added_toggled_and_deleted(): void
    {
        $habit = $this->habit();

        $this->post("/habits/{$habit->id}/reminders", [])->assertSessionHasErrors(['remind_at', 'days_of_week']);
        $this->post("/habits/{$habit->id}/reminders", ['remind_at' => '07:15', 'days_of_week' => [5, 1, 1]])->assertRedirect("/habits/{$habit->id}/edit");

        $reminder = $habit->reminders()->first();
        $this->assertSame([1, 5], $reminder->days_of_week);

        $this->get("/habits/{$habit->id}/edit")->assertOk()->assertSee('07:15')->assertSee('Mon, Fri');

        $this->patch("/reminders/{$reminder->id}", ['is_enabled' => 0]);
        $this->assertFalse($reminder->fresh()->is_enabled);

        $this->delete("/reminders/{$reminder->id}");
        $this->assertModelMissing($reminder);
    }

    public function test_reminders_of_other_users_are_off_limits(): void
    {
        $foreign = Reminder::factory()->create(['habit_id' => Habit::factory()->create(['user_id' => User::factory()->create()->id])->id]);

        $this->patch("/reminders/{$foreign->id}", ['is_enabled' => 0])->assertNotFound();
        $this->delete("/reminders/{$foreign->id}")->assertNotFound();
        $this->post("/habits/{$foreign->habit_id}/reminders", ['remind_at' => '08:00', 'days_of_week' => [1]])->assertNotFound();
    }

    public function test_dashboard_lists_todays_reminders_by_weekday(): void
    {
        $habit = $this->habit(['name' => 'Stretch']);
        $habit->reminders()->create(['remind_at' => '06:30', 'days_of_week' => [3]]); // Wednesday = 28 Oct 2026
        $habit->reminders()->create(['remind_at' => '19:45', 'days_of_week' => [1]]);

        $this->get('/')->assertSee('06:30')->assertDontSee('19:45');
    }

    // ---- profile ----

    public function test_profile_can_be_updated_including_password(): void
    {
        $this->put('/profile', ['name' => 'New Name', 'email' => 'new@example.com', 'timezone' => 'Asia/Jakarta', 'daily_goal' => 5, 'bio' => 'Hi', 'password' => 'brand-new-pass', 'password_confirmation' => 'brand-new-pass'])
            ->assertRedirect('/profile');

        $user = $this->user->fresh();
        $this->assertSame('New Name', $user->name);
        $this->assertSame('Asia/Jakarta', $user->profile->timezone);
        $this->assertTrue(\Hash::check('brand-new-pass', $user->password));
    }

    public function test_profile_rejects_duplicate_email_and_bad_timezone(): void
    {
        User::factory()->create(['email' => 'taken@example.com']);

        $this->put('/profile', ['name' => 'A', 'email' => 'taken@example.com', 'timezone' => 'Mars/Base', 'daily_goal' => 3])
            ->assertSessionHasErrors(['email', 'timezone']);

        $this->put('/profile', ['name' => 'A', 'email' => $this->user->email, 'timezone' => 'UTC', 'daily_goal' => 3])
            ->assertSessionDoesntHaveErrors();
    }
}
