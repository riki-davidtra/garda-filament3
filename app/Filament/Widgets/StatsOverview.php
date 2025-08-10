<?php

namespace App\Filament\Widgets;

use App\Models\Dokumen;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\App;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {

        $targetDokumen = App::make('settingItems')['target_dokumen']->value ?? 0;
        $totalDokumen = Dokumen::count();

        $persentase = $targetDokumen > 0
            ? round(($totalDokumen / $targetDokumen) * 100, 1)
            : 0;

        return [
            Stat::make('Total Dokumen Terkumpul', number_format($totalDokumen))
                ->description("Terkumpul {$persentase}% dari target {$targetDokumen}")
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success')
                ->chart([$totalDokumen, $targetDokumen - $totalDokumen]),
        ];
    }
}
