<?php

namespace App\Livewire\Organizer;

use App\Models\Event;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Create Event')]
class EventCreate extends Component
{
    public string $title = '';

    public string $description = '';

    public string $starts_at = '';

    public string $location = '';

    public int $capacity = 50;

    public function mount(): void
    {
        Gate::authorize('create', Event::class);
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'starts_at' => ['required', 'date', 'after:now'],
            'location' => ['required', 'string', 'max:255'],
            'capacity' => ['required', 'integer', 'min:1'],
        ];
    }

    public function save(): void
    {
        Gate::authorize('create', Event::class);

        $validated = $this->validate();

        Auth::user()->organizedEvents()->create([
            ...$validated,
            'tickets_sold' => 0,
        ]);

        Flux::toast(variant: 'success', text: __('Event created successfully!'));

        $this->redirect(route('organizer.events.index'), navigate: true);
    }

    public function render(): View
    {
        return view('livewire.organizer.event-create');
    }
}
