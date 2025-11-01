<?php

declare(strict_types=1);

use App\Models\Startup;
use App\Models\StartupGrossRevenue;
use App\Models\StartupMonthlyMrr;
use Illuminate\Support\Facades\Http;

it('runs sync command successfully', function (): void {
    $this->artisan('app:sync-revenue-data')
        ->assertExitCode(0);
});

it('displays message when no startups to sync', function (): void {
    $this->artisan('app:sync-revenue-data')
        ->expectsOutput('No startups to sync.')
        ->assertExitCode(0);
});

it('returns zero exit code', function (): void {
    $this->artisan('app:sync-revenue-data')
        ->assertExitCode(0);
});

it('syncs multi-month historical data on first sync', function (): void {
    $startup = Startup::factory()->create([
        'encrypted_api_key' => encrypt('polar_oat_test-api-key'),
        'last_synced_at' => now()->subHours(2),
        'last_processed_subscription_id' => null,
        'last_processed_order_id' => null,
    ]);

    Http::fake([
        'api.polar.sh/v1/subscriptions*' => Http::response([
            'items' => [
                [
                    'id' => 'sub_001',
                    'created_at' => now()->subMonths(3)->startOfMonth()->toIso8601String(),
                    'status' => 'active',
                    'recurring_interval' => 'month',
                    'amount' => 100000,
                ],
                [
                    'id' => 'sub_002',
                    'created_at' => now()->subMonths(2)->startOfMonth()->toIso8601String(),
                    'status' => 'active',
                    'recurring_interval' => 'month',
                    'amount' => 200000,
                ],
                [
                    'id' => 'sub_003',
                    'created_at' => now()->subMonths(1)->startOfMonth()->toIso8601String(),
                    'status' => 'active',
                    'recurring_interval' => 'month',
                    'amount' => 300000,
                ],
            ],
            'pagination' => ['max_page' => 1],
        ]),
        'api.polar.sh/v1/orders*' => Http::response([
            'items' => [
                [
                    'id' => 'order_001',
                    'created_at' => now()->subMonths(3)->startOfMonth()->toIso8601String(),
                    'total_amount' => 150000,
                ],
                [
                    'id' => 'order_002',
                    'created_at' => now()->subMonths(2)->startOfMonth()->toIso8601String(),
                    'total_amount' => 250000,
                ],
                [
                    'id' => 'order_003',
                    'created_at' => now()->subMonths(1)->startOfMonth()->toIso8601String(),
                    'total_amount' => 350000,
                ],
            ],
            'pagination' => ['max_page' => 1],
        ]),
        'api.polar.sh/v1/customers*' => Http::response([
            'pagination' => ['total_count' => 10],
        ]),
    ]);

    $this->artisan('app:sync-revenue-data')
        ->assertExitCode(0);

    $mrrRecords = StartupMonthlyMrr::where('startup_id', $startup->id)
        ->orderBy('year_month')
        ->get();

    $revenueRecords = StartupGrossRevenue::where('startup_id', $startup->id)
        ->orderBy('year_month')
        ->get();

    expect($mrrRecords)->toHaveCount(3);
    expect($revenueRecords)->toHaveCount(3);

    expect($mrrRecords[0]->monthly_recurring_revenue)->toBe(1000);
    expect($mrrRecords[1]->monthly_recurring_revenue)->toBe(2000);
    expect($mrrRecords[2]->monthly_recurring_revenue)->toBe(3000);

    expect($revenueRecords[0]->gross_revenue)->toBe(1500);
    expect($revenueRecords[1]->gross_revenue)->toBe(2500);
    expect($revenueRecords[2]->gross_revenue)->toBe(3500);
});

