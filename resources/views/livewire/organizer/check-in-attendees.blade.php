<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <flux:breadcrumbs class="mb-4">
                <flux:breadcrumbs.item :href="route('organizer.events.index')" wire:navigate>Managed Events</flux:breadcrumbs.item>
                <flux:breadcrumbs.item>{{ $event->hasEnded() ? 'Event Details' : 'Door Check-In' }}</flux:breadcrumbs.item>
            </flux:breadcrumbs>
            <flux:heading size="xl" level="1">{{ $event->title }}</flux:heading>
            <flux:subheading class="flex items-center gap-2 mt-1">
                <span>{{ $event->starts_at->format('l, F j, Y \a\t g:i A') }}</span>
                <span>•</span>
                <span>{{ $event->location }}</span>
            </flux:subheading>
        </div>
    </div>

    @if ($event->hasEnded())
        <div class="p-4 rounded-xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-900/60 flex items-center gap-3 text-amber-800 dark:text-amber-200 text-sm">
            <flux:icon name="exclamation-triangle" class="size-5 text-amber-600 shrink-0" />
            <span>This event has concluded. You are viewing attendance records in post-event audit mode.</span>
        </div>
    @endif

    <!-- Stats Overview Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <flux:card class="space-y-1 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs uppercase tracking-wider text-zinc-600 dark:text-zinc-400 font-bold">Total Registered</span>
                <flux:icon name="ticket" class="size-5 text-purple-700 dark:text-purple-400" />
            </div>
            <div class="text-2xl font-bold text-zinc-900 dark:text-white">
                {{ number_format($this->stats['total']) }}
            </div>
            <p class="text-xs text-zinc-600 dark:text-zinc-400 font-medium">Tickets claimed for this event</p>
        </flux:card>

        <flux:card class="space-y-1 bg-teal-50/80 dark:bg-teal-950/40 border border-teal-200/90 dark:border-teal-900/60 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs uppercase tracking-wider text-teal-800 dark:text-teal-300 font-bold">Checked In</span>
                <flux:icon name="check-circle" class="size-5 text-teal-700 dark:text-teal-400" />
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-2xl font-bold text-teal-950 dark:text-teal-100">
                    {{ number_format($this->stats['checkedIn']) }}
                </span>
                <span class="text-xs font-bold text-teal-800 dark:text-teal-300">
                    ({{ $this->stats['percentage'] }}%)
                </span>
            </div>
            <!-- Progress Bar -->
            <div class="w-full bg-teal-200 dark:bg-teal-900/60 h-2 rounded-full overflow-hidden mt-1">
                <div class="bg-teal-700 dark:bg-teal-400 h-full rounded-full transition-all duration-300" style="width: {{ $this->stats['percentage'] }}%;"></div>
            </div>
        </flux:card>

        <flux:card class="space-y-1 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs uppercase tracking-wider text-zinc-600 dark:text-zinc-400 font-bold">{{ $event->hasEnded() ? 'Absent / No-Show' : 'Pending Check-In' }}</span>
                @if ($event->hasEnded())
                    <flux:icon name="x-mark" class="size-5 text-zinc-500 dark:text-zinc-400" />
                @else
                    <flux:icon name="clock" class="size-5 text-amber-600 dark:text-amber-400" />
                @endif
            </div>
            <div class="text-2xl font-bold text-zinc-900 dark:text-white">
                {{ number_format($this->stats['pending']) }}
            </div>
            <p class="text-xs text-zinc-600 dark:text-zinc-400 font-medium">{{ $event->hasEnded() ? 'Attendees who missed the event' : 'Attendees expected at venue' }}</p>
        </flux:card>
    </div>

    <!-- Fast Ticket Code Scanner Box (Only active during event) -->
    @if (! $event->hasEnded())
        <flux:card class="space-y-4 bg-gradient-to-r from-purple-50 to-indigo-50 dark:from-purple-950/20 dark:to-indigo-950/20 border-purple-200 dark:border-purple-800/40">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="space-y-1">
                    <div class="flex items-center gap-2">
                        <flux:icon name="qr-code" class="size-5 text-purple-600 dark:text-purple-400" />
                        <flux:heading size="md">{{ __('Fast Ticket Code Check-In') }}</flux:heading>
                    </div>
                    <p class="text-xs text-zinc-600 dark:text-zinc-400">
                        {{ __('Use live camera scanning, USB barcode scanner, or manual ticket code entry to check in attendees.') }}
                    </p>
                </div>
            </div>

            <div wire:ignore>
                <x-ui.qr-scanner />
            </div>

            <div class="pt-2 border-t border-purple-200/60 dark:border-purple-800/40">
                <form wire:submit.prevent="scanCheckIn" class="flex gap-2">
                    <div class="flex-1">
                        <flux:input
                            wire:model="ticketInput"
                            placeholder="{{ __('Scan QR code or paste ticket UUID code here...') }}"
                            icon="qr-code"
                            clearable
                            autofocus
                        />
                    </div>
                    <flux:button variant="primary" type="submit" icon="check" class="bg-purple-600 hover:bg-purple-500 text-white">
                        {{ __('Check In') }}
                    </flux:button>
                </form>
            </div>
        </flux:card>
    @endif

    <!-- Search & Filter Controls -->
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="w-full sm:w-80">
            <flux:input
                wire:model.live.debounce.300ms="search"
                placeholder="Search attendee name, email, code..."
                icon="magnifying-glass"
                clearable
            />
        </div>

        <div class="flex items-center gap-2 w-full sm:w-auto">
            <flux:select wire:model.live="statusFilter" class="w-48">
                <option value="all">All Attendees</option>
                <option value="pending">{{ $event->hasEnded() ? 'Absent (No-Show)' : 'Pending Door Check-In' }}</option>
                <option value="checked_in">Checked In</option>
            </flux:select>
        </div>
    </div>

    <!-- Roster Table -->
    <div class="overflow-x-auto rounded-lg border border-zinc-200 dark:border-zinc-700 p-4 sm:p-6">
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Attendee</flux:table.column>
                <flux:table.column>Ticket Code</flux:table.column>
                <flux:table.column>Status</flux:table.column>
                @if (! $event->hasEnded())
                    <flux:table.column>Actions</flux:table.column>
                @endif
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($this->tickets as $ticket)
                    <flux:table.row wire:key="ticket-{{ $ticket->id }}">
                        <flux:table.cell>
                            <div class="flex items-center gap-3">
                                <div class="size-8 rounded-full bg-purple-100 dark:bg-purple-900/50 flex items-center justify-center text-xs font-bold text-purple-700 dark:text-purple-300">
                                    {{ strtoupper(substr($ticket->user?->name ?? 'A', 0, 1)) }}
                                </div>
                                <div>
                                    <div class="font-medium text-zinc-900 dark:text-white">
                                        {{ $ticket->user?->name ?? 'Unknown User' }}
                                    </div>
                                    <div class="text-xs text-zinc-500 dark:text-zinc-400">
                                        {{ $ticket->user?->email ?? 'N/A' }}
                                    </div>
                                </div>
                            </div>
                        </flux:table.cell>

                        <flux:table.cell>
                            <span class="font-mono text-xs font-semibold text-purple-600 dark:text-purple-400 select-all">
                                {{ $ticket->ticket_code }}
                            </span>
                        </flux:table.cell>

                        <flux:table.cell>
                            @if ($ticket->isCheckedIn())
                                <flux:badge color="teal" size="sm" icon="check-circle">
                                    Checked In ({{ $ticket->checked_in_at?->format('g:i A') }})
                                </flux:badge>
                            @elseif ($event->hasEnded())
                                <flux:badge color="red" size="sm" icon="x-circle">
                                    Absent
                                </flux:badge>
                            @else
                                <flux:badge color="amber" size="sm" icon="clock">
                                    Pending
                                </flux:badge>
                            @endif
                        </flux:table.cell>

                        @if (! $event->hasEnded())
                            <flux:table.cell>
                                @if ($ticket->isCheckedIn())
                                    <flux:button
                                        variant="subtle"
                                        size="sm"
                                        icon="arrow-path"
                                        wire:click="undoCheckIn({{ $ticket->id }})"
                                    >
                                        Revert
                                    </flux:button>
                                @else
                                    <flux:button
                                        variant="primary"
                                        size="sm"
                                        icon="check"
                                        wire:click="checkIn({{ $ticket->id }})"
                                    >
                                        Check In
                                    </flux:button>
                                @endif
                            </flux:table.cell>
                        @endif
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="{{ $event->hasEnded() ? 3 : 4 }}" class="py-10 text-center px-4">
                            <flux:icon name="ticket" class="mx-auto size-10 text-zinc-400 mb-2" />
                            <p class="text-zinc-500 text-sm font-medium">
                                {{ $search || $statusFilter !== 'all' ? __('No attendees match your search or filter.') : __('No tickets claimed for this event yet.') }}
                            </p>
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </div>

    <!-- Pagination -->
    @if ($this->tickets->hasPages())
        <div class="pt-2">
            {{ $this->tickets->links() }}
        </div>
    @endif
</div>
