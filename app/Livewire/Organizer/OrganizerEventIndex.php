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
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Organizer Events')]
class OrganizerEventIndex extends Component
{
    use WithPagination;

    #[Url(history: true)]
    public string $search = '';

    #[Url(history: true)]
    public string $statusFilter = 'all';

    public ?int $deletingEventId = null;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

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
        $query = Auth::user()
            ->organizedEvents()
            ->latest('starts_at');

        if (filled($this->search)) {
            $search = '%'.$this->search.'%';
            $query->where(function ($q) use ($search): void {
                $q->where('title', 'like', $search)
                    ->orWhere('location', 'like', $search)
                    ->orWhere('description', 'like', $search);
            });
        }

        match ($this->statusFilter) {
            'active' => $query->where('starts_at', '>', now())->whereColumn('tickets_sold', '<', 'capacity'),
            'in_progress' => $query->where('starts_at', '<=', now())->where('starts_at', '>', now()->subHours(24)),
            'sold_out' => $query->whereColumn('tickets_sold', '>=', 'capacity')->where('starts_at', '>', now()->subHours(24)),
            'concluded' => $query->where('starts_at', '<=', now()->subHours(24)),
            'upcoming' => $query->where('starts_at', '>=', now()),
            default => null,
        };

        $events = $query->paginate(10);

        return view('livewire.organizer.organizer-event-index', [
            'events' => $events,
        ]);
    }
}
