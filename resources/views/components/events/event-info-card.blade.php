@props([
    'event',
])

<flux:card {{ $attributes->merge(['class' => 'space-y-6']) }}>
    <flux:heading size="lg" class="text-lg font-bold text-zinc-900 dark:text-white">
        {{ __('Event Information') }}
    </flux:heading>

    <div class="space-y-5 text-sm">
        <div class="flex items-start gap-3">
            <flux:icon name="calendar" class="size-5 text-purple-600 dark:text-purple-400 mt-0.5 shrink-0" />
            <div>
                <p class="font-bold text-zinc-900 dark:text-zinc-100">{{ __('Date & Time') }}</p>
                <p class="text-zinc-600 dark:text-zinc-400 text-xs font-medium">{{ $event->starts_at->format('l, F j, Y') }}</p>
                <p class="text-zinc-600 dark:text-zinc-400 text-xs font-medium">{{ $event->starts_at->format('g:i A') }}</p>
            </div>
        </div>

        <div class="flex items-start gap-3">
            <flux:icon name="map-pin" class="size-5 text-purple-600 dark:text-purple-400 mt-0.5 shrink-0" />
            <div>
                <p class="font-bold text-zinc-900 dark:text-zinc-100">{{ __('Location') }}</p>
                <p class="text-zinc-600 dark:text-zinc-400 text-xs font-medium leading-normal">{{ $event->location }}</p>
            </div>
        </div>

        <div class="flex items-start gap-3">
            <flux:icon name="ticket" class="size-5 text-purple-600 dark:text-purple-400 mt-0.5 shrink-0" />
            <div>
                <p class="font-bold text-zinc-900 dark:text-zinc-100">{{ __('Availability') }}</p>
                <p class="text-zinc-600 dark:text-zinc-400 text-xs font-medium">{{ __(':sold / :capacity tickets claimed', ['sold' => $event->tickets_sold, 'capacity' => $event->capacity]) }}</p>
            </div>
        </div>
    </div>

    <flux:separator />

    <!-- Registration Component -->
    <livewire:tickets.register-ticket :event="$event" :key="$event->id" />
</flux:card>
