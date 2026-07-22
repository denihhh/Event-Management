<?php

use App\Livewire\Organizer\EventAttendees;
use App\Livewire\Organizer\EventCreate;
use App\Livewire\Organizer\EventEdit;
use App\Livewire\Organizer\OrganizerEventIndex;
use App\Mail\EventCancelledMail;
use App\Mail\EventUpdatedMail;
use App\Models\Event;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;

test('guest or attendee cannot access organizer dashboard', function () {
    $attendee = User::factory()->create();

    $this->get(route('organizer.events.index'))->assertRedirect(route('login'));

    $this->actingAs($attendee)
        ->get(route('organizer.events.index'))
        ->assertForbidden();
});

test('organizer can view organizer event index', function () {
    $organizer = User::factory()->organizer()->create();
    $myEvent = Event::factory()->create(['user_id' => $organizer->id, 'title' => 'My Secret Summit']);
    $otherEvent = Event::factory()->create(['title' => 'Other Organizer Fest']);

    $this->actingAs($organizer)
        ->get(route('organizer.events.index'))
        ->assertOk()
        ->assertSee('My Secret Summit')
        ->assertDontSee('Other Organizer Fest');
});

test('organizer can create a new event', function () {
    $organizer = User::factory()->organizer()->create();

    Livewire::actingAs($organizer)
        ->test(EventCreate::class)
        ->set('title', 'Hackathon 2026')
        ->set('description', 'Build cool stuff.')
        ->set('starts_at', now()->addDays(10)->format('Y-m-d\TH:i'))
        ->set('location', 'Tech Hub Floor 4')
        ->set('capacity', 75)
        ->call('save')
        ->assertRedirect(route('organizer.events.index'));

    $event = Event::where('title', 'Hackathon 2026')->first();
    expect($event)->not->toBeNull()
        ->and($event->user_id)->toBe($organizer->id)
        ->and($event->capacity)->toBe(75);
});

test('organizer can edit their event and notify attendees on schedule change', function () {
    Mail::fake();

    $organizer = User::factory()->organizer()->create();
    $event = Event::factory()->create([
        'user_id' => $organizer->id,
        'starts_at' => now()->addDays(5),
        'location' => 'Old Location',
    ]);

    $attendee = User::factory()->create();
    $ticket = Ticket::factory()->create([
        'event_id' => $event->id,
        'user_id' => $attendee->id,
        'reminder_sent' => true,
    ]);

    $newDate = now()->addDays(12)->format('Y-m-d\TH:i');

    Livewire::actingAs($organizer)
        ->test(EventEdit::class, ['event' => $event])
        ->set('title', $event->title)
        ->set('description', $event->description)
        ->set('starts_at', $newDate)
        ->set('location', 'New Grand Arena')
        ->set('capacity', $event->capacity)
        ->call('update')
        ->assertRedirect(route('organizer.events.index'));

    expect($ticket->fresh()->reminder_sent)->toBeFalse()
        ->and($event->fresh()->location)->toBe('New Grand Arena');

    Mail::assertQueued(EventUpdatedMail::class, function ($mail) use ($ticket) {
        return $mail->ticket->id === $ticket->id;
    });
});

test('organizer cannot edit another organizer event', function () {
    $organizer = User::factory()->organizer()->create();
    $otherEvent = Event::factory()->create();

    $this->actingAs($organizer)
        ->get(route('organizer.events.edit', $otherEvent))
        ->assertForbidden();
});

test('organizer can cancel event which soft deletes and notifies attendees', function () {
    Mail::fake();

    $organizer = User::factory()->organizer()->create();
    $event = Event::factory()->create(['user_id' => $organizer->id]);

    $attendee = User::factory()->create();
    Ticket::factory()->create(['event_id' => $event->id, 'user_id' => $attendee->id]);

    Livewire::actingAs($organizer)
        ->test(OrganizerEventIndex::class)
        ->set('deletingEventId', $event->id)
        ->call('deleteEvent');

    expect($event->fresh()->trashed())->toBeTrue();

    Mail::assertQueued(EventCancelledMail::class, function ($mail) use ($event) {
        return $mail->eventTitle === $event->title;
    });
});

test('organizer can view attendee roster and export CSV', function () {
    $organizer = User::factory()->organizer()->create();
    $event = Event::factory()->create(['user_id' => $organizer->id]);
    $attendee = User::factory()->create(['name' => 'Alice Walker']);
    $ticket = Ticket::factory()->create(['event_id' => $event->id, 'user_id' => $attendee->id]);

    $this->actingAs($organizer)
        ->get(route('organizer.events.attendees', $event))
        ->assertOk()
        ->assertSee('Alice Walker')
        ->assertSee($ticket->ticket_code);

    Livewire::actingAs($organizer)
        ->test(EventAttendees::class, ['event' => $event])
        ->call('exportCsv')
        ->assertFileDownloaded();
});
