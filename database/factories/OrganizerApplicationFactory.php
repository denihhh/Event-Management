<?php

namespace Database\Factories;

use App\Models\OrganizerApplication;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OrganizerApplication>
 */
class OrganizerApplicationFactory extends Factory
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
            'organization_name' => fake()->company(),
            'business_reg_number' => fake()->numerify('202401###### (######-X)'),
            'contact_phone' => fake()->phoneNumber(),
            'bank_name' => fake()->randomElement(['Maybank', 'CIMB Bank', 'Public Bank', 'RHB Bank']),
            'bank_account_number' => fake()->bankAccountNumber(),
            'event_title' => fake()->sentence(4),
            'event_description' => fake()->paragraph(),
            'proposed_starts_at' => now()->addDays(30),
            'requested_duration_days' => 30,
            'status' => 'pending',
        ];
    }
}
