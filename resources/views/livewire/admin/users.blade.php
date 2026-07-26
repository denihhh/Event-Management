<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <flux:heading size="xl" level="1">{{ __('User Management') }}</flux:heading>
            <flux:subheading>{{ __('Search platform users, view granted roles, and impersonate user accounts.') }}</flux:subheading>
        </div>
    </div>

    <!-- Search & Role Filter Toolbar -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="md:col-span-2">
            <flux:input
                wire:model.live.debounce.300ms="search"
                icon="magnifying-glass"
                placeholder="{{ __('Search by name or email...') }}"
                clearable
            />
        </div>
        <div>
            <flux:select wire:model.live="role" placeholder="{{ __('Filter by role') }}">
                <flux:select.option value="all">{{ __('All Roles') }}</flux:select.option>
                <flux:select.option value="admin">{{ __('Admin Only') }}</flux:select.option>
                <flux:select.option value="organizer">{{ __('Organizer Only') }}</flux:select.option>
                <flux:select.option value="attendee">{{ __('Attendee Only') }}</flux:select.option>
            </flux:select>
        </div>
    </div>

    <!-- User Roster Table -->
    <div class="overflow-x-auto rounded-xl border border-zinc-200 dark:border-zinc-700/60 bg-white dark:bg-zinc-800/50 shadow-xs">
        <flux:table>
            <flux:table.columns>
                <flux:table.column class="px-4 py-3.5 first:pl-6 last:pr-6">{{ __('User') }}</flux:table.column>
                <flux:table.column class="w-36 whitespace-nowrap px-4 py-3.5 first:pl-6 last:pr-6">{{ __('Role') }}</flux:table.column>
                <flux:table.column align="end" class="w-40 whitespace-nowrap px-4 py-3.5 first:pl-6 last:pr-6">{{ __('Actions') }}</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($this->users as $user)
                    <flux:table.row wire:key="user-{{ $user->id }}">
                        <flux:table.cell class="px-4 py-3.5 first:pl-6 last:pr-6">
                            <div class="flex items-center gap-3 py-0.5">
                                <flux:avatar
                                    size="sm"
                                    :name="$user->name"
                                    class="shrink-0"
                                />
                                <div class="min-w-0">
                                    <p class="font-medium truncate text-sm text-zinc-900 dark:text-zinc-100">{{ $user->name }}</p>
                                    <p class="text-xs text-zinc-500 dark:text-zinc-400 truncate">{{ $user->email }}</p>
                                </div>
                            </div>
                        </flux:table.cell>

                        <flux:table.cell class="whitespace-nowrap px-4 py-3.5 first:pl-6 last:pr-6">
                            @if ($user->isAdmin())
                                <flux:badge color="amber" size="sm" rounded>{{ __('Admin') }}</flux:badge>
                            @elseif ($user->isOrganizer())
                                <div class="space-y-0.5">
                                    <flux:badge color="purple" size="sm" rounded>{{ __('Organizer') }}</flux:badge>
                                    @if ($user->organizer_expires_at)
                                        <p class="text-[10px] text-zinc-400">Expires {{ $user->organizer_expires_at->format('M j, Y') }}</p>
                                    @endif
                                </div>
                            @else
                                <flux:badge color="zinc" size="sm" rounded>{{ __('Attendee') }}</flux:badge>
                            @endif
                        </flux:table.cell>

                        <flux:table.cell align="end" class="px-4 py-3.5 first:pl-6 last:pr-6">
                            <div class="flex items-center justify-end gap-2 whitespace-nowrap">
                                @if ($user->canBeImpersonated())
                                    <flux:button
                                        size="sm"
                                        variant="subtle"
                                        icon="arrow-right-end-on-rectangle"
                                        wire:click="impersonate({{ $user->id }})"
                                        wire:loading.attr="disabled"
                                        wire:target="impersonate({{ $user->id }})"
                                    >
                                        {{ __('Impersonate') }}
                                    </flux:button>
                                @else
                                    <flux:badge color="zinc" size="sm" rounded>{{ __('Cannot impersonate') }}</flux:badge>
                                @endif
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="3" class="py-10 text-center px-4 first:pl-6 last:pr-6">
                            <p class="text-zinc-400 text-sm">
                                {{ ($search || $role !== 'all') ? __('No users match your search or role filter.') : __('No other users found.') }}
                            </p>
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </div>

    <!-- Pagination -->
    @if ($this->users->hasPages())
        <div class="pt-2">
            {{ $this->users->links() }}
        </div>
    @endif
</div>
