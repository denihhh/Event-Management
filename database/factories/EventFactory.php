<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Event>
 */
class EventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => fake()->sentence(3),
            'description' => fake()->paragraphs(2, true),
            'starts_at' => fake()->dateTimeBetween('+1 day', '+3 months'),
            'location' => fake()->address(),
            'banner_path' => null,
            'capacity' => fake()->numberBetween(10, 500),
            'tickets_sold' => 0,
        ];
    }

    /**
     * Indicate that the event has a banner image.
     */
    public function withBanner(?string $path = null): static
    {
        return $this->state(fn (array $attributes) => [
            'banner_path' => $path ?? 'events/banners/sample-banner.jpg',
        ]);
    }

    /**
     * Indicate that the event is sold out.
     */
    public function soldOut(?int $capacity = null): static
    {
        return $this->state(function (array $attributes) use ($capacity) {
            $cap = $capacity ?? $attributes['capacity'] ?? 100;

            return [
                'capacity' => $cap,
                'tickets_sold' => $cap,
            ];
        });
    }

    /**
     * Indicate that the event has already started.
     */
    public function past(): static
    {
        return $this->state(fn (array $attributes) => [
            'starts_at' => fake()->dateTimeBetween('-1 month', '-1 hour'),
        ]);
    }

    /**
     * Indicate that the event starts within the next 24 hours.
     */
    public function startingSoon(): static
    {
        return $this->state(fn (array $attributes) => [
            'starts_at' => now()->addHours(fake()->numberBetween(1, 23)),
        ]);
    }
}
