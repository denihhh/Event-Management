<?php

namespace App\Livewire\Organizer;

use App\Models\Event;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Symfony\Component\HttpFoundation\StreamedResponse;

#[Title('Attendee Roster')]
class EventAttendees extends Component
{
    use WithPagination;

    #[Locked]
    public Event $event;

    public string $search = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function mount(Event $event): void
    {
        Gate::authorize('manageRoster', $event);
        $this->event = $event;
    }

    public function exportCsv(): StreamedResponse
    {
        Gate::authorize('update', $this->event);

        $eventTitle = $this->event->title;
        $fileName = 'attendees-'.Str::slug($eventTitle).'-'.now()->format('Y-m-d').'.csv';

        $tickets = $this->event
            ->tickets()
            ->with('user')
            ->whereNull('cancelled_at')
            ->latest('created_at')
            ->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ];

        return response()->stream(function () use ($tickets): void {
            $handle = fopen('php://output', 'w');
            if ($handle === false) {
                return;
            }

            fputcsv($handle, ['Ticket Code', 'Attendee Name', 'Attendee Email', 'Registration Date', 'Reminder Sent', 'Checked In', 'Checked In At']);

            foreach ($tickets as $ticket) {
                fputcsv($handle, [
                    $ticket->ticket_code,
                    $ticket->user ? $ticket->user->name : 'N/A',
                    $ticket->user ? $ticket->user->email : 'N/A',
                    $ticket->created_at->format('Y-m-d H:i:s'),
                    $ticket->reminder_sent ? 'Yes' : 'No',
                    $ticket->isCheckedIn() ? 'Yes' : 'No',
                    $ticket->checked_in_at ? $ticket->checked_in_at->format('Y-m-d H:i:s') : 'N/A',
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }

    public function render(): View
    {
        $query = $this->event
            ->tickets()
            ->with('user')
            ->whereNull('cancelled_at')
            ->latest('created_at');

        if (filled($this->search)) {
            $search = '%'.$this->search.'%';
            $query->where(function ($q) use ($search) {
                $q->where('ticket_code', 'like', $search)
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', $search)
                            ->orWhere('email', 'like', $search);
                    });
            });
        }

        return view('livewire.organizer.event-attendees', [
            'tickets' => $query->paginate(15),
        ]);
    }
}
