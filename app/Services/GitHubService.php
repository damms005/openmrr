<?php

declare(strict_types=1);

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

final readonly class GitHubService
{
    private const string BASE_URL = 'https://api.github.com';

    /**
     * @return array<string, mixed>
     */
    public function getRepositoryData(string $owner, string $repo): array
    {
        if ($owner === '' || $repo === '') {
            return [];
        }

        $cacheKey = "github_repo_{$owner}_{$repo}";

        return Cache::remember($cacheKey, now()->addMinutes(10), function () use ($owner, $repo) {
            $response = Http::withHeaders([
                'Accept' => 'application/vnd.github.v3+json',
                'User-Agent' => 'OpenMRR-App',
            ])->get("{$this->baseUrl()}/repos/{$owner}/{$repo}");

            if ($response->failed()) {
                return [
                    'stargazers_count' => 1,
                ];
            }

            return $response->json();
        });
    }

    public function getStarCount(string $owner, string $repo): int
    {
        $data = $this->getRepositoryData($owner, $repo);

        return (int) ($data['stargazers_count']);
    }

    private function baseUrl(): string
    {
        return self::BASE_URL;
    }
}
