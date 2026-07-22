<?php

use App\Livewire\Tickets\RegisterTicket;
use App\Models\Event;
use App\Models\Ticket;
use App\Models\User;
use Livewire\Livewire;

test('simulated concurrent registration respects capacity limit and does not oversell', function () {
    $event = Event::factory()->create([
        'capacity' => 1,
        'tickets_sold' => 0,
    ]);

    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    // Register user 1
    Livewire::actingAs($user1)
        ->test(RegisterTicket::class, ['event' => $event])
        ->call('register');

    expect($event->fresh()->tickets_sold)->toBe(1);

    // Attempt register user 2 (event now at capacity)
    Livewire::actingAs($user2)
        ->test(RegisterTicket::class, ['event' => $event])
        ->call('register');

    expect($event->fresh()->tickets_sold)->toBe(1)
        ->and(Ticket::where('event_id', $event->id)->count())->toBe(1);
});

test('deleting a ticket via model lifecycle automatically decrements tickets_sold counter', function () {
    $event = Event::factory()->create(['capacity' => 10, 'tickets_sold' => 3]);
    $ticket = Ticket::factory()->create(['event_id' => $event->id]);

    // Force sync counter to match 4 tickets
    $event->forceFill(['tickets_sold' => 4])->save();

    $ticket->delete();

    expect($event->fresh()->tickets_sold)->toBe(3);
});

test('deleting an attendee user cascades ticket deletion and updates event tickets_sold counter', function () {
    $event = Event::factory()->create(['capacity' => 10, 'tickets_sold' => 1]);
    $attendee = User::factory()->create();
    $ticket = Ticket::factory()->create(['event_id' => $event->id, 'user_id' => $attendee->id]);

    $attendee->delete();

    expect(Ticket::find($ticket->id))->toBeNull()
        ->and($event->fresh()->tickets_sold)->toBe(0);
});

test('soft deleting an event retains ticket records for audit history', function () {
    $event = Event::factory()->create();
    $ticket = Ticket::factory()->create(['event_id' => $event->id]);

    $event->delete();

    expect($event->fresh()->trashed())->toBeTrue()
        ->and(Ticket::find($ticket->id))->not->toBeNull()
        ->and(Event::find($event->id))->toBeNull()
        ->and(Event::withTrashed()->find($event->id))->not->toBeNull();
});
