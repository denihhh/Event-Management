<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-zinc-50 dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100 flex flex-col antialiased">
        <flux:header container class="border-b border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900">
            <x-app-logo href="{{ route('home') }}" wire:navigate />

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

        @include('partials.impersonation-banner')

        <main class="flex-1 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            {{ $slot }}
        </main>

        <footer class="w-full border-t border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-sm text-zinc-500">
                <p>&copy; {{ date('Y') }} Eventive. {{ __('All rights reserved.') }}</p>
            </div>
        </footer>

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
