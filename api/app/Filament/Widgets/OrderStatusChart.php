<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;

class OrderStatusChart extends ChartWidget
{
    protected ?string $heading = 'Sebaran Status Pesanan';

    protected function getData(): array
    {
        $counts = Order::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        $cancelled = ($counts['cancelled'] ?? 0) + ($counts['expired'] ?? 0);

        return [
            'datasets' => [
                [
                    'data' => [
                        $counts['pending'] ?? 0,
                        $counts['paid'] ?? 0,
                        $counts['processing'] ?? 0,
                        $counts['shipped'] ?? 0,
                        $counts['completed'] ?? 0,
                        $cancelled,
                    ],
                    'backgroundColor' => [
                        '#9ca3af',
                        '#3b82f6',
                        '#f59e0b',
                        '#6366f1',
                        '#10b981',
                        '#ef4444',
                    ],
                ],
            ],
            'labels' => ['Pending', 'Paid', 'Processing', 'Shipped', 'Completed', 'Dibatalkan'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
