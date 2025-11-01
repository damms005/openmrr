<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class StartupMonthlyMrr extends Model
{
    /** @use HasFactory<\Database\Factories\StartupMonthlyMrrFactory> */
    use HasFactory;

    protected $table = 'startup_monthly_mrrs';

    protected $guarded = ['id'];

    public function casts(): array
    {
        return [
            'year_month' => 'string',
            'monthly_recurring_revenue' => 'integer',
        ];
    }

    public function startup(): BelongsTo
    {
        return $this->belongsTo(Startup::class);
    }
}
