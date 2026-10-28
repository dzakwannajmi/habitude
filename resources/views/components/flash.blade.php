@if (session('success'))
    <div role="status" class="mb-4 flex items-center gap-2 rounded-xl border border-mist/60 bg-fog px-4 py-3 text-body-sm text-ink">
        <x-icon name="circle-check" class="size-4 shrink-0" />
        {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div role="alert" class="mb-4 flex items-center gap-2 rounded-xl border border-ember bg-paper px-4 py-3 text-body-sm text-ink">
        <x-icon name="circle-alert" class="size-4 shrink-0 text-ember" />
        {{ session('error') }}
    </div>
@endif
