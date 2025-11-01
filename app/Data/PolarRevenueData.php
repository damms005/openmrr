<?php

declare(strict_types=1);

namespace App\Data;

use Spatie\LaravelData\Data;

final class PolarRevenueData extends Data
{
    public function __construct(
        public float $monthlyRecurringRevenue,
        public float $totalRevenue,
        public int $subscriberCount,
        public array $monthlyMrrAggregates,
        public array $monthlyGrossRevenueAggregates,
        public ?string $lastProcessedSubscriptionId,
        public ?string $lastProcessedOrderId = null,
    ) {}
}
