<div class="space-y-6">
    <style media="print">
        @media print {
            body * {
                visibility: hidden !important;
            }
            .print-ticket-target, .print-ticket-target * {
                visibility: visible !important;
            }
            .print-ticket-target {
                position: absolute !important;
                left: 0 !important;
                top: 0 !important;
                width: 100% !important;
                border: 1px solid #d4d4d8 !important;
                background-color: #ffffff !important;
                color: #09090b !important;
                box-shadow: none !important;
                padding: 1.5rem !important;
                border-radius: 0.75rem !important;
            }
            .print-ticket-target [data-flux-dropdown],
            .print-ticket-target button {
                display: none !important;
            }
        }
    </style>

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <flux:heading size="xl" level="1">My Tickets</flux:heading>
            <flux:subheading>Manage your event passes and digital ticket codes.</flux:subheading>
        </div>

        <flux:radio.group wire:model.live="filter" variant="segmented" size="sm">
            <flux:radio value="all" label="All" />
            <flux:radio value="active" label="Active" />
            <flux:radio value="checked_in" label="Checked In" />
            <flux:radio value="past" label="Past" />
            <flux:radio value="cancelled" label="Cancelled" />
        </flux:radio.group>
    </div>

    @if ($tickets->isEmpty())
        <x-ui.empty-state
            icon="ticket"
            title="No tickets found"
            description="No ticket passes match your selected filter. Explore upcoming events to claim digital passes."
        >
            <x-slot:actions>
                <flux:button variant="primary" :href="route('events.index')" wire:navigate>
                    Browse Events
                </flux:button>
            </x-slot:actions>
        </x-ui.empty-state>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach ($tickets as $ticket)
                <x-cards.ticket-card wire:key="ticket-{{ $ticket->id }}" :ticket="$ticket" />
            @endforeach
        </div>

        <div class="pt-4">
            {{ $tickets->links() }}
        </div>
    @endif

    <!-- Cancel Confirmation Modal -->
    <flux:modal name="cancel-ticket-modal" class="space-y-4 backdrop:backdrop-blur-sm backdrop:bg-black/50">
        <div>
            <flux:heading size="lg">Cancel Ticket?</flux:heading>
            <p class="text-sm text-zinc-500 mt-1">
                Are you sure you want to cancel this ticket? Your reservation will be released back to the event capacity.
            </p>
        </div>
        <div class="flex justify-end gap-3 pt-2">
            <flux:modal.close>
                <flux:button variant="subtle">
                    Keep Ticket
                </flux:button>
            </flux:modal.close>
            <flux:button variant="danger" wire:click="cancelTicket" wire:loading.attr="disabled">
                Yes, Cancel Ticket
            </flux:button>
        </div>
    </flux:modal>
</div>
