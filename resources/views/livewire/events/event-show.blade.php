<div class="max-w-4xl mx-auto space-y-8">
    <!-- Breadcrumb / Back button -->
    <div>
        <flux:button variant="subtle" icon="arrow-left" size="sm" :href="route('events.index')" wire:navigate>
            Back to Events
        </flux:button>
    </div>

    <!-- Main Card -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Event Details Column -->
        <div class="lg:col-span-2 space-y-6">
            <div class="space-y-3">
                <div class="flex flex-wrap items-center gap-2">
                    <flux:badge color="purple" icon="user">
                        Organized by {{ $event->organizer->name }}
                    </flux:badge>
                    @if ($event->isSoldOut())
                        <flux:badge color="red">Sold Out</flux:badge>
                    @else
                        <flux:badge color="emerald">{{ $event->remainingTickets() }} Tickets Left</flux:badge>
                    @endif
                </div>

                <flux:heading size="xl" level="1">{{ $event->title }}</flux:heading>
            </div>

            <flux:separator />

            <div class="prose dark:prose-invert max-w-none">
                <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-2">About This Event</h3>
                <p class="whitespace-pre-line text-zinc-700 dark:text-zinc-300 leading-relaxed">
                    {{ $event->description }}
                </p>
            </div>
        </div>

        <!-- Sidebar / Action Column -->
        <div class="space-y-6">
            <flux:card class="space-y-6">
                <flux:heading size="lg">Event Information</flux:heading>

                <div class="space-y-4 text-sm">
                    <div class="flex items-start gap-3">
                        <flux:icon name="calendar" class="size-5 text-purple-700 dark:text-purple-400 mt-0.5" />
                        <div>
                            <p class="font-medium text-zinc-900 dark:text-zinc-100">Date & Time</p>
                            <p class="text-zinc-600 dark:text-zinc-400 text-xs font-medium">{{ $event->starts_at->format('l, F j, Y') }}</p>
                            <p class="text-zinc-600 dark:text-zinc-400 text-xs font-medium">{{ $event->starts_at->format('g:i A') }}</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <flux:icon name="map-pin" class="size-5 text-purple-700 dark:text-purple-400 mt-0.5" />
                        <div>
                            <p class="font-medium text-zinc-900 dark:text-zinc-100">Location</p>
                            <p class="text-zinc-600 dark:text-zinc-400 text-xs font-medium">{{ $event->location }}</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <flux:icon name="ticket" class="size-5 text-purple-700 dark:text-purple-400 mt-0.5" />
                        <div>
                            <p class="font-medium text-zinc-900 dark:text-zinc-100">Availability</p>
                            <p class="text-zinc-600 dark:text-zinc-400 text-xs font-medium">{{ $event->tickets_sold }} / {{ $event->capacity }} tickets claimed</p>
                        </div>
                    </div>
                </div>

                <flux:separator />

                <!-- Registration Component -->
                <livewire:tickets.register-ticket :event="$event" :key="$event->id" />
            </flux:card>
        </div>
    </div>
</div>
