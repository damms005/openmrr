<?php

declare(strict_types=1);

namespace App\View\Components;

use App\Services\GitHubService;
use Illuminate\View\Component;
use Illuminate\View\View;

final class GitHubStar extends Component
{
    public int $starCount;

    public function __construct(
        private readonly GitHubService $gitHubService,
        public string $owner = 'damms005',
        public string $repo = 'openmrr'
    ) {
        $this->starCount = $this->gitHubService->getStarCount($this->owner, $this->repo);
    }

    public function render(): View
    {
        return view('components.github-star');
    }
}
