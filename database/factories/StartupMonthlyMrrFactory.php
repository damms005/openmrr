<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Startup;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

final class StartupMonthlyMrrFactory extends Factory
{
    public function definition(): array
    {
        $mrr = $this->faker->numberBetween(1000, 50000);
        $monthOffset = $this->faker->numberBetween(-12, 0);
        $yearMonth = Carbon::now()->addMonths($monthOffset)->format('Y-m');

        return [
            'startup_id' => Startup::factory(),
            'year_month' => $yearMonth,
            'monthly_recurring_revenue' => $mrr,
        ];
    }
}
