<?php

namespace App\Livewire\Organizer;

use App\Livewire\Concerns\HasEventImageUploads;
use App\Mail\EventUpdatedMail;
use App\Models\Event;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

#[Title('Edit Event')]
class EventEdit extends Component
{
    use HasEventImageUploads, WithFileUploads;

    #[Locked]
    public Event $event;

    public string $title = '';

    public string $description = '';

    public string $starts_at = '';

    public string $location = '';

    public int $capacity = 50;

    /** @var TemporaryUploadedFile|null */
    public mixed $banner = null;

    /** @var TemporaryUploadedFile|null */
    public mixed $inlineImage = null;

    public ?string $existingBannerPath = null;

    public function mount(Event $event): void
    {
        Gate::authorize('update', $event);

        $this->event = $event;
        $this->title = $event->title;
        $this->description = $event->description;
        $this->starts_at = $event->starts_at->format('Y-m-d\TH:i');
        $this->location = $event->location;
        $this->capacity = $event->capacity;
        $this->existingBannerPath = $event->banner_path;
    }

    /**
     * Get the validation rules for updating an event.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $minCapacity = max(1, $this->event->tickets_sold);

        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'starts_at' => ['required', 'date'],
            'location' => ['required', 'string', 'max:255'],
            'capacity' => ['required', 'integer', 'min:'.$minCapacity],
            ...$this->bannerValidationRules(),
        ];
    }

    /**
     * Remove the current or selected banner selection in component memory.
     */
    public function removeBanner(): void
    {
        $this->removeBannerSelection();
        $this->existingBannerPath = null;
    }

    public function update(): void
    {
        Gate::authorize('update', $this->event);

        $validated = $this->validate();

        $newBannerPath = $this->storeBannerFile();

        if ($newBannerPath !== null) {
            $finalBannerPath = $newBannerPath;
        } elseif ($this->existingBannerPath !== null) {
            $finalBannerPath = $this->existingBannerPath;
        } else {
            $finalBannerPath = null;
        }

        // Clean up old file from storage if banner changed or was removed
        if ($this->event->banner_path && $this->event->banner_path !== $finalBannerPath) {
            if (Storage::disk('public')->exists($this->event->banner_path)) {
                Storage::disk('public')->delete($this->event->banner_path);
            }
        }

        $originalStartsAt = $this->event->starts_at->format('Y-m-d H:i');
        $originalLocation = $this->event->location;

        $this->event->update([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'starts_at' => $validated['starts_at'],
            'location' => $validated['location'],
            'capacity' => $validated['capacity'],
            'banner_path' => $finalBannerPath,
        ]);

        $newStartsAt = $this->event->fresh()->starts_at->format('Y-m-d H:i');
        $significantChange = ($originalStartsAt !== $newStartsAt) || ($originalLocation !== $validated['location']);

        if ($significantChange) {
            // Reset reminder sent flag so reminder re-fires for new date
            $this->event->tickets()->update(['reminder_sent' => false]);

            // Notify all ticket holders of update
            $this->event->loadMissing('tickets.user');
            foreach ($this->event->tickets as $ticket) {
                if ($ticket->user) {
                    Mail::to($ticket->user)->queue(new EventUpdatedMail($ticket));
                }
            }

            Flux::toast(variant: 'success', text: __('Event updated and attendees notified of changes!'));
        } else {
            Flux::toast(variant: 'success', text: __('Event updated successfully!'));
        }

        $this->redirect(route('organizer.events.index'), navigate: true);
    }

    public function render(): View
    {
        return view('livewire.organizer.event-edit');
    }
}
