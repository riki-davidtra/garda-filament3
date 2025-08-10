<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Filament\Forms;
use Illuminate\Support\Carbon;

class PerkembanganJenisDokumenChart extends ChartWidget implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static ?string $heading = 'Persentase Perkembangan per Jenis Dokumen';
    protected int | string | array $columnSpan = 'full';
    protected static ?int $sort = 2;

    public ?string $dateFrom = null;
    public ?string $dateTo = null;

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\DatePicker::make('dateFrom')
                ->label('Tanggal Mulai')
                ->default(now()->startOfMonth())
                ->required(),

            Forms\Components\DatePicker::make('dateTo')
                ->label('Tanggal Akhir')
                ->default(now()->endOfMonth())
                ->required(),
        ];
    }

    public function mount(): void
    {
        $this->form->fill([
            'dateFrom' => now()->startOfMonth()->toDateString(),
            'dateTo' => now()->endOfMonth()->toDateString(),
        ]);
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        // Ambil nilai tanggal dari form
        $filters = $this->form->getState();
        $dateFrom = $filters['dateFrom'] ?? now()->startOfMonth()->toDateString();
        $dateTo = $filters['dateTo'] ?? now()->endOfMonth()->toDateString();

        // Contoh logika query data sesuaikan dengan model dan data kamu
        // Misal:
        // $data = ModelDokumen::query()
        //     ->whereBetween('created_at', [$dateFrom, $dateTo])
        //     ->groupBy('jenis_dokumen')
        //     ->selectRaw('jenis_dokumen, AVG(progress) as avg_progress')
        //     ->get();

        // Sementara dummy data pakai tetap sama, tapi sebenarnya harus dari database dengan filter tanggal
        return [
            'labels' => ['Jenis A', 'Jenis B', 'Jenis C', 'Jenis D', 'Jenis E', 'Jenis F', 'Jenis G', 'Jenis H'],
            'datasets' => [
                [
                    'label' => 'Persentase Progres (%)',
                    'data' => [75, 50, 90, 60, 80, 90, 100, 75],
                    'backgroundColor' => [
                        '#60A5FA',
                        '#FCA5A5',
                        '#6EE7B7',
                        '#FCD34D',
                        '#C4B5FD',
                        '#93C5FD',
                        '#34D399',
                        '#FBBF24',
                    ],
                ],
            ],
        ];
    }

    // Jangan lupa method ini agar widget refresh ketika filter diubah
    public function updated($propertyName): void
    {
        $this->emit('refreshWidget');
    }
}
