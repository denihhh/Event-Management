@props([
    'label',
    'title',
    'description',
    'icon',
    'iconColor' => 'text-purple-600 dark:text-purple-400',
    'buttonText',
    'href',
])

<flux:card {{ $attributes->merge(['class' => 'space-y-3 flex flex-col justify-between']) }}>
    <div class="space-y-3">
        <div class="flex items-center justify-between">
            <span class="text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">{{ $label }}</span>
            <flux:icon :name="$icon" class="size-5 {{ $iconColor }}" />
        </div>
        <flux:heading size="lg">{{ $title }}</flux:heading>
        <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ $description }}</p>
    </div>
    <div class="pt-2">
        <flux:button
            size="sm"
            variant="primary"
            :href="$href"
            wire:navigate
            class="w-full bg-purple-600 hover:bg-purple-500 text-white dark:bg-white dark:hover:bg-zinc-100 dark:text-zinc-900 font-medium"
        >
            {{ $buttonText }}
        </flux:button>
    </div>
</flux:card>
