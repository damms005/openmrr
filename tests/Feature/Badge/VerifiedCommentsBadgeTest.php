<?php

declare(strict_types=1);

use App\Models\Rfc;
use App\Models\Startup;

it('displays badge with zero count when startup has no responses', function () {
    $html = view('components.verified-comments-badge', ['commentCount' => 0])->render();

    expect($html)->toContain('0');
    expect($html)->toContain('text-gray-500 dark:text-gray-600');
    expect($html)->toContain('text-gray-200 dark:text-gray-700');
});

it('displays badge with count when startup has responses', function () {
    $html = view('components.verified-comments-badge', ['commentCount' => 5])->render();

    expect($html)->toContain('5');
    expect($html)->toContain('text-green-800 dark:text-green-500');
    expect($html)->toContain('text-green-300 dark:text-green-300');
});

it('abbreviates large comment counts', function () {
    $html = view('components.verified-comments-badge', ['commentCount' => 1234])->render();

    expect($html)->toContain('1K');
});

it('displays badge on revenue table', function () {
    $startup = Startup::factory()->create();
    Rfc::factory(3)->for($startup)->create(['response' => 'Excellent!']);

    $response = $this->get(route('home'));

    $response->assertSee('Comments from verified RFCs');
});

it('displays green shield icon for startup with comments', function () {
    $html = view('components.verified-comments-badge', ['commentCount' => 1])->render();

    expect($html)->toContain('<svg');
    expect($html)->toContain('viewBox="0 0 24 24"');
    expect($html)->toContain('text-green-300 dark:text-green-300');
});

it('displays grey shield icon for startup without comments', function () {
    $html = view('components.verified-comments-badge', ['commentCount' => 0])->render();

    expect($html)->toContain('<svg');
    expect($html)->toContain('viewBox="0 0 24 24"');
    expect($html)->toContain('text-gray-200 dark:text-gray-700');
});
