<div class="max-w-5xl mx-auto space-y-6">
    <!-- Back Navigation -->
    <div>
        <flux:button variant="subtle" icon="arrow-left" size="sm" :href="route('events.index')" wire:navigate>
            {{ __('Back to Events') }}
        </flux:button>
    </div>

    <!-- Top Header (Badges & Title) -->
    <div class="space-y-3">
        <div class="flex flex-wrap items-center gap-2">
            <flux:badge color="purple" icon="user" class="px-3 py-1 text-xs font-semibold">
                {{ __('Organized by :name', ['name' => $event->organizer->name ?? __('Unknown')]) }}
            </flux:badge>

            @if ($event->isSoldOut())
                <flux:badge color="red" class="px-3 py-1 text-xs font-semibold">{{ __('Sold Out') }}</flux:badge>
            @else
                <flux:badge color="emerald" class="px-3 py-1 text-xs font-semibold">{{ __(':count Tickets Left', ['count' => $event->remainingTickets()]) }}</flux:badge>
            @endif
        </div>

        <flux:heading size="xl" level="1" class="text-2xl sm:text-3xl font-bold tracking-tight text-zinc-900 dark:text-white">
            {{ $event->title }}
        </flux:heading>
    </div>

    <!-- Middle Section: Banner Image (Left) + Event Information Card (Right) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8 items-start">
        <!-- Banner Image Container (16:9 Widescreen) -->
        <div class="lg:col-span-2">
            <div class="relative w-full aspect-[16/9] rounded-2xl overflow-hidden border border-zinc-200 dark:border-zinc-800 shadow-sm bg-zinc-900">
                @if ($event->banner_path)
                    <img src="{{ $event->bannerUrl() }}" alt="{{ $event->title }}" class="w-full h-full object-cover" />
                @else
                    <div class="w-full h-full bg-gradient-to-br from-purple-600 via-indigo-700 to-zinc-900 flex flex-col items-center justify-center text-white/80 p-6 text-center">
                        <flux:icon name="calendar" class="size-16 text-purple-200/50 mb-2" />
                        <span class="text-sm font-semibold text-purple-100/90">{{ $event->title }}</span>
                    </div>
                @endif
            </div>
        </div>

        <!-- Right Column: Event Information Card -->
        <div class="lg:col-span-1">
            <x-events.event-info-card :event="$event" />
        </div>
    </div>

    <!-- Horizontal Separator -->
    <div class="pt-2">
        <flux:separator />
    </div>

    <!-- Bottom Section: About This Event -->
    <div class="space-y-4 pt-2">
        <flux:heading size="xl" level="2" class="text-xl font-bold text-zinc-900 dark:text-white">
            {{ __('About This Event') }}
        </flux:heading>

        <div class="prose dark:prose-invert max-w-none text-zinc-700 dark:text-zinc-300 leading-relaxed [&_img]:rounded-xl [&_img]:max-h-96 [&_img]:object-cover [&_img]:my-4 [&_img]:shadow-md">
            {!! $event->description !!}
        </div>
    </div>
</div>
