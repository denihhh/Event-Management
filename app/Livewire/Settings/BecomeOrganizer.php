<?php

namespace App\Livewire\Settings;

use App\Models\OrganizerApplication;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Become an Organizer')]
class BecomeOrganizer extends Component
{
    public string $organization_name = '';

    public string $business_reg_number = '';

    public string $contact_phone = '';

    public string $bank_name = '';

    public string $bank_account_number = '';

    public string $event_title = '';

    public string $event_description = '';

    public string $proposed_starts_at = '';

    /**
     * Get the validation rules for the application form.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'organization_name' => ['required', 'string', 'max:255'],
            'business_reg_number' => ['required', 'string', 'max:255'],
            'contact_phone' => ['required', 'string', 'max:50'],
            'bank_name' => ['required', 'string', 'max:100'],
            'bank_account_number' => ['required', 'string', 'max:100'],
            'event_title' => ['required', 'string', 'max:255'],
            'event_description' => ['required', 'string'],
            'proposed_starts_at' => ['required', 'date', 'after:now'],
        ];
    }

    public function submitApplication(): void
    {
        $user = Auth::user();

        if ($user->isOrganizer()) {
            Flux::toast(variant: 'warning', text: __('You are already an active event organizer.'));

            return;
        }

        if ($user->organizerApplications()->where('status', 'pending')->exists()) {
            Flux::toast(variant: 'warning', text: __('You already have a pending application under review.'));

            return;
        }

        $validated = $this->validate();

        $user->organizerApplications()->create([
            ...$validated,
            'status' => 'pending',
        ]);

        $this->reset([
            'organization_name',
            'business_reg_number',
            'contact_phone',
            'bank_name',
            'bank_account_number',
            'event_title',
            'event_description',
            'proposed_starts_at',
        ]);

        Flux::toast(variant: 'success', text: __('Organizer application submitted! An admin will review your request.'));
    }

    #[Computed]
    public function latestApplication(): ?OrganizerApplication
    {
        return Auth::user()->latestOrganizerApplication;
    }

    public function render(): View
    {
        return view('livewire.settings.become-organizer');
    }
}
