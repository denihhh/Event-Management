<?php

namespace App\Livewire\Organizer;

use App\Mail\EventCancelledMail;
use App\Models\Event;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Organizer Events')]
class OrganizerEventIndex extends Component
{
    use WithPagination;

    public ?int $deletingEventId = null;

    public function confirmDelete(int $eventId): void
    {
        $this->deletingEventId = $eventId;
        Flux::modal('cancel-event-modal')->show();
    }

    public function deleteEvent(): void
    {
        if (! $this->deletingEventId) {
            return;
        }

        $event = Event::where('id', $this->deletingEventId)
            ->where('user_id', Auth::id())
            ->with(['tickets.user'])
            ->first();

        if (! $event) {
            Flux::toast(variant: 'danger', text: __('Event not found or unauthorized.'));
            $this->deletingEventId = null;
            Flux::modal('cancel-event-modal')->close();

            return;
        }

        Gate::authorize('delete', $event);

        // Mark tickets as cancelled and notify attendees before soft-deleting
        $startsAtFormatted = $event->starts_at->format('F j, Y \a\t g:i A');
        foreach ($event->tickets as $ticket) {
            if (! $ticket->isCancelled()) {
                $ticket->update(['cancelled_at' => now()]);
            }

            if ($ticket->user) {
                Mail::to($ticket->user)->queue(new EventCancelledMail(
                    eventTitle: $event->title,
                    eventStartsAt: $startsAtFormatted,
                    eventLocation: $event->location
                ));
            }
        }

        $event->delete();

        $this->deletingEventId = null;
        Flux::modal('cancel-event-modal')->close();
        Flux::toast(variant: 'success', text: __('Event cancelled and attendees notified.'));
    }

    public function render(): View
    {
        $events = Auth::user()
            ->organizedEvents()
            ->latest('starts_at')
            ->paginate(10);

        return view('livewire.organizer.organizer-event-index', [
            'events' => $events,
        ]);
    }
}
