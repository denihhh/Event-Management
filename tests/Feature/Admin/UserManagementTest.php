<?php

use App\Livewire\Admin\Users;
use App\Models\User;
use Livewire\Livewire;

// ──────────────────────────────────────────────────────────────────────────────
// Route access
// ──────────────────────────────────────────────────────────────────────────────

it('blocks unauthenticated access to the users management page', function (): void {
    $this->get(route('admin.users.index'))->assertRedirect(route('login'));
});

it('blocks non-admin users from accessing the users management page', function (): void {
    $attendee = User::factory()->create();
    $organizer = User::factory()->organizer()->create();

    $this->actingAs($attendee)->get(route('admin.users.index'))->assertForbidden();
    $this->actingAs($organizer)->get(route('admin.users.index'))->assertForbidden();
});

it('allows admins to access the users management page', function (): void {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)->get(route('admin.users.index'))->assertOk();
});

// ──────────────────────────────────────────────────────────────────────────────
// Component rendering
// ──────────────────────────────────────────────────────────────────────────────

it('displays other users in the list', function (): void {
    $admin = User::factory()->admin()->create();
    $attendee = User::factory()->create(['name' => 'Alice Attendee']);

    Livewire::actingAs($admin)
        ->test(Users::class)
        ->assertSee('Alice Attendee');
});

it('does not display the admin themselves in the list', function (): void {
    $admin = User::factory()->admin()->create(['name' => 'Super Admin']);

    Livewire::actingAs($admin)
        ->test(Users::class)
        ->assertDontSee('Super Admin');
});

it('filters users by search query', function (): void {
    $admin = User::factory()->admin()->create();
    User::factory()->create(['name' => 'Bob Builder']);
    User::factory()->create(['name' => 'Carol Carpenter']);

    Livewire::actingAs($admin)
        ->test(Users::class)
        ->set('search', 'Bob')
        ->assertSee('Bob Builder')
        ->assertDontSee('Carol Carpenter');
});

it('filters users by role', function (): void {
    $admin = User::factory()->admin()->create();
    $otherAdmin = User::factory()->admin()->create(['name' => 'Second Admin']);
    $organizer = User::factory()->organizer()->create(['name' => 'Event Organizer']);
    $attendee = User::factory()->create(['name' => 'Regular Attendee']);

    Livewire::actingAs($admin)
        ->test(Users::class)
        ->set('role', 'organizer')
        ->assertSee('Event Organizer')
        ->assertDontSee('Second Admin')
        ->assertDontSee('Regular Attendee')
        ->set('role', 'attendee')
        ->assertSee('Regular Attendee')
        ->assertDontSee('Event Organizer')
        ->set('role', 'admin')
        ->assertSee('Second Admin')
        ->assertDontSee('Regular Attendee');
});

// ──────────────────────────────────────────────────────────────────────────────
// Impersonation via Livewire action
// ──────────────────────────────────────────────────────────────────────────────

it('redirects to dashboard after impersonating a user', function (): void {
    $admin = User::factory()->admin()->create();
    $attendee = User::factory()->create();

    Livewire::actingAs($admin)
        ->test(Users::class)
        ->call('impersonate', $attendee->id)
        ->assertRedirect(route('dashboard'));
});

it('forbids impersonating an admin user', function (): void {
    $admin = User::factory()->admin()->create();
    $anotherAdmin = User::factory()->admin()->create();

    Livewire::actingAs($admin)
        ->test(Users::class)
        ->call('impersonate', $anotherAdmin->id)
        ->assertForbidden();
});
