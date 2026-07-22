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

    public ?int $cancellingTicketId = null;

    public function confirmCancel(int $ticketId): void
    {
        $this->cancellingTicketId = $ticketId;
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

            return;
        }

        try {
            DB::transaction(function () use ($ticket): void {
                $ticket->delete();
            });

            $this->cancellingTicketId = null;
            Flux::toast(variant: 'success', text: __('Your ticket has been cancelled.'));
        } catch (\Throwable $e) {
            report($e);
            Flux::toast(variant: 'danger', text: __('Failed to cancel ticket. Please try again.'));
        }
    }

    public function render(): View
    {
        $tickets = Auth::user()
            ->tickets()
            ->with(['event.organizer'])
            ->latest()
            ->paginate(10);

        return view('livewire.tickets.my-tickets', [
            'tickets' => $tickets,
        ]);
    }
}
