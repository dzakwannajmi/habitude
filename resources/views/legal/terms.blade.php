<x-auth-layout title="Terms of Service" :wide="true">
    <div class="w-full space-y-4 text-left">
        <h1 class="text-heading-sm">Terms of Service</h1>
        <p class="text-caption text-pewter">Habitude is a beta preview built for a university course. Last updated 28 October 2026.</p>
        <section class="space-y-2 text-body-sm text-graphite">
            <h2 class="text-body font-semibold text-ink">Using Habitude</h2>
            <p>Habitude helps you track habits, mood and journal entries. You are responsible for the content you enter and for keeping your password safe.</p>
            <h2 class="pt-2 text-body font-semibold text-ink">A beta, not a finished product</h2>
            <p>The service is a preview. Features can change, and data may be reset or deleted without notice. Do not store anything you cannot afford to lose.</p>
            <h2 class="pt-2 text-body font-semibold text-ink">Your content</h2>
            <p>Your habits, moods and journal entries belong to you. Administrators can see account details (name, email, activity counts) to run the service, but not what you wrote in your journal through the admin pages.</p>
            <h2 class="pt-2 text-body font-semibold text-ink">No warranty</h2>
            <p>The service is provided as is, without warranty of any kind.</p>
        </section>
        <a href="{{ url()->previous() === url()->current() ? route('login') : url()->previous() }}" class="btn btn-outline"><x-icon name="arrow-left" /> Back</a>
    </div>
</x-auth-layout>
