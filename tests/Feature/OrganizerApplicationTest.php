<?php

use App\Livewire\Admin\ReviewApplications;
use App\Livewire\Settings\BecomeOrganizer;
use App\Models\OrganizerApplication;
use App\Models\User;
use Livewire\Livewire;

it('allows attendees to submit an organizer application with business details', function (): void {
    $attendee = User::factory()->create();

    Livewire::actingAs($attendee)
        ->test(BecomeOrganizer::class)
        ->set('organization_name', 'Apex Sports & Events Sdn Bhd')
        ->set('business_reg_number', '202401099881 (1234567-A)')
        ->set('contact_phone', '+60 12-345 6789')
        ->set('bank_name', 'Maybank')
        ->set('bank_account_number', '514012345678')
        ->set('event_title', 'Tech Conference 2026')
        ->set('event_description', 'Annual developer gathering for software engineers.')
        ->set('proposed_starts_at', now()->addDays(30)->format('Y-m-d\TH:i'))
        ->set('requested_duration_days', 45)
        ->call('submitApplication');

    expect(OrganizerApplication::count())->toBe(1);

    $app = OrganizerApplication::first();
    expect($app->user_id)->toBe($attendee->id)
        ->and($app->organization_name)->toBe('Apex Sports & Events Sdn Bhd')
        ->and($app->business_reg_number)->toBe('202401099881 (1234567-A)')
        ->and($app->contact_phone)->toBe('+60 12-345 6789')
        ->and($app->bank_name)->toBe('Maybank')
        ->and($app->bank_account_number)->toBe('514012345678')
        ->and($app->event_title)->toBe('Tech Conference 2026')
        ->and($app->status)->toBe('pending')
        ->and($app->requested_duration_days)->toBe(45);
});

it('allows admins to approve an application and grant temporary organizer status', function (): void {
    $admin = User::factory()->admin()->create();
    $attendee = User::factory()->create();

    $app = OrganizerApplication::create([
        'user_id' => $attendee->id,
        'event_title' => 'Design Workshop',
        'event_description' => 'UI/UX interactive workshop.',
        'proposed_starts_at' => now()->addDays(15),
        'requested_duration_days' => 30,
        'status' => 'pending',
    ]);

    Livewire::actingAs($admin)
        ->test(ReviewApplications::class)
        ->set('reviewingApplicationId', $app->id)
        ->set('approvalDurationDays', 60)
        ->call('approveApplication');

    $app->refresh();
    $attendee->refresh();

    expect($app->status)->toBe('approved')
        ->and($attendee->is_organizer)->toBeTrue()
        ->and($attendee->organizer_expires_at)->not()->toBeNull()
        ->and($attendee->isOrganizer())->toBeTrue();
});

it('automatically revokes organizer access after organizer_expires_at passes', function (): void {
    $user = User::factory()->create([
        'is_organizer' => true,
        'organizer_expires_at' => now()->subDay(),
    ]);

    expect($user->isOrganizer())->toBeFalse();
});

it('allows admins to reject an application with notes', function (): void {
    $admin = User::factory()->admin()->create();
    $attendee = User::factory()->create();

    $app = OrganizerApplication::create([
        'user_id' => $attendee->id,
        'event_title' => 'Unrealistic Mega Fest',
        'event_description' => 'Vague event description.',
        'proposed_starts_at' => now()->addDays(10),
        'requested_duration_days' => 365,
        'status' => 'pending',
    ]);

    Livewire::actingAs($admin)
        ->test(ReviewApplications::class)
        ->set('reviewingApplicationId', $app->id)
        ->set('rejectionNotes', 'Please provide a more detailed event proposal.')
        ->call('rejectApplication');

    $app->refresh();
    $attendee->refresh();

    expect($app->status)->toBe('rejected')
        ->and($app->admin_notes)->toBe('Please provide a more detailed event proposal.')
        ->and($attendee->isOrganizer())->toBeFalse();
});
