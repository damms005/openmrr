<?php

declare(strict_types=1);

use App\Models\Founder;
use App\Models\Startup;
use App\Models\StartupGrossRevenue;
use Illuminate\Database\Eloquent\Factories\Sequence;

it('displays founder profile page correctly', function (): void {
    $founder = Founder::factory()->create(['x_handle' => 'johndoe']);

    $startups = Startup::factory()
        ->count(2)
        ->state(new Sequence(
            [
                'name' => 'Product Alpha',
                'slug' => 'product-alpha',
                'description' => 'The first amazing product',
                'monthly_recurring_revenue' => 5000,
            ],
            [
                'name' => 'Product Beta',
                'slug' => 'product-beta',
                'description' => 'The second amazing product',
                'monthly_recurring_revenue' => 3000,
            ],
        ))
        ->create(['founder_id' => $founder->id]);

    StartupGrossRevenue::factory()
        ->count(2)
        ->state(new Sequence(
            [
                'year_month' => now()->subMonth()->format('Y-m'),
                'gross_revenue' => 60000,
            ],
            [
                'year_month' => now()->format('Y-m'),
                'gross_revenue' => 75000,
            ],
        ))
        ->create(['startup_id' => $startups->first()->id]);

    $page = visit(route('founder.show', $founder->x_handle));

    $page->assertSee('johndoe')
        ->assertSee('2 verified startups')
        ->assertSee('Product Alpha')
        ->assertSee('Product Beta')
        ->assertSee('MRR')
        ->assertNoJavaScriptErrors();
});
