<?php

use App\Models\User;

// ──────────────────────────────────────────────────────────────────────────────
// Route Access & Role Guards for All 12 Navigation Endpoints
// ──────────────────────────────────────────────────────────────────────────────

it('allows attendees to access Module 1, Module 4, and Apply as Organizer routes', function (): void {
    $attendee = User::factory()->create();

    $this->actingAs($attendee)->get(route('dashboard'))->assertOk();
    $this->actingAs($attendee)->get(route('events.index'))->assertOk();
    $this->actingAs($attendee)->get(route('tickets.index'))->assertOk();

    $this->actingAs($attendee)->get(route('become-organizer.edit'))->assertOk();

    $this->actingAs($attendee)->get(route('profile.edit'))->assertOk();
    $this->actingAs($attendee)->get(route('appearance.edit'))->assertOk();
});

it('blocks attendees from accessing organizer management and admin routes', function (): void {
    $attendee = User::factory()->create();

    $this->actingAs($attendee)->get(route('organizer.events.index'))->assertForbidden();
    $this->actingAs($attendee)->get(route('organizer.events.create'))->assertForbidden();
    $this->actingAs($attendee)->get(route('organizer.attendees.index'))->assertForbidden();

    $this->actingAs($attendee)->get(route('admin.organizer-applications.index'))->assertForbidden();
    $this->actingAs($attendee)->get(route('admin.users.index'))->assertForbidden();
    $this->actingAs($attendee)->get(route('admin.events.index'))->assertForbidden();
    $this->actingAs($attendee)->get(route('admin.system.status'))->assertForbidden();
});

it('allows organizers to access Module 1, Module 2, and Module 4 routes', function (): void {
    $organizer = User::factory()->organizer()->create();

    $this->actingAs($organizer)->get(route('organizer.events.index'))->assertOk();
    $this->actingAs($organizer)->get(route('organizer.events.create'))->assertOk();
    $this->actingAs($organizer)->get(route('organizer.attendees.index'))->assertOk();
});

it('blocks organizers from accessing admin routes', function (): void {
    $organizer = User::factory()->organizer()->create();

    $this->actingAs($organizer)->get(route('admin.organizer-applications.index'))->assertForbidden();
    $this->actingAs($organizer)->get(route('admin.users.index'))->assertForbidden();
    $this->actingAs($organizer)->get(route('admin.events.index'))->assertForbidden();
    $this->actingAs($organizer)->get(route('admin.system.status'))->assertForbidden();
});

it('allows admins to access all platform and admin routes', function (): void {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)->get(route('dashboard'))->assertOk();
    $this->actingAs($admin)->get(route('events.index'))->assertOk();
    $this->actingAs($admin)->get(route('tickets.index'))->assertOk();

    $this->actingAs($admin)->get(route('organizer.events.index'))->assertOk();
    $this->actingAs($admin)->get(route('organizer.events.create'))->assertOk();
    $this->actingAs($admin)->get(route('organizer.attendees.index'))->assertOk();

    $this->actingAs($admin)->get(route('admin.organizer-applications.index'))->assertOk();
    $this->actingAs($admin)->get(route('admin.users.index'))->assertOk();
    $this->actingAs($admin)->get(route('admin.events.index'))->assertOk();
    $this->actingAs($admin)->get(route('admin.system.status'))->assertOk();
});

it('hides admin routes during active impersonation of a regular user', function (): void {
    $admin = User::factory()->admin()->create();
    $attendee = User::factory()->create();

    $this->actingAs($admin)->get(route('impersonate', $attendee->id));

    // When impersonating an attendee, the active user context is the attendee
    $this->get(route('admin.users.index'))->assertForbidden();
    $this->get(route('admin.system.status'))->assertForbidden();
});
