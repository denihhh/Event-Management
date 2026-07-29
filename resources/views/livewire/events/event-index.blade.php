<div class="space-y-8">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <flux:heading size="xl" level="1">Explore Events</flux:heading>
            <flux:subheading>Discover upcoming events, workshops, and gatherings.</flux:subheading>
        </div>
    </div>

    <!-- Search & Filter Toolbar -->
    <x-ui.search-toolbar placeholder="Search by event title, location, or keyword...">
        <flux:select wire:model.live="filter" placeholder="Filter by date" class="w-full sm:w-64">
            <flux:select.option value="upcoming">Upcoming Events</flux:select.option>
            <flux:select.option value="this_week">This Week</flux:select.option>
            <flux:select.option value="this_month">This Month</flux:select.option>
            <flux:select.option value="all">All Events</flux:select.option>
            <flux:select.option value="past">Past Events</flux:select.option>
        </flux:select>
    </x-ui.search-toolbar>

    <!-- Event Cards Grid -->
    @if ($events->isEmpty())
        <x-ui.empty-state
            icon="calendar"
            title="No events found"
            description="Try adjusting your search criteria or date filter."
        />
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($events as $event)
                <x-cards.event-card wire:key="event-{{ $event->id }}" :event="$event" />
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="pt-4">
            {{ $events->links() }}
        </div>
    @endif

    <!-- Image Lightbox Modal -->
    <x-ui.image-lightbox />
</div>
