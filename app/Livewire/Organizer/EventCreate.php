<?php

namespace App\Livewire\Organizer;

use App\Livewire\Concerns\HasEventImageUploads;
use App\Models\Event;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

#[Title('Create Event')]
class EventCreate extends Component
{
    use HasEventImageUploads, WithFileUploads;

    public string $title = '';

    public string $description = '';

    public string $starts_at = '';

    public string $location = '';

    public int $capacity = 50;

    /** @var TemporaryUploadedFile|null */
    public mixed $banner = null;

    /** @var TemporaryUploadedFile|null */
    public mixed $inlineImage = null;

    public function mount(): void
    {
        Gate::authorize('create', Event::class);
    }

    /**
     * Get the validation rules for creating an event.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'starts_at' => ['required', 'date', 'after:now'],
            'location' => ['required', 'string', 'max:255'],
            'capacity' => ['required', 'integer', 'min:1'],
            ...$this->bannerValidationRules(),
        ];
    }

    /**
     * Remove banner file selection.
     */
    public function removeBanner(): void
    {
        $this->removeBannerSelection();
    }

    public function save(): void
    {
        Gate::authorize('create', Event::class);

        $validated = $this->validate();

        Auth::user()->organizedEvents()->create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'starts_at' => $validated['starts_at'],
            'location' => $validated['location'],
            'capacity' => $validated['capacity'],
            'banner_path' => $this->storeBannerFile(),
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
