<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Rfc>
 */
final class RfcFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'startup_id' => \App\Models\Startup::factory(),
            'uuid' => (string) \Illuminate\Support\Str::uuid(),
            'customer_name' => $this->faker->name(),
            'customer_email' => $this->faker->email(),
            'response' => null,
        ];
    }
}
