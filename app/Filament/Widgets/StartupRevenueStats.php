<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\Startup;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\HtmlString;

final class StartupRevenueStats extends StatsOverviewWidget
{
    public Startup $startup;

    protected function getColumns(): array
    {
        return [
            'default' => 1,
            'md' => 2,
            'lg' => 3,
        ];
    }

    protected function getStats(): array
    {
        $totalRevenue = (float) $this->startup->total_revenue;
        $mrr = (float) $this->startup->monthly_recurring_revenue;

        return [
            Stat::make('Total Revenue', '$' . number_format($totalRevenue, 0))
                ->description(new HtmlString('<span class="text-xs">Lifetime revenue</span>'))
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('success'),

            Stat::make('MRR', '$' . number_format($mrr, 0))
                ->description(new HtmlString('<span class="text-xs">Current MRR</span>'))
                ->descriptionIcon('heroicon-m-chart-pie')
                ->color('info'),

            Stat::make('Customers', (string) $this->startup->subscriber_count)
                ->description(new HtmlString('<span class="text-xs">Active subscribers</span>'))
                ->descriptionIcon('heroicon-m-users')
                ->color('warning'),
        ];
    }
}
