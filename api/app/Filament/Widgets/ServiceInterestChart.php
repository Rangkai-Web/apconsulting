<?php

namespace App\Filament\Widgets;

use App\Models\Inquiry;
use App\Models\ServiceCategory;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class ServiceInterestChart extends ChartWidget
{
    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = [
        'default' => 'full',
        'lg' => 1,
    ];

    protected ?string $heading = 'Minat Kategori Layanan';

    protected ?string $description = 'Proporsi kebutuhan layanan konsultasi dari form website';

    protected ?string $maxHeight = '280px';

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getData(): array
    {
        // Ambil data inquiry yang memiliki service_interest
        $records = Inquiry::query()
            ->whereNotNull('service_interest')
            ->where('service_interest', '!=', '')
            ->select('service_interest', DB::raw('count(*) as count'))
            ->groupBy('service_interest')
            ->pluck('count', 'service_interest')
            ->toArray();

        // Fallback jika belum ada data konsultasi di database
        if (empty($records)) {
            return [
                'datasets' => [
                    [
                        'label' => 'Konsultasi Masuk',
                        'data' => [1],
                        'backgroundColor' => ['rgba(255, 255, 255, 0.08)'],
                        'borderColor' => ['rgba(255, 255, 255, 0.12)'],
                        'borderWidth' => 1,
                    ],
                ],
                'labels' => ['Belum ada data masuk'],
            ];
        }

        // Peta warna harmonis korporat AP Consulting
        $palette = [
            '#0869B2', // AP Primary Navy Blue
            '#0EA5E9', // Sky Blue
            '#06B6D4', // Cyan
            '#3B82F6', // Blue
            '#6366F1', // Indigo
            '#10B981', // Emerald
            '#F59E0B', // Amber
            '#8B5CF6', // Purple
        ];

        $labels = [];
        $data = [];
        $backgroundColors = [];
        $idx = 0;

        foreach ($records as $serviceName => $count) {
            $labels[] = (string) $serviceName;
            $data[] = (int) $count;
            $backgroundColors[] = $palette[$idx % count($palette)];
            $idx++;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Total Konsultasi',
                    'data' => $data,
                    'backgroundColor' => $backgroundColors,
                    'borderColor' => '#091726',
                    'borderWidth' => 3,
                    'hoverOffset' => 4,
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
                    'position' => 'bottom',
                    'labels' => [
                        'color' => '#d1e4f5',
                        'boxWidth' => 12,
                        'padding' => 14,
                        'font' => [
                            'size' => 11,
                        ],
                    ],
                ],
            ],
            'cutout' => '68%',
        ];
    }
}
