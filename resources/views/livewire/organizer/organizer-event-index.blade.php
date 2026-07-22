<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <flux:heading size="xl" level="1">{{ __('Managed Events') }}</flux:heading>
            <flux:subheading>{{ __('Manage your published events, view attendee rosters, and track capacity.') }}</flux:subheading>
        </div>
        <div>
            <flux:button variant="primary" icon="plus" :href="route('organizer.events.create')" wire:navigate>
                {{ __('Create New Event') }}
            </flux:button>
        </div>
    </div>

    @if ($events->isEmpty())
        <div class="text-center py-16 bg-white dark:bg-zinc-800/50 rounded-xl border border-zinc-200 dark:border-zinc-700/60 p-8 space-y-4">
            <flux:icon name="calendar" class="mx-auto size-12 text-zinc-400" />
            <flux:heading size="lg">{{ __('No events created yet') }}</flux:heading>
            <p class="text-sm text-zinc-500 max-w-md mx-auto">
                {{ __('Get started by creating your first event to publish dates, location, and ticket capacity.') }}
            </p>
            <flux:button variant="primary" :href="route('organizer.events.create')" wire:navigate>
                {{ __('Create Event') }}
            </flux:button>
        </div>
    @else
        <div class="overflow-x-auto rounded-xl border border-zinc-200 dark:border-zinc-700/60 bg-white dark:bg-zinc-800/50 shadow-xs">
            <flux:table>
                <flux:table.columns>
                    <flux:table.column class="px-4 py-3.5 first:pl-6 last:pr-6">{{ __('Event Title') }}</flux:table.column>
                    <flux:table.column class="w-48 whitespace-nowrap px-4 py-3.5 first:pl-6 last:pr-6">{{ __('Date & Time') }}</flux:table.column>
                    <flux:table.column class="w-36 whitespace-nowrap px-4 py-3.5 first:pl-6 last:pr-6">{{ __('Location') }}</flux:table.column>
                    <flux:table.column class="w-44 whitespace-nowrap px-4 py-3.5 first:pl-6 last:pr-6">{{ __('Tickets Sold / Capacity') }}</flux:table.column>
                    <flux:table.column class="w-24 whitespace-nowrap px-4 py-3.5 first:pl-6 last:pr-6">{{ __('Status') }}</flux:table.column>
                    <flux:table.column align="end" class="w-16 whitespace-nowrap px-4 py-3.5 first:pl-6 last:pr-6">{{ __('Actions') }}</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @foreach ($events as $event)
                        <flux:table.row key="{{ $event->id }}">
                            <flux:table.cell class="font-medium px-4 py-3.5 first:pl-6 last:pr-6">
                                <a href="{{ route('events.show', $event) }}" target="_blank" class="hover:underline flex items-center gap-1.5 text-sm">
                                    <span>{{ $event->title }}</span>
                                    <flux:icon name="arrow-top-right-on-square" class="size-3.5 text-zinc-400 shrink-0" />
                                </a>
                            </flux:table.cell>

                            <flux:table.cell class="text-xs text-zinc-500 whitespace-nowrap px-4 py-3.5 first:pl-6 last:pr-6">
                                {{ $event->starts_at->format('M j, Y \a\t g:i A') }}
                            </flux:table.cell>

                            <flux:table.cell class="text-xs text-zinc-500 max-w-xs truncate whitespace-nowrap px-4 py-3.5 first:pl-6 last:pr-6" title="{{ $event->location }}">
                                {{ $event->location }}
                            </flux:table.cell>

                            <flux:table.cell class="whitespace-nowrap px-4 py-3.5 first:pl-6 last:pr-6">
                                <div class="flex items-center gap-1.5 text-xs font-mono">
                                    <span class="font-semibold">{{ $event->tickets_sold }} / {{ $event->capacity }}</span>
                                    <span class="text-zinc-400">({{ $event->remainingTickets() }} left)</span>
                                </div>
                            </flux:table.cell>

                            <flux:table.cell class="whitespace-nowrap px-4 py-3.5 first:pl-6 last:pr-6">
                                @if ($event->hasStarted())
                                    <flux:badge size="sm" color="zinc" rounded>{{ __('Past') }}</flux:badge>
                                @elseif ($event->isSoldOut())
                                    <flux:badge size="sm" color="amber" rounded>{{ __('Sold Out') }}</flux:badge>
                                @else
                                    <flux:badge size="sm" color="emerald" rounded>{{ __('Active') }}</flux:badge>
                                @endif
                            </flux:table.cell>

                            <flux:table.cell align="end" class="px-4 py-3.5 first:pl-6 last:pr-6">
                                <flux:dropdown align="end">
                                    <flux:button variant="subtle" icon="ellipsis-horizontal" size="sm" aria-label="{{ __('Actions') }}" />

                                    <flux:menu>
                                        <flux:menu.item icon="users" :href="route('organizer.events.attendees', $event)" wire:navigate>
                                            {{ __('View Roster') }}
                                        </flux:menu.item>
                                        <flux:menu.item icon="pencil-square" :href="route('organizer.events.edit', $event)" wire:navigate>
                                            {{ __('Edit Event') }}
                                        </flux:menu.item>
                                        <flux:menu.separator />
                                        <flux:menu.item icon="trash" variant="danger" wire:click="confirmDelete({{ $event->id }})">
                                            {{ __('Cancel Event') }}
                                        </flux:menu.item>
                                    </flux:menu>
                                </flux:dropdown>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>
        </div>

        <div class="pt-4">
            {{ $events->links() }}
        </div>
    @endif

    <!-- Delete Confirmation Modal -->
    <flux:modal :open="(bool) $deletingEventId" wire:close="$set('deletingEventId', null)">
        <div class="space-y-4">
            <flux:heading size="lg">{{ __('Cancel Event?') }}</flux:heading>
            <p class="text-sm text-zinc-500">
                {{ __('Are you sure you want to cancel this event? This will soft-delete the event and queue email notifications to all registered ticket holders.') }}
            </p>
            <div class="flex justify-end gap-3 pt-2">
                <flux:button variant="subtle" wire:click="$set('deletingEventId', null)">
                    {{ __('Keep Event') }}
                </flux:button>
                <flux:button variant="danger" wire:click="deleteEvent">
                    {{ __('Confirm Cancellation') }}
                </flux:button>
            </div>
        </div>
    </flux:modal>
</div>
