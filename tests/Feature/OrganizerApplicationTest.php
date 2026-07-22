<?php

use App\Livewire\Admin\ReviewApplications;
use App\Livewire\Settings\BecomeOrganizer;
use App\Mail\OrganizerApplicationApprovedMail;
use App\Mail\OrganizerApplicationRejectedMail;
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
        ->and($app->status)->toBe('pending');
});

it('prevents attendees from submitting multiple pending applications', function (): void {
    $attendee = User::factory()->create();

    OrganizerApplication::create([
        'user_id' => $attendee->id,
        'organization_name' => 'First Venture',
        'business_reg_number' => '12345',
        'contact_phone' => '+60 1234567',
        'bank_name' => 'Maybank',
        'bank_account_number' => '11112222',
        'event_title' => 'First Event',
        'event_description' => 'Description',
        'proposed_starts_at' => now()->addDays(10),
        'status' => 'pending',
    ]);

    Livewire::actingAs($attendee)
        ->test(BecomeOrganizer::class)
        ->set('organization_name', 'Second Venture')
        ->set('business_reg_number', '67890')
        ->set('contact_phone', '+60 9876543')
        ->set('bank_name', 'CIMB')
        ->set('bank_account_number', '33334444')
        ->set('event_title', 'Second Event')
        ->set('event_description', 'Description 2')
        ->set('proposed_starts_at', now()->addDays(20)->format('Y-m-d\TH:i'))
        ->call('submitApplication');

    expect(OrganizerApplication::where('user_id', $attendee->id)->count())->toBe(1);
});

it('allows admins to approve an application and grant permanent organizer status', function (): void {
    Mail::fake();

    $admin = User::factory()->admin()->create();
    $attendee = User::factory()->create();

    $app = OrganizerApplication::create([
        'user_id' => $attendee->id,
        'event_title' => 'Design Workshop',
        'event_description' => 'UI/UX interactive workshop.',
        'proposed_starts_at' => now()->addDays(15),
        'status' => 'pending',
    ]);

    Livewire::actingAs($admin)
        ->test(ReviewApplications::class)
        ->set('reviewingApplicationId', $app->id)
        ->call('approveApplication');

    $app->refresh();
    $attendee->refresh();

    expect($app->status)->toBe('approved')
        ->and($attendee->is_organizer)->toBeTrue()
        ->and($attendee->isOrganizer())->toBeTrue();

    Mail::assertQueued(OrganizerApplicationApprovedMail::class, function ($mail) use ($app) {
        return $mail->application->id === $app->id;
    });
});

it('allows admins to reject an application with notes', function (): void {
    Mail::fake();

    $admin = User::factory()->admin()->create();
    $attendee = User::factory()->create();

    $app = OrganizerApplication::create([
        'user_id' => $attendee->id,
        'event_title' => 'Unrealistic Mega Fest',
        'event_description' => 'Vague event description.',
        'proposed_starts_at' => now()->addDays(10),
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

    Mail::assertQueued(OrganizerApplicationRejectedMail::class, function ($mail) use ($app) {
        return $mail->application->id === $app->id;
    });
});

it('prevents re-processing applications that are no longer pending', function (): void {
    $admin = User::factory()->admin()->create();
    $attendee = User::factory()->create();

    $app = OrganizerApplication::create([
        'user_id' => $attendee->id,
        'event_title' => 'Processed Event',
        'event_description' => 'Description.',
        'proposed_starts_at' => now()->addDays(15),
        'status' => 'rejected',
        'admin_notes' => 'Original rejection reason',
    ]);

    Livewire::actingAs($admin)
        ->test(ReviewApplications::class)
        ->set('reviewingApplicationId', $app->id)
        ->call('approveApplication');

    $app->refresh();
    $attendee->refresh();

    expect($app->status)->toBe('rejected')
        ->and($attendee->isOrganizer())->toBeFalse();
});
