<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Startup;
use App\Models\StartupGrossRevenue;
use App\Models\StartupMonthlyMrr;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Lottery;

final class StartupRevenueSnapshotSeeder extends Seeder
{
    public function run(): void
    {
        $startups = Startup::all();
        $skipCount = 2;
        $totalCount = $startups->count();

        foreach ($startups as $index => $startup) {
            $shouldSkip = $index >= $totalCount - $skipCount;

            if ($shouldSkip) {
                $this->wipeStartupRevenue($startup);
            } else {
                $this->generateRevenueHistory($startup);
            }
        }
    }

    private function wipeStartupRevenue(Startup $startup): void
    {
        $startup->update([
            'total_revenue' => 0,
            'monthly_recurring_revenue' => 0,
        ]);
    }

    private function generateRevenueHistory(Startup $startup): void
    {
        $faker = Faker::create();
        $hasSubscriptionBusiness = true;

        Lottery::odds(30, 100)
            ->winner(fn() => $hasSubscriptionBusiness = false)
            ->choose();

        $currentGrossRevenue = (int) $startup->total_revenue;
        $currentMrr = (int) $startup->monthly_recurring_revenue;

        for ($i = 0; $i < 12; $i++) {
            $monthOffset = -11 + $i;
            $yearMonth = now()->addMonths($monthOffset)->format('Y-m');

            $variance = $faker->numberBetween(-10, 15) / 100;
            $grossRevenue = max(1000, (int) ($currentGrossRevenue + ($currentGrossRevenue * $variance)));

            if ($hasSubscriptionBusiness) {
                $mrr = max(100, (int) ($currentMrr + ($currentMrr * $variance)));

                StartupMonthlyMrr::create([
                    'startup_id' => $startup->id,
                    'year_month' => $yearMonth,
                    'monthly_recurring_revenue' => $mrr,
                ]);
            }

            StartupGrossRevenue::create([
                'startup_id' => $startup->id,
                'year_month' => $yearMonth,
                'gross_revenue' => $grossRevenue,
            ]);
        }
    }
}
