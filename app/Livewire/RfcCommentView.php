<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Rfc;
use Livewire\Component;

final class RfcCommentView extends Component
{
    public Rfc $rfc;

    public function render()
    {
        return view('livewire.rfc-comment-view');
    }
}
