<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\Founder;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\HtmlString;

final class FounderRevenueStats extends StatsOverviewWidget
{
    public Founder $founder;

    protected function getColumns(): array
    {
        return [
            'default' => 1,
            'md' => 2,
            'lg' => 4,
        ];
    }

    protected function getStats(): array
    {
        $startups = $this->founder->startups()->get();
        $totalRevenueSum = $startups->sum('total_revenue');
        $mrrSum = $startups->sum('monthly_recurring_revenue');
        $subscriberSum = $startups->sum('subscriber_count');

        $totalRevenue = (float) (is_numeric($totalRevenueSum) ? $totalRevenueSum : 0);
        $totalMrr = (float) (is_numeric($mrrSum) ? $mrrSum : 0);
        $totalSubscribers = (int) (is_numeric($subscriberSum) ? $subscriberSum : 0);

        $stats = [
            Stat::make('Total Revenue', '$' . number_format($totalRevenue, 0))
                ->description(new HtmlString('<span class="text-xs">Combined total revenue</span>'))
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
        ];

        $mrrStat = $this->buildMrrStat($totalMrr);
        if ($mrrStat !== null) {
            $stats[] = $mrrStat;
        }

        $stats[] = Stat::make('Startups', (string) count($startups))
            ->description(new HtmlString('<span class="text-xs">Active ' . str('startup')->plural(count($startups)) . '</span>'))
            ->descriptionIcon('heroicon-m-rocket-launch')
            ->color('primary');

        $stats[] = Stat::make('Customers', number_format($totalSubscribers, 0))
            ->description(new HtmlString('<span class="text-xs">Across products</span>'))
            ->descriptionIcon('heroicon-m-users')
            ->color('warning');

        return $stats;
    }

    private function buildMrrStat(float $totalMrr): ?Stat
    {
        $startups = $this->founder->startups()->get();
        $lastMonth = Carbon::now()->subMonth()->format('Y-m');
        $monthBeforeLast = Carbon::now()->subMonths(2)->format('Y-m');

        $lastMrrSum = 0;
        $previousMrrSum = 0;
        $hasBothMonths = true;

        foreach ($startups as $startup) {
            $lastMrrValue = $startup->monthlyMrrs()
                ->where('year_month', $lastMonth)
                ->value('monthly_recurring_revenue');

            $previousMrrValue = $startup->monthlyMrrs()
                ->where('year_month', $monthBeforeLast)
                ->value('monthly_recurring_revenue');

            if ($lastMrrValue !== null) {
                $lastMrrSum += (float) $lastMrrValue;
            } else {
                $hasBothMonths = false;
            }

            if ($previousMrrValue !== null) {
                $previousMrrSum += (float) $previousMrrValue;
            } else {
                $hasBothMonths = false;
            }
        }

        if (! $hasBothMonths || $previousMrrSum === 0.0) {
            return Stat::make('MRR', '$' . number_format($totalMrr, 0))
                ->description(new HtmlString('<span class="text-xs">Combined MRR</span>'))
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('info');
        }

        $percentageChange = $previousMrrSum > 0 ? (($lastMrrSum - $previousMrrSum) / $previousMrrSum) * 100 : 0;
        $formattedPercentage = number_format(abs($percentageChange), 1);

        $description = $this->buildTrendDescription($lastMrrSum, $previousMrrSum, $formattedPercentage);

        return Stat::make('MRR', '$' . number_format($totalMrr, 0))
            ->description(new HtmlString($description))
            ->color('info');
    }

    private function buildTrendDescription(float $lastMrrSum, float $previousMrrSum, string $formattedPercentage): string
    {
        if ($lastMrrSum > $previousMrrSum) {
            return '<div class="flex items-center gap-1"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-4 text-green-600 dark:text-green-400"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 10.5L12 3m0 0l7.5 7.5M12 3v18" /></svg><span class="text-green-600 dark:text-green-400 font-medium text-xs">+' . $formattedPercentage . '%</span></div>';
        }

        if ($lastMrrSum < $previousMrrSum) {
            return '<div class="flex items-center gap-1"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-4 text-red-600 dark:text-red-400"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 13.5L12 21m0 0l-7.5-7.5M12 21V3" /></svg><span class="text-red-600 dark:text-red-400 font-medium text-xs">-' . $formattedPercentage . '%</span></div>';
        }

        return '<div class="flex items-center gap-1"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-4 text-gray-400 dark:text-gray-500"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6h12v12" /></svg><span class="text-gray-400 dark:text-gray-500 font-medium text-xs">0%</span></div>';
    }
}
