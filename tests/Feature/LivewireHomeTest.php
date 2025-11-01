<?php

declare(strict_types=1);

use App\Livewire\Home;
use App\Models\Founder;
use App\Models\Startup;
use Livewire\Livewire;

it('renders home page', function (): void {
    $response = $this->get('/');

    $response->assertStatus(200);
});

it('displays revenue table', function (): void {
    Startup::factory()->count(3)->create();

    Livewire::test(Home::class)
        ->assertSeeLivewire('revenue-table');
});

it('shows startup data in table', function (): void {
    $founder = Founder::factory()->create();
    $startup = Startup::factory()->create([
        'founder_id' => $founder->id,
        'name' => 'TestStartup',
        'monthly_recurring_revenue' => 5000,
    ]);

    $this->get(route('home'))
        ->assertSee('TestStartup');
});

it('has livewire layout', function (): void {
    $response = $this->get('/');

    $response->assertSee('OpenMRR');
});
