<?php

declare(strict_types=1);

namespace App\Actions;

use App\Data\CustomerData;
use App\Models\Rfc;
use App\Models\Startup;
use App\Notifications\RfcRequestNotification;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Str;

final readonly class SendRfcNotification
{
    public function handle(Startup $startup, CustomerData $customer): Rfc
    {
        $rfc = Rfc::create([
            'startup_id' => $startup->id,
            'uuid' => Str::uuid(),
            'customer_name' => $customer->name,
            'customer_email' => $customer->email,
        ]);

        (new AnonymousNotifiable)
            ->route('mail', $customer->email)
            ->notify(new RfcRequestNotification($startup, $rfc));

        return $rfc;
    }
}
