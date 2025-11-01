<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class Advertiser extends Model
{
    /** @use HasFactory<\Database\Factories\AdvertiserFactory> */
    use HasFactory;

    protected $guarded = ['id'];

    public function casts(): array
    {
        return [
            'active_till' => 'datetime',
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('position', 'sidebar')
            ->where('active_till', '>=', now())
            ->inRandomOrder();
    }
}
