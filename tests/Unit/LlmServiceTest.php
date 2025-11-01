<?php

declare(strict_types=1);

use App\Models\BusinessCategory;
use App\Models\Startup;
use App\Services\LlmService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

it('can be instantiated with config values', function () {
    config(['llm.openai.api_key' => 'test-key']);
    config(['llm.openai.model' => 'gpt-4o-mini']);

    $service = new LlmService();

    expect($service)->toBeInstanceOf(LlmService::class);
});

it('can categorize a startup when openai responds', function () {
    Http::fake([
        'https://api.openai.com/v1/chat/completions' => Http::response([
            'choices' => [
                [
                    'message' => [
                        'content' => '5812',
                    ],
                ],
            ],
        ], 200),
    ]);

    $startup = Startup::factory()->create();
    $restaurantCategory = BusinessCategory::where('code', '5812')->first();

    $service = new LlmService('test-key');
    $result = $service->getCategoryFor($startup);

    expect($result)->toEqual($restaurantCategory);
});

it('returns null when openai api fails', function () {
    Http::fake([
        'https://api.openai.com/v1/chat/completions' => Http::response([
            'error' => [
                'message' => 'Test OpenAI error',
                'type' => 'test_error',
                'code' => '123'
            ]
        ], 500),
    ]);

    Log::shouldReceive('error')
        ->once()
        ->with('Failed to categorize startup', \Mockery::on(function ($context) {
            return isset($context['startup_id'])
                && isset($context['startup_name'])
                && isset($context['error'])
                && str_contains($context['error'], 'Test OpenAI error');
        }));

    $startup = Startup::factory()->create();

    $service = new LlmService('test-key');
    $result = $service->getCategoryFor($startup);

    expect($result)->toBeNull();
});

it('returns null when category code does not exist', function () {
    Http::fake([
        'https://api.openai.com/v1/chat/completions' => Http::response([
            'choices' => [
                [
                    'message' => [
                        'content' => '9999',
                    ],
                ],
            ],
        ], 200),
    ]);

    $startup = Startup::factory()->create();

    $service = new LlmService('test-key');
    $result = $service->getCategoryFor($startup);

    expect($result)->toBeNull();
});
