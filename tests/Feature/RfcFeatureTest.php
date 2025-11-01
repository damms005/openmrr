<?php

declare(strict_types=1);

use App\Data\CustomerData;
use App\Livewire\RfcInitiation;
use App\Livewire\RfcResponseForm;
use App\Models\Rfc;
use App\Models\Startup;
use Livewire\Livewire;

it('can load rfc initiation page', function () {
    $startup = Startup::factory()->create();

    $response = $this->get(route('rfc.initiation', $startup->slug));

    $response->assertSeeLivewire(RfcInitiation::class);
});

it('can submit rfc response via form', function () {
    $rfc = Rfc::factory()->create(['response' => null]);

    Livewire::test(RfcResponseForm::class, ['rfc' => $rfc])
        ->set('data.response', 'This product is absolutely amazing and transformed our workflow!')
        ->call('submitResponse')
        ->assertSet('isSubmitted', true);

    expect($rfc->fresh()->response)->toBe('This product is absolutely amazing and transformed our workflow!');
});

it('validates rfc response inputs', function () {
    $rfc = Rfc::factory()->create();

    Livewire::test(RfcResponseForm::class, ['rfc' => $rfc])
        ->set('data.response', 'short')
        ->call('submitResponse')
        ->assertHasErrors();
});

it('can create rfc from customer data', function () {
    $startup = Startup::factory()->create();
    $customer = new CustomerData(
        id: 'cust_123',
        name: 'Jane Smith',
        email: 'jane@example.com',
    );

    $rfc = Rfc::factory()->create([
        'startup_id' => $startup->id,
        'customer_name' => $customer->name,
        'customer_email' => $customer->email,
    ]);

    expect($rfc->customer_name)->toBe('Jane Smith');
    expect($rfc->customer_email)->toBe('jane@example.com');
    expect($rfc->response)->toBeNull();
});

it('can route rfc by uuid', function () {
    $rfc = Rfc::factory()->create();

    $route = route('rfc.respond', $rfc->uuid);
    expect($route)->toContain((string) $rfc->uuid);
});

it('generates unique uuid for each rfc', function () {
    $rfc1 = Rfc::factory()->create();
    $rfc2 = Rfc::factory()->create();

    expect($rfc1->uuid)->not->toBe($rfc2->uuid);
});

it('handles polar service customer fetching', function () {
    $startup = Startup::factory()->create(['account_type' => 'polar']);

    $response = $this->get(route('rfc.initiation', $startup->slug));

    $response->assertOk();
    $response->assertSeeLivewire(RfcInitiation::class);
});

it('handles stripe service customer fetching', function () {
    $startup = Startup::factory()->create(['account_type' => 'stripe']);

    $response = $this->get(route('rfc.initiation', $startup->slug));

    $response->assertOk();
    $response->assertSeeLivewire(RfcInitiation::class);
});
