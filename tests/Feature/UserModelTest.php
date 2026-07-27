<?php

use App\Models\Event;
use App\Models\Ticket;
use App\Models\User;

test('user has organized events relationship', function () {
    $organizer = User::factory()->organizer()->create();
    Event::factory()->count(2)->create(['user_id' => $organizer->id]);

    expect($organizer->organizedEvents)->toHaveCount(2);
});

test('user has tickets relationship', function () {
    $user = User::factory()->create();
    Ticket::factory()->count(3)->create(['user_id' => $user->id]);

    expect($user->tickets)->toHaveCount(3);
});

test('user is_organizer defaults to false', function () {
    $user = User::factory()->create();

    expect($user->is_organizer)->toBeFalse();
});

test('user can be created as organizer via factory state', function () {
    $organizer = User::factory()->organizer()->create();

    expect($organizer->is_organizer)->toBeTrue();
});

test('user registration creates standard attendee account', function () {
    $response = $this->post(route('register.store'), [
        'name' => 'Test User',
        'email' => 'newuser@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertRedirect(route('dashboard'));

    $user = User::where('email', 'newuser@example.com')->first();
    expect($user)->not->toBeNull()
        ->and($user->is_organizer)->toBeFalse();
});
