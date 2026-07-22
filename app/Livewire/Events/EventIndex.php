<?php

namespace App\Livewire\Events;

use App\Models\Event;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Browse Events')]
class EventIndex extends Component
{
    use WithPagination;

    #[Url(history: true)]
    public string $search = '';

    #[Url(history: true)]
    public string $filter = 'upcoming';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedFilter(): void
    {
        $this->resetPage();
    }

    public function render(): View
    {
        $query = Event::query()
            ->with('organizer')
            ->orderBy('starts_at', 'asc');

        if (filled($this->search)) {
            $search = '%'.$this->search.'%';
            $query->where(function ($q) use ($search): void {
                $q->where('title', 'like', $search)
                    ->orWhere('location', 'like', $search)
                    ->orWhere('description', 'like', $search);
            });
        }

        match ($this->filter) {
            'upcoming' => $query->where('starts_at', '>=', now()),
            'this_week' => $query->whereBetween('starts_at', [now(), now()->endOfWeek()]),
            'this_month' => $query->whereBetween('starts_at', [now(), now()->endOfMonth()]),
            'past' => $query->where('starts_at', '<', now()),
            default => null,
        };

        $layout = Auth::check() ? 'layouts.app' : 'layouts.public';

        return view('livewire.events.event-index', [
            'events' => $query->paginate(9),
        ])->layout($layout);
    }
}
