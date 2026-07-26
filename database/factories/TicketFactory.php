<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Ticket>
 */
class TicketFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'event_id' => Event::factory(),
            'user_id' => User::factory(),
            'reminder_sent' => false,
        ];
    }

    /**
     * Indicate that the reminder has been sent for this ticket.
     */
    public function reminderSent(): static
    {
        return $this->state(fn (array $attributes) => [
            'reminder_sent' => true,
        ]);
    }

    /**
     * Indicate that the ticket has been checked in.
     */
    public function checkedIn(): static
    {
        return $this->state(fn (array $attributes) => [
            'checked_in_at' => now(),
        ]);
    }
}
