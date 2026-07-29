@props([
    'event',
])

<div {{ $attributes->merge(['class' => 'shrink-0 w-80 sm:w-96 snap-start flex flex-col justify-between rounded-2xl overflow-hidden border border-zinc-200 dark:border-zinc-800/80 bg-white dark:bg-zinc-900 shadow-sm hover:shadow-xl transition-all duration-300 group min-w-0']) }}>
    <div class="space-y-4">
        <!-- 16:9 Banner Image Header with Overlays -->
        <div class="relative overflow-hidden aspect-[16/9] bg-zinc-900 border-b border-zinc-100 dark:border-zinc-800">
            <!-- Floating Date Badge Overlay (Top Left) -->
            <div class="absolute top-3 left-3 z-10 bg-white/95 dark:bg-zinc-900/95 backdrop-blur-md border border-white/20 dark:border-zinc-700/50 shadow-md rounded-xl px-3 py-1.5 text-center min-w-14 select-none">
                <span class="block text-[10px] font-black uppercase text-purple-600 dark:text-purple-400 tracking-wider leading-tight">
                    {{ $event->starts_at->format('M') }}
                </span>
                <span class="block text-lg font-extrabold text-zinc-900 dark:text-white leading-none mt-0.5">
                    {{ $event->starts_at->format('d') }}
                </span>
            </div>

            <!-- Availability Status Badge (Top Right) -->
            <div class="absolute top-3 right-3 z-20 select-none">
                @if ($event->isSoldOut())
                    <flux:badge size="sm" color="red" class="rounded-full border border-white/20 bg-red-600/90 backdrop-blur-sm shadow-lg px-3 py-1 font-semibold text-white">
                        {{ __('Sold Out') }}
                    </flux:badge>
                @elseif ($event->remainingTickets() <= 5)
                    <flux:badge size="sm" color="amber" class="rounded-full border border-white/20 bg-amber-500/90 backdrop-blur-sm shadow-lg px-3 py-1 font-semibold text-white">
                        {{ __(':count left', ['count' => $event->remainingTickets()]) }}
                    </flux:badge>
                @else
                    <flux:badge size="sm" color="emerald" class="rounded-full border border-white/20 bg-emerald-600/90 backdrop-blur-sm shadow-lg px-3 py-1 font-semibold text-white">
                        {{ __(':count available', ['count' => $event->remainingTickets()]) }}
                    </flux:badge>
                @endif
            </div>

            <!-- Banner Poster Image -->
            <a href="{{ route('events.show', $event) }}" wire:navigate class="block w-full h-full">
                @if ($event->banner_path)
                    <img src="{{ $event->bannerUrl() }}" alt="{{ $event->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
                @else
                    <div class="w-full h-full bg-gradient-to-br from-purple-600 via-indigo-700 to-zinc-900 flex items-center justify-center text-white/80 group-hover:scale-105 transition-transform duration-500">
                        <flux:icon name="calendar" class="size-12 text-purple-200/60" />
                    </div>
                @endif
            </a>

            <!-- Quick Lightbox Trigger -->
            @if ($event->banner_path)
                <button
                    type="button"
                    x-data
                    x-on:click.stop="$dispatch('preview-image', { src: '{{ $event->bannerUrl() }}', alt: '{{ e($event->title) }}' })"
                    class="absolute bottom-2.5 right-2.5 z-10 bg-black/60 hover:bg-black text-white p-1.5 rounded-lg backdrop-blur-xs transition opacity-0 group-hover:opacity-100 shadow-sm"
                    title="{{ __('Preview banner image') }}"
                >
                    <flux:icon name="magnifying-glass-plus" class="size-4" />
                </button>
            @endif
        </div>

        <!-- Card Content Body -->
        <div class="px-5 space-y-3">
            <div class="flex items-center justify-between gap-2">
                <flux:badge size="sm" color="purple" icon="user" class="max-w-[80%] shrink-0">
                    <span class="truncate inline-block max-w-[170px]">{{ $event->organizer->name ?? __('Unknown') }}</span>
                </flux:badge>

                <span class="text-[11px] font-medium text-zinc-500 dark:text-zinc-400">
                    {{ $event->starts_at->format('g:i A') }}
                </span>
            </div>

            <div class="space-y-1">
                <flux:heading size="lg" class="line-clamp-1 group-hover:text-purple-600 dark:group-hover:text-purple-400 transition">
                    <a href="{{ route('events.show', $event) }}" wire:navigate class="hover:underline">
                        {{ $event->title }}
                    </a>
                </flux:heading>

                <p class="text-xs text-zinc-600 dark:text-zinc-400 line-clamp-2 leading-relaxed">
                    {{ strip_tags($event->description) }}
                </p>
            </div>
        </div>
    </div>

    <!-- Card Footer -->
    <div class="px-5 pb-5 mt-4 pt-3 border-t border-zinc-100 dark:border-zinc-800/80 space-y-3">
        <div class="flex items-center gap-1.5 text-xs text-zinc-600 dark:text-zinc-400 font-medium truncate">
            <flux:icon name="map-pin" class="size-4 text-purple-600 dark:text-purple-400 shrink-0" />
            <span class="truncate">{{ $event->location }}</span>
        </div>

        <flux:button variant="primary" size="sm" class="w-full bg-purple-600 hover:bg-purple-500 text-white dark:bg-white dark:hover:bg-zinc-100 dark:text-zinc-900 font-semibold transition" :href="route('events.show', $event)" wire:navigate>
            {{ __('View Details') }}
        </flux:button>
    </div>
</div>
