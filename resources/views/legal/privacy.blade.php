<x-auth-layout title="Privacy Policy" :wide="true">
    <div class="w-full space-y-4 text-left">
        <h1 class="text-heading-sm">Privacy Policy</h1>
        <p class="text-caption text-pewter">Habitude is a beta preview built for a university course. Last updated 28 October 2026.</p>
        <section class="space-y-2 text-body-sm text-graphite">
            <h2 class="text-body font-semibold text-ink">What we store</h2>
            <p>Your name, email address and a hashed password; your timezone and daily goal; and the habits, check-ins, moods, journal entries and reminders you create.</p>
            <h2 class="pt-2 text-body font-semibold text-ink">How it is used</h2>
            <p>Only to run the app for you: to show your streaks and calendar, and to send reminder emails if you leave them on. Your data is not sold or shared.</p>
            <h2 class="pt-2 text-body font-semibold text-ink">Cookies and local storage</h2>
            <p>A session cookie keeps you signed in. Your browser also remembers interface choices such as the sidebar and how many habits to show.</p>
            <h2 class="pt-2 text-body font-semibold text-ink">Your choices</h2>
            <p>You can edit your profile at any time and turn reminder emails off there. To delete your account and all of its data, ask an administrator.</p>
        </section>
        <a href="{{ url()->previous() === url()->current() ? route('login') : url()->previous() }}" class="btn btn-outline"><x-icon name="arrow-left" /> Back</a>
    </div>
</x-auth-layout>
