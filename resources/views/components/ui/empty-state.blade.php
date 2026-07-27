@props([
    'icon' => 'calendar',
    'title' => __('No results found'),
    'description' => null,
])

<div {{ $attributes->merge(['class' => 'text-center py-16 bg-white dark:bg-zinc-800/50 rounded-xl border border-zinc-200 dark:border-zinc-700/60 p-8 space-y-4']) }}>
    <flux:icon :name="$icon" class="mx-auto size-12 text-zinc-400" />
    <flux:heading size="lg">{{ $title }}</flux:heading>
    @if ($description)
        <p class="text-sm text-zinc-500 max-w-md mx-auto">
            {{ $description }}
        </p>
    @endif
    @if (isset($actions))
        <div class="pt-2 flex justify-center gap-3">
            {{ $actions }}
        </div>
    @endif
</div>
