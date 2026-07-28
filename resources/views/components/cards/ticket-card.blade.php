@props([
    'ticket',
])

@php
    $isCancelled = $ticket->isCancelled();
    $isCheckedIn = $ticket->isCheckedIn();
    $hasStarted = $ticket->event?->hasStarted();
@endphp

<flux:card wire:key="ticket-{{ $ticket->id }}" {{ $attributes->merge(['class' => 'space-y-4 flex flex-col justify-between transition-all min-w-0 overflow-hidden bg-white dark:bg-zinc-900 shadow-sm border ' . ($isCancelled ? 'border-red-500/30 dark:border-red-900/40 bg-zinc-50/50 dark:bg-zinc-900/50' : ($isCheckedIn ? 'border-teal-500/40 dark:border-teal-900/50' : ($hasStarted ? 'border-zinc-200 dark:border-zinc-800 opacity-90' : 'border-purple-500/30 dark:border-purple-900/40')))]) }}>
    <div class="space-y-3 min-w-0">
        <div class="flex items-center justify-between gap-2 min-w-0">
            <flux:badge color="purple" size="sm" icon="user" class="shrink-0 max-w-[60%] font-semibold">
                <span class="truncate">{{ $ticket->event?->organizer?->name ?? __('Organizer') }}</span>
            </flux:badge>

            <div class="flex items-center gap-1.5 shrink-0">
                @if ($isCancelled)
                    <flux:badge color="red" size="sm" icon="x-circle" class="shrink-0 font-semibold">
                        {{ __('Cancelled') }}
                    </flux:badge>
                @elseif ($isCheckedIn)
                    <flux:badge color="teal" size="sm" icon="check-circle" class="shrink-0 font-semibold">
                        {{ __('Checked In') }}
                    </flux:badge>
                @elseif ($hasStarted)
                    <flux:badge color="zinc" size="sm" icon="clock" class="shrink-0 font-semibold">
                        {{ __('Past Event') }}
                    </flux:badge>
                @else
                    <flux:badge color="emerald" size="sm" icon="ticket" class="shrink-0 font-semibold">
                        {{ __('Active Pass') }}
                    </flux:badge>
                @endif

                @if (! $ticket->event?->trashed() || (! $isCancelled && ! $isCheckedIn && ! $hasStarted))
                    <flux:dropdown align="end">
                        <flux:button variant="subtle" icon="ellipsis-vertical" size="sm" aria-label="{{ __('Ticket Options') }}" />

                        <flux:menu>
                            @if (! $ticket->event?->trashed())
                                <flux:menu.item icon="eye" :href="route('events.show', $ticket->event)" wire:navigate>
                                    {{ __('View Details') }}
                                </flux:menu.item>
                            @endif
                            @if (! $isCancelled && ! $isCheckedIn && ! $hasStarted && ! $ticket->event?->trashed())
                                <flux:menu.item icon="arrow-down-tray" :href="route('tickets.pdf', $ticket)" target="_blank">
                                    {{ __('Download PDF') }}
                                </flux:menu.item>
                                <flux:menu.separator />
                                <flux:menu.item icon="x-circle" variant="danger" wire:click="confirmCancel({{ $ticket->id }})">
                                    {{ __('Cancel Ticket') }}
                                </flux:menu.item>
                            @endif
                        </flux:menu>
                    </flux:dropdown>
                @endif
            </div>
        </div>

        <flux:heading size="lg">
            @if ($ticket->event?->trashed())
                <span class="text-zinc-800 dark:text-zinc-200 font-bold opacity-80" title="{{ __('This event has been cancelled by the organizer.') }}">
                    {{ $ticket->event->title }}
                </span>
            @else
                <a href="{{ route('events.show', $ticket->event) }}" wire:navigate class="hover:underline text-zinc-900 dark:text-white font-bold">
                    {{ $ticket->event->title }}
                </a>
            @endif
        </flux:heading>

        <div class="space-y-1.5 text-xs text-zinc-700 dark:text-zinc-300 font-medium">
            <div class="flex items-center gap-2">
                <flux:icon name="calendar" class="size-4 text-purple-700 dark:text-purple-400 shrink-0" />
                <span>{{ $ticket->event?->starts_at?->format('l, F j, Y \a\t g:i A') }}</span>
            </div>
            <div class="flex items-center gap-2">
                <flux:icon name="map-pin" class="size-4 text-purple-700 dark:text-purple-400 shrink-0" />
                <span class="truncate">{{ $ticket->event?->location }}</span>
            </div>
        </div>

        <!-- Ticket Pass & QR Code Box -->
        @if ($isCancelled)
            <div class="p-3.5 bg-red-50/80 dark:bg-red-950/40 rounded-xl border border-red-200/90 dark:border-red-900/60 flex items-center justify-between gap-4">
                <div class="space-y-1 min-w-0">
                    <span class="text-xs uppercase tracking-wider text-red-800 dark:text-red-300 font-bold">{{ __('Void Digital Ticket') }}</span>
                    <div class="font-mono text-xs text-zinc-500 dark:text-zinc-400 line-through select-all break-all">
                        {{ $ticket->ticket_code }}
                    </div>
                    @if ($ticket->event?->trashed())
                        <p class="text-[11px] text-red-700 dark:text-red-400 font-bold">{{ __('Event was cancelled by organizer') }}</p>
                    @else
                        <p class="text-[11px] text-red-700 dark:text-red-400 font-medium">{{ __('Reservation cancelled on :date', ['date' => $ticket->cancelled_at?->format('M j, Y')]) }}</p>
                    @endif
                </div>
                <div class="shrink-0 p-2 rounded-lg bg-red-100 dark:bg-red-950/80 border border-red-200 dark:border-red-900 text-red-800 dark:text-red-300 flex flex-col items-center justify-center size-20 text-[10px] font-bold text-center">
                    <flux:icon name="x-circle" class="size-7 mb-0.5 text-red-700 dark:text-red-400" />
                    <span>{{ __('Pass Void') }}</span>
                </div>
            </div>
        @elseif ($isCheckedIn)
            <div class="p-3.5 bg-teal-50/80 dark:bg-teal-950/40 rounded-xl border border-teal-200/90 dark:border-teal-900/60 flex items-center justify-between gap-4">
                <div class="space-y-1 min-w-0">
                    <span class="text-xs uppercase tracking-wider text-teal-800 dark:text-teal-300 font-bold">{{ __('Digital Ticket') }}</span>
                    <div class="font-mono text-xs font-extrabold text-teal-900 dark:text-teal-100 select-all break-all">
                        {{ $ticket->ticket_code }}
                    </div>
                    <p class="text-[11px] text-teal-800 dark:text-teal-300 font-semibold">{{ __('Checked in at door (:time)', ['time' => $ticket->checked_in_at?->format('g:i A')]) }}</p>
                </div>
                <div class="relative shrink-0 overflow-hidden rounded-lg border border-teal-300 dark:border-teal-700 shadow-xs">
                    <img src="{{ $ticket->getQrCodeDataUri() }}" alt="QR Code" class="size-20 bg-white p-1 opacity-40 grayscale" />
                    <div class="absolute inset-0 bg-teal-950/40 dark:bg-teal-950/70 backdrop-blur-[2px] flex flex-col items-center justify-center text-center p-1 rounded-lg">
                        <div class="size-6 rounded-full bg-teal-700 dark:bg-teal-500 text-white flex items-center justify-center mb-0.5 shadow-xs">
                            <flux:icon name="check" class="size-4 stroke-[3]" />
                        </div>
                        <span class="text-[10px] font-bold tracking-wide uppercase text-white drop-shadow-xs">{{ __('Redeemed') }}</span>
                    </div>
                </div>
            </div>
        @elseif ($hasStarted)
            <div class="p-3.5 bg-zinc-100 dark:bg-zinc-800/60 rounded-xl border border-zinc-200 dark:border-zinc-700 flex items-center justify-between gap-4">
                <div class="space-y-1 min-w-0">
                    <span class="text-xs uppercase tracking-wider text-zinc-600 dark:text-zinc-400 font-semibold">{{ __('Expired Pass Code') }}</span>
                    <div class="font-mono text-xs font-semibold text-zinc-800 dark:text-zinc-300 select-all break-all">
                        {{ $ticket->ticket_code }}
                    </div>
                    <p class="text-[11px] text-zinc-600 dark:text-zinc-400 font-medium">{{ __('Event ended on :date', ['date' => $ticket->event?->starts_at?->format('M j, Y')]) }}</p>
                </div>
                <div class="relative shrink-0 overflow-hidden rounded-lg border border-zinc-300 dark:border-zinc-700 shadow-xs">
                    <img src="{{ $ticket->getQrCodeDataUri() }}" alt="QR Code" class="size-20 bg-white p-1 opacity-30 grayscale" />
                    <div class="absolute inset-0 bg-zinc-900/50 backdrop-blur-[1px] flex flex-col items-center justify-center text-center p-1 rounded-lg">
                        <flux:icon name="clock" class="size-6 text-zinc-200 mb-0.5" />
                        <span class="text-[10px] font-bold tracking-wide uppercase text-white drop-shadow-xs">{{ __('Expired') }}</span>
                    </div>
                </div>
            </div>
        @else
            <div class="p-3.5 bg-purple-50/70 dark:bg-purple-950/30 rounded-xl border border-purple-200/90 dark:border-purple-900/60 flex items-center justify-between gap-4">
                <div class="space-y-1 min-w-0">
                    <span class="text-xs uppercase tracking-wider text-purple-800 dark:text-purple-300 font-bold">{{ __('Digital Ticket') }}</span>
                    <div class="font-mono text-xs font-extrabold text-purple-900 dark:text-purple-100 select-all break-all">
                        {{ $ticket->ticket_code }}
                    </div>
                    <p class="text-[11px] text-purple-700 dark:text-purple-300 font-semibold">{{ __('Valid for event entry') }}</p>
                </div>
                <div class="shrink-0">
                    <img src="{{ $ticket->getQrCodeDataUri() }}" alt="QR Code" class="size-20 rounded-lg bg-white p-1 border border-purple-200 dark:border-purple-800 shadow-xs" />
                </div>
            </div>
        @endif
    </div>

    <div class="pt-3 border-t border-zinc-200 dark:border-zinc-800 flex items-center justify-between">
        <span class="text-xs text-zinc-600 dark:text-zinc-400 font-medium">
            {{ __('Claimed :time', ['time' => $ticket->created_at->diffForHumans()]) }}
        </span>
        @if (! $isCancelled && ! $isCheckedIn && ! $hasStarted && ! $ticket->event?->trashed())
            <flux:button
                variant="primary"
                size="sm"
                icon="arrow-down-tray"
                class="h-7 text-xs bg-purple-600 hover:bg-purple-500 text-white dark:bg-purple-500 dark:hover:bg-purple-400 dark:text-white font-medium"
                :href="route('tickets.pdf', $ticket)"
                target="_blank"
            >
                {{ __('Download PDF') }}
            </flux:button>
        @endif
    </div>
</flux:card>
