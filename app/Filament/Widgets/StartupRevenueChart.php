<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\Startup;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;

final class StartupRevenueChart extends ChartWidget
{
    public Startup $startup;

    protected ?string $heading = '';

    public function getData(): array
    {
        $aggregates = $this->startup->grossRevenues()
            ->orderBy('year_month')
            ->get()
            ->map(function ($aggregate) {
                return [
                    'date' => \Carbon\Carbon::createFromFormat('Y-m', $aggregate->year_month)->format('M Y'),
                    'revenue' => $aggregate->gross_revenue,
                ];
            })
            ->values();

        return [
            'datasets' => [
                [
                    'label' => 'Gross Revenue',
                    'data' => $aggregates->pluck('revenue')->values(),
                    'fill' => true,
                    'tension' => 0.4,
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'pointRadius' => 4,
                    'pointBackgroundColor' => '#3b82f6',
                ],
            ],
            'labels' => $aggregates->pluck('date')->values(),
        ];
    }

    protected function getOptions(): RawJs
    {
        $aggregates = $this->startup->grossRevenues()
            ->orderBy('year_month')
            ->get();

        $revenueValues = $aggregates->pluck('gross_revenue')->toArray();
        $maxRevenue = ! empty($revenueValues) ? max($revenueValues) : 0;
        $minRevenue = ! empty($revenueValues) ? min($revenueValues) : 0;
        $range = $maxRevenue - $minRevenue;
        $padding = $range > 0 ? $range * 0.15 : max(1, $maxRevenue * 0.15);

        $yMin = max(0, (int) ($minRevenue - $padding));
        $yMax = (int) ($maxRevenue + $padding);

        return RawJs::make(<<<JS
            {
                responsive: true,
                maintainAspectRatio: true,
                scales: {
                    y: {
                        min: $yMin,
                        max: $yMax,
                        ticks: {
                            callback: function(value) {
                                if (value >= 1000000) {
                                    return '\$' + (value / 1000000).toFixed(0) + 'M';
                                } else if (value >= 1000) {
                                    return '\$' + (value / 1000).toFixed(0) + 'k';
                                }
                                return '\$' + value;
                            }
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    },
                    x: {
                        ticks: {
                            callback: function(index) {
                                return index % 2 === 0 ? this.getLabelForValue(index) : '';
                            }
                        },
                        grid: {
                            display: false
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    filler: {
                        propagate: true
                    }
                }
            }
        JS);
    }

    protected function getType(): string
    {
        return 'line';
    }
}
