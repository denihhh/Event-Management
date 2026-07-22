<?php

namespace App\Livewire\Admin;

use App\Models\OrganizerApplication;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
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

    public int $approvalDurationDays = 30;

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
        $this->approvalDurationDays = $app->requested_duration_days ?: 30;
        $this->rejectionNotes = $app->admin_notes ?? '';
    }

    public function approveApplication(): void
    {
        abort_unless(Auth::user()?->isAdmin(), 403);

        if (! $this->reviewingApplicationId) {
            return;
        }

        $application = OrganizerApplication::with('user')->findOrFail($this->reviewingApplicationId);
        $expiresAt = now()->addDays($this->approvalDurationDays);

        $application->update([
            'status' => 'approved',
            'approved_until' => $expiresAt,
        ]);

        $application->user->update([
            'is_organizer' => true,
            'organizer_expires_at' => $expiresAt,
        ]);

        $this->reviewingApplicationId = null;
        Flux::toast(variant: 'success', text: __('Application approved! User granted organizer role for :days days.', ['days' => $this->approvalDurationDays]));
    }

    public function rejectApplication(): void
    {
        abort_unless(Auth::user()?->isAdmin(), 403);

        if (! $this->reviewingApplicationId) {
            return;
        }

        $application = OrganizerApplication::findOrFail($this->reviewingApplicationId);

        $application->update([
            'status' => 'rejected',
            'admin_notes' => $this->rejectionNotes,
        ]);

        $this->reviewingApplicationId = null;
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
