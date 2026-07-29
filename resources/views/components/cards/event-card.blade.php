@props([
    'event',
])

<flux:card {{ $attributes->merge(['class' => 'flex flex-col justify-between hover:shadow-md transition-shadow min-w-0 overflow-hidden']) }}>
    <div class="space-y-4 min-w-0">
        <!-- 16:9 Banner Image Header -->
        <div class="relative overflow-hidden rounded-lg aspect-[16/9] bg-zinc-900 border border-zinc-200 dark:border-zinc-800 group">
            @if ($event->banner_path)
                <a href="{{ route('events.show', $event) }}" wire:navigate class="block w-full h-full">
                    <img src="{{ $event->bannerUrl() }}" alt="{{ $event->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" />
                </a>
                <button
                    type="button"
                    x-data
                    x-on:click.stop="$dispatch('preview-image', { src: '{{ $event->bannerUrl() }}', alt: '{{ e($event->title) }}' })"
                    class="absolute top-2.5 right-2.5 bg-black/60 hover:bg-black text-white p-1.5 rounded-lg backdrop-blur-xs transition opacity-0 group-hover:opacity-100 shadow-sm"
                    title="{{ __('Preview full image') }}"
                >
                    <flux:icon name="magnifying-glass-plus" class="size-4" />
                </button>
            @else
                <a href="{{ route('events.show', $event) }}" wire:navigate class="block w-full h-full">
                    <div class="w-full h-full bg-gradient-to-br from-purple-600 via-indigo-700 to-zinc-900 flex items-center justify-center text-white/80 group-hover:scale-105 transition-transform duration-300">
                        <flux:icon name="calendar" class="size-10 text-purple-200/60" />
                    </div>
                </a>
            @endif
        </div>

        <div class="flex flex-wrap items-center justify-between gap-2 min-w-0">
            <flux:badge size="sm" color="purple" icon="user" class="max-w-[70%] shrink-0">
                <span class="truncate inline-block max-w-[130px] sm:max-w-[170px]">{{ $event->organizer->name ?? __('Unknown') }}</span>
            </flux:badge>

            @if ($event->isSoldOut())
                <flux:badge size="sm" color="red" class="shrink-0">{{ __('Sold Out') }}</flux:badge>
            @elseif ($event->remainingTickets() <= 5)
                <flux:badge size="sm" color="amber" class="shrink-0">{{ __(':count left', ['count' => $event->remainingTickets()]) }}</flux:badge>
            @else
                <flux:badge size="sm" color="emerald" class="shrink-0">{{ __(':count available', ['count' => $event->remainingTickets()]) }}</flux:badge>
            @endif
        </div>

        <div class="space-y-2">
            <flux:heading size="lg">
                <a href="{{ route('events.show', $event) }}" wire:navigate class="hover:underline">
                    {{ $event->title }}
                </a>
            </flux:heading>
            <p class="text-sm text-zinc-600 dark:text-zinc-400 line-clamp-2">
                {{ strip_tags($event->description) }}
            </p>
        </div>
    </div>

    <div class="mt-6 pt-4 border-t border-zinc-200 dark:border-zinc-800 space-y-3">
        <div class="space-y-1.5 text-xs text-zinc-700 dark:text-zinc-300 font-medium">
            <div class="flex items-center gap-1.5">
                <flux:icon name="calendar" class="size-4 text-purple-700 dark:text-purple-400 shrink-0" />
                <span>{{ $event->starts_at->format('M j, Y \a\t g:i A') }}</span>
            </div>
            <div class="flex items-center gap-1.5">
                <flux:icon name="map-pin" class="size-4 text-purple-700 dark:text-purple-400 shrink-0" />
                <span class="truncate">{{ $event->location }}</span>
            </div>
        </div>

        <flux:button variant="primary" class="w-full" :href="route('events.show', $event)" wire:navigate>
            {{ __('View Details') }}
        </flux:button>
    </div>
</flux:card>
