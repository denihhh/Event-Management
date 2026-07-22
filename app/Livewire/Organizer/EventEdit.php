<?php

namespace App\Livewire\Organizer;

use App\Mail\EventUpdatedMail;
use App\Models\Event;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Edit Event')]
class EventEdit extends Component
{
    #[Locked]
    public Event $event;

    public string $title = '';

    public string $description = '';

    public string $starts_at = '';

    public string $location = '';

    public int $capacity = 50;

    public function mount(Event $event): void
    {
        Gate::authorize('update', $event);

        $this->event = $event;
        $this->title = $event->title;
        $this->description = $event->description;
        $this->starts_at = $event->starts_at->format('Y-m-d\TH:i');
        $this->location = $event->location;
        $this->capacity = $event->capacity;
    }

    public function rules(): array
    {
        $minCapacity = max(1, $this->event->tickets_sold);

        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'starts_at' => ['required', 'date'],
            'location' => ['required', 'string', 'max:255'],
            'capacity' => ['required', 'integer', 'min:'.$minCapacity],
        ];
    }

    public function update(): void
    {
        Gate::authorize('update', $this->event);

        $validated = $this->validate();

        $originalStartsAt = $this->event->starts_at->format('Y-m-d H:i');
        $originalLocation = $this->event->location;

        $this->event->update($validated);

        $newStartsAt = $this->event->fresh()->starts_at->format('Y-m-d H:i');
        $significantChange = ($originalStartsAt !== $newStartsAt) || ($originalLocation !== $validated['location']);

        if ($significantChange) {
            // Reset reminder sent flag so reminder re-fires for new date
            $this->event->tickets()->update(['reminder_sent' => false]);

            // Notify all ticket holders of update
            $this->event->loadMissing('tickets.user');
            foreach ($this->event->tickets as $ticket) {
                if ($ticket->user) {
                    Mail::to($ticket->user)->queue(new EventUpdatedMail($ticket));
                }
            }

            Flux::toast(variant: 'success', text: __('Event updated and attendees notified of changes!'));
        } else {
            Flux::toast(variant: 'success', text: __('Event updated successfully!'));
        }

        $this->redirect(route('organizer.events.index'), navigate: true);
    }

    public function render(): View
    {
        return view('livewire.organizer.event-edit');
    }
}
