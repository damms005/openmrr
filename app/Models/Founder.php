<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

final class Founder extends Model
{
    /** @use HasFactory<\Database\Factories\FounderFactory> */
    use HasFactory;

    protected $guarded = ['id'];

    public function startups(): HasMany
    {
        return $this->hasMany(Startup::class);
    }

    public function disaplayedHandle(): Attribute
    {
        return Attribute::make(
            get: fn(): string => "@{$this->x_handle}",
        );
    }
}
