<?php

return [
    // Release stage shown as a badge next to the logo. Use "production" to hide the badge and the preview banner.
    'stage' => env('HABITUDE_STAGE', 'beta'),

    // Where beta testers can report problems.
    'feedback_url' => env('HABITUDE_FEEDBACK_URL', 'https://github.com/dzakwannajmi/PBO2_LARAVEL_5C/issues'),

    // Timezones offered in the profile and onboarding forms.
    'timezones' => [
        'Asia/Jakarta' => 'WIB (Jakarta)',
        'Asia/Makassar' => 'WITA (Makassar)',
        'Asia/Jayapura' => 'WIT (Jayapura)',
        'UTC' => 'UTC',
    ],

    // Units offered when creating a habit.
    'units' => ['times', 'glasses', 'pages', 'minutes', 'hours', 'steps', 'km', 'sessions'],

    // Units that get a timer in the daily list.
    'timed_units' => ['minutes'],

    // Mood scale labels, 1 to 5.
    'moods' => [1 => 'Awful', 2 => 'Bad', 3 => 'Okay', 4 => 'Good', 5 => 'Great'],

    // Achievement criteria types: key => label.
    'criteria' => [
        'logs' => 'Completed check-ins',
        'streak' => 'Day streak',
        'moods' => 'Mood check-ins',
        'journals' => 'Journal entries',
        'habits' => 'Habits created',
    ],

    // Starter habits offered during onboarding, per category slug: [name, daily target, unit].
    'starters' => [
        'health-fitness' => [['Workout', 1, 'sessions'], ['Walk', 8000, 'steps']],
        'mindfulness' => [['Meditate', 10, 'minutes'], ['Gratitude note', 1, 'times']],
        'productivity' => [['Deep work', 60, 'minutes'], ['Plan the day', 1, 'times']],
        'better-sleep' => [['Sleep before 11 pm', 1, 'times'], ['No screens in bed', 1, 'times']],
        'stay-hydrated' => [['Drink water', 8, 'glasses']],
        'read-more' => [['Read', 10, 'pages']],
        'social-connections' => [['Message a friend', 1, 'times']],
        'self-care' => [['Skincare', 1, 'times'], ['Stretch', 5, 'minutes']],
    ],
];
