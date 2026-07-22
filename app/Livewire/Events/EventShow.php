<?php

namespace App\Livewire\Events;

use App\Models\Event;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class EventShow extends Component
{
    public Event $event;

    public function mount(Event $event): void
    {
        $this->event = $event->loadMissing('organizer');
    }

    #[On('ticket-registered')]
    public function refreshEvent(): void
    {
        $this->event->refresh();
    }

    public function render(): View
    {
        $layout = Auth::check() ? 'layouts.app' : 'layouts.public';

        return view('livewire.events.event-show')
            ->title($this->event->title)
            ->layout($layout);
    }
}
