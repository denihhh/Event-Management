<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <flux:heading size="xl" level="1">{{ __('System Events Audit') }}</flux:heading>
            <flux:subheading>{{ __('Master platform-wide audit log of all published, past, and cancelled events.') }}</flux:subheading>
        </div>
    </div>

    <!-- Search & Filter Toolbar -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="sm:col-span-2">
            <flux:input
                wire:model.live.debounce.300ms="search"
                icon="magnifying-glass"
                placeholder="{{ __('Search by title, location, or organizer...') }}"
                clearable
            />
        </div>
        <div>
            <flux:select wire:model.live="statusFilter">
                <flux:select.option value="all">{{ __('All Events') }}</flux:select.option>
                <flux:select.option value="active">{{ __('Active / Upcoming') }}</flux:select.option>
                <flux:select.option value="past">{{ __('Past Events') }}</flux:select.option>
                <flux:select.option value="trashed">{{ __('Cancelled / Soft-deleted') }}</flux:select.option>
            </flux:select>
        </div>
    </div>

    @if ($events->isEmpty())
        <div class="text-center py-16 bg-white dark:bg-zinc-800/50 rounded-xl border border-zinc-200 dark:border-zinc-700/60 p-8 space-y-3">
            <flux:icon name="shield-check" class="mx-auto size-12 text-zinc-400" />
            <flux:heading size="lg">{{ __('No events found') }}</flux:heading>
            <p class="text-sm text-zinc-500">{{ __('No events match your current filter settings.') }}</p>
        </div>
    @else
        <div class="overflow-x-auto rounded-xl border border-zinc-200 dark:border-zinc-700/60 bg-white dark:bg-zinc-800/50 shadow-xs">
            <flux:table>
                <flux:table.columns>
                    <flux:table.column class="px-4 py-3.5 first:pl-6 last:pr-6">{{ __('Event') }}</flux:table.column>
                    <flux:table.column class="px-4 py-3.5 first:pl-6 last:pr-6">{{ __('Organizer') }}</flux:table.column>
                    <flux:table.column class="whitespace-nowrap px-4 py-3.5 first:pl-6 last:pr-6">{{ __('Date & Time') }}</flux:table.column>
                    <flux:table.column class="whitespace-nowrap px-4 py-3.5 first:pl-6 last:pr-6">{{ __('Tickets Sold') }}</flux:table.column>
                    <flux:table.column class="whitespace-nowrap px-4 py-3.5 first:pl-6 last:pr-6">{{ __('Status') }}</flux:table.column>
                    <flux:table.column align="end" class="px-4 py-3.5 first:pl-6 last:pr-6">{{ __('Actions') }}</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @foreach ($events as $event)
                        <flux:table.row key="{{ $event->id }}">
                            <flux:table.cell class="font-medium px-4 py-3.5 first:pl-6 last:pr-6">
                                <div class="space-y-0.5">
                                    <a href="{{ route('events.show', $event) }}" target="_blank" class="hover:underline flex items-center gap-1.5 font-semibold text-sm">
                                        <span>{{ $event->title }}</span>
                                        <flux:icon name="arrow-top-right-on-square" class="size-3.5 text-zinc-400 shrink-0" />
                                    </a>
                                    <p class="text-xs text-zinc-500 truncate max-w-xs" title="{{ $event->location }}">{{ $event->location }}</p>
                                </div>
                            </flux:table.cell>

                            <flux:table.cell class="px-4 py-3.5 first:pl-6 last:pr-6">
                                <div class="min-w-0">
                                    <p class="text-sm font-medium truncate text-zinc-900 dark:text-zinc-100">{{ $event->organizer->name ?? 'N/A' }}</p>
                                    <p class="text-xs text-zinc-500 truncate">{{ $event->organizer->email ?? 'N/A' }}</p>
                                </div>
                            </flux:table.cell>

                            <flux:table.cell class="text-xs text-zinc-500 whitespace-nowrap px-4 py-3.5 first:pl-6 last:pr-6">
                                {{ $event->starts_at->format('M j, Y \a\t g:i A') }}
                            </flux:table.cell>

                            <flux:table.cell class="whitespace-nowrap px-4 py-3.5 first:pl-6 last:pr-6">
                                <span class="font-mono text-xs font-semibold">{{ $event->tickets_sold }} / {{ $event->capacity }}</span>
                            </flux:table.cell>

                            <flux:table.cell class="whitespace-nowrap px-4 py-3.5 first:pl-6 last:pr-6">
                                @if ($event->trashed())
                                    <flux:badge color="red" size="sm" rounded>{{ __('Cancelled') }}</flux:badge>
                                @elseif ($event->hasStarted())
                                    <flux:badge color="zinc" size="sm" rounded>{{ __('Past') }}</flux:badge>
                                @elseif ($event->isSoldOut())
                                    <flux:badge color="amber" size="sm" rounded>{{ __('Sold Out') }}</flux:badge>
                                @else
                                    <flux:badge color="emerald" size="sm" rounded>{{ __('Active') }}</flux:badge>
                                @endif
                            </flux:table.cell>

                            <flux:table.cell align="end" class="px-4 py-3.5 first:pl-6 last:pr-6">
                                <div class="flex items-center justify-end gap-2 whitespace-nowrap">
                                    @if ($event->trashed())
                                        <flux:button
                                            size="sm"
                                            variant="subtle"
                                            icon="arrow-path"
                                            wire:click="restoreEvent({{ $event->id }})"
                                        >
                                            {{ __('Restore') }}
                                        </flux:button>
                                    @else
                                        <flux:button
                                            size="sm"
                                            variant="danger"
                                            icon="trash"
                                            wire:click="deleteEvent({{ $event->id }})"
                                        >
                                            {{ __('Cancel') }}
                                        </flux:button>
                                    @endif
                                </div>
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
</div>
