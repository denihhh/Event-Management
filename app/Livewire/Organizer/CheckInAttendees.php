<?php

namespace App\Livewire\Organizer;

use App\Models\Event;
use App\Models\Ticket;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Event Door Check-In')]
class CheckInAttendees extends Component
{
    use AuthorizesRequests, WithPagination;

    public Event $event;

    #[Url(history: true)]
    public string $search = '';

    public string $ticketInput = '';

    #[Url(history: true)]
    public string $statusFilter = 'all';

    public function mount(Event $event): void
    {
        $this->authorize('manageRoster', $event);
        $this->event = $event;
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    /**
     * Fast check-in by scanning or typing exact ticket code.
     */
    public function scanCheckIn(): void
    {
        if ($this->event->hasEnded()) {
            Flux::toast(variant: 'warning', text: __('Check-in is closed for concluded events.'));

            return;
        }

        $code = trim($this->ticketInput);

        if (empty($code)) {
            return;
        }

        /** @var Ticket|null $ticket */
        $ticket = $this->event->tickets()
            ->with('user')
            ->where('ticket_code', $code)
            ->first();

        if (! $ticket) {
            Flux::toast(variant: 'danger', text: __('Ticket code not found for this event.'));

            return;
        }

        if ($ticket->isCancelled()) {
            Flux::toast(variant: 'danger', text: __('This ticket was cancelled and is void.'));

            return;
        }

        if ($ticket->isCheckedIn()) {
            Flux::toast(
                variant: 'warning',
                text: __('Ticket already checked in at :time by :name.', [
                    'time' => $ticket->checked_in_at?->format('g:i A') ?? '',
                    'name' => $ticket->user ? $ticket->user->name : 'Attendee',
                ])
            );
            $this->ticketInput = '';

            return;
        }

        $ticket->checkIn();
        $this->ticketInput = '';

        Flux::toast(
            variant: 'success',
            text: __('Check-in successful! Welcome, :name.', ['name' => $ticket->user ? $ticket->user->name : 'Attendee'])
        );
    }

    /**
     * Fast check-in helper for camera QR code scanner.
     *
     * @return array{status: string, message: string}
     */
    public function scanCheckInWithCode(string $code): array
    {
        $this->ticketInput = $code;
        $this->scanCheckIn();

        return [
            'status' => 'completed',
            'message' => __('Scan processed.'),
        ];
    }

    /**
     * Check in a specific ticket by ID.
     */
    public function checkIn(int $ticketId): void
    {
        if ($this->event->hasEnded()) {
            Flux::toast(variant: 'warning', text: __('Check-in is closed for concluded events.'));

            return;
        }

        /** @var Ticket|null $ticket */
        $ticket = $this->event->tickets()->with('user')->find($ticketId);

        if (! $ticket) {
            Flux::toast(variant: 'danger', text: __('Ticket not found.'));

            return;
        }

        if ($ticket->isCancelled()) {
            Flux::toast(variant: 'danger', text: __('This ticket was cancelled and is void.'));

            return;
        }

        $ticket->checkIn();

        Flux::toast(
            variant: 'success',
            text: __('Checked in :name.', ['name' => $ticket->user ? $ticket->user->name : 'Attendee'])
        );
    }

    /**
     * Undo check-in for a ticket by ID.
     */
    public function undoCheckIn(int $ticketId): void
    {
        if ($this->event->hasEnded()) {
            Flux::toast(variant: 'warning', text: __('Check-in status cannot be modified for concluded events.'));

            return;
        }

        /** @var Ticket|null $ticket */
        $ticket = $this->event->tickets()->with('user')->find($ticketId);

        if (! $ticket) {
            Flux::toast(variant: 'danger', text: __('Ticket not found.'));

            return;
        }

        $ticket->undoCheckIn();

        Flux::toast(
            variant: 'info',
            text: __('Check-in reverted for :name.', ['name' => $ticket->user ? $ticket->user->name : 'Attendee'])
        );
    }

    /**
     * Paginated list of event tickets based on search and filters.
     *
     * @return LengthAwarePaginator<int, Ticket>
     */
    #[Computed]
    public function tickets(): LengthAwarePaginator
    {
        $query = $this->event->tickets()
            ->with('user')
            ->whereNull('cancelled_at')
            ->latest('created_at');

        if (filled($this->search)) {
            $search = '%'.$this->search.'%';
            $query->where(function ($q) use ($search): void {
                $q->where('ticket_code', 'like', $search)
                    ->orWhereHas('user', function ($uq) use ($search): void {
                        $uq->where('name', 'like', $search)
                            ->orWhere('email', 'like', $search);
                    });
            });
        }

        match ($this->statusFilter) {
            'checked_in' => $query->whereNotNull('checked_in_at'),
            'pending' => $query->whereNull('checked_in_at'),
            default => null,
        };

        return $query->paginate(15);
    }

    /**
     * Statistics for door check-in progress.
     *
     * @return array{total: int, checkedIn: int, pending: int, percentage: int}
     */
    #[Computed]
    public function stats(): array
    {
        $total = $this->event->tickets()->whereNull('cancelled_at')->count();
        $checkedIn = $this->event->tickets()->whereNull('cancelled_at')->whereNotNull('checked_in_at')->count();
        $pending = max(0, $total - $checkedIn);
        $percentage = $total > 0 ? (int) round(($checkedIn / $total) * 100) : 0;

        return [
            'total' => $total,
            'checkedIn' => $checkedIn,
            'pending' => $pending,
            'percentage' => $percentage,
        ];
    }

    public function render(): View
    {
        return view('livewire.organizer.check-in-attendees');
    }
}
