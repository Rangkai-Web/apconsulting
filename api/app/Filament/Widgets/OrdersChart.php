<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\LineChartWidget;

class OrdersChart extends LineChartWidget
{
    protected ?string $heading = 'Pesanan 7 Hari Terakhir';

    protected function getData(): array
    {
        $days = collect(range(6, 0))->map(fn (int $daysAgo) => now()->subDays($daysAgo)->startOfDay());

        $counts = Order::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', $days->first())
            ->groupBy('date')
            ->pluck('count', 'date');

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Pesanan',
                    'data' => $days->map(fn ($day) => $counts[$day->toDateString()] ?? 0)->all(),
                ],
            ],
            'labels' => $days->map(fn ($day) => $day->translatedFormat('d M'))->all(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
