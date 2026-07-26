<div class="space-y-6">
    <div>
        <flux:heading size="xl" level="1">My Tickets</flux:heading>
        <flux:subheading>Manage your event passes and digital ticket codes.</flux:subheading>
    </div>

    @if ($tickets->isEmpty())
        <div class="text-center py-16 bg-white dark:bg-zinc-800/50 rounded-xl border border-zinc-200 dark:border-zinc-700/60 p-8 space-y-4">
            <flux:icon name="ticket" class="mx-auto size-12 text-zinc-400" />
            <flux:heading size="lg">No tickets claimed yet</flux:heading>
            <p class="text-sm text-zinc-500 max-w-md mx-auto">
                Explore upcoming events and claim your free digital tickets to see them listed here.
            </p>
            <flux:button variant="primary" :href="route('events.index')" wire:navigate>
                Browse Events
            </flux:button>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach ($tickets as $ticket)
                <flux:card wire:key="ticket-{{ $ticket->id }}" class="space-y-4 flex flex-col justify-between">
                    <div class="space-y-3">
                        <div class="flex items-start justify-between gap-2">
                            <flux:badge color="purple" size="sm" icon="user">
                                {{ $ticket->event->organizer->name }}
                            </flux:badge>

                            @if ($ticket->isCheckedIn())
                                <flux:badge color="teal" size="sm" icon="check-circle">
                                    Checked In ({{ $ticket->checked_in_at?->format('g:i A') }})
                                </flux:badge>
                            @elseif ($ticket->event->hasStarted())
                                <flux:badge color="zinc" size="sm">Past Event</flux:badge>
                            @else
                                <flux:badge color="emerald" size="sm">Active Ticket</flux:badge>
                            @endif
                        </div>

                        <flux:heading size="lg">
                            <a href="{{ route('events.show', $ticket->event) }}" wire:navigate class="hover:underline">
                                {{ $ticket->event->title }}
                            </a>
                        </flux:heading>

                        <div class="space-y-1.5 text-xs text-zinc-500 dark:text-zinc-400">
                            <div class="flex items-center gap-2">
                                <flux:icon name="calendar" class="size-4 text-zinc-400" />
                                <span>{{ $ticket->event->starts_at->format('l, F j, Y \a\t g:i A') }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <flux:icon name="map-pin" class="size-4 text-zinc-400" />
                                <span>{{ $ticket->event->location }}</span>
                            </div>
                        </div>

                        <!-- Ticket Pass & QR Code Box -->
                        <div class="p-3 bg-zinc-50 dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 flex items-center justify-between gap-4">
                            <div class="space-y-1">
                                <span class="text-xs uppercase tracking-wider text-zinc-400 font-semibold">Digital Ticket Pass Code</span>
                                <div class="font-mono text-xs font-bold text-purple-600 dark:text-purple-400 select-all break-all">
                                    {{ $ticket->ticket_code }}
                                </div>
                            </div>
                            <div class="shrink-0">
                                <img src="{{ $ticket->getQrCodeDataUri() }}" alt="QR Code" class="size-20 rounded-md bg-white p-1 border border-zinc-200 dark:border-zinc-600 shadow-2xs" />
                            </div>
                        </div>
                    </div>

                    <div class="pt-3 border-t border-zinc-100 dark:border-zinc-800 flex items-center justify-between">
                        <span class="text-xs text-zinc-400">
                            Claimed {{ $ticket->created_at->diffForHumans() }}
                        </span>

                        @if (! $ticket->event->hasStarted())
                            <flux:button variant="danger" size="sm" wire:click="confirmCancel({{ $ticket->id }})">
                                Cancel Ticket
                            </flux:button>
                        @endif
                    </div>
                </flux:card>
            @endforeach
        </div>

        <div class="pt-4">
            {{ $tickets->links() }}
        </div>
    @endif

    <!-- Cancel Confirmation Modal -->
    <flux:modal :open="(bool) $cancellingTicketId" wire:close="$set('cancellingTicketId', null)">
        <div class="space-y-4">
            <flux:heading size="lg">Cancel Ticket?</flux:heading>
            <p class="text-sm text-zinc-500">
                Are you sure you want to cancel this ticket? Your reservation will be released back to the event capacity.
            </p>
            <div class="flex justify-end gap-3 pt-2">
                <flux:button variant="subtle" wire:click="$set('cancellingTicketId', null)">
                    Keep Ticket
                </flux:button>
                <flux:button variant="danger" wire:click="cancelTicket">
                    Yes, Cancel Ticket
                </flux:button>
            </div>
        </div>
    </flux:modal>
</div>