it('prevents duplicate data on re-sync of same month', function (): void {
    $pastMonth = now()->subMonths(1)->format('Y-m');
    $targetMonth = now()->format('Y-m');

    $startup = Startup::factory()->create([
        'encrypted_api_key' => encrypt('polar_oat_test-api-key'),
        'last_synced_at' => now()->subHours(2),
        'last_processed_subscription_id' => 'sub_001',
        'last_processed_order_id' => 'order_001',
    ]);

    StartupMonthlyMrr::create([
        'startup_id' => $startup->id,
        'year_month' => $pastMonth,
        'monthly_recurring_revenue' => 1000,
    ]);

    StartupGrossRevenue::create([
        'startup_id' => $startup->id,
        'year_month' => $pastMonth,
        'gross_revenue' => 1500,
    ]);

    Http::fake([
        'api.polar.sh/v1/subscriptions*' => Http::response([
            'items' => [
                [
                    'id' => 'sub_001',
                    'created_at' => now()->subMonths(1)->toIso8601String(),
                    'status' => 'active',
                    'recurring_interval' => 'month',
                    'amount' => 100000,
                ],
                [
                    'id' => 'sub_002',
                    'created_at' => now()->toIso8601String(),
                    'status' => 'active',
                    'recurring_interval' => 'month',
                    'amount' => 200000,
                ],
            ],
            'pagination' => ['max_page' => 1],
        ]),
        'api.polar.sh/v1/orders*' => Http::response([
            'items' => [
                [
                    'id' => 'order_001',
                    'created_at' => now()->subMonths(1)->toIso8601String(),
                    'total_amount' => 150000,
                ],
                [
                    'id' => 'order_002',
                    'created_at' => now()->toIso8601String(),
                    'total_amount' => 250000,
                ],
            ],
            'pagination' => ['max_page' => 1],
        ]),
        'api.polar.sh/v1/customers*' => Http::response([
            'pagination' => ['total_count' => 5],
        ]),
    ]);

    $this->artisan('app:sync-revenue-data')
        ->assertExitCode(0);

    $mrrRecords = StartupMonthlyMrr::where('startup_id', $startup->id)->get();
    $revenueRecords = StartupGrossRevenue::where('startup_id', $startup->id)->get();

    expect($mrrRecords)->toHaveCount(2);
    expect($revenueRecords)->toHaveCount(2);

    $currentMonthMrr = $mrrRecords->firstWhere('year_month', $targetMonth);
    expect($currentMonthMrr->monthly_recurring_revenue)->toBe(2000);

    $currentMonthRevenue = $revenueRecords->firstWhere('year_month', $targetMonth);
    expect($currentMonthRevenue->gross_revenue)->toBe(2500);
});

it('tracks pagination ids for resumption on next sync', function (): void {
    $startup = Startup::factory()->create([
        'encrypted_api_key' => encrypt('polar_oat_test-api-key'),
        'last_synced_at' => now()->subHours(2),
        'last_processed_subscription_id' => null,
        'last_processed_order_id' => null,
    ]);

    Http::fake([
        'api.polar.sh/v1/subscriptions*' => Http::response([
            'items' => [
                [
                    'id' => 'sub_001',
                    'created_at' => now()->subMonths(2)->toIso8601String(),
                    'status' => 'active',
                    'recurring_interval' => 'month',
                    'amount' => 100000,
                ],
                [
                    'id' => 'sub_002',
                    'created_at' => now()->subMonths(1)->toIso8601String(),
                    'status' => 'active',
                    'recurring_interval' => 'month',
                    'amount' => 200000,
                ],
            ],
            'pagination' => ['max_page' => 1],
        ]),
        'api.polar.sh/v1/orders*' => Http::response([
            'items' => [
                [
                    'id' => 'order_001',
                    'created_at' => now()->subMonths(2)->toIso8601String(),
                    'total_amount' => 150000,
                ],
                [
                    'id' => 'order_002',
                    'created_at' => now()->subMonths(1)->toIso8601String(),
                    'total_amount' => 250000,
                ],
            ],
            'pagination' => ['max_page' => 1],
        ]),
        'api.polar.sh/v1/customers*' => Http::response([
            'pagination' => ['total_count' => 8],
        ]),
    ]);

    $this->artisan('app:sync-revenue-data')
        ->assertExitCode(0);

    $startup->refresh();

    expect($startup->last_processed_subscription_id)->toBe('sub_002');
    expect($startup->last_processed_order_id)->toBe('order_002');
});

