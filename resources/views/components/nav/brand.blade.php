@props([
    'href' => route('home'),
])

<a href="{{ $href }}" wire:navigate {{ $attributes->merge(['class' => 'flex items-center gap-2 font-bold text-xl tracking-tight text-zinc-900 dark:text-white']) }}>
    <x-app-logo-icon class="size-6 text-purple-600 dark:text-purple-400" />
    <span>Eventive</span>
</a>
