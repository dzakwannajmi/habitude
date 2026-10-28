<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Habit;
use App\Models\Tag;
use App\Models\User;
use App\Services\AchievementService;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database. Demo logins (password is "password"): test@example.com (user), admin@example.com (admin).
     */
    public function run(): void
    {
        $this->call([CategorySeeder::class, AchievementSeeder::class]);

        mt_srand(42); // the same demo data on every run

        $admin = User::factory()->admin()->create(['name' => 'Admin', 'email' => 'admin@example.com']);
        $admin->profile()->create(['timezone' => 'Asia/Makassar', 'onboarded_at' => now()]);

        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
        $user->profile()->create([
            'bio' => 'Building small habits, one day at a time.',
            'timezone' => 'Asia/Makassar',
            'daily_goal' => 4,
            'onboarded_at' => now(),
        ]);

        $tags = collect(['morning', 'evening', 'quick', 'outdoor'])
            ->map(fn (string $name) => Tag::firstOrCreate(['name' => $name]));

        $today = $user->today();
        $categories = Category::all()->keyBy('slug');

        foreach (config('habitude.starters') as $slug => $starters) {
            foreach ($starters as [$name, $target, $unit]) {

                /** @var Habit $habit */
                $habit = $user->habits()->create([
                    'category_id' => $categories[$slug]->id,
                    'name' => $name,
                    'description' => $categories[$slug]->name.' starter habit.',
                    'target_count' => $target,
                    'unit' => $unit,
                ]);

                $habit->forceFill(['created_at' => $today->subDays(46)])->save(); // the demo history starts 45 days ago
                $habit->tags()->attach($tags->random(2)->pluck('id'));

                // Every habit gets a reminder; the first three also get a second one.
                $habit->reminders()->create(['remind_at' => sprintf('%02d:00', mt_rand(6, 21)), 'days_of_week' => [1, 2, 3, 4, 5, 6, 7]]);

                // The first three habits keep a 9-day streak up to yesterday, today is left open.
                $streaky = $habit->id <= 3;
                foreach (range(1, 45) as $daysAgo) {
                    $roll = mt_rand(1, 100);
                    $value = $streaky && $daysAgo <= 9 ? $target
                        : ($roll <= 70 ? $target : ($roll <= 85 ? max(1, intdiv($target, 2)) : 0));

                    if ($value > 0) {
                        $habit->logs()->create(['logged_date' => $today->subDays($daysAgo)->toDateString(), 'value' => $value]);
                    }
                }
            }
        }

        foreach (range(0, 24) as $daysAgo) {
            $date = $today->subDays($daysAgo)->toDateString();
            $mood = $user->moodEntries()->create([
                'entry_date' => $date,
                'mood_level' => mt_rand(2, 5),
                'note' => $daysAgo % 4 === 0 ? 'A calm, productive day.' : null,
            ]);

            if ($daysAgo % 3 === 0) {
                $user->journalEntries()->create([
                    'mood_entry_id' => $mood->id,
                    'entry_date' => $date,
                    'title' => 'Notes from '.$today->subDays($daysAgo)->format('j M'),
                    'content' => "Kept my habits going today.\nSmall steps add up, and the streak feels good.",
                ]);
            }
        }

        app(AchievementService::class)->award($user);
    }
}
