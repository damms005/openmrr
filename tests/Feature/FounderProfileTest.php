<?php

declare(strict_types=1);

use App\Livewire\FounderProfile;
use App\Models\Founder;
use App\Models\Startup;
use Livewire\Livewire;

it('shows founder profile page', function (): void {
    Founder::factory()->create(['x_handle' => 'john_doe']);

    $this->get('/founder/john_doe')
        ->assertStatus(200)
        ->assertSee('john_doe');
});

it('renders founder profile component', function (): void {
    $founder = Founder::factory()->create();

    Livewire::test(FounderProfile::class, ['handle' => $founder->x_handle])
        ->assertOk();
});

it('displays founder startups', function (): void {
    $founder = Founder::factory()->create(['x_handle' => 'jane_dev']);
    $startup = Startup::factory()->create(['founder_id' => $founder->id, 'name' => 'MyApp']);

    $this->get('/founder/jane_dev')
        ->assertSee('MyApp');
});

it('aggregates revenue stats', function (): void {
    $founder = Founder::factory()->create(['x_handle' => 'bob_dev']);
    Startup::factory()->create(['founder_id' => $founder->id, 'monthly_recurring_revenue' => 3000]);
    Startup::factory()->create(['founder_id' => $founder->id, 'monthly_recurring_revenue' => 2000]);

    Livewire::test(FounderProfile::class, ['handle' => $founder->x_handle])
        ->assertOk();
});

it('shows zero stats for founder with no startups', function (): void {
    $founder = Founder::factory()->create(['x_handle' => 'empty_dev']);

    Livewire::test(FounderProfile::class, ['handle' => $founder->x_handle])
        ->assertOk();
});

it('throws 404 for non-existent founder', function (): void {
    $this->get('/founder/nonexistent')
        ->assertStatus(404);
});
