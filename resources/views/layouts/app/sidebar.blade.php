<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <flux:sidebar sticky collapsible="mobile" class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.header>
                <x-app-logo :sidebar="true" href="{{ route('dashboard') }}" wire:navigate />
                <flux:sidebar.collapse class="lg:hidden" />
            </flux:sidebar.header>

            <flux:sidebar.nav>
                {{-- Module 1: Main --}}
                <flux:sidebar.group :heading="__('Main')" class="grid mb-4">
                    <flux:sidebar.item icon="layout-grid" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>
                        {{ __('Dashboard') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="calendar" :href="route('events.index')" :current="request()->routeIs('events.*')" wire:navigate>
                        {{ __('Browse Events') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="ticket" :href="route('tickets.index')" :current="request()->routeIs('tickets.*')" wire:navigate>
                        {{ __('My Tickets') }}
                    </flux:sidebar.item>
                </flux:sidebar.group>

                {{-- Module 2: Event Operations --}}
                <flux:sidebar.group :heading="__('Event Operations')" class="grid mb-4">
                    @if (auth()->user()?->isOrganizer() || auth()->user()?->isAdmin())
                        <flux:sidebar.item icon="building-office-2" :href="route('organizer.events.index')" :current="request()->routeIs('organizer.events.index') || request()->routeIs('organizer.events.edit') || request()->routeIs('organizer.events.create') || request()->routeIs('organizer.events.check-in')" wire:navigate>
                            {{ __('Managed Events') }}
                        </flux:sidebar.item>
                        <flux:sidebar.item icon="users" :href="route('organizer.attendees.index')" :current="request()->routeIs('organizer.attendees.*') || request()->routeIs('organizer.events.attendees')" wire:navigate>
                            {{ __('Attendee Roster') }}
                        </flux:sidebar.item>
                    @endif

                    @if (! auth()->user()?->isOrganizer() && ! auth()->user()?->isAdmin())
                        <flux:sidebar.item icon="identification" :href="route('become-organizer.edit')" :current="request()->routeIs('become-organizer.*')" wire:navigate>
                            {{ __('Apply Organizer') }}
                        </flux:sidebar.item>
                    @endif

                    @if (auth()->user()?->isAdmin() && ! is_impersonating())
                        <flux:sidebar.item icon="clipboard-document-check" :href="route('admin.organizer-applications.index')" :current="request()->routeIs('admin.organizer-applications.*')" wire:navigate>
                            {{ __('Review Requests') }}
                        </flux:sidebar.item>
                    @endif
                </flux:sidebar.group>

                {{-- Module 3: System Admin (Guard: Admins Only && !is_impersonating()) --}}
                @if (auth()->user()?->isAdmin() && ! is_impersonating())
                    <flux:sidebar.group :heading="__('System Admin')" class="grid">
                        <flux:sidebar.item icon="user-group" :href="route('admin.users.index')" :current="request()->routeIs('admin.users*')" wire:navigate>
                            {{ __('User Management') }}
                        </flux:sidebar.item>
                        <flux:sidebar.item icon="shield-check" :href="route('admin.events.index')" :current="request()->routeIs('admin.events.*')" wire:navigate>
                            {{ __('Event Audit') }}
                        </flux:sidebar.item>
                        <flux:sidebar.item icon="cpu-chip" :href="route('admin.system.status')" :current="request()->routeIs('admin.system.status')" wire:navigate>
                            {{ __('System Status') }}
                        </flux:sidebar.item>
                    </flux:sidebar.group>
                @endif
            </flux:sidebar.nav>

            <flux:spacer />

            <x-desktop-user-menu class="hidden lg:block" :name="auth()->user()->name" />
        </flux:sidebar>

        <!-- Mobile Header & Navigation Drawer -->
        <flux:header class="lg:hidden">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <flux:spacer />

            <flux:dropdown position="top" align="end">
                <flux:profile
                    :initials="auth()->user()->initials()"
                    icon-trailing="chevron-down"
                />

                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <flux:avatar
                                    :name="auth()->user()->name"
                                    :initials="auth()->user()->initials()"
                                />

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <flux:heading class="truncate">{{ auth()->user()->name }}</flux:heading>
                                    <flux:text class="truncate">{{ auth()->user()->email }}</flux:text>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('profile.edit')" icon="cog-6-tooth" wire:navigate>
                            {{ __('Settings') }}
                        </flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item
                            as="button"
                            type="submit"
                            icon="arrow-right-start-on-rectangle"
                            class="w-full cursor-pointer"
                            data-test="logout-button"
                        >
                            {{ __('Log out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        {{ $slot }}

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
