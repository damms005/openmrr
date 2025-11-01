<?php

declare(strict_types=1);

use App\Actions\CreateStartup;
use App\Livewire\SearchAndCreate;
use App\Models\Founder;
use App\Models\Startup;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Livewire\Livewire;

beforeEach(function () {
    Queue::fake();
});

it('renders search and create component', function (): void {
    Livewire::test(SearchAndCreate::class)
        ->assertStatus(200);
});

it('displays startup options in search dropdown', function (): void {
    Startup::factory()->create(['name' => 'TestApp']);

    Livewire::test(SearchAndCreate::class)
        ->assertSee('TestApp');
});

it('redirects to startup detail when option selected', function (): void {
    $startup = Startup::factory()->create(['name' => 'MyStartup', 'slug' => 'my-startup']);

    Livewire::test(SearchAndCreate::class)
        ->set('data.startup_id', $startup->id)
        ->assertRedirect(route('startup.show', $startup->slug));
});

it('handles polar api errors gracefully', function (): void {
    Http::fake([
        'api.polar.sh/v1/organizations' => Http::response([
            'detail' => [
                [
                    'loc' => ['header', 'authorization'],
                    'msg' => 'Test polar request error',
                    'type' => 'test_error'
                ]
            ]
        ], 401),
    ]);

    Livewire::test(SearchAndCreate::class)
        ->callAction('createStartup', data: [
            'polar_api_key' => 'invalid-key',
            'x_handle' => 'testuser',
        ])
        ->assertNotified();

    expect(Startup::count())->toBe(0);
});

it('requires polar api key field', function (): void {
    Http::fake();

    Livewire::test(SearchAndCreate::class)
        ->callAction('createStartup', data: [
            'polar_api_key' => '',
            'x_handle' => 'testuser',
        ]);

    expect(Startup::count())->toBe(0);
});

it('creates startup from polar api key and x handle', function (): void {
    $apiKey = 'polar_oat_test-api-key-123';
    $xHandle = 'testuserhandle';

    Http::fake([
        'api.polar.sh/v1/organizations*' => Http::response([
            'items' => [
                [
                    'name' => 'TestApp Inc',
                    'website' => 'https://testapp.com',
                    'avatar_url' => 'https://example.com/avatar.jpg',
                    'created_at' => '2023-01-15T10:00:00Z',
                ],
            ],
        ]),
        'api.polar.sh/v1/subscriptions*' => Http::response([
            'items' => [
                [
                    'id' => 'sub_001',
                    'created_at' => now()->toIso8601String(),
                    'status' => 'active',
                    'recurring_interval' => 'month',
                    'amount' => 500000,
                    'quantity' => 1,
                ],
            ],
            'pagination' => ['max_page' => 1],
        ]),
        'api.polar.sh/v1/orders*' => Http::response([
            'items' => [
                [
                    'id' => 'order_001',
                    'created_at' => now()->toIso8601String(),
                    'total_amount' => 500000,
                ],
            ],
            'pagination' => ['max_page' => 1],
        ]),
        'api.polar.sh/v1/customers*' => Http::response([
            'pagination' => ['total_count' => 5],
        ]),
    ]);

    $action = app(CreateStartup::class);
    $startup = $action->handle($apiKey, $xHandle);

    expect($startup->name)->toBe('TestApp Inc');
    expect((string) $startup->slug)->toBe('testapp-inc');
    expect($startup->business_created_at)->not()->toBeNull();
});

