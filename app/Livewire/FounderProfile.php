<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Founder;
use Livewire\Component;
use Illuminate\View\View;

final class FounderProfile extends Component
{
    public Founder $founder;

    public function mount(string $handle): void
    {
        $this->founder = Founder::where('x_handle', $handle)->firstOrFail();
    }

    public function render(): View
    {
        $startups = $this->founder->startups()->get();

        return view('livewire.founder-profile', [
            'startups' => $startups,
        ])->title($this->founder->x_handle);
    }
}
