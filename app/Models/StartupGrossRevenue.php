<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class StartupGrossRevenue extends Model
{
    /** @use HasFactory<\Database\Factories\StartupGrossRevenueFactory> */
    use HasFactory;

    protected $table = 'startup_gross_revenues';

    protected $guarded = ['id'];

    public function casts(): array
    {
        return [
            'year_month' => 'string',
            'gross_revenue' => 'integer',
        ];
    }

    public function startup(): BelongsTo
    {
        return $this->belongsTo(Startup::class);
    }
}
