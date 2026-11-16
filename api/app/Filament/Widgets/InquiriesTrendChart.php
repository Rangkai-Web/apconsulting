<?php

namespace App\Filament\Widgets;

use App\Models\Inquiry;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class InquiriesTrendChart extends ChartWidget
{
    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = [
        'default' => 'full',
        'lg' => 1,
    ];

    protected ?string $heading = 'Tren Konsultasi Masuk';

    protected ?string $description = 'Jumlah pesan konsultasi baru per bulan pada tahun berjalan';

    protected ?string $maxHeight = '280px';

    protected function getType(): string
    {
        return 'line';
    }

    protected function getData(): array
    {
        $currentYear = (int) date('Y');

        // Mengambil jumlah pesan masuk per bulan secara dinamis
        $monthlyCounts = Inquiry::query()
            ->whereYear('created_at', $currentYear)
            ->selectRaw('MONTH(created_at) as month_num, count(*) as total')
            ->groupBy('month_num')
            ->pluck('total', 'month_num');

        $labels = [];
        $data = [];

        // 12 Bulan (Jan - Des)
        for ($month = 1; $month <= 12; $month++) {
            $labels[] = Carbon::create($currentYear, $month, 1)->translatedFormat('M');
            $data[] = (int) ($monthlyCounts->get($month, 0));
        }

        return [
            'datasets' => [
                [
                    'label' => 'Konsultasi Masuk',
                    'data' => $data,
                    'fill' => true,
                    'borderColor' => '#0869B2',
                    'backgroundColor' => 'rgba(8, 105, 178, 0.16)',
                    'tension' => 0.35,
                    'pointBackgroundColor' => '#0ea5e9',
                    'pointBorderColor' => '#ffffff',
                    'pointBorderWidth' => 2,
                    'pointRadius' => 4,
                    'pointHoverRadius' => 6,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'precision' => 0,
                    ],
                    'grid' => [
                        'color' => 'rgba(255, 255, 255, 0.06)',
                    ],
                ],
                'x' => [
                    'grid' => [
                        'display' => false,
                    ],
                ],
            ],
        ];
    }
}
