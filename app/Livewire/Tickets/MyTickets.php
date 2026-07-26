<?php

namespace App\Livewire\Tickets;

use App\Models\Ticket;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('My Tickets')]
class MyTickets extends Component
{
    use WithPagination;

    #[Url(history: true)]
    public string $filter = 'all';

    public ?int $cancellingTicketId = null;

    public function updatedFilter(): void
    {
        $this->resetPage();
    }

    public function confirmCancel(int $ticketId): void
    {
        $this->cancellingTicketId = $ticketId;
        Flux::modal('cancel-ticket-modal')->show();
    }

    public function cancelTicket(): void
    {
        if (! $this->cancellingTicketId) {
            return;
        }

        $ticket = Ticket::where('id', $this->cancellingTicketId)
            ->where('user_id', Auth::id())
            ->first();

        if (! $ticket) {
            Flux::toast(variant: 'danger', text: __('Ticket not found or unauthorized.'));
            $this->cancellingTicketId = null;
            Flux::modal('cancel-ticket-modal')->close();

            return;
        }

        if ($ticket->isCheckedIn() || $ticket->event->hasStarted()) {
            Flux::toast(variant: 'danger', text: __('Checked-in or past event tickets cannot be cancelled.'));
            $this->cancellingTicketId = null;
            Flux::modal('cancel-ticket-modal')->close();

            return;
        }

        try {
            DB::transaction(function () use ($ticket): void {
                $ticket->update([
                    'cancelled_at' => now(),
                ]);

                if ($ticket->event && $ticket->event->tickets_sold > 0) {
                    $ticket->event->decrement('tickets_sold');
                }
            });

            $this->cancellingTicketId = null;
            Flux::modal('cancel-ticket-modal')->close();
            Flux::toast(variant: 'success', text: __('Your ticket has been cancelled.'));
        } catch (\Throwable $e) {
            report($e);
            Flux::toast(variant: 'danger', text: __('Failed to cancel ticket. Please try again.'));
        }
    }

    public function render(): View
    {
        $query = Auth::user()
            ->tickets()
            ->with(['event.organizer'])
            ->latest();

        match ($this->filter) {
            'active' => $query->whereNull('cancelled_at')
                ->whereNull('checked_in_at')
                ->whereHas('event', fn ($eq) => $eq->whereNull('deleted_at')->where('starts_at', '>', now())),
            'checked_in' => $query->whereNull('cancelled_at')
                ->whereNotNull('checked_in_at'),
            'past' => $query->whereNull('cancelled_at')
                ->whereNull('checked_in_at')
                ->whereHas('event', fn ($eq) => $eq->whereNull('deleted_at')->where('starts_at', '<=', now())),
            'cancelled' => $query->where(function ($q): void {
                $q->whereNotNull('cancelled_at')
                    ->orWhereHas('event', fn ($eq) => $eq->onlyTrashed());
            }),
            default => null,
        };

        return view('livewire.tickets.my-tickets', [
            'tickets' => $query->paginate(10),
        ]);
    }
}
