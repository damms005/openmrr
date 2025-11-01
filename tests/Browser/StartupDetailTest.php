<?php

declare(strict_types=1);

use App\Models\Founder;
use App\Models\Startup;
use App\Models\StartupGrossRevenue;

it('displays startup detail page correctly', function (): void {
    $founder = Founder::factory()->create(['x_handle' => 'founder1']);

    $startup = Startup::factory()->create([
        'name' => 'Amazing SaaS',
        'slug' => 'amazing-saas',
        'founder_id' => $founder->id,
        'description' => 'The best SaaS application ever built',
        'total_revenue' => 144000,
        'monthly_recurring_revenue' => 12000,
        'subscriber_count' => 125,
    ]);

    StartupGrossRevenue::factory()->create([
        'startup_id' => $startup->id,
        'year_month' => now()->subMonths(2)->format('Y-m'),
        'gross_revenue' => 72000,
    ]);

    StartupGrossRevenue::factory()->create([
        'startup_id' => $startup->id,
        'year_month' => now()->subMonth()->format('Y-m'),
        'gross_revenue' => 108000,
    ]);

    StartupGrossRevenue::factory()->create([
        'startup_id' => $startup->id,
        'year_month' => now()->format('Y-m'),
        'gross_revenue' => 144000,
    ]);

    $page = visit(route('startup.show', $startup->slug));

    $page->assertSee('Amazing SaaS')
        ->assertSee('The best SaaS application ever built')
        ->assertSee('founder1')
        ->assertSee('Revenue')
        ->assertSee('All revenue data is verified')
        ->assertNoJavaScriptErrors();
});
