<div>
    @if ($hasTicket)
        <flux:button variant="filled" icon="check" disabled class="w-full">
            You Have a Ticket
        </flux:button>
    @elseif ($isOrganizer)
        <flux:button variant="subtle" icon="building-office-2" disabled class="w-full">
            Your Event (Organizer)
        </flux:button>
    @elseif ($event->isSoldOut())
        <flux:button variant="danger" disabled class="w-full">
            Sold Out
        </flux:button>
    @elseif ($event->hasStarted())
        <flux:button variant="subtle" disabled class="w-full">
            Event Has Ended
        </flux:button>
    @else
        <flux:button variant="primary" wire:click="openModal" icon="ticket" class="w-full">
            Claim Free Ticket
        </flux:button>
    @endif

    <flux:modal wire:model="showModal" class="space-y-6 backdrop:backdrop-blur-sm backdrop:bg-black/50">
        <div>
            <flux:heading size="lg">Confirm Ticket Registration</flux:heading>
            <flux:subheading>Claim your digital ticket for {{ $event->title }}.</flux:subheading>
        </div>

        <div class="space-y-3 p-4 bg-zinc-50 dark:bg-zinc-800/80 rounded-lg text-sm border border-zinc-200 dark:border-zinc-700">
            <div class="flex justify-between">
                <span class="text-zinc-600 dark:text-zinc-400 font-medium">Event:</span>
                <span class="font-bold text-zinc-900 dark:text-zinc-100">{{ $event->title }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-zinc-600 dark:text-zinc-400 font-medium">Date & Time:</span>
                <span class="font-medium text-zinc-900 dark:text-zinc-100">{{ $event->starts_at->format('M j, Y \a\t g:i A') }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-zinc-600 dark:text-zinc-400 font-medium">Location:</span>
                <span class="font-medium text-zinc-900 dark:text-zinc-100">{{ $event->location }}</span>
            </div>
            <div class="flex justify-between border-t border-zinc-200 dark:border-zinc-700 pt-2">
                <span class="text-zinc-600 dark:text-zinc-400 font-medium">Price:</span>
                <span class="font-extrabold text-emerald-800 dark:text-emerald-400">FREE</span>
            </div>
        </div>

        <p class="text-xs text-zinc-600 dark:text-zinc-400">
            By claiming this ticket, your digital pass code will be sent to <strong>{{ auth()->user()?->email }}</strong>.
        </p>

        <div class="flex justify-end gap-3">
            <flux:modal.close>
                <flux:button variant="subtle">Cancel</flux:button>
            </flux:modal.close>
            <flux:button variant="primary" wire:click="register" wire:loading.attr="disabled">
                Confirm & Claim Ticket
            </flux:button>
        </div>
    </flux:modal>
</div>