it('creates founder with correct data from polar organization details', function (): void {
    $apiKey = 'polar_oat_test-api-key-123';
    $xHandle = 'founderhandle';
    $organizationName = 'Amazing Startup Co';

    Http::fake([
        'api.polar.sh/v1/organizations*' => Http::response([
            'items' => [
                [
                    'name' => $organizationName,
                    'website' => 'https://amazing.com',
                    'avatar_url' => 'https://example.com/avatar.jpg',
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
    $startup = $action->handle($apiKey, $xHandle);

    $founder = $startup->founder;
    expect($founder->x_handle)->toBe($xHandle);
});

it('creates startup with correct founder relationship', function (): void {
    $apiKey = 'polar_oat_test-api-key-123';
    $xHandle = 'relationshiptest';

    Http::fake([
        'api.polar.sh/v1/organizations*' => Http::response([
            'items' => [
                [
                    'name' => 'RelatedApp',
                    'website' => 'https://related.com',
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
            'pagination' => ['total_count' => 3],
        ]),
    ]);

    $action = app(CreateStartup::class);
    $startup = $action->handle($apiKey, $xHandle);

    $founder = Founder::where('x_handle', $xHandle)->first();
    expect($startup->founder_id)->toBe($founder->id);
    expect($startup->founder->x_handle)->toBe($xHandle);
});

it('calculates revenue metrics correctly', function (): void {
    $apiKey = 'polar_oat_test-api-key-123';
    $xHandle = 'metricstest';
    $monthlyAmount = 5000;
    $yearlyAmount = 60000;

    Http::fake([
        'api.polar.sh/v1/organizations*' => Http::response([
            'items' => [
                [
                    'name' => 'MetricsApp',
                    'website' => 'https://metrics.com',
                    'avatar_url' => null,
                    'created_at' => '2023-01-15T10:00:00Z',
                ],
            ],
        ]),
        'api.polar.sh/v1/subscriptions*' => Http::response([
            'items' => [
                [
                    'id' => 'sub_monthly',
                    'created_at' => now()->toIso8601String(),
                    'status' => 'active',
                    'recurring_interval' => 'month',
                    'amount' => ($monthlyAmount * 100),
                    'quantity' => 1,
                ],
                [
                    'id' => 'sub_yearly',
                    'created_at' => now()->toIso8601String(),
                    'status' => 'active',
                    'recurring_interval' => 'year',
                    'amount' => ($yearlyAmount * 100),
                    'quantity' => 1,
                ],
            ],
            'pagination' => ['max_page' => 1],
        ]),
        'api.polar.sh/v1/orders*' => Http::response([
            'items' => [
                [
                    'id' => 'order_001',
                    'created_at' => now()->toIso8601String(),
                    'total_amount' => (($monthlyAmount + ($yearlyAmount / 12)) * 100),
                ],
            ],
            'pagination' => ['max_page' => 1],
        ]),
        'api.polar.sh/v1/customers*' => Http::response([
            'pagination' => ['total_count' => 10],
        ]),
    ]);

    $action = app(CreateStartup::class);
    $startup = $action->handle($apiKey, $xHandle);

    $expectedMrr = round($monthlyAmount + ($yearlyAmount / 12), 2);

    expect((float) $startup->monthly_recurring_revenue)->toBe($expectedMrr);
    expect($startup->subscriber_count)->toBe(10);
});

it('captures all historical monthly revenue data on initial creation', function (): void {
    $apiKey = 'polar_oat_test-api-key-historical';

    Http::fake([
        'api.polar.sh/v1/organizations*' => Http::response([
            'items' => [
                [
                    'name' => 'HistoricalApp',
                    'website' => 'https://historical.com',
                    'avatar_url' => null,
                    'created_at' => '2023-01-15T10:00:00Z',
                ],
            ],
        ]),
        'api.polar.sh/v1/subscriptions*' => Http::response([
            'items' => [
                [
                    'id' => 'sub_001',
                    'created_at' => now()->subMonths(3)->toIso8601String(),
                    'status' => 'active',
                    'recurring_interval' => 'month',
                    'amount' => 100000,
                ],
                [
                    'id' => 'sub_002',
                    'created_at' => now()->subMonths(2)->toIso8601String(),
                    'status' => 'active',
                    'recurring_interval' => 'month',
                    'amount' => 200000,
                ],
                [
                    'id' => 'sub_003',
                    'created_at' => now()->toIso8601String(),
                    'status' => 'active',
                    'recurring_interval' => 'month',
                    'amount' => 300000,
                ],
            ],
            'pagination' => ['max_page' => 1],
        ]),
        'api.polar.sh/v1/orders*' => Http::response([
            'items' => [
                [
                    'id' => 'order_001',
                    'created_at' => now()->subMonths(3)->toIso8601String(),
                    'total_amount' => 150000,
                ],
                [
                    'id' => 'order_002',
                    'created_at' => now()->subMonths(2)->toIso8601String(),
                    'total_amount' => 250000,
                ],
                [
                    'id' => 'order_003',
                    'created_at' => now()->toIso8601String(),
                    'total_amount' => 350000,
                ],
            ],
            'pagination' => ['max_page' => 1],
        ]),
        'api.polar.sh/v1/customers*' => Http::response([
            'pagination' => ['total_count' => 5],
        ]),
    ]);

    $action = app(CreateStartup::class);
    $startup = $action->handle($apiKey);

    expect((float) $startup->monthly_recurring_revenue)->toBeGreaterThan(0);
    expect((float) $startup->total_revenue)->toBeGreaterThan(0);
    expect($startup->monthly_recurring_revenue)->toEqual('3000.00');
    expect($startup->total_revenue)->toEqual('3500.00');
});
