<?php

use App\Models\Event;
use App\Models\Ticket;
use App\Models\User;

test('ticket belongs to an event', function () {
    $ticket = Ticket::factory()->create();

    expect($ticket->event)->toBeInstanceOf(Event::class);
});

test('ticket belongs to a user', function () {
    $ticket = Ticket::factory()->create();

    expect($ticket->user)->toBeInstanceOf(User::class);
});

test('ticket auto-generates a uuid ticket code on creation', function () {
    $ticket = Ticket::factory()->create();

    expect($ticket->ticket_code)->not->toBeNull()
        ->and($ticket->ticket_code)->toMatch('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/');
});

test('each ticket gets a unique ticket code', function () {
    $tickets = Ticket::factory()->count(5)->create();
    $codes = $tickets->pluck('ticket_code')->unique();

    expect($codes)->toHaveCount(5);
});

test('ticket casts reminder_sent to boolean', function () {
    $ticket = Ticket::factory()->create(['reminder_sent' => false]);

    expect($ticket->reminder_sent)->toBeFalse()->toBeBool();
});

test('ticket factory reminder sent state works', function () {
    $ticket = Ticket::factory()->reminderSent()->create();

    expect($ticket->reminder_sent)->toBeTrue();
});

test('ticket defaults reminder_sent to false', function () {
    $ticket = Ticket::factory()->create();

    expect($ticket->reminder_sent)->toBeFalse();
});
