<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class StartupMonthlyRevenueAggregate extends Model
{
    /** @use HasFactory<\Database\Factories\StartupMonthlyRevenueAggregateFactory> */
    use HasFactory;

    protected $table = 'startup_monthly_revenue_aggregates';

    protected $guarded = ['id'];

    public function casts(): array
    {
        return [
            'year_month' => 'string',
            'total_revenue' => 'integer',
            'monthly_recurring_revenue' => 'integer',
            'subscriber_count' => 'integer',
        ];
    }

    public function startup(): BelongsTo
    {
        return $this->belongsTo(Startup::class);
    }
}
