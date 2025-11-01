<?php

declare(strict_types=1);

use App\Console\Commands\SyncRevenueData;
use App\Models\Startup;
use App\Models\StartupGrossRevenue;
use App\Models\StartupMonthlyMrr;
use Illuminate\Support\Facades\Http;

it('monthly mrr maintains correct relationships', function (): void {
    $startup = Startup::factory()->create();
    $mrr = StartupMonthlyMrr::factory()
        ->create(['startup_id' => $startup->id]);

    expect($mrr->startup->id)->toBe($startup->id);
    expect($startup->monthlyMrrs()->count())->toBe(1);
});

it('gross revenue maintains correct relationships', function (): void {
    $startup = Startup::factory()->create();
    $revenue = StartupGrossRevenue::factory()
        ->create(['startup_id' => $startup->id]);

    expect($revenue->startup->id)->toBe($startup->id);
    expect($startup->grossRevenues()->count())->toBe(1);
});

it('syncs startups without errors', function (): void {
    Http::fake([
        'api.polar.sh/v1/subscriptions*' => Http::response([
            'items' => [
                [
                    'id' => 'sub_001',
                    'created_at' => now()->toIso8601String(),
                    'status' => 'active',
                    'recurring_interval' => 'month',
                    'amount' => 500000,
                    'quantity' => 1,
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
            'pagination' => ['total_count' => 5],
        ]),
    ]);

    Startup::factory()->create(['created_at' => now()->subHour()]);

    $this->artisan(SyncRevenueData::class)
        ->assertExitCode(0);
});

it('displays message when no startups to sync', function (): void {
    $this->artisan(SyncRevenueData::class)
        ->expectsOutput('No startups to sync.')
        ->assertExitCode(0);
});

it('creates monthly mrr records from factory', function (): void {
    $startup = Startup::factory()->create();

    foreach (range(0, 2) as $monthOffset) {
        StartupMonthlyMrr::factory()
            ->create([
                'startup_id' => $startup->id,
                'year_month' => now()->subMonths($monthOffset)->format('Y-m'),
            ]);
    }

    expect(StartupMonthlyMrr::where('startup_id', $startup->id)->count())->toBe(3);
});

it('creates gross revenue records from factory', function (): void {
    $startup = Startup::factory()->create();

    foreach (range(0, 2) as $monthOffset) {
        StartupGrossRevenue::factory()
            ->create([
                'startup_id' => $startup->id,
                'year_month' => now()->subMonths($monthOffset)->format('Y-m'),
            ]);
    }

    expect(StartupGrossRevenue::where('startup_id', $startup->id)->count())->toBe(3);
});

it('has proper column casting for monthly mrr', function (): void {
    $mrr = StartupMonthlyMrr::factory()->create([
        'monthly_recurring_revenue' => 5000,
    ]);

    expect($mrr->monthly_recurring_revenue)->toBeInt();
    expect($mrr->year_month)->toBeString();
});

it('has proper column casting for gross revenue', function (): void {
    $revenue = StartupGrossRevenue::factory()->create([
        'gross_revenue' => 50000,
    ]);

    expect($revenue->gross_revenue)->toBeInt();
    expect($revenue->year_month)->toBeString();
});
