<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <flux:heading size="xl" level="1">{{ __('Attendee Roster') }}</flux:heading>
            <flux:subheading>{{ __('View and manage attendees registered across your hosted events.') }}</flux:subheading>
        </div>
        <div>
            <flux:button variant="primary" icon="arrow-down-tray" wire:click="exportCsv" wire:loading.attr="disabled">
                {{ __('Export All Attendees CSV') }}
            </flux:button>
        </div>
    </div>

    <!-- Toolbar (Search & Event Filter) -->
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="w-full sm:w-80">
            <flux:input
                wire:model.live.debounce.300ms="search"
                icon="magnifying-glass"
                placeholder="{{ __('Search by name, email, event, or ticket code...') }}"
                clearable
            />
        </div>

        <div class="flex items-center gap-2 w-full sm:w-auto">
            <flux:select wire:model.live="eventId" class="w-64">
                <option value="all">{{ __('All Events') }}</option>
                @foreach ($this->events as $event)
                    <option value="{{ $event->id }}">{{ $event->title }}</option>
                @endforeach
            </flux:select>
        </div>
    </div>

    @if ($tickets->isEmpty())
        <div class="text-center py-16 bg-white dark:bg-zinc-800/50 rounded-xl border border-zinc-200 dark:border-zinc-700/60 p-8 space-y-3">
            <flux:icon name="users" class="mx-auto size-12 text-zinc-400" />
            <flux:heading size="lg">{{ __('No attendees found') }}</flux:heading>
            <p class="text-sm text-zinc-500">{{ $search ? __('No attendees match your search query.') : __('When attendees register for your events, they will appear here.') }}</p>
        </div>
    @else
        <div class="overflow-x-auto rounded-xl border border-zinc-200 dark:border-zinc-700/60 bg-white dark:bg-zinc-800/50 shadow-xs">
            <flux:table>
                <flux:table.columns>
                    <flux:table.column class="px-4 py-3.5 first:pl-6 last:pr-6">{{ __('Attendee') }}</flux:table.column>
                    <flux:table.column class="px-4 py-3.5 first:pl-6 last:pr-6">{{ __('Event') }}</flux:table.column>
                    <flux:table.column class="whitespace-nowrap px-4 py-3.5 first:pl-6 last:pr-6">{{ __('Digital Ticket Pass') }}</flux:table.column>
                    <flux:table.column class="whitespace-nowrap px-4 py-3.5 first:pl-6 last:pr-6">{{ __('Registration Date') }}</flux:table.column>
                    <flux:table.column class="whitespace-nowrap px-4 py-3.5 first:pl-6 last:pr-6">{{ __('Reminder Sent') }}</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @forelse ($tickets as $ticket)
                        <flux:table.row wire:key="ticket-{{ $ticket->id }}">
                            <flux:table.cell class="px-4 py-3.5 first:pl-6 last:pr-6">
                                <div class="min-w-0 py-0.5">
                                    <p class="font-medium truncate text-sm text-zinc-900 dark:text-zinc-100">{{ $ticket->user?->name ?? 'N/A' }}</p>
                                    <p class="text-xs text-zinc-500 truncate">{{ $ticket->user?->email ?? 'N/A' }}</p>
                                </div>
                            </flux:table.cell>

                            <flux:table.cell class="px-4 py-3.5 first:pl-6 last:pr-6">
                                <div class="max-w-xs">
                                    @if ($ticket->event)
                                        <a href="{{ route('events.show', $ticket->event) }}" wire:navigate class="hover:underline font-medium text-sm text-purple-600 dark:text-purple-400">
                                            {{ $ticket->event->title }}
                                        </a>
                                    @else
                                        <span class="text-zinc-400 italic text-xs">{{ __('Cancelled Event') }}</span>
                                    @endif
                                </div>
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
