<?php

declare(strict_types=1);

use App\Actions\CreateStartup;
use App\Enums\AccountType;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;

beforeEach(function () {
    Queue::fake();
});

it('creates stripe startup from stripe api key', function (): void {
    $apiKey = 'sk_test_123456789';
    $xHandle = 'stripeuser';

    Http::fake([
        'api.stripe.com/v1/accounts*' => Http::response([
            'business_profile' => [
                'name' => 'Stripe Test Business',
                'url' => 'https://stripebusiness.com',
                'product_description' => 'A test business using Stripe',
            ],
            'created' => now()->subYear()->timestamp,
        ]),
        'api.stripe.com/v1/subscriptions*' => Http::response([
            'data' => [
                [
                    'id' => 'sub_stripe_001',
                    'created' => now()->timestamp,
                    'status' => 'active',
                    'items' => [
                        'data' => [
                            [
                                'price' => [
                                    'unit_amount' => 100000,
                                    'recurring' => ['interval' => 'month'],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'has_more' => false,
        ]),
        'api.stripe.com/v1/charges*' => Http::response([
            'data' => [
                [
                    'id' => 'ch_stripe_001',
                    'created' => now()->timestamp,
                    'amount' => 100000,
                    'paid' => true,
                ],
            ],
            'has_more' => false,
        ]),
        'api.stripe.com/v1/customers*' => Http::response([
            'total_count' => 5,
        ]),
    ]);

    $action = app(CreateStartup::class);
    $startup = $action->handle($apiKey, $xHandle);

    expect($startup->name)->toBe('Stripe Test Business');
    expect($startup->account_type)->toBe(AccountType::STRIPE);
    expect($startup->description)->toBe('A test business using Stripe');
    expect($startup->website_url)->toBe('https://stripebusiness.com');
});

it('detects polar account type for polar api keys', function (): void {
    $apiKey = 'polar_oat_123456789';

    Http::fake([
        'api.polar.sh/v1/organizations*' => Http::response([
            'items' => [
                [
                    'name' => 'Polar Test Business',
                    'website' => 'https://polarbusiness.com',
                    'avatar_url' => null,
                    'created_at' => '2023-01-15T10:00:00Z',
                ],
            ],
        ]),
        'api.polar.sh/v1/subscriptions*' => Http::response([
            'items' => [],
            'pagination' => ['max_page' => 1],
        ]),
        'api.polar.sh/v1/orders*' => Http::response([
            'items' => [],
            'pagination' => ['max_page' => 1],
        ]),
        'api.polar.sh/v1/customers*' => Http::response([
            'pagination' => ['total_count' => 0],
        ]),
    ]);

    $action = app(CreateStartup::class);
    $startup = $action->handle($apiKey);

    expect($startup->account_type)->toBe(AccountType::POLAR);
});

it('skips llm category assignment for stripe businesses', function (): void {
    $apiKey = 'sk_test_no_llm';

    Http::fake([
        'api.stripe.com/v1/accounts*' => Http::response([
            'business_profile' => [
                'name' => 'No LLM Business',
                'url' => 'https://nollm.com',
                'product_description' => 'Business without LLM categorization',
            ],
            'created' => now()->subYear()->timestamp,
        ]),
        'api.stripe.com/v1/subscriptions*' => Http::response([
            'data' => [],
            'has_more' => false,
        ]),
        'api.stripe.com/v1/charges*' => Http::response([
            'data' => [],
            'has_more' => false,
        ]),
        'api.stripe.com/v1/customers*' => Http::response([
            'total_count' => 0,
        ]),
    ]);

    $action = app(CreateStartup::class);
    $startup = $action->handle($apiKey);

    expect($startup->account_type)->toBe(AccountType::STRIPE);
    expect($startup->business_category_id)->toBeNull();
});
