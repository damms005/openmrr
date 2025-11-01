<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\BusinessCategory;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

final class CategoryListing extends Component
{
    use WithPagination;

    public BusinessCategory $category;

    #[Computed]
    public function startups()
    {
        return $this->category->startups()
            ->orderByDesc('rank')
            ->paginate(15);
    }

    #[Computed]
    public function allCategories()
    {
        return BusinessCategory::query()
            ->orderBy('label')
            ->get();
    }

    public function render()
    {
        return view('livewire.category-listing', [
            'startups' => $this->startups,
            'allCategories' => $this->allCategories,
        ]);
    }
}
