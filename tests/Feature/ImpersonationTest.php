<?php

use App\Models\Event;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Lab404\Impersonate\Services\ImpersonateManager;

// ──────────────────────────────────────────────────────────────────────────────
// canImpersonate / canBeImpersonated contracts
// ──────────────────────────────────────────────────────────────────────────────

it('allows admins to impersonate other users', function (): void {
    $admin = User::factory()->admin()->create();

    expect($admin->canImpersonate())->toBeTrue();
});

it('prevents non-admins from impersonating', function (): void {
    $attendee = User::factory()->create();
    $organizer = User::factory()->organizer()->create();

    expect($attendee->canImpersonate())->toBeFalse();
    expect($organizer->canImpersonate())->toBeFalse();
});

it('allows regular users to be impersonated', function (): void {
    $attendee = User::factory()->create();
    $organizer = User::factory()->organizer()->create();

    expect($attendee->canBeImpersonated())->toBeTrue();
    expect($organizer->canBeImpersonated())->toBeTrue();
});

it('prevents admins from being impersonated', function (): void {
    $admin = User::factory()->admin()->create();

    expect($admin->canBeImpersonated())->toBeFalse();
});

// ──────────────────────────────────────────────────────────────────────────────
// Role helper methods
// ──────────────────────────────────────────────────────────────────────────────

it('correctly identifies an admin user', function (): void {
    $admin = User::factory()->admin()->create();

    expect($admin->isAdmin())->toBeTrue();
    expect($admin->isOrganizer())->toBeFalse();
    expect($admin->isAttendee())->toBeFalse();
});

it('correctly identifies an organizer user', function (): void {
    $organizer = User::factory()->organizer()->create();

    expect($organizer->isAdmin())->toBeFalse();
    expect($organizer->isOrganizer())->toBeTrue();
    expect($organizer->isAttendee())->toBeFalse();
});

it('correctly identifies a regular attendee', function (): void {
    $attendee = User::factory()->create();

    expect($attendee->isAdmin())->toBeFalse();
    expect($attendee->isOrganizer())->toBeFalse();
    expect($attendee->isAttendee())->toBeTrue();
});

// ──────────────────────────────────────────────────────────────────────────────
// HTTP impersonation routes
// ──────────────────────────────────────────────────────────────────────────────

it('allows an admin to take impersonation via HTTP', function (): void {
    $admin = User::factory()->admin()->create();
    $attendee = User::factory()->create();

    $this->actingAs($admin)
        ->get(route('impersonate', $attendee->id))
        ->assertRedirect();

    /** @var ImpersonateManager $manager */
    $manager = app(ImpersonateManager::class);

    expect($manager->isImpersonating())->toBeTrue();
    expect($manager->getImpersonatorId())->toBe($admin->id);
});

it('prevents a non-admin from taking impersonation via HTTP', function (): void {
    $attendee = User::factory()->create();
    $target = User::factory()->create();

    $this->actingAs($attendee)
        ->get(route('impersonate', $target->id))
        ->assertForbidden();
});

it('prevents impersonating an admin user via HTTP', function (): void {
    $admin = User::factory()->admin()->create();
    $anotherAdmin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(route('impersonate', $anotherAdmin->id))
        ->assertRedirect();

    // Impersonation must NOT be active — the package silently redirects back
    /** @var ImpersonateManager $manager */
    $manager = app(ImpersonateManager::class);

    expect($manager->isImpersonating())->toBeFalse();
});

it('allows an impersonating admin to leave impersonation', function (): void {
    $admin = User::factory()->admin()->create();
    $attendee = User::factory()->create();

    $this->actingAs($admin)
        ->get(route('impersonate', $attendee->id));

    $this->get(route('impersonate.leave'))
        ->assertRedirect();

    /** @var ImpersonateManager $manager */
    $manager = app(ImpersonateManager::class);

    expect($manager->isImpersonating())->toBeFalse();
});

// ──────────────────────────────────────────────────────────────────────────────
// Gate::before admin superpower
// ──────────────────────────────────────────────────────────────────────────────

it('grants admin users all gate abilities', function (): void {
    $admin = User::factory()->admin()->create();

    expect(Gate::forUser($admin)->allows('update', Event::factory()->create()))->toBeTrue();
    expect(Gate::forUser($admin)->allows('delete', Event::factory()->create()))->toBeTrue();
});

it('does not grant non-admin users blanket gate access', function (): void {
    $attendee = User::factory()->create();
    $event = Event::factory()->create();

    expect(Gate::forUser($attendee)->allows('update', $event))->toBeFalse();
});

// ──────────────────────────────────────────────────────────────────────────────
// Admin bypasses EnsureUserIsOrganizer middleware
// ──────────────────────────────────────────────────────────────────────────────

it('allows admins to access organizer routes without is_organizer flag', function (): void {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(route('organizer.events.index'))
        ->assertOk();
});

it('blocks regular attendees from organizer routes', function (): void {
    $attendee = User::factory()->create();

    $this->actingAs($attendee)
        ->get(route('organizer.events.index'))
        ->assertForbidden();
});
