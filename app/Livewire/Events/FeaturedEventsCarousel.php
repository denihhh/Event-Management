<?php

namespace App\Livewire\Events;

use App\Models\Event;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;

class FeaturedEventsCarousel extends Component
{
    /**
     * Get featured upcoming events for the carousel.
     *
     * @return Collection<int, Event>
     */
    public function getFeaturedEvents(): Collection
    {
        return Event::query()
            ->with('organizer')
            ->where('starts_at', '>=', now())
            ->orderBy('starts_at', 'asc')
            ->take(8)
            ->get();
    }

    public function render(): View
    {
        return view('livewire.events.featured-events-carousel', [
            'featuredEvents' => $this->getFeaturedEvents(),
        ]);
    }
}
