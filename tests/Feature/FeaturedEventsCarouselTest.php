<?php

use App\Livewire\Events\FeaturedEventsCarousel;
use App\Models\Event;
use App\Models\User;
use Livewire\Livewire;

test('featured events carousel renders upcoming events in chronological order', function (): void {
    $organizer = User::factory()->organizer()->create();

    $futureEvent1 = Event::factory()->create([
        'user_id' => $organizer->id,
        'title' => 'Future Symposium A',
        'starts_at' => now()->addDays(2),
    ]);

    $futureEvent2 = Event::factory()->create([
        'user_id' => $organizer->id,
        'title' => 'Future Summit B',
        'starts_at' => now()->addDays(5),
    ]);

    $pastEvent = Event::factory()->create([
        'user_id' => $organizer->id,
        'title' => 'Concluded Event C',
        'starts_at' => now()->subDays(3),
    ]);

    Livewire::test(FeaturedEventsCarousel::class)
        ->assertSee('Future Symposium A')
        ->assertSee('Future Summit B')
        ->assertDontSee('Concluded Event C');
});

test('featured events carousel excludes soft deleted events', function (): void {
    $organizer = User::factory()->organizer()->create();

    $activeEvent = Event::factory()->create([
        'user_id' => $organizer->id,
        'title' => 'Active Event 101',
        'starts_at' => now()->addDays(1),
    ]);

    $deletedEvent = Event::factory()->create([
        'user_id' => $organizer->id,
        'title' => 'Deleted Event 999',
        'starts_at' => now()->addDays(2),
    ]);
    $deletedEvent->delete();

    Livewire::test(FeaturedEventsCarousel::class)
        ->assertSee('Active Event 101')
        ->assertDontSee('Deleted Event 999');
});

test('attendee dashboard includes featured events carousel', function (): void {
    $attendee = User::factory()->create();

    $this->actingAs($attendee)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSeeLivewire(FeaturedEventsCarousel::class);
});
