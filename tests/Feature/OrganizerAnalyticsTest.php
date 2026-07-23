<?php

use App\Livewire\Organizer\OrganizerAnalytics;
use App\Models\Event;
use App\Models\Ticket;
use App\Models\User;
use Livewire\Livewire;

test('organizers can render the analytics livewire component with correct metrics', function () {
    $organizer = User::factory()->organizer()->create();
    $event = Event::factory()->create(['user_id' => $organizer->id, 'tickets_sold' => 5]);

    Ticket::factory()->count(5)->create(['event_id' => $event->id]);

    Livewire::actingAs($organizer)
        ->test(OrganizerAnalytics::class)
        ->assertStatus(200)
        ->assertViewHas('totalEvents', 1)
        ->assertViewHas('totalTicketsSold', 5)
        ->assertSee('Organizer Analytics &amp; Performance', false)
        ->assertSee('Ticket Sales Trend');
});

test('attendees do not see organizer analytics component on dashboard', function () {
    $attendee = User::factory()->create();

    $this->actingAs($attendee)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertDontSeeText('Organizer Analytics & Performance');
});

test('organizers see analytics component on dashboard', function () {
    $organizer = User::factory()->organizer()->create();

    $this->actingAs($organizer)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSeeText('Organizer Analytics & Performance');
});
