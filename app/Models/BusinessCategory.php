<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class BusinessCategory extends Model
{
    protected $guarded = ['id'];

    public function startups(): HasMany
    {
        return $this->hasMany(Startup::class);
    }

    public function pageUrl(): Attribute
    {
        return Attribute::make(
            get: fn(): string => route('category.show', $this->slug),
        );
    }
}
