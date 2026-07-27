@props([
    'searchModel' => 'search',
    'placeholder' => __('Search...'),
])

<div {{ $attributes->merge(['class' => 'flex flex-col sm:flex-row items-center justify-between gap-4']) }}>
    <div class="w-full sm:w-100">
        <flux:input
            :wire:model.live.debounce.300ms="$searchModel"
            icon="magnifying-glass"
            :placeholder="$placeholder"
            clearable
        />
    </div>
    @if ($slot->isNotEmpty())
        <div class="flex items-center gap-2 w-full sm:w-auto">
            {{ $slot }}
        </div>
    @endif
</div>
