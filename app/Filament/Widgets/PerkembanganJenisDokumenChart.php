<?php

namespace App\Filament\Widgets;

use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;
use Filament\Forms\Components\DatePicker;
use Illuminate\Support\Carbon;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use App\Models\JenisDokumen;
use App\Models\Dokumen;

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
                ->default(Carbon::now()->startOfYear())
                ->required(),
            DatePicker::make('date_end')
                ->label('Tanggal Akhir')
                ->default(Carbon::now()->endOfDay())
                ->required(),
        ];
    }

    protected function getOptions(): array
    {
        $dateStart = isset($this->filterFormData['date_start']) ? Carbon::parse($this->filterFormData['date_start'])->startOfDay() : Carbon::now()->startOfYear();
        $dateEnd = isset($this->filterFormData['date_end']) ? Carbon::parse($this->filterFormData['date_end'])->endOfDay() : Carbon::now()->endOfDay();

        // Ambil semua jenis dokumen
        $jenisDokumens = JenisDokumen::all();

        $categories = [];
        $data = [];

        foreach ($jenisDokumens as $jenis) {
            $categories[] = $jenis->nama; // sesuaikan field nama jenis dokumen

            // Hitung jumlah dokumen dari jenis ini dalam rentang waktu
            $count = Dokumen::where('jenis_dokumen_id', $jenis->id)
                ->whereBetween('created_at', [$dateStart, $dateEnd])
                ->count();

            $data[] = $count;
        }

        // dd($dateStart);
        // Generate warna random
        $randomColors = array_map(fn($_) => sprintf('#%06X', mt_rand(0, 0xFFFFFF)), $data);

        return [
            'chart' => [
                'type' => 'bar',
                'height' => 300,
            ],
            'series' => [
                [
                    'name' => "Jumlah Dokumen ({$dateStart->format('d M Y')} - {$dateEnd->format('d M Y')})",
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
