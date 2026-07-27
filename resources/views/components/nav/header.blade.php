<flux:header container {{ $attributes->merge(['class' => 'border-b border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900']) }}>
    <x-nav.brand />

    <flux:navbar class="ml-6 -mb-px hidden md:flex">
        <flux:navbar.item icon="home" :href="route('home')" :current="request()->routeIs('home')" wire:navigate>
            {{ __('Home') }}
        </flux:navbar.item>
        <flux:navbar.item icon="calendar" :href="route('events.index')" :current="request()->routeIs('events.*')" wire:navigate>
            {{ __('Explore Events') }}
        </flux:navbar.item>
    </flux:navbar>

    <flux:spacer />

    <flux:navbar class="space-x-2 rtl:space-x-reverse">
        @auth
            <flux:navbar.item icon="ticket" :href="route('tickets.index')" :current="request()->routeIs('tickets.*')" wire:navigate>
                {{ __('My Tickets') }}
            </flux:navbar.item>
            @if (auth()->user()?->isOrganizer() || auth()->user()?->isAdmin())
                <flux:navbar.item icon="building-office-2" :href="route('organizer.events.index')" :current="request()->routeIs('organizer.*')" wire:navigate>
                    {{ __('Organizer Dashboard') }}
                </flux:navbar.item>
            @endif
            <flux:navbar.item icon="layout-grid" :href="route('dashboard')" wire:navigate>
                {{ __('Dashboard') }}
            </flux:navbar.item>
        @else
            <flux:navbar.item :href="route('login')" wire:navigate>
                {{ __('Log in') }}
            </flux:navbar.item>
            <flux:button variant="primary" size="sm" :href="route('register')" wire:navigate class="bg-purple-600 hover:bg-purple-500 text-white font-medium">
                {{ __('Register') }}
            </flux:button>
        @endauth
    </flux:navbar>
</flux:header>
