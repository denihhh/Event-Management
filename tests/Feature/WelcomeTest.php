<?php

use App\Livewire\Welcome;
use App\Models\Event;
use App\Models\User;

test('guests can visit the welcome landing page', function () {
    $response = $this->get(route('home'));

    $response->assertOk()
        ->assertSeeText('Where Unforgettable Gatherings Begin')
        ->assertSeeText('Discover & Host Extraordinary Events');
});

test('authenticated users are redirected from landing page to dashboard', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('home'));

    $response->assertRedirect(route('dashboard'));
});

test('welcome component displays platform stats and featured events', function () {
    Event::factory()->count(3)->create([
        'starts_at' => now()->addDays(5),
    ]);

    Livewire::test(Welcome::class)
        ->assertStatus(200)
        ->assertViewHas('stats')
        ->assertViewHas('featuredEvents');
});

test('performing search redirects to events index with query', function () {
    Livewire::test(Welcome::class)
        ->set('search', 'Marathon')
        ->call('performSearch')
        ->assertRedirect(route('events.index', ['search' => 'Marathon']));
});
