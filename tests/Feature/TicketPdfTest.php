<?php

use App\Mail\EventTicketMail;
use App\Models\Event;
use App\Models\Ticket;
use App\Models\User;

test('unauthenticated user cannot download ticket pdf', function (): void {
    $ticket = Ticket::factory()->create();

    $this->get(route('tickets.pdf', $ticket))
        ->assertRedirect(route('login'));
});

test('unauthorized user cannot download another user ticket pdf', function (): void {
    $user = User::factory()->create(['is_admin' => false, 'is_organizer' => false]);
    $otherUser = User::factory()->create(['is_admin' => false, 'is_organizer' => false]);
    $ticket = Ticket::factory()->create(['user_id' => $otherUser->id]);

    $this->actingAs($user)
        ->get(route('tickets.pdf', $ticket))
        ->assertForbidden();
});

test('ticket owner can download their pdf ticket pass', function (): void {
    $user = User::factory()->create();
    $ticket = Ticket::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)
        ->get(route('tickets.pdf', $ticket));

    $response->assertOk();
    $response->assertHeader('content-type', 'application/pdf');
});

test('event organizer can download attendee pdf ticket pass', function (): void {
    $organizer = User::factory()->create(['is_organizer' => true]);
    $attendee = User::factory()->create();
    $event = Event::factory()->create(['user_id' => $organizer->id]);
    $ticket = Ticket::factory()->create(['event_id' => $event->id, 'user_id' => $attendee->id]);

    $response = $this->actingAs($organizer)
        ->get(route('tickets.pdf', $ticket));

    $response->assertOk();
    $response->assertHeader('content-type', 'application/pdf');
});

test('admin can download any ticket pdf pass', function (): void {
    $admin = User::factory()->create(['is_admin' => true]);
    $ticket = Ticket::factory()->create();

    $response = $this->actingAs($admin)
        ->get(route('tickets.pdf', $ticket));

    $response->assertOk();
    $response->assertHeader('content-type', 'application/pdf');
});

test('user cannot download pdf for cancelled ticket', function (): void {
    $user = User::factory()->create();
    $ticket = Ticket::factory()->cancelled()->create(['user_id' => $user->id]);

    $this->actingAs($user)
        ->get(route('tickets.pdf', $ticket))
        ->assertForbidden();
});

test('user cannot download pdf for checked-in ticket', function (): void {
    $user = User::factory()->create();
    $ticket = Ticket::factory()->checkedIn()->create(['user_id' => $user->id]);

    $this->actingAs($user)
        ->get(route('tickets.pdf', $ticket))
        ->assertForbidden();
});

test('event ticket mail includes the pdf pass attachment', function (): void {
    $user = User::factory()->create();
    $ticket = Ticket::factory()->create(['user_id' => $user->id]);

    $mailable = new EventTicketMail($ticket);
    $attachments = $mailable->attachments();

    expect($attachments)->toHaveCount(1)
        ->and($attachments[0]->as)->toBe("Ticket-Pass-{$ticket->ticket_code}.pdf")
        ->and($attachments[0]->mime)->toBe('application/pdf');
});
