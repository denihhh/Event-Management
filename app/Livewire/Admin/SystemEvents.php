<?php

namespace App\Livewire\Admin;

use App\Models\Event;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Event Audit')]
class SystemEvents extends Component
{
    use WithPagination;

    public string $search = '';

    public string $statusFilter = 'all'; // all, active, past, trashed

    public ?int $restoringEventId = null;

    public function mount(): void
    {
        abort_unless(Auth::user()?->isAdmin(), 403);
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function restoreEvent(int $eventId): void
    {
        abort_unless(Auth::user()?->isAdmin(), 403);

        $event = Event::withTrashed()->findOrFail($eventId);
        $event->restore();

        Flux::toast(variant: 'success', text: __('Event restored successfully!'));
    }

    public function deleteEvent(int $eventId): void
    {
        abort_unless(Auth::user()?->isAdmin(), 403);

        $event = Event::findOrFail($eventId);
        $event->delete();

        Flux::toast(variant: 'info', text: __('Event soft-deleted.'));
    }

    public function render(): View
    {
        $query = Event::withTrashed()->with('organizer')->latest('created_at');

        if (filled($this->search)) {
            $search = '%'.$this->search.'%';
            $query->where(function ($q) use ($search): void {
                $q->where('title', 'like', $search)
                    ->orWhere('location', 'like', $search)
                    ->orWhereHas('organizer', function ($oq) use ($search): void {
                        $oq->where('name', 'like', $search)
                            ->orWhere('email', 'like', $search);
                    });
            });
        }

        match ($this->statusFilter) {
            'active' => $query->whereNull('deleted_at')->where('starts_at', '>=', now()),
            'past' => $query->whereNull('deleted_at')->where('starts_at', '<', now()),
            'trashed' => $query->onlyTrashed(),
            default => null,
        };

        return view('livewire.admin.system-events', [
            'events' => $query->paginate(15),
        ]);
    }
}
