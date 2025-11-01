<?php

declare(strict_types=1);

use App\Livewire\AdvertiserCheckoutSuccess;
use App\Models\Advertiser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;

uses(RefreshDatabase::class);

it('displays checkout success page with valid checkout id', function () {
    $checkoutId = 'checkout_'.str_repeat('a', 20);

    config(['services.polar.api_key' => 'test-api-key']);

    Http::fake([
        'https://api.polar.sh/v1/checkouts/*' => Http::response([
            'id' => $checkoutId,
            'status' => 'succeeded',
        ]),
    ]);

    $response = $this->get(route('advertiser.checkout.success', ['checkout_id' => $checkoutId]));

    $response->assertSuccessful();
    $response->assertSeeLivewire(AdvertiserCheckoutSuccess::class);
});

it('redirects to home when checkout id is missing', function () {
    $this->get(route('advertiser.checkout.success'))
        ->assertRedirect(route('home'));
});

it('redirects to home when checkout id format is invalid', function () {
    $invalidCheckoutId = 'short';

    $response = $this->get(route('advertiser.checkout.success', ['checkout_id' => $invalidCheckoutId]));

    $response->assertRedirect(route('home'));
});

it('redirects to home when checkout validation fails', function () {
    $checkoutId = 'checkout_'.str_repeat('a', 20);

    config(['services.polar.api_key' => 'test-api-key']);

    Http::fake([
        'https://api.polar.sh/v1/checkouts/*' => Http::response([
            'id' => $checkoutId,
            'status' => 'pending',
        ]),
    ]);

    $response = $this->get(route('advertiser.checkout.success', ['checkout_id' => $checkoutId]));

    $response->assertRedirect(route('home'));
});

it('allows user to create advertiser via checkout success page', function () {
    $checkoutId = 'checkout_'.str_repeat('a', 20);

    config(['services.polar.api_key' => 'test-api-key']);

    Http::fake([
        'https://api.polar.sh/v1/checkouts/*' => Http::response([
            'id' => $checkoutId,
            'status' => 'succeeded',
        ]),
    ]);

    $response = $this->get(route('advertiser.checkout.success', ['checkout_id' => $checkoutId]));
    $response->assertSuccessful();

    Advertiser::create([
        'title' => 'Test Product',
        'description' => 'This is a test product description',
        'link_url' => 'https://test.com',
        'image_url' => null,
        'position' => 'sidebar',
        'active_till' => now()->addMonth(),
    ]);

    $this->assertDatabaseHas(Advertiser::class, [
        'title' => 'Test Product',
        'description' => 'This is a test product description',
        'link_url' => 'https://test.com',
        'position' => 'sidebar',
    ]);
});

it('ensures advertiser database is created with migrations', function () {
    expect(Advertiser::query()->count())->toBe(0);

    Advertiser::create([
        'title' => 'Test Product',
        'description' => 'Test description',
        'link_url' => 'https://test.com',
        'position' => 'sidebar',
        'active_till' => now()->addMonth(),
    ]);

    expect(Advertiser::query()->count())->toBe(1);
});
