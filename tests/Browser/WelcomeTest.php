<?php

declare(strict_types=1);

use App\Models\Startup;
use App\Models\StartupGrossRevenue;

it('displays home page correctly', function (): void {
    $startup = Startup::factory()->create([
        'name' => 'Test Product',
        'total_revenue' => 60000,
        'monthly_recurring_revenue' => 5000,
        'subscriber_count' => 100,
    ]);

    StartupGrossRevenue::factory()->create([
        'startup_id' => $startup->id,
        'year_month' => now()->subMonth()->format('Y-m'),
        'gross_revenue' => 54000,
    ]);

    StartupGrossRevenue::factory()->create([
        'startup_id' => $startup->id,
        'year_month' => now()->format('Y-m'),
        'gross_revenue' => 60000,
    ]);

    $page = visit(route('home'));

    $page->assertSee('OpenMRR')
        ->assertSee('Openly verifiable database of startup revenue')
        ->assertNoJavaScriptErrors();
});
