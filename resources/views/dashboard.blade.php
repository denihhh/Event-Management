<x-layouts::app :title="__('Dashboard')">
    <div class="space-y-6">
        {{-- Welcome Header --}}
        <div
            class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white dark:bg-zinc-800/80 p-6 rounded-xl border border-zinc-200 dark:border-zinc-700/60 shadow-xs">
            <div>
                <flux:heading size="xl" level="1">{{ __('Welcome back, :name!', ['name' => auth()->user()->name]) }}
                </flux:heading>
                <flux:subheading>
                    {{ __('Manage your event tickets, discover upcoming gatherings, or host your own events.') }}
                </flux:subheading>
            </div>

            @if (auth()->user()?->isOrganizer() || auth()->user()?->isAdmin())
                <div>
                    <flux:button variant="outline" icon="plus" :href="route('organizer.events.create')" wire:navigate>
                        {{ __('Create New Event') }}
                    </flux:button>
                </div>
            @endif
        </div>

        {{-- Attendee Onboarding Callout Banner --}}
        @if (!auth()->user()?->isOrganizer() && !auth()->user()?->isAdmin())
            <div
                class="p-6 bg-gradient-to-r from-purple-900/90 to-indigo-900/90 text-white rounded-xl shadow-md flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
                <div class="space-y-2 max-w-xl">
                    <div class="flex items-center gap-2">
                        <flux:badge color="purple" variant="solid" size="sm">{{ __('Organizer Program') }}</flux:badge>
                    </div>
                    <h2 class="text-xl font-bold text-white">{{ __('Want to host your own events?') }}</h2>
                    <p class="text-sm text-purple-100/90">
                        {{ __('Apply for organizer privileges to publish public events, manage maximum ticket capacity, track attendee rosters, and stream CSV reports.') }}
                    </p>
                </div>
                <flux:button variant="primary" :href="route('become-organizer.edit')" wire:navigate
                    class="shrink-0 bg-white text-purple-950 hover:bg-purple-100 font-semibold border-0">
                    {{ __('Apply as Organizer') }}
                </flux:button>
            </div>
        @endif

        {{-- Dashboard Stat Cards Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <flux:card class="space-y-3">
                <div class="flex items-center justify-between">
                    <span
                        class="text-xs font-semibold uppercase tracking-wider text-zinc-500">{{ __('Event Discovery') }}</span>
                    <flux:icon name="calendar" class="size-5 text-purple-600 dark:text-purple-400" />
                </div>
                <flux:heading size="lg">{{ __('Browse Events') }}</flux:heading>
                <p class="text-xs text-zinc-500">{{ __('Explore upcoming summits, workshops, and concerts.') }}</p>
                <div class="pt-2">
                    <flux:button size="sm" variant="primary" :href="route('events.index')" wire:navigate class="w-full bg-purple-600 hover:bg-purple-500 text-white dark:bg-white dark:hover:bg-zinc-100 dark:text-zinc-900 font-medium">
                        {{ __('View All Events') }}
                    </flux:button>
                </div>
            </flux:card>

            <flux:card class="space-y-3">
                <div class="flex items-center justify-between">
                    <span
                        class="text-xs font-semibold uppercase tracking-wider text-zinc-500">{{ __('Digital Passes') }}</span>
                    <flux:icon name="ticket" class="size-5 text-emerald-600 dark:text-emerald-400" />
                </div>
                <flux:heading size="lg">{{ __('My Tickets') }}</flux:heading>
                <p class="text-xs text-zinc-500">{{ __('View and present your claimed digital ticket pass codes.') }}
                </p>
                <div class="pt-2">
                    <flux:button size="sm" variant="primary" :href="route('tickets.index')" wire:navigate class="w-full bg-purple-600 hover:bg-purple-500 text-white dark:bg-white dark:hover:bg-zinc-100 dark:text-zinc-900 font-medium">
                        {{ __('My Pass Codes') }}
                    </flux:button>
                </div>
            </flux:card>

            @if (auth()->user()?->isOrganizer() || auth()->user()?->isAdmin())
                <flux:card class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span
                            class="text-xs font-semibold uppercase tracking-wider text-zinc-500">{{ __('Hosting') }}</span>
                        <flux:icon name="building-office-2" class="size-5 text-indigo-600 dark:text-indigo-400" />
                    </div>
                    <flux:heading size="lg">{{ __('Managed Events') }}</flux:heading>
                    <p class="text-xs text-zinc-500">{{ __('Track event sales, edit details, and view attendee rosters.') }}
                    </p>
                    <div class="pt-2">
                        <flux:button size="sm" variant="primary" :href="route('organizer.events.index')" wire:navigate
                            class="w-full bg-purple-600 hover:bg-purple-500 text-white dark:bg-white dark:hover:bg-zinc-100 dark:text-zinc-900 font-medium">
                            {{ __('Manage Hosted Events') }}
                        </flux:button>
                    </div>
                </flux:card>
            @else
                <flux:card class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span
                            class="text-xs font-semibold uppercase tracking-wider text-zinc-500">{{ __('Account') }}</span>
                        <flux:icon name="cog-6-tooth" class="size-5 text-zinc-500" />
                    </div>
                    <flux:heading size="lg">{{ __('Account Settings') }}</flux:heading>
                    <p class="text-xs text-zinc-500">{{ __('Manage profile information, password, and security.') }}</p>
                    <div class="pt-2">
                        <flux:button size="sm" variant="primary" :href="route('profile.edit')" wire:navigate class="w-full bg-purple-600 hover:bg-purple-500 text-white dark:bg-white dark:hover:bg-zinc-100 dark:text-zinc-900 font-medium">
                            {{ __('Settings') }}
                        </flux:button>
                    </div>
                </flux:card>
            @endif
        </div>

        {{-- Organizer Analytics & Performance Dashboard --}}
        @if (auth()->user()?->isOrganizer() || auth()->user()?->isAdmin())
            <div class="pt-4">
                <livewire:organizer.organizer-analytics />
            </div>
        @endif
    </div>
</x-layouts::app>