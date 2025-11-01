<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\AccountType;
use App\Models\Founder;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Startup>
 */
final class StartupFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->words(2, true);

        $mrr = $this->faker->numberBetween(1000, 50000);
        $totalRevenue = $this->faker->numberBetween($mrr * 6, $mrr * 24);

        return [
            'founder_id' => Founder::factory(),
            'encrypted_api_key' => encrypt($this->faker->sha256()),
            'name' => ucwords($name),
            'slug' => str($name)->slug(),
            'description' => $this->faker->sentence(),
            'website_url' => $this->faker->url(),
            'business_created_at' => $this->faker->dateTimeBetween('-5 years'),
            'total_revenue' => $totalRevenue,
            'monthly_recurring_revenue' => $mrr,
            'subscriber_count' => $this->faker->numberBetween(10, 500),
            'last_synced_at' => now(),
            'rank' => $this->faker->numberBetween(1, 100),
            'account_type' => AccountType::POLAR->value,
        ];
    }
}
