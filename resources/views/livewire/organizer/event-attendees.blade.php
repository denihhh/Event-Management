<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <flux:button variant="subtle" icon="arrow-left" size="sm" :href="route('organizer.events.index')" wire:navigate class="mb-2">
                {{ __('Back to Events') }}
            </flux:button>
            <flux:heading size="xl" level="1">{{ __('Attendee Roster') }}</flux:heading>
            <flux:subheading>{{ __('Registered attendees for :title (:count total)', ['title' => $event->title, 'count' => $tickets->total()]) }}</flux:subheading>
        </div>
        <div class="flex items-center gap-3">
            <flux:button variant="filled" icon="qr-code" :href="route('organizer.events.check-in', $event)" wire:navigate>
                {{ __('Door Check-In') }}
            </flux:button>
            <flux:button variant="primary" icon="arrow-down-tray" wire:click="exportCsv">
                {{ __('Export CSV') }}
            </flux:button>
        </div>
    </div>

    <!-- Search Toolbar -->
    <div class="max-w-md">
        <flux:input
            wire:model.live.debounce.300ms="search"
            icon="magnifying-glass"
            placeholder="{{ __('Search by name, email, or ticket code...') }}"
            clearable
        />
    </div>

    @if ($tickets->isEmpty())
        <div class="text-center py-16 bg-white dark:bg-zinc-800/50 rounded-xl border border-zinc-200 dark:border-zinc-700/60 p-8 space-y-3">
            <flux:icon name="users" class="mx-auto size-12 text-zinc-400" />
            <flux:heading size="lg">{{ __('No attendees registered yet') }}</flux:heading>
            <p class="text-sm text-zinc-500">{{ __('When attendees register for this event, their tickets will appear here.') }}</p>
        </div>
    @else
        <div class="overflow-x-auto rounded-xl border border-zinc-200 dark:border-zinc-700/60 bg-white dark:bg-zinc-800/50 shadow-xs">
            <flux:table>
                <flux:table.columns>
                    <flux:table.column class="px-4 py-3.5 first:pl-6 last:pr-6">{{ __('Attendee Name') }}</flux:table.column>
                    <flux:table.column class="px-4 py-3.5 first:pl-6 last:pr-6">{{ __('Email Address') }}</flux:table.column>
                    <flux:table.column class="whitespace-nowrap px-4 py-3.5 first:pl-6 last:pr-6">{{ __('Digital Ticket Code') }}</flux:table.column>
                    <flux:table.column class="whitespace-nowrap px-4 py-3.5 first:pl-6 last:pr-6">{{ __('Registration Date') }}</flux:table.column>
                    <flux:table.column class="whitespace-nowrap px-4 py-3.5 first:pl-6 last:pr-6">{{ __('Reminder Sent') }}</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @forelse ($tickets as $ticket)
                        <flux:table.row wire:key="ticket-{{ $ticket->id }}">
                            <flux:table.cell class="font-medium text-sm px-4 py-3.5 first:pl-6 last:pr-6">
                                {{ $ticket->user?->name ?? 'N/A' }}
                            </flux:table.cell>

                            <flux:table.cell class="text-xs text-zinc-500 px-4 py-3.5 first:pl-6 last:pr-6">
                                {{ $ticket->user?->email ?? 'N/A' }}
                            </flux:table.cell>

                            <flux:table.cell class="font-mono text-xs font-semibold text-purple-600 dark:text-purple-400 select-all whitespace-nowrap px-4 py-3.5 first:pl-6 last:pr-6">
                                {{ $ticket->ticket_code }}
                            </flux:table.cell>

                            <flux:table.cell class="text-xs text-zinc-500 whitespace-nowrap px-4 py-3.5 first:pl-6 last:pr-6">
                                {{ $ticket->created_at->format('M j, Y \a\t g:i A') }}
                            </flux:table.cell>

                            <flux:table.cell class="whitespace-nowrap px-4 py-3.5 first:pl-6 last:pr-6">
                                @if ($ticket->reminder_sent)
                                    <flux:badge size="sm" color="emerald" rounded>{{ __('Yes') }}</flux:badge>
                                @else
                                    <flux:badge size="sm" color="zinc" rounded>{{ __('Pending') }}</flux:badge>
                                @endif
                            </flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>
        </div>

        <div class="pt-4">
            {{ $tickets->links() }}
        </div>
    @endif
</div>
