<x-layouts::app :title="__('Dashboard')">
    <div class="space-y-6">
        {{-- Welcome Header --}}
        @if (auth()->user()?->isOrganizer() || auth()->user()?->isAdmin())
            <div
                class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white dark:bg-zinc-800/80 p-6 rounded-xl border border-zinc-200 dark:border-zinc-700/60 shadow-xs">
                <div>
                    <flux:heading size="xl" level="1">{{ __('Welcome back, :name!', ['name' => auth()->user()->name]) }}
                    </flux:heading>
                    <flux:subheading>
                        {{ __('Manage your event tickets, discover upcoming gatherings, or host your own events.') }}
                    </flux:subheading>
                </div>

                <div>
                    <flux:button variant="outline" icon="plus" :href="route('organizer.events.create')" wire:navigate>
                        {{ __('Create New Event') }}
                    </flux:button>
                </div>
            </div>
        @endif

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
            <x-cards.dashboard-stat-card
                :label="__('Event Discovery')"
                :title="__('Browse Events')"
                :description="__('Explore upcoming summits, workshops, and concerts.')"
                icon="calendar"
                iconColor="text-purple-600 dark:text-purple-400"
                :buttonText="__('View All Events')"
                :href="route('events.index')"
            />

            <x-cards.dashboard-stat-card
                :label="__('Digital Passes')"
                :title="__('My Tickets')"
                :description="__('View and present your claimed digital ticket pass codes.')"
                icon="ticket"
                iconColor="text-emerald-600 dark:text-emerald-400"
                :buttonText="__('My Pass Codes')"
                :href="route('tickets.index')"
            />

            @if (auth()->user()?->isOrganizer() || auth()->user()?->isAdmin())
                <x-cards.dashboard-stat-card
                    :label="__('Hosting')"
                    :title="__('Managed Events')"
                    :description="__('Track event sales, edit details, and view attendee rosters.')"
                    icon="building-office-2"
                    iconColor="text-indigo-600 dark:text-indigo-400"
                    :buttonText="__('Manage Hosted Events')"
                    :href="route('organizer.events.index')"
                />
            @else
                <x-cards.dashboard-stat-card
                    :label="__('Account')"
                    :title="__('Account Settings')"
                    :description="__('Manage profile information, password, and security.')"
                    icon="cog-6-tooth"
                    iconColor="text-zinc-500"
                    :buttonText="__('Settings')"
                    :href="route('profile.edit')"
                />
            @endif
        </div>

        {{-- Featured Events Carousel (Attendees) --}}
        @if (!auth()->user()?->isOrganizer() && !auth()->user()?->isAdmin())
            <div class="pt-2">
                <livewire:events.featured-events-carousel />
            </div>
        @endif

        {{-- Organizer Analytics & Performance Dashboard --}}
        @if (auth()->user()?->isOrganizer() || auth()->user()?->isAdmin())
            <div class="pt-4">
                <livewire:organizer.organizer-analytics />
            </div>
        @endif
    </div>
</x-layouts::app>