<?php

namespace App\Filament\Widgets;

use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;
use Filament\Forms\Components\DatePicker;
use Illuminate\Support\Carbon;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class PerkembanganJenisDokumenChart extends ApexChartWidget
{
    protected static ?string $chartId = 'perkembanganJenisDokumenChart';
    protected static ?string $heading = 'Persentase Perkembangan per Jenis Dokumen';
    protected int | string | array $columnSpan = 'full';
    protected static ?int $sort = 2;

    protected function getFormSchema(): array
    {
        return [
            DatePicker::make('date_start')
                ->label('Tanggal Mulai')
                ->default(now()->subMonth())
                ->required(),
            DatePicker::make('date_end')
                ->label('Tanggal Akhir')
                ->default(now())
                ->required(),
        ];
    }

    protected function getOptions(): array
    {
        $dateStart = isset($this->filterFormData['date_start']) ? Carbon::parse($this->filterFormData['date_start']) : now()->subMonth();
        $dateEnd = isset($this->filterFormData['date_end']) ? Carbon::parse($this->filterFormData['date_end']) : now();

        $categories = ['Jenis A', 'Jenis B', 'Jenis C', 'Jenis D', 'Jenis E', 'Jenis F', 'Jenis G', 'Jenis H'];
        $data = [75, 50, 90, 60, 80, 90, 100, 75];

        // Fungsi untuk generate warna hex random
        $randomColors = array_map(fn($_) => sprintf('#%06X', mt_rand(0, 0xFFFFFF)), $data);

        return [
            'chart' => [
                'type' => 'bar',
                'height' => 300,
            ],
            'series' => [
                [
                    'name' => "Persentase Progres ({$dateStart->format('d M Y')} - {$dateEnd->format('d M Y')})",
                    'data' => $data,
                ],
            ],
            'xaxis' => [
                'categories' => $categories,
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                    ],
                ],
            ],
            'yaxis' => [
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                    ],
                ],
            ],
            'colors' => $randomColors,
            'plotOptions' => [
                'bar' => [
                    'borderRadius' => 3,
                    'horizontal' => false,
                ],
            ],
        ];
    }
}
