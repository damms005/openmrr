<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\BusinessCategory;
use Illuminate\View\View;
use Livewire\Component;

final class Home extends Component
{
    public function render(): View
    {
        $topCategories = BusinessCategory::query()
            ->withCount('startups')
            ->orderByDesc('startups_count')
            ->limit(15)
            ->get();

        return view('livewire.home', [
            'topCategories' => $topCategories,
        ])->title('Home');
    }
}
