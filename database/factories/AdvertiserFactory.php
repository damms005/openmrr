<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Advertiser>
 */
final class AdvertiserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $email = fake()->email();
        $hash = md5(strtolower(trim($email)));
        $gravatarUrl = "https://www.gravatar.com/avatar/{$hash}?s=300&d=identicon";

        return [
            'title' => fake()->company(),
            'description' => fake()->sentence(),
            'image_url' => $gravatarUrl,
            'link_url' => fake()->url(),
            'position' => fake()->randomElement(['sidebar', 'header', 'footer']),
            'active_till' => now()->addMonth(),
        ];
    }
}
