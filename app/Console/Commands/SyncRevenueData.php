<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\AccountType;
use App\Models\Startup;
use App\Models\StartupGrossRevenue;
use App\Models\StartupMonthlyMrr;
use App\Services\PolarService;
use App\Services\StripeService;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

final class SyncRevenueData extends Command
{
    protected $signature = 'app:sync-revenue-data';

    protected $description = 'Sync revenue data';

    public function handle(PolarService $polarService, StripeService $stripeService): int
    {
        $startups = Startup::query()
            ->where('created_at', '<=', now()->subHour())
            ->orWhere('last_synced_at', '<=', now()->subHour())
            ->get();

        if ($startups->isEmpty()) {
            $this->info('No startups to sync.');

            return 0;
        }

        $this->info("Syncing {$startups->count()} startups...");
        $count = 0;
        $failedCount = 0;

        foreach ($startups as $startup) {
            try {
                $service = $startup->account_type === AccountType::STRIPE ? $stripeService : $polarService;
                $this->sync($startup, $service);
                $count++;
            } catch (Exception $e) {
                info("✗ Failed to sync {$startup->name}: {$e->getMessage()}");
                throw $e;
                $failedCount++;
            }
        }

        $this->info("Sync completed. Successful: {$count}, Failed: {$failedCount}");

        return 0;
    }

    private function sync(Startup $startup, PolarService|StripeService $service): void
    {
        $lastMrrMonth = StartupMonthlyMrr::where('startup_id', $startup->id)
            ->orderBy('year_month', 'desc')
            ->value('year_month');

        $lastGrossRevenueMonth = StartupGrossRevenue::where('startup_id', $startup->id)
            ->orderBy('year_month', 'desc')
            ->value('year_month');

        $polarData = $service->getPolarData(
            $startup->decrypted_api_key,
            $startup->last_processed_subscription_id,
            $startup->last_processed_order_id,
            $lastMrrMonth ?? $lastGrossRevenueMonth
        );

        if (empty($polarData->monthlyMrrAggregates)) {
            return;
        }

        DB::transaction(function () use ($startup, $polarData): void {

            foreach ($polarData->monthlyMrrAggregates as $yearMonth => $data) {
                StartupMonthlyMrr::upsert(
                    [
                        [
                            'startup_id' => $startup->id,
                            'year_month' => $yearMonth,
                            'monthly_recurring_revenue' => (int) $data['monthly_recurring_revenue'],
                            'created_at' => now(),
                            'updated_at' => now(),
                        ],
                    ],
                    ['startup_id', 'year_month'],
                    ['monthly_recurring_revenue', 'updated_at']
                );
            }

            if (! empty($polarData->monthlyGrossRevenueAggregates)) {
                foreach ($polarData->monthlyGrossRevenueAggregates as $yearMonth => $data) {
                    StartupGrossRevenue::upsert(
                        [
                            [
                                'startup_id' => $startup->id,
                                'year_month' => $yearMonth,
                                'gross_revenue' => (int) $data['gross_revenue'],
                                'created_at' => now(),
                                'updated_at' => now(),
                            ],
                        ],
                        ['startup_id', 'year_month'],
                        ['gross_revenue', 'updated_at']
                    );
                }
            }

            $startup->update([
                'monthly_recurring_revenue' => $polarData->monthlyRecurringRevenue,
                'total_revenue' => $polarData->totalRevenue,
                'subscriber_count' => $polarData->subscriberCount,
                'last_processed_subscription_id' => $polarData->lastProcessedSubscriptionId,
                'last_processed_order_id' => $polarData->lastProcessedOrderId,
                'last_synced_at' => now(),
            ]);
        });
    }
}
