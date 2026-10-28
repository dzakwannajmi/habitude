@props(['editUrl' => null, 'deleteId'])

<div class="sw" data-swipe-row>
    <div class="sw-actions">
        @if ($editUrl)
            <a href="{{ $editUrl }}" class="sw-action sw-edit"><span><x-icon name="pencil" /> Edit</span></a>
        @endif
        <button type="button" class="sw-action sw-delete" data-modal-open="{{ $deleteId }}"><span><x-icon name="trash" /> Delete</span></button>
    </div>
    <div {{ $attributes->class('sw-surface') }}>
        {{ $slot }}
        <button type="button" class="btn btn-ghost btn-icon sw-more mr-2" data-swipe-toggle data-no-swipe aria-expanded="false" aria-label="Show actions">
            <x-icon name="ellipsis" />
        </button>
    </div>
</div>
