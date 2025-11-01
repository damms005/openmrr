<?php

declare(strict_types=1);

use App\Models\Founder;
use App\Models\Startup;

it('belongs to founder', function (): void {
    $founder = Founder::factory()->create();
    $startup = Startup::factory()->create(['founder_id' => $founder->id]);

    expect($startup->founder->id)->toBe($founder->id);
});

it('has proper casts', function (): void {
    $startup = Startup::factory()->create([
        'total_revenue' => 60606.00,
        'monthly_recurring_revenue' => 5050.50,
    ]);

    expect($startup->total_revenue)->toBeNumeric();
    expect($startup->monthly_recurring_revenue)->toBeNumeric();
});

it('has unique name and slug', function (): void {
    Startup::factory()->create(['name' => 'TestApp', 'slug' => 'testapp']);

    $duplicate = Startup::factory()->make(['name' => 'TestApp', 'slug' => 'testapp', 'founder_id' => Founder::factory()]);

    expect(fn() => $duplicate->save())->toThrow(Exception::class);
});

it('can be retrieved by slug', function (): void {
    $startup = Startup::factory()->create(['slug' => 'my-startup']);

    $retrieved = Startup::where('slug', 'my-startup')->first();

    expect($retrieved)->not->toBeNull();
    expect($retrieved->id)->toBe($startup->id);
});

it('has all required attributes', function (): void {
    $startup = Startup::factory()->create();

    expect($startup->name)->not->toBeNull();
    expect($startup->slug)->not->toBeNull();
    expect($startup->founder_id)->not->toBeNull();
    expect($startup->total_revenue)->not->toBeNull();
    expect($startup->monthly_recurring_revenue)->not->toBeNull();
    expect($startup->subscriber_count)->toBeInt();
});
