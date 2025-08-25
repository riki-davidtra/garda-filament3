<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use App\Models\JenisDokumen;
use App\Models\Dokumen;
use App\Models\Pengaduan;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $user = Auth::user();

        $totalJenisDokumen = JenisDokumen::count();

        $totalDokumen = Dokumen::whereBetween('created_at', [
            Carbon::now()->startOfYear(),
            Carbon::now(),
        ])->count();

        $totalPengaduanMenunggu = Pengaduan::where('status', 'Menunggu')
            ->when(!$user->hasRole(['Super Admin', 'admin', 'perencana']), function ($query) use ($user) {
                $query->where('dibuat_oleh', $user->id);
            })->count();

        return [
            Stat::make('Total Jenis Dokumen', number_format($totalJenisDokumen))
                ->description('Jumlah semua jenis dokumen yang ada')
                ->color('primary')
                ->chart(array_map(fn($_) => rand(0, 20), range(1, 7))),
            Stat::make('Total Dokumen Terkumpul', number_format($totalDokumen))
                ->description("Jumlah dokumen yang sudah terkumpul tahun ini")
                ->color('success')
                ->chart(array_map(fn($_) => rand(0, 20), range(1, 7))),
            Stat::make('Total Pengaduan', number_format($totalPengaduanMenunggu))
                ->description("Jumlah pengaduan dengan status Menunggu")
                ->color('warning')
                ->chart(array_map(fn($_) => rand(0, 20), range(1, 7)))
                ->url(route('filament.admin.resources.pengaduans.index')),
        ];
    }
}
