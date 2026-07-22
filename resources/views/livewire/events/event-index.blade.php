<div class="space-y-8">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <flux:heading size="xl" level="1">Explore Events</flux:heading>
            <flux:subheading>Discover upcoming events, workshops, and gatherings.</flux:subheading>
        </div>
    </div>

    <!-- Search & Filter Toolbar -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="md:col-span-2">
            <flux:input
                wire:model.live.debounce.300ms="search"
                icon="magnifying-glass"
                placeholder="Search by event title, location, or keyword..."
                clearable
            />
        </div>
        <div>
            <flux:select wire:model.live="filter" placeholder="Filter by date">
                <flux:select.option value="upcoming">Upcoming Events</flux:select.option>
                <flux:select.option value="this_week">This Week</flux:select.option>
                <flux:select.option value="this_month">This Month</flux:select.option>
                <flux:select.option value="all">All Events</flux:select.option>
                <flux:select.option value="past">Past Events</flux:select.option>
            </flux:select>
        </div>
    </div>

    <!-- Event Cards Grid -->
    @if ($events->isEmpty())
        <div class="text-center py-16 bg-white dark:bg-zinc-800/50 rounded-xl border border-zinc-200 dark:border-zinc-700/60 p-8 space-y-3">
            <flux:icon name="calendar" class="mx-auto size-12 text-zinc-400" />
            <flux:heading size="lg">No events found</flux:heading>
            <p class="text-sm text-zinc-500">Try adjusting your search criteria or date filter.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($events as $event)
                <flux:card key="{{ $event->id }}" class="flex flex-col justify-between hover:shadow-md transition-shadow">
                    <div class="space-y-4">
                        <div class="flex items-start justify-between gap-2">
                            <flux:badge size="sm" color="purple" icon="user">
                                {{ $event->organizer->name }}
                            </flux:badge>

                            @if ($event->isSoldOut())
                                <flux:badge size="sm" color="red">Sold Out</flux:badge>
                            @elseif ($event->remainingTickets() <= 5)
                                <flux:badge size="sm" color="amber">{{ $event->remainingTickets() }} left</flux:badge>
                            @else
                                <flux:badge size="sm" color="emerald">{{ $event->remainingTickets() }} available</flux:badge>
                            @endif
                        </div>

                        <div class="space-y-2">
                            <flux:heading size="lg">
                                <a href="{{ route('events.show', $event) }}" wire:navigate class="hover:underline">
                                    {{ $event->title }}
                                </a>
                            </flux:heading>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400 line-clamp-2">
                                {{ $event->description }}
                            </p>
                        </div>
                    </div>

                    <div class="mt-6 pt-4 border-t border-zinc-100 dark:border-zinc-800 space-y-3">
                        <div class="space-y-1 text-xs text-zinc-500 dark:text-zinc-400">
                            <div class="flex items-center gap-1.5">
                                <flux:icon name="calendar" class="size-4 text-zinc-400" />
                                <span>{{ $event->starts_at->format('M j, Y \a\t g:i A') }}</span>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <flux:icon name="map-pin" class="size-4 text-zinc-400" />
                                <span class="truncate">{{ $event->location }}</span>
                            </div>
                        </div>

                        <flux:button variant="primary" class="w-full" :href="route('events.show', $event)" wire:navigate>
                            View Details
                        </flux:button>
                    </div>
                </flux:card>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="pt-4">
            {{ $events->links() }}
        </div>
    @endif
</div>
