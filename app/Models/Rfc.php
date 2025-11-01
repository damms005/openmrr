<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class Rfc extends Model
{
    /** @use HasFactory<\Database\Factories\RfcFactory> */
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'uuid' => 'string',
    ];

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function startup(): BelongsTo
    {
        return $this->belongsTo(Startup::class);
    }
}