it('correctly aggregates cumulative mrr from multiple subscriptions in same month', function (): void {
    $startup = Startup::factory()->create([
        'encrypted_api_key' => encrypt('polar_oat_test-api-key'),
        'last_synced_at' => now()->subHours(2),
        'last_processed_subscription_id' => null,
        'last_processed_order_id' => null,
    ]);

    $targetMonth = now()->format('Y-m');

    Http::fake([
        'api.polar.sh/v1/subscriptions*' => Http::response([
            'items' => [
                [
                    'id' => 'sub_001',
                    'created_at' => now()->toIso8601String(),
                    'status' => 'active',
                    'recurring_interval' => 'month',
                    'amount' => 100000,
                ],
                [
                    'id' => 'sub_002',
                    'created_at' => now()->toIso8601String(),
                    'status' => 'active',
                    'recurring_interval' => 'month',
                    'amount' => 200000,
                ],
                [
                    'id' => 'sub_003',
                    'created_at' => now()->toIso8601String(),
                    'status' => 'active',
                    'recurring_interval' => 'month',
                    'amount' => 300000,
                ],
            ],
            'pagination' => ['max_page' => 1],
        ]),
        'api.polar.sh/v1/orders*' => Http::response([
            'items' => [
                [
                    'id' => 'order_001',
                    'created_at' => now()->toIso8601String(),
                    'total_amount' => 500000,
                ],
            ],
            'pagination' => ['max_page' => 1],
        ]),
        'api.polar.sh/v1/customers*' => Http::response([
            'pagination' => ['total_count' => 15],
        ]),
    ]);

    $this->artisan('app:sync-revenue-data')
        ->assertExitCode(0);

    $mrrRecord = StartupMonthlyMrr::where('startup_id', $startup->id)
        ->where('year_month', $targetMonth)
        ->first();

    expect($mrrRecord->monthly_recurring_revenue)->toBe(6000);
});

it('correctly converts yearly subscriptions to monthly mrr', function (): void {
    $startup = Startup::factory()->create([
        'encrypted_api_key' => encrypt('polar_oat_test-api-key'),
        'last_synced_at' => now()->subHours(2),
        'last_processed_subscription_id' => null,
        'last_processed_order_id' => null,
    ]);

    $targetMonth = now()->format('Y-m');

    Http::fake([
        'api.polar.sh/v1/subscriptions*' => Http::response([
            'items' => [
                [
                    'id' => 'sub_yearly',
                    'created_at' => now()->toIso8601String(),
                    'status' => 'active',
                    'recurring_interval' => 'year',
                    'amount' => 120000,
                ],
                [
                    'id' => 'sub_monthly',
                    'created_at' => now()->toIso8601String(),
                    'status' => 'active',
                    'recurring_interval' => 'month',
                    'amount' => 100000,
                ],
            ],
            'pagination' => ['max_page' => 1],
        ]),
        'api.polar.sh/v1/orders*' => Http::response([
            'items' => [],
            'pagination' => ['max_page' => 1],
        ]),
        'api.polar.sh/v1/customers*' => Http::response([
            'pagination' => ['total_count' => 3],
        ]),
    ]);

    $this->artisan('app:sync-revenue-data')
        ->assertExitCode(0);

    $mrrRecord = StartupMonthlyMrr::where('startup_id', $startup->id)
        ->where('year_month', $targetMonth)
        ->first();

    $expectedMrr = 1000 + (1200 / 12);
    expect($mrrRecord->monthly_recurring_revenue)->toBe((int) $expectedMrr);
});

it('ignores inactive subscriptions in mrr calculation', function (): void {
    $startup = Startup::factory()->create([
        'encrypted_api_key' => encrypt('polar_oat_test-api-key'),
        'last_synced_at' => now()->subHours(2),
        'last_processed_subscription_id' => null,
        'last_processed_order_id' => null,
    ]);

    $targetMonth = now()->format('Y-m');

    Http::fake([
        'api.polar.sh/v1/subscriptions*' => Http::response([
            'items' => [
                [
                    'id' => 'sub_active',
                    'created_at' => now()->toIso8601String(),
                    'status' => 'active',
                    'recurring_interval' => 'month',
                    'amount' => 100000,
                ],
                [
                    'id' => 'sub_inactive',
                    'created_at' => now()->toIso8601String(),
                    'status' => 'canceled',
                    'recurring_interval' => 'month',
                    'amount' => 500000,
                ],
            ],
            'pagination' => ['max_page' => 1],
        ]),
        'api.polar.sh/v1/orders*' => Http::response([
            'items' => [],
            'pagination' => ['max_page' => 1],
        ]),
        'api.polar.sh/v1/customers*' => Http::response([
            'pagination' => ['total_count' => 2],
        ]),
    ]);

    $this->artisan('app:sync-revenue-data')
        ->assertExitCode(0);

    $mrrRecord = StartupMonthlyMrr::where('startup_id', $startup->id)
        ->where('year_month', $targetMonth)
        ->first();

    expect($mrrRecord->monthly_recurring_revenue)->toBe(1000);
});
