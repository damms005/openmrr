<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Startup;
use App\Services\LlmService;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

final class AssignCategory implements ShouldQueue
{
    use Queueable;

    public $tries = 1;

    public function __construct(
        public Startup $startup,
    ) {}

    public function handle(LlmService $llmService): void
    {
        $category = $llmService->getCategoryFor($this->startup);

        $this->startup->update([
            'business_category_id' => $category?->id,
        ]);
    }
}
