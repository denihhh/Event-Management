<?php

use App\Livewire\Events\EventIndex;
use App\Livewire\Tickets\MyTickets;
use App\Livewire\Tickets\RegisterTicket;
use App\Mail\EventTicketMail;
use App\Models\Event;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;

test('public user can view event index', function () {
    $events = Event::factory()->count(3)->create();

    $response = $this->get(route('events.index'));
    $response->assertOk();

    foreach ($events as $event) {
        $response->assertSee($event->title);
    }
});

test('event index filters by search query', function () {
    $matching = Event::factory()->create(['title' => 'Laravel Conference 2026']);
    $other = Event::factory()->create(['title' => 'Vue Meetup']);

    Livewire::test(EventIndex::class)
        ->set('search', 'Laravel')
        ->assertSee('Laravel Conference 2026')
        ->assertDontSee('Vue Meetup');
});

test('public user can view event details', function () {
    $event = Event::factory()->create([
        'title' => 'Tech Summit 2026',
        'description' => 'A great tech conference.',
    ]);

    $response = $this->get(route('events.show', $event));
    $response->assertOk()
        ->assertSee('Tech Summit 2026')
        ->assertSee('A great tech conference.');
});

test('unauthenticated user claiming ticket is redirected to login', function () {
    $event = Event::factory()->create();

    Livewire::test(RegisterTicket::class, ['event' => $event])
        ->call('register')
        ->assertRedirect(route('login'));
});

test('authenticated user can successfully register for a ticket', function () {
    Mail::fake();

    $user = User::factory()->create();
    $event = Event::factory()->create(['capacity' => 100, 'tickets_sold' => 0]);

    Livewire::actingAs($user)
        ->test(RegisterTicket::class, ['event' => $event])
        ->call('register');

    expect($event->fresh()->tickets_sold)->toBe(1);

    $ticket = Ticket::where('event_id', $event->id)->where('user_id', $user->id)->first();
    expect($ticket)->not->toBeNull()
        ->and($ticket->ticket_code)->not->toBeNull();

    Mail::assertQueued(EventTicketMail::class, function ($mail) use ($ticket) {
        return $mail->ticket->id === $ticket->id;
    });
});

test('user cannot register for sold out event', function () {
    Mail::fake();

    $user = User::factory()->create();
    $event = Event::factory()->soldOut(10)->create();

    Livewire::actingAs($user)
        ->test(RegisterTicket::class, ['event' => $event])
        ->call('register');

    expect($event->fresh()->tickets_sold)->toBe(10);
    expect(Ticket::where('event_id', $event->id)->count())->toBe(0);

    Mail::assertNothingQueued();
});

test('user cannot register twice for the same event', function () {
    Mail::fake();

    $user = User::factory()->create();
    $event = Event::factory()->create(['capacity' => 10, 'tickets_sold' => 0]);

    // First registration
    Livewire::actingAs($user)
        ->test(RegisterTicket::class, ['event' => $event])
        ->call('register');

    expect($event->fresh()->tickets_sold)->toBe(1);

    // Second registration attempt
    Livewire::actingAs($user)
        ->test(RegisterTicket::class, ['event' => $event])
        ->call('register');

    expect($event->fresh()->tickets_sold)->toBe(1);
    expect(Ticket::where('event_id', $event->id)->count())->toBe(1);
});

test('organizer cannot register for their own event', function () {
    Mail::fake();

    $organizer = User::factory()->organizer()->create();
    $event = Event::factory()->create(['user_id' => $organizer->id]);

    Livewire::actingAs($organizer)
        ->test(RegisterTicket::class, ['event' => $event])
        ->call('register');

    expect($event->fresh()->tickets_sold)->toBe(0);
    expect(Ticket::where('event_id', $event->id)->count())->toBe(0);

    Mail::assertNothingQueued();
});

test('attendee can view my tickets page', function () {
    $user = User::factory()->create();
    $ticket = Ticket::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user)
        ->get(route('tickets.index'))
        ->assertOk()
        ->assertSee($ticket->event->title)
        ->assertSee($ticket->ticket_code);
});

test('attendee can cancel active ticket', function () {
    $user = User::factory()->create();
    $event = Event::factory()->create(['capacity' => 10, 'tickets_sold' => 1]);
    $ticket = Ticket::factory()->create(['event_id' => $event->id, 'user_id' => $user->id]);

    Livewire::actingAs($user)
        ->test(MyTickets::class)
        ->set('cancellingTicketId', $ticket->id)
        ->call('cancelTicket');

    expect(Ticket::find($ticket->id))->toBeNull()
        ->and($event->fresh()->tickets_sold)->toBe(0);
});
