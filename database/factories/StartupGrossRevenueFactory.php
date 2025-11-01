<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Startup;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

final class StartupGrossRevenueFactory extends Factory
{
    public function definition(): array
    {
        $grossRevenue = $this->faker->numberBetween(5000, 100000);
        $monthOffset = $this->faker->numberBetween(-12, 0);
        $yearMonth = Carbon::now()->addMonths($monthOffset)->format('Y-m');

        return [
            'startup_id' => Startup::factory(),
            'year_month' => $yearMonth,
            'gross_revenue' => $grossRevenue,
        ];
    }
}
