<div class="space-y-16 pb-12">
    {{-- 1. Hero Section --}}
    <section class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-purple-900 via-indigo-950 to-zinc-950 text-white p-8 md:p-14 shadow-2xl border border-purple-800/30">
        {{-- Background Glow Accent --}}
        <div class="absolute -top-24 -right-24 size-96 rounded-full bg-purple-600/20 blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-24 -left-24 size-96 rounded-full bg-indigo-600/20 blur-3xl pointer-events-none"></div>

        <div class="relative z-10 max-w-3xl space-y-6">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 backdrop-blur-md border border-white/15 text-xs font-semibold text-purple-200">
                <flux:icon name="magnifying-glass" class="size-3.5 text-amber-400" />
                <span>{{ __('Discover & Host Extraordinary Events') }}</span>
            </div>

            <h1 class="text-4xl md:text-6xl font-extrabold tracking-tight text-white leading-tight">
                {{ __('Where Unforgettable Gatherings Begin.') }}
            </h1>

            <p class="text-base md:text-lg text-purple-100/80 leading-relaxed max-w-2xl">
                {{ __('Explore tech summits, marathons, food festivals, and concerts. Claim digital pass codes instantly or host your own audience with Eventive.') }}
            </p>

            {{-- Hero Search Form --}}
            <form wire:submit="performSearch" class="pt-2 flex flex-col sm:flex-row gap-3 max-w-xl">
                <div class="flex-1">
                    <flux:input
                        wire:model="search"
                        icon="magnifying-glass"
                        placeholder="{{ __('Search events, locations, or topics...') }}"
                        class="bg-white/10 dark:bg-zinc-900/60 border-white/20 dark:border-zinc-700 text-white placeholder-purple-200/60 shadow-lg rounded-xl"
                    />
                </div>
                <flux:button type="submit" variant="primary" class="shrink-0 bg-purple-600 hover:bg-purple-500 text-white font-semibold">
                    {{ __('Find Events') }}
                </flux:button>
            </form>

            {{-- Quick Tag Pills --}}
            <div class="flex flex-wrap items-center gap-2 pt-2 text-xs text-purple-200/70">
                <span class="font-medium text-purple-200/90">{{ __('Popular:') }}</span>
                <a href="{{ route('events.index', ['filter' => 'this_week']) }}" wire:navigate class="px-2.5 py-1 rounded-md bg-white/10 hover:bg-white/20 transition-colors text-white">
                    {{ __('This Week') }}
                </a>
                <a href="{{ route('events.index', ['filter' => 'upcoming']) }}" wire:navigate class="px-2.5 py-1 rounded-md bg-white/10 hover:bg-white/20 transition-colors text-white">
                    {{ __('Upcoming Summits') }}
                </a>
                <a href="{{ route('events.index', ['search' => 'Marathon']) }}" wire:navigate class="px-2.5 py-1 rounded-md bg-white/10 hover:bg-white/20 transition-colors text-white">
                    {{ __('Marathons') }}
                </a>
                <a href="{{ route('events.index', ['search' => 'Tech']) }}" wire:navigate class="px-2.5 py-1 rounded-md bg-white/10 hover:bg-white/20 transition-colors text-white">
                    {{ __('Tech & AI') }}
                </a>
            </div>
        </div>
    </section>

    {{-- 2. Platform Stats Banner --}}
    <section class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="p-6 rounded-2xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 shadow-xs dark:shadow-xl flex items-center gap-4">
            <div class="p-3.5 rounded-xl bg-purple-100 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 shrink-0">
                <flux:icon name="calendar" class="size-7" />
            </div>
            <div>
                <p class="text-2xl font-extrabold text-zinc-900 dark:text-white">{{ number_format($stats['totalEvents']) }}</p>
                <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400">{{ __('Public Events Hosted') }}</p>
            </div>
        </div>

        <div class="p-6 rounded-2xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 shadow-xs dark:shadow-xl flex items-center gap-4">
            <div class="p-3.5 rounded-xl bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 shrink-0">
                <flux:icon name="ticket" class="size-7" />
            </div>
            <div>
                <p class="text-2xl font-extrabold text-zinc-900 dark:text-white">{{ number_format($stats['totalTickets']) }}</p>
                <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400">{{ __('Digital Passes Issued') }}</p>
            </div>
        </div>

        <div class="p-6 rounded-2xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 shadow-xs dark:shadow-xl flex items-center gap-4">
            <div class="p-3.5 rounded-xl bg-indigo-100 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 shrink-0">
                <flux:icon name="building-office-2" class="size-7" />
            </div>
            <div>
                <p class="text-2xl font-extrabold text-zinc-900 dark:text-white">{{ number_format($stats['totalOrganizers']) }}</p>
                <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400">{{ __('Verified Organizers') }}</p>
            </div>
        </div>
    </section>

    {{-- 3. Featured Events Grid --}}
    <section class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
            <div>
                <flux:heading size="xl" level="2">{{ __('Featured Upcoming Events') }}</flux:heading>
                <flux:subheading>{{ __('Hand-picked gatherings happening across Malaysia & digital venues.') }}</flux:subheading>
            </div>
            <flux:button variant="subtle" icon="arrow-right" icon-position="trailing" :href="route('events.index')" wire:navigate class="shrink-0">
                {{ __('View Full Catalog') }}
            </flux:button>
        </div>

        @if ($featuredEvents->isEmpty())
            <div class="text-center py-16 bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 p-8 space-y-3">
                <flux:icon name="calendar" class="mx-auto size-12 text-zinc-400" />
                <flux:heading size="lg">{{ __('No upcoming events scheduled') }}</flux:heading>
                <p class="text-sm text-zinc-500">{{ __('Check back soon for new summits, conferences, and sports runs.') }}</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($featuredEvents as $event)
                    <flux:card wire:key="event-{{ $event->id }}" class="flex flex-col justify-between hover:shadow-lg transition-shadow bg-white dark:bg-zinc-900 border-zinc-200 dark:border-zinc-800 min-w-0 overflow-hidden">
                        <div class="space-y-4 min-w-0">
                            <div class="flex flex-wrap items-center justify-between gap-2 min-w-0">
                                <flux:badge size="sm" color="purple" icon="user" class="max-w-[70%] shrink-0">
                                    <span class="truncate inline-block max-w-[130px] sm:max-w-[170px]">{{ $event->organizer->name }}</span>
                                </flux:badge>

                                @if ($event->isSoldOut())
                                    <flux:badge size="sm" color="red" class="shrink-0">{{ __('Sold Out') }}</flux:badge>
                                @elseif ($event->remainingTickets() <= 5)
                                    <flux:badge size="sm" color="amber" class="shrink-0">{{ $event->remainingTickets() }} {{ __('left') }}</flux:badge>
                                @else
                                    <flux:badge size="sm" color="emerald" class="shrink-0">{{ $event->remainingTickets() }} {{ __('available') }}</flux:badge>
                                @endif
                            </div>

                            <div class="space-y-2">
                                <flux:heading size="lg">
                                    <a href="{{ route('events.show', $event) }}" wire:navigate class="hover:underline text-zinc-900 dark:text-white">
                                        {{ $event->title }}
                                    </a>
                                </flux:heading>
                                <p class="text-sm text-zinc-600 dark:text-zinc-400 line-clamp-2">
                                    {{ $event->description }}
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

                            <flux:button variant="primary" class="w-full bg-purple-600 hover:bg-purple-500 text-white font-medium" :href="route('events.show', $event)" wire:navigate>
                                {{ __('View Details & Passes') }}
                            </flux:button>
                        </div>
                    </flux:card>
                @endforeach
            </div>
        @endif
    </section>

    {{-- 4. Host Events Callout Banner --}}
    <section class="relative overflow-hidden p-8 md:p-12 rounded-3xl bg-gradient-to-r from-zinc-900 via-purple-950 to-zinc-900 border border-zinc-800 text-white shadow-2xl flex flex-col md:flex-row items-start md:items-center justify-between gap-8">
        <div class="space-y-3 max-w-2xl">
            <flux:badge color="amber" variant="solid" size="sm">{{ __('Organizer Partner Network') }}</flux:badge>
            <h2 class="text-2xl md:text-3xl font-extrabold text-white">{{ __('Planning your next marathon or conference?') }}</h2>
            <p class="text-sm text-zinc-300 leading-relaxed">
                {{ __('Join Eventive as a verified event organizer. Create public listings, manage attendee capacities, issue digital passcodes, and monitor real-time ticket sales analytics.') }}
            </p>
        </div>

        @auth
            @if (auth()->user()?->isOrganizer() || auth()->user()?->isAdmin())
                <flux:button variant="primary" :href="route('organizer.events.create')" wire:navigate class="shrink-0 bg-white text-zinc-950 hover:bg-purple-100 font-bold border-0 px-6 py-3">
                    {{ __('Create Event Now') }}
                </flux:button>
            @else
                <flux:button variant="primary" :href="route('become-organizer.edit')" wire:navigate class="shrink-0 bg-white text-zinc-950 hover:bg-purple-100 font-bold border-0 px-6 py-3">
                    {{ __('Apply as Organizer') }}
                </flux:button>
            @endif
        @else
            <flux:button variant="primary" :href="route('register')" wire:navigate class="shrink-0 bg-white text-zinc-950 hover:bg-purple-100 font-bold border-0 px-6 py-3">
                {{ __('Get Started Free') }}
            </flux:button>
        @endauth
    </section>
</div>
