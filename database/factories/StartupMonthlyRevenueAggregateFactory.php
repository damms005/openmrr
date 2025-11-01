<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Startup;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StartupMonthlyRevenueAggregate>
 */
final class StartupMonthlyRevenueAggregateFactory extends Factory
{
    public function definition(): array
    {
        $mrr = $this->faker->numberBetween(1000, 50000);
        $totalRevenue = $this->faker->numberBetween($mrr * 6, $mrr * 24);
        $monthOffset = $this->faker->numberBetween(-12, 0);
        $yearMonth = Carbon::now()->addMonths($monthOffset)->format('Y-m');

        return [
            'startup_id' => Startup::factory(),
            'year_month' => $yearMonth,
            'total_revenue' => $totalRevenue,
            'monthly_recurring_revenue' => $mrr,
            'subscriber_count' => $this->faker->numberBetween(10, 500),
        ];
    }
}
