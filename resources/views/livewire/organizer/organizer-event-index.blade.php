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

    <!-- Search & Filter Toolbar -->
    <x-ui.search-toolbar :placeholder="__('Search events by title, location, or description...')">
        <flux:select wire:model.live="statusFilter" class="w-64">
            <flux:select.option value="all">{{ __('All Statuses') }}</flux:select.option>
            <flux:select.option value="upcoming">{{ __('Upcoming') }}</flux:select.option>
            <flux:select.option value="active">{{ __('Active') }}</flux:select.option>
            <flux:select.option value="in_progress">{{ __('In Progress') }}</flux:select.option>
            <flux:select.option value="sold_out">{{ __('Sold Out') }}</flux:select.option>
            <flux:select.option value="concluded">{{ __('Concluded') }}</flux:select.option>
        </flux:select>
    </x-ui.search-toolbar>

    @if ($events->isEmpty())
        @if (filled($search) || $statusFilter !== 'all')
            <x-ui.empty-state
                icon="calendar"
                :title="__('No matching events found')"
                :description="__('No events match your current search criteria or status filter. Try clearing your search query or changing the filter.')"
            />
        @else
            <x-ui.empty-state
                icon="calendar"
                :title="__('No events created yet')"
                :description="__('Get started by creating your first event to publish dates, location, and ticket capacity.')"
            >
                <x-slot:actions>
                    <flux:button variant="primary" :href="route('organizer.events.create')" wire:navigate>
                        {{ __('Create Event') }}
                    </flux:button>
                </x-slot:actions>
            </x-ui.empty-state>
        @endif
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
                        <flux:table.row wire:key="event-{{ $event->id }}">
                            <flux:table.cell class="font-medium px-4 py-3.5 first:pl-6 last:pr-6">
                                <div class="flex items-center gap-3">
                                    @if ($event->banner_path)
                                        <img src="{{ $event->bannerUrl() }}" alt="Thumbnail" class="size-9 aspect-[16/9] object-cover rounded-lg shrink-0 border border-zinc-200 dark:border-zinc-700 shadow-2xs" />
                                    @else
                                        <div class="size-9 aspect-[16/9] bg-purple-100 dark:bg-purple-950/60 border border-purple-200 dark:border-purple-800 rounded-lg flex items-center justify-center text-purple-600 dark:text-purple-400 shrink-0">
                                            <flux:icon name="photo" class="size-4" />
                                        </div>
                                    @endif
                                    <a href="{{ route('events.show', $event) }}" target="_blank" class="hover:underline flex items-center gap-1.5 text-sm">
                                        <span>{{ $event->title }}</span>
                                        <flux:icon name="arrow-top-right-on-square" class="size-3.5 text-zinc-400 shrink-0" />
                                    </a>
                                </div>
                            </flux:table.cell>

                            <flux:table.cell class="text-xs text-zinc-700 dark:text-zinc-300 font-medium whitespace-nowrap px-4 py-3.5 first:pl-6 last:pr-6">
                                {{ $event->starts_at->format('M j, Y \a\t g:i A') }}
                            </flux:table.cell>

                            <flux:table.cell class="text-xs text-zinc-700 dark:text-zinc-300 font-medium max-w-xs truncate whitespace-nowrap px-4 py-3.5 first:pl-6 last:pr-6" title="{{ $event->location }}">
                                {{ $event->location }}
                            </flux:table.cell>

                            <flux:table.cell class="whitespace-nowrap px-4 py-3.5 first:pl-6 last:pr-6">
                                <div class="flex items-center gap-1.5 text-xs font-mono">
                                    <span class="font-bold text-zinc-900 dark:text-white">{{ $event->tickets_sold }} / {{ $event->capacity }}</span>
                                    <span class="text-zinc-600 dark:text-zinc-400 font-medium">({{ $event->remainingTickets() }} left)</span>
                                </div>
                            </flux:table.cell>

                            <flux:table.cell class="whitespace-nowrap px-4 py-3.5 first:pl-6 last:pr-6">
                                <x-ui.status-badge :event="$event" />
                            </flux:table.cell>

                            <flux:table.cell align="end" class="px-4 py-3.5 first:pl-6 last:pr-6">
                                <flux:dropdown align="end">
                                    <flux:button variant="subtle" icon="ellipsis-horizontal" size="sm" aria-label="{{ __('Actions') }}" />

                                    <flux:menu>
                                        @if ($event->hasEnded())
                                            <flux:menu.item icon="eye" :href="route('organizer.events.check-in', $event)" wire:navigate>
                                                {{ __('View Details') }}
                                            </flux:menu.item>
                                        @else
                                            <flux:menu.item icon="qr-code" :href="route('organizer.events.check-in', $event)" wire:navigate>
                                                {{ __('Door Check-In') }}
                                            </flux:menu.item>
                                        @endif
                                        <flux:menu.item icon="users" :href="route('organizer.events.attendees', $event)" wire:navigate>
                                            {{ __('View Roster') }}
                                        </flux:menu.item>
                                        @if (! $event->hasEnded())
                                            <flux:menu.item icon="pencil-square" :href="route('organizer.events.edit', $event)" wire:navigate>
                                                {{ __('Edit Event') }}
                                            </flux:menu.item>
                                            <flux:menu.separator />
                                            <flux:menu.item icon="trash" variant="danger" wire:click="confirmDelete({{ $event->id }})">
                                                {{ __('Cancel Event') }}
                                            </flux:menu.item>
                                        @endif
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
    <flux:modal name="cancel-event-modal" class="space-y-4 backdrop:backdrop-blur-sm backdrop:bg-black/50">
        <div class="space-y-4">
            <flux:heading size="lg">{{ __('Cancel Event?') }}</flux:heading>
            <p class="text-sm text-zinc-600 dark:text-zinc-400 font-medium">
                {{ __('Are you sure you want to cancel this event? This will soft-delete the event and queue email notifications to all registered ticket holders.') }}
            </p>
            <div class="flex justify-end gap-3 pt-2">
                <flux:modal.close>
                    <flux:button variant="subtle">
                        {{ __('Keep Event') }}
                    </flux:button>
                </flux:modal.close>
                <flux:button variant="danger" wire:click="deleteEvent" wire:loading.attr="disabled">
                    {{ __('Confirm Cancellation') }}
                </flux:button>
            </div>
        </div>
    </flux:modal>
</div>
