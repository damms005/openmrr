<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Startup;
use Illuminate\View\View;
use Livewire\Component;

final class StartupSuggestions extends Component
{
    public Startup $currentStartup;

    public function mount(Startup $currentStartup): void
    {
        $this->currentStartup = $currentStartup;
    }

    public function render(): View
    {
        $suggestions = Startup::with('founder')
            ->where('id', '!=', $this->currentStartup->id)
            ->inRandomOrder()
            ->limit(3)
            ->get();

        return view('livewire.startup-suggestions', [
            'suggestions' => $suggestions,
        ]);
    }
}
