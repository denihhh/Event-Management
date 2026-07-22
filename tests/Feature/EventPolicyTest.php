<?php

use App\Models\Event;
use App\Models\User;
use App\Policies\EventPolicy;

test('any authenticated user can view any events', function () {
    $user = User::factory()->create();

    expect($user->can('viewAny', Event::class))->toBeTrue();
});

test('guest can view a specific event', function () {
    $event = Event::factory()->create();

    expect((new EventPolicy)->view(null, $event))->toBeTrue();
});

test('organizer can create events', function () {
    $organizer = User::factory()->organizer()->create();

    expect($organizer->can('create', Event::class))->toBeTrue();
});

test('attendee cannot create events', function () {
    $attendee = User::factory()->create();

    expect($attendee->can('create', Event::class))->toBeFalse();
});

test('organizer can update their own event', function () {
    $organizer = User::factory()->organizer()->create();
    $event = Event::factory()->create(['user_id' => $organizer->id]);

    expect($organizer->can('update', $event))->toBeTrue();
});

test('organizer cannot update another organizer event', function () {
    $organizer = User::factory()->organizer()->create();
    $otherEvent = Event::factory()->create();

    expect($organizer->can('update', $otherEvent))->toBeFalse();
});

test('organizer can delete their own event', function () {
    $organizer = User::factory()->organizer()->create();
    $event = Event::factory()->create(['user_id' => $organizer->id]);

    expect($organizer->can('delete', $event))->toBeTrue();
});

test('organizer cannot delete another organizer event', function () {
    $organizer = User::factory()->organizer()->create();
    $otherEvent = Event::factory()->create();

    expect($organizer->can('delete', $otherEvent))->toBeFalse();
});

test('attendee cannot delete events', function () {
    $attendee = User::factory()->create();
    $event = Event::factory()->create();

    expect($attendee->can('delete', $event))->toBeFalse();
});

test('attendee can register for events', function () {
    $attendee = User::factory()->create();
    $event = Event::factory()->create();

    expect($attendee->can('register', $event))->toBeTrue();
});

test('organizer cannot register for their own event', function () {
    $organizer = User::factory()->organizer()->create();
    $event = Event::factory()->create(['user_id' => $organizer->id]);

    expect($organizer->can('register', $event))->toBeFalse();
});

test('organizer can register for another organizer event', function () {
    $organizer = User::factory()->organizer()->create();
    $otherEvent = Event::factory()->create();

    expect($organizer->can('register', $otherEvent))->toBeTrue();
});

test('organizer can restore their own event', function () {
    $organizer = User::factory()->organizer()->create();
    $event = Event::factory()->create(['user_id' => $organizer->id]);

    expect($organizer->can('restore', $event))->toBeTrue();
});

test('no user can force delete events', function () {
    $organizer = User::factory()->organizer()->create();
    $event = Event::factory()->create(['user_id' => $organizer->id]);

    expect($organizer->can('forceDelete', $event))->toBeFalse();
});
