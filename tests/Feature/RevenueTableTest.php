<?php

declare(strict_types=1);

use App\Livewire\RevenueTable;
use App\Models\Startup;
use Livewire\Livewire;

it('renders table with startups', function (): void {
    Startup::factory()->create([
        'name' => 'TopRevenue Co',
        'total_revenue' => 100000,
        'monthly_recurring_revenue' => 8000,
        'rank' => 1,
    ]);

    Startup::factory()->create([
        'name' => 'MidRevenue Inc',
        'total_revenue' => 50000,
        'monthly_recurring_revenue' => 4000,
        'rank' => 2,
    ]);

    Startup::factory()->create([
        'name' => 'SmallRevenue LLC',
        'total_revenue' => 25000,
        'monthly_recurring_revenue' => 2000,
        'rank' => 3,
    ]);

    Livewire::test(RevenueTable::class)
        ->assertOk()
        ->assertSee('TopRevenue Co')
        ->assertSee('MidRevenue Inc')
        ->assertSee('SmallRevenue LLC')
        ->assertSee('$100,000')
        ->assertSee('$50,000')
        ->assertSee('$25,000')
        ->assertSee('🥇')
        ->assertSee('🥈')
        ->assertSee('🥉');
});
