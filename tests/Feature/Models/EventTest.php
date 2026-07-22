<?php

use App\Models\Event;
use App\Models\Ticket;
use App\Models\User;
use Carbon\CarbonInterface;

test('event belongs to an organizer', function () {
    $organizer = User::factory()->organizer()->create();
    $event = Event::factory()->create(['user_id' => $organizer->id]);

    expect($event->organizer->id)->toBe($organizer->id);
});

test('event has many tickets', function () {
    $event = Event::factory()->create();
    Ticket::factory()->count(3)->create(['event_id' => $event->id]);

    expect($event->tickets)->toHaveCount(3);
});

test('event is sold out when tickets sold equals capacity', function () {
    $event = Event::factory()->create(['capacity' => 10, 'tickets_sold' => 10]);

    expect($event->isSoldOut())->toBeTrue();
});

test('event is not sold out when tickets remain', function () {
    $event = Event::factory()->create(['capacity' => 10, 'tickets_sold' => 5]);

    expect($event->isSoldOut())->toBeFalse();
});

test('event calculates remaining tickets', function () {
    $event = Event::factory()->create(['capacity' => 100, 'tickets_sold' => 42]);

    expect($event->remainingTickets())->toBe(58);
});

test('event remaining tickets is never negative', function () {
    $event = Event::factory()->create(['capacity' => 10, 'tickets_sold' => 15]);

    expect($event->remainingTickets())->toBe(0);
});

test('event detects if it has started', function () {
    $pastEvent = Event::factory()->past()->create();
    $futureEvent = Event::factory()->create();

    expect($pastEvent->hasStarted())->toBeTrue()
        ->and($futureEvent->hasStarted())->toBeFalse();
});

test('event uses soft deletes', function () {
    $event = Event::factory()->create();
    $event->delete();

    expect($event->trashed())->toBeTrue()
        ->and(Event::withTrashed()->find($event->id))->not->toBeNull()
        ->and(Event::find($event->id))->toBeNull();
});

test('event factory sold out state works', function () {
    $event = Event::factory()->soldOut(50)->create();

    expect($event->tickets_sold)->toBe(50)
        ->and($event->isSoldOut())->toBeTrue();
});

test('event casts starts_at to datetime', function () {
    $event = Event::factory()->create();

    expect($event->starts_at)->toBeInstanceOf(CarbonInterface::class);
});

test('event casts capacity and tickets_sold to integers', function () {
    $event = Event::factory()->create(['capacity' => 100, 'tickets_sold' => 5]);

    expect($event->capacity)->toBeInt()
        ->and($event->tickets_sold)->toBeInt();
});
