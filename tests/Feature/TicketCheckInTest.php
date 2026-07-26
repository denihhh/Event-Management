<?php

use App\Livewire\Organizer\CheckInAttendees;
use App\Models\Event;
use App\Models\Ticket;
use App\Models\User;
use Livewire\Livewire;

test('unauthenticated user cannot access check-in page', function (): void {
    $event = Event::factory()->create();

    $this->get(route('organizer.events.check-in', $event))
        ->assertRedirect(route('login'));
});

test('attendee cannot access check-in page', function (): void {
    $attendee = User::factory()->create(['is_organizer' => false, 'is_admin' => false]);
    $event = Event::factory()->create();

    $this->actingAs($attendee)
        ->get(route('organizer.events.check-in', $event))
        ->assertForbidden();
});

test('organizer cannot access check-in page for another organizer event', function (): void {
    $organizer1 = User::factory()->create(['is_organizer' => true]);
    $organizer2 = User::factory()->create(['is_organizer' => true]);
    $event = Event::factory()->create(['user_id' => $organizer1->id]);

    $this->actingAs($organizer2)
        ->get(route('organizer.events.check-in', $event))
        ->assertForbidden();
});

test('organizer can view check-in page for their event', function (): void {
    $organizer = User::factory()->create(['is_organizer' => true]);
    $event = Event::factory()->create(['user_id' => $organizer->id]);

    $this->actingAs($organizer)
        ->get(route('organizer.events.check-in', $event))
        ->assertOk();
});

test('organizer can check in attendee manually', function (): void {
    $organizer = User::factory()->create(['is_organizer' => true]);
    $event = Event::factory()->create(['user_id' => $organizer->id]);
    $ticket = Ticket::factory()->create(['event_id' => $event->id]);

    expect($ticket->isCheckedIn())->toBeFalse();

    Livewire::actingAs($organizer)
        ->test(CheckInAttendees::class, ['event' => $event])
        ->call('checkIn', $ticket->id);

    expect($ticket->fresh()->isCheckedIn())->toBeTrue();
});

test('organizer can undo check-in for an attendee', function (): void {
    $organizer = User::factory()->create(['is_organizer' => true]);
    $event = Event::factory()->create(['user_id' => $organizer->id]);
    $ticket = Ticket::factory()->checkedIn()->create(['event_id' => $event->id]);

    expect($ticket->isCheckedIn())->toBeTrue();

    Livewire::actingAs($organizer)
        ->test(CheckInAttendees::class, ['event' => $event])
        ->call('undoCheckIn', $ticket->id);

    expect($ticket->fresh()->isCheckedIn())->toBeFalse();
});

test('organizer can fast check-in by scanning ticket code', function (): void {
    $organizer = User::factory()->create(['is_organizer' => true]);
    $event = Event::factory()->create(['user_id' => $organizer->id]);
    $ticket = Ticket::factory()->create(['event_id' => $event->id]);

    Livewire::actingAs($organizer)
        ->test(CheckInAttendees::class, ['event' => $event])
        ->set('ticketInput', $ticket->ticket_code)
        ->call('scanCheckIn')
        ->assertSet('ticketInput', '');

    expect($ticket->fresh()->isCheckedIn())->toBeTrue();
});

test('scanning invalid ticket code does not crash and leaves ticket untouched', function (): void {
    $organizer = User::factory()->create(['is_organizer' => true]);
    $event = Event::factory()->create(['user_id' => $organizer->id]);
    $ticket = Ticket::factory()->create(['event_id' => $event->id]);

    Livewire::actingAs($organizer)
        ->test(CheckInAttendees::class, ['event' => $event])
        ->set('ticketInput', 'non-existent-code')
        ->call('scanCheckIn');

    expect($ticket->fresh()->isCheckedIn())->toBeFalse();
});

test('organizer can filter attendees by checked-in status', function (): void {
    $organizer = User::factory()->create(['is_organizer' => true]);
    $event = Event::factory()->create(['user_id' => $organizer->id]);
    $ticket1 = Ticket::factory()->create(['event_id' => $event->id]);
    $ticket2 = Ticket::factory()->checkedIn()->create(['event_id' => $event->id]);

    Livewire::actingAs($organizer)
        ->test(CheckInAttendees::class, ['event' => $event])
        ->set('statusFilter', 'checked_in')
        ->assertSee($ticket2->ticket_code)
        ->assertDontSee($ticket1->ticket_code);
});

test('ticket model provides valid QR code data uri', function (): void {
    $ticket = Ticket::factory()->create();

    $qrCodeDataUri = $ticket->getQrCodeDataUri();

    expect($qrCodeDataUri)
        ->toBeString()
        ->toContain('data:image/svg+xml;base64,');
});
