<?php

declare(strict_types=1);

use App\Livewire\StartupDetail;
use App\Livewire\VerifiedCommentsTable;
use App\Models\Founder;
use App\Models\Rfc;
use App\Models\Startup;
use Livewire\Livewire;

it('shows startup detail page', function (): void {
    $founder = Founder::factory()->create();
    Startup::factory()->create([
        'founder_id' => $founder->id,
        'name' => 'TestApp',
        'slug' => 'testapp',
    ]);

    $this->get('/startup/testapp')
        ->assertStatus(200)
        ->assertSee('TestApp');
});

it('renders startup detail component', function (): void {
    $founder = Founder::factory()->create();
    $startup = Startup::factory()->create(['founder_id' => $founder->id]);

    Livewire::test(StartupDetail::class, ['slug' => $startup->slug])
        ->assertOk();
});

it('displays revenue information', function (): void {
    $founder = Founder::factory()->create();
    Startup::factory()->create([
        'founder_id' => $founder->id,
        'slug' => 'myapp',
        'total_revenue' => 42000,
        'monthly_recurring_revenue' => 3500,
        'subscriber_count' => 250,
    ]);

    $response = $this->get('/startup/myapp');
    $response->assertStatus(200);
});

it('shows founder link', function (): void {
    $founder = Founder::factory()->create(['x_handle' => 'dev_pro']);
    Startup::factory()->create([
        'founder_id' => $founder->id,
        'slug' => 'awesome-app',
    ]);

    $this->get('/startup/awesome-app')
        ->assertSee('dev_pro');
});

it('throws 404 for non-existent startup', function (): void {
    $this->get('/startup/nonexistent')
        ->assertStatus(404);
});

it('displays last synced timestamp', function (): void {
    $founder = Founder::factory()->create();
    Startup::factory()->create([
        'founder_id' => $founder->id,
        'slug' => 'sync-app',
        'last_synced_at' => now(),
    ]);

    $response = $this->get('/startup/sync-app');
    $response->assertOk();
});

it('loads startup detail with verified comments table', function (): void {
    $founder = Founder::factory()->create();
    $startup = Startup::factory()->create(['founder_id' => $founder->id]);
    Rfc::factory(3)->for($startup)->create(['response' => 'Great product!']);

    $response = $this->get('/startup/' . $startup->slug);

    $response->assertOk();
    $response->assertSeeLivewire(VerifiedCommentsTable::class);
});

it('displays verified comments in paginated table', function (): void {
    $founder = Founder::factory()->create();
    $startup = Startup::factory()->create(['founder_id' => $founder->id]);
    Rfc::factory(3)->for($startup)->create(['response' => 'Excellent!']);

    Livewire::test(VerifiedCommentsTable::class, ['startup' => $startup])
        ->set('showComments', true)
        ->assertCanSeeTableRecords(Rfc::where('startup_id', $startup->id)->get());
});

it('renders only verified comments without null responses', function (): void {
    $founder = Founder::factory()->create();
    $startup = Startup::factory()->create(['founder_id' => $founder->id]);
    Rfc::factory(2)->for($startup)->create(['response' => 'Great!']);
    Rfc::factory(2)->for($startup)->create(['response' => null]);

    Livewire::test(VerifiedCommentsTable::class, ['startup' => $startup])
        ->set('showComments', true)
        ->assertCountTableRecords(2);
});

it('paginates comments with default 10 items per page', function (): void {
    $founder = Founder::factory()->create();
    $startup = Startup::factory()->create(['founder_id' => $founder->id]);
    Rfc::factory(15)->for($startup)->create(['response' => 'Amazing!']);

    Livewire::test(VerifiedCommentsTable::class, ['startup' => $startup])
        ->set('showComments', true)
        ->assertCountTableRecords(15)
        ->call('gotoPage', 2)
        ->assertCountTableRecords(15);
});

it('requires api key to view comments', function (): void {
    $founder = Founder::factory()->create();
    $startup = Startup::factory()->create(['founder_id' => $founder->id]);
    Rfc::factory(3)->for($startup)->create(['response' => 'Great!']);

    Livewire::test(VerifiedCommentsTable::class, ['startup' => $startup])
        ->assertSet('showComments', false)
        ->assertSeeText('Unlock Comments');
});

it('verifies api key and shows comments', function (): void {
    $founder = Founder::factory()->create();
    $apiKey = 'test-api-key-123';
    $startup = Startup::factory()->create([
        'founder_id' => $founder->id,
        'encrypted_api_key' => encrypt($apiKey),
    ]);
    Rfc::factory(3)->for($startup)->create(['response' => 'Excellent!']);

    Livewire::test(VerifiedCommentsTable::class, ['startup' => $startup])
        ->callAction('viewComments', data: ['api_key' => $apiKey])
        ->assertSet('showComments', true);
});

it('rejects invalid api key', function (): void {
    $founder = Founder::factory()->create();
    $startup = Startup::factory()->create([
        'founder_id' => $founder->id,
        'encrypted_api_key' => encrypt('correct-key'),
    ]);

    Livewire::test(VerifiedCommentsTable::class, ['startup' => $startup])
        ->callAction('viewComments', data: ['api_key' => 'wrong-key'])
        ->assertSet('showComments', false);
});
