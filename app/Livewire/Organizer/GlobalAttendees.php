<?php

namespace App\Livewire\Organizer;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Symfony\Component\HttpFoundation\StreamedResponse;

#[Title('Attendee Roster')]
class GlobalAttendees extends Component
{
    use WithPagination;

    public string $search = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function exportCsv(): StreamedResponse
    {
        /** @var User $user */
        $user = Auth::user();
        $fileName = 'all-attendees-'.now()->format('Y-m-d').'.csv';

        $query = Ticket::query()
            ->with(['user', 'event'])
            ->latest('created_at');

        if (! $user->isAdmin()) {
            $query->whereHas('event', function ($q) use ($user): void {
                $q->where('user_id', $user->id);
            });
        }

        $tickets = $query->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ];

        return response()->stream(function () use ($tickets): void {
            $handle = fopen('php://output', 'w');
            if ($handle === false) {
                return;
            }

            fputcsv($handle, ['Ticket Code', 'Attendee Name', 'Attendee Email', 'Event Title', 'Registration Date', 'Reminder Sent']);

            foreach ($tickets as $ticket) {
                fputcsv($handle, [
                    $ticket->ticket_code,
                    $ticket->user ? $ticket->user->name : 'N/A',
                    $ticket->user ? $ticket->user->email : 'N/A',
                    $ticket->event ? $ticket->event->title : 'N/A',
                    $ticket->created_at->format('Y-m-d H:i:s'),
                    $ticket->reminder_sent ? 'Yes' : 'No',
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }

    public function render(): View
    {
        /** @var User $user */
        $user = Auth::user();

        $query = Ticket::query()
            ->with(['user', 'event'])
            ->latest('created_at');

        if (! $user->isAdmin()) {
            $query->whereHas('event', function ($q) use ($user): void {
                $q->where('user_id', $user->id);
            });
        }

        if (filled($this->search)) {
            $search = '%'.$this->search.'%';
            $query->where(function ($q) use ($search): void {
                $q->where('ticket_code', 'like', $search)
                    ->orWhereHas('user', function ($uq) use ($search): void {
                        $uq->where('name', 'like', $search)
                            ->orWhere('email', 'like', $search);
                    })
                    ->orWhereHas('event', function ($eq) use ($search): void {
                        $eq->where('title', 'like', $search);
                    });
            });
        }

        return view('livewire.organizer.global-attendees', [
            'tickets' => $query->paginate(15),
        ]);
    }
}
