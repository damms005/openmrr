<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\BusinessCategory;
use App\Models\Startup;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

final class LlmService
{
    private const string OPENAI_API_URL = 'https://api.openai.com/v1/chat/completions';

    private ?string $apiKey;

    private ?string $model;

    public function __construct(
        ?string $apiKey = null,
        ?string $model = null,
    ) {
        $this->apiKey = $apiKey ?? config('llm.openai.api_key', '');
        $this->model = $model ?? config('llm.openai.model', 'gpt-4o-mini');
    }

    public function getCategoryFor(Startup $startup): ?BusinessCategory
    {
        try {
            $prompt = $this->buildCategoryPrompt($startup);
            $response = $this->callOpenAi($prompt);

            if (! $response) {
                return null;
            }

            return $this->parseCategoryResponse($response);
        } catch (Exception $e) {
            Log::error('Failed to categorize startup', [
                'startup_id' => $startup->id,
                'startup_name' => $startup->name,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    private function buildCategoryPrompt(Startup $startup): string
    {
        $categories = BusinessCategory::pluck('label', 'code')->toArray();
        $categoryList = collect($categories)
            ->map(fn($label, $code) => "- {$code}: {$label}")
            ->join("\n");

        return <<<PROMPT
Based on the following startup information, categorize it with the most appropriate Stripe Merchant Category Code (MCC).

Startup Name: {$startup->name}
Description: {$startup->description}
Website: {$startup->website_url}

Available Categories:
{$categoryList}

Respond ONLY with the MCC code (e.g., 5812, 7372, etc.) that best matches this startup's business category. Do not include any explanation or additional text.
PROMPT;
    }

    private function callOpenAi(string $prompt): ?string
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
        ])->post(self::OPENAI_API_URL, [
            'model' => $this->model,
            'messages' => [
                [
                    'role' => 'user',
                    'content' => $prompt,
                ],
            ],
            'temperature' => 0.3,
            'max_tokens' => 10,
        ]);

        if ($response->failed()) {
            $responseData = $response->json();
            $errorMessage = $responseData['error']['message'] ?? "OpenAI API call failed with status {$response->status()}";
            throw new Exception($errorMessage);
        }

        $content = $response->json('choices.0.message.content');

        return is_string($content) ? mb_trim($content) : null;
    }

    private function parseCategoryResponse(string $mccCode): ?BusinessCategory
    {
        $mccCode = mb_trim($mccCode);

        return BusinessCategory::where('code', $mccCode)->first();
    }
}
