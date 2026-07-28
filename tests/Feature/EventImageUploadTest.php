<?php

use App\Livewire\Organizer\EventCreate;
use App\Livewire\Organizer\EventEdit;
use App\Models\Event;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

test('organizer can create an event with a banner image', function (): void {
    Storage::fake('public');

    $organizer = User::factory()->organizer()->create();
    $banner = UploadedFile::fake()->image('banner.jpg', 1920, 1080);

    Livewire::actingAs($organizer)
        ->test(EventCreate::class)
        ->set('title', 'Widescreen Tech Conference 2026')
        ->set('description', 'Join us for an exciting tech summit.')
        ->set('starts_at', now()->addDays(5)->format('Y-m-d\TH:i'))
        ->set('location', 'Tech Center Hall A')
        ->set('capacity', 200)
        ->set('banner', $banner)
        ->call('save')
        ->assertRedirect(route('organizer.events.index'));

    $event = Event::where('title', 'Widescreen Tech Conference 2026')->first();

    expect($event)->not->toBeNull()
        ->and($event->banner_path)->not->toBeNull();

    Storage::disk('public')->assertExists($event->banner_path);
});

test('organizer can upload inline description images', function (): void {
    Storage::fake('public');

    $organizer = User::factory()->organizer()->create();
    $inlineImg = UploadedFile::fake()->image('merch-tshirt.png', 800, 600);

    Livewire::actingAs($organizer)
        ->test(EventCreate::class)
        ->set('inlineImage', $inlineImg)
        ->call('uploadInlineImage')
        ->assertHasNoErrors();

    $component = Livewire::actingAs($organizer)
        ->test(EventCreate::class)
        ->set('inlineImage', $inlineImg)
        ->call('uploadInlineImage');

    expect($component->get('description'))->toContain('<img src=')
        ->and($component->get('uploadedInlineImages'))->not->toBeEmpty();
});

test('organizer can replace and remove an event banner image', function (): void {
    Storage::fake('public');

    $organizer = User::factory()->organizer()->create();
    $originalPath = 'events/banners/original.jpg';
    Storage::disk('public')->put($originalPath, 'dummy content');

    $event = Event::factory()->create([
        'user_id' => $organizer->id,
        'banner_path' => $originalPath,
    ]);

    $newBanner = UploadedFile::fake()->image('new-banner.png', 1920, 1080);

    Livewire::actingAs($organizer)
        ->test(EventEdit::class, ['event' => $event])
        ->set('banner', $newBanner)
        ->call('update')
        ->assertRedirect(route('organizer.events.index'));

    $event->refresh();

    expect($event->banner_path)->not->toBe($originalPath);
    Storage::disk('public')->assertExists($event->banner_path);

    // Test removing banner requires update() to persist
    Livewire::actingAs($organizer)
        ->test(EventEdit::class, ['event' => $event])
        ->call('removeBanner')
        ->call('update');

    $event->refresh();
    expect($event->banner_path)->toBeNull();
});

test('cancelling edit does not remove or change event banner in database', function (): void {
    Storage::fake('public');

    $organizer = User::factory()->organizer()->create();
    $originalPath = 'events/banners/original.jpg';
    Storage::disk('public')->put($originalPath, 'dummy content');

    $event = Event::factory()->create([
        'user_id' => $organizer->id,
        'banner_path' => $originalPath,
    ]);

    // Call removeBanner in Livewire component without calling update()
    Livewire::actingAs($organizer)
        ->test(EventEdit::class, ['event' => $event])
        ->call('removeBanner');

    $event->refresh();

    // Database record and storage file remain untouched
    expect($event->banner_path)->toBe($originalPath);
    Storage::disk('public')->assertExists($originalPath);
});

test('non-image files are rejected for event banner', function (): void {
    Storage::fake('public');

    $organizer = User::factory()->organizer()->create();

    Livewire::actingAs($organizer)
        ->test(EventCreate::class)
        ->set('banner', UploadedFile::fake()->create('video.mp4', 100, 'video/mp4'))
        ->call('save')
        ->assertHasErrors(['banner' => 'image']);
});
