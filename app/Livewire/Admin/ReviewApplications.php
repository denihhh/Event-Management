<?php

namespace App\Livewire\Admin;

use App\Mail\OrganizerApplicationApprovedMail;
use App\Mail\OrganizerApplicationRejectedMail;
use App\Models\OrganizerApplication;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Review Requests')]
class ReviewApplications extends Component
{
    use WithPagination;

    public string $filter = 'pending'; // pending, approved, rejected, all

    public ?int $reviewingApplicationId = null;

    public string $rejectionNotes = '';

    #[Computed]
    public function activeApplication(): ?OrganizerApplication
    {
        return $this->reviewingApplicationId
            ? OrganizerApplication::with('user')->find($this->reviewingApplicationId)
            : null;
    }

    public function mount(): void
    {
        abort_unless(Auth::user()?->isAdmin(), 403);
    }

    public function updatedFilter(): void
    {
        $this->resetPage();
    }

    public function reviewApplication(int $applicationId): void
    {
        $app = OrganizerApplication::findOrFail($applicationId);
        $this->reviewingApplicationId = $app->id;
        $this->rejectionNotes = $app->admin_notes ?? '';
        Flux::modal('review-application-modal')->show();
    }

    public function approveApplication(): void
    {
        abort_unless(Auth::user()?->isAdmin(), 403);

        if (! $this->reviewingApplicationId) {
            return;
        }

        $application = OrganizerApplication::with('user')->findOrFail($this->reviewingApplicationId);

        if (! $application->isPending()) {
            Flux::toast(variant: 'warning', text: __('This application has already been processed.'));
            $this->reviewingApplicationId = null;
            Flux::modal('review-application-modal')->close();

            return;
        }

        $application->update([
            'status' => 'approved',
            'approved_until' => null,
        ]);

        $application->user->update([
            'is_organizer' => true,
            'organizer_expires_at' => null,
        ]);

        if ($application->user) {
            Mail::to($application->user)->queue(new OrganizerApplicationApprovedMail($application));
        }

        $this->reviewingApplicationId = null;
        Flux::modal('review-application-modal')->close();
        Flux::toast(variant: 'success', text: __('Application approved! User granted permanent organizer role.'));
    }

    public function rejectApplication(): void
    {
        abort_unless(Auth::user()?->isAdmin(), 403);

        if (! $this->reviewingApplicationId) {
            return;
        }

        $application = OrganizerApplication::with('user')->findOrFail($this->reviewingApplicationId);

        if (! $application->isPending()) {
            Flux::toast(variant: 'warning', text: __('This application has already been processed.'));
            $this->reviewingApplicationId = null;
            Flux::modal('review-application-modal')->close();

            return;
        }

        $application->update([
            'status' => 'rejected',
            'admin_notes' => $this->rejectionNotes,
        ]);

        if ($application->user) {
            Mail::to($application->user)->queue(new OrganizerApplicationRejectedMail($application));
        }

        $this->reviewingApplicationId = null;
        Flux::modal('review-application-modal')->close();
        Flux::toast(variant: 'info', text: __('Organizer application rejected.'));
    }

    public function render(): View
    {
        $query = OrganizerApplication::with('user')->latest();

        if ($this->filter !== 'all') {
            $query->where('status', $this->filter);
        }

        return view('livewire.admin.review-applications', [
            'applications' => $query->paginate(15),
        ]);
    }
}
