<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Startup;
use Livewire\Component;

final class StartupCard extends Component
{
    public Startup $startup;

    public function getVerifiedCommentCount(): int
    {
        $latestRfc = $this->startup->rfcs()->latest()->first();

        return $latestRfc?->total_comments_count ?? 0;
    }

    public function render()
    {
        return view('livewire.startup-card', [
            'commentCount' => $this->getVerifiedCommentCount(),
        ]);
    }
}
