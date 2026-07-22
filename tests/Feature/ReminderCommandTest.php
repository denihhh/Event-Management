<?php

use App\Mail\EventReminderMail;
use App\Models\Event;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

test('reminder command dispatches emails for events starting within 24 hours', function () {
    Mail::fake();

    $user = User::factory()->create();
    $event = Event::factory()->create([
        'starts_at' => now()->addHours(12),
    ]);

    $ticket = Ticket::factory()->create([
        'event_id' => $event->id,
        'user_id' => $user->id,
        'reminder_sent' => false,
    ]);

    $this->artisan('app:send-event-reminders')
        ->expectsOutputToContain('Dispatched 1 reminder(s).')
        ->assertSuccessful();

    expect($ticket->fresh()->reminder_sent)->toBeTrue();

    Mail::assertQueued(EventReminderMail::class, function ($mail) use ($ticket) {
        return $mail->ticket->id === $ticket->id;
    });
});

test('reminder command ignores already sent reminders', function () {
    Mail::fake();

    $user = User::factory()->create();
    $event = Event::factory()->create([
        'starts_at' => now()->addHours(12),
    ]);

    Ticket::factory()->create([
        'event_id' => $event->id,
        'user_id' => $user->id,
        'reminder_sent' => true,
    ]);

    $this->artisan('app:send-event-reminders')
        ->expectsOutputToContain('Dispatched 0 reminder(s).')
        ->assertSuccessful();

    Mail::assertNothingQueued();
});

test('reminder command ignores events starting more than 24 hours in the future', function () {
    Mail::fake();

    $user = User::factory()->create();
    $event = Event::factory()->create([
        'starts_at' => now()->addHours(48),
    ]);

    $ticket = Ticket::factory()->create([
        'event_id' => $event->id,
        'user_id' => $user->id,
        'reminder_sent' => false,
    ]);

    $this->artisan('app:send-event-reminders')
        ->expectsOutputToContain('Dispatched 0 reminder(s).')
        ->assertSuccessful();

    expect($ticket->fresh()->reminder_sent)->toBeFalse();
    Mail::assertNothingQueued();
});
