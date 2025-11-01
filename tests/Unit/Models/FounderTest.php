<?php

declare(strict_types=1);

use App\Models\Founder;
use App\Models\Startup;

it('has many startups', function (): void {
    $founder = Founder::factory()->has(Startup::factory()->count(3))->create();

    expect($founder->startups)->toHaveCount(3);
    expect($founder->startups->first())->toBeInstanceOf(Startup::class);
});

it('can be created with factory', function (): void {
    $founder = Founder::factory()->create();

    expect($founder)->toBeInstanceOf(Founder::class);
    expect($founder->x_handle)->not->toBeNull();
});

it('has unique x_handle', function (): void {
    $founder1 = Founder::factory()->create();
    $founder2 = Founder::factory()->make(['x_handle' => $founder1->x_handle]);

    expect(fn() => $founder2->save())->toThrow(Exception::class);
});

it('can create and retrieve founder', function (): void {
    Founder::factory()->create(['x_handle' => 'testuser']);

    $retrieved = Founder::where('x_handle', 'testuser')->first();

    expect($retrieved)->not->toBeNull();
});
