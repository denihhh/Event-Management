<?php

namespace App\Livewire;

use App\Models\Event;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Eventive - Discover & Host Extraordinary Events')]
class Welcome extends Component
{
    public string $search = '';

    /**
     * Redirect authenticated users to the dashboard.
     */
    public function mount(): mixed
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return null;
    }

    /**
     * Search events and redirect to explore page.
     */
    public function performSearch(): void
    {
        if (filled($this->search)) {
            $this->redirect(route('events.index', ['search' => $this->search]), navigate: true);
        } else {
            $this->redirect(route('events.index'), navigate: true);
        }
    }

    /**
     * Get key platform statistics for the hero banner.
     *
     * @return array<string, int>
     */
    public function getPlatformStats(): array
    {
        return [
            'totalEvents' => Event::count(),
            'totalTickets' => Ticket::count(),
            'totalOrganizers' => User::where('is_organizer', true)->count(),
        ];
    }

    /**
     * Get featured upcoming events for the landing page grid.
     *
     * @return Collection<int, Event>
     */
    public function getFeaturedEvents(): Collection
    {
        return Event::query()
            ->with('organizer')
            ->where('starts_at', '>=', now())
            ->orderBy('starts_at', 'asc')
            ->take(6)
            ->get();
    }

    public function render(): View
    {
        return view('livewire.welcome', [
            'stats' => $this->getPlatformStats(),
            'featuredEvents' => $this->getFeaturedEvents(),
        ])->layout('layouts.public');
    }
}
