<div class="max-w-3xl mx-auto space-y-6">
    <div>
        <flux:button variant="subtle" icon="arrow-left" size="sm" :href="route('organizer.events.index')" wire:navigate class="mb-2">
            Back to Events
        </flux:button>
        <flux:heading size="xl" level="1">Edit Event</flux:heading>
        <flux:subheading>Update event information. Modifying date or location will notify registered attendees.</flux:subheading>
    </div>

    <form wire:submit="update" class="space-y-6 bg-white dark:bg-zinc-800 p-6 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-xs">
        <flux:input
            wire:model="title"
            label="Event Title"
            required
        />

        <flux:textarea
            wire:model="description"
            label="Description"
            rows="5"
            required
        />

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <flux:input
                wire:model="starts_at"
                type="datetime-local"
                label="Date & Start Time"
                required
            />

            <div>
                <flux:input
                    wire:model="capacity"
                    type="number"
                    label="Ticket Capacity"
                    min="{{ max(1, $event->tickets_sold) }}"
                    required
                />
                <p class="text-xs text-zinc-500 mt-1">
                    Currently {{ $event->tickets_sold }} ticket(s) sold. Cannot reduce below this amount.
                </p>
            </div>
        </div>

        <flux:input
            wire:model="location"
            label="Location / Venue"
            required
        />

        <div class="flex items-center justify-end gap-3 pt-4 border-t border-zinc-100 dark:border-zinc-700">
            <flux:button variant="subtle" :href="route('organizer.events.index')" wire:navigate>
                Cancel
            </flux:button>
            <flux:button type="submit" variant="primary" wire:loading.attr="disabled">
                Save Changes
            </flux:button>
        </div>
    </form>
</div>
