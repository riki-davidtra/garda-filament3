<?php

namespace App\Providers;

use App\Filament\Resources\DokumenResource\Pages\ListDokumens;
use Illuminate\Support\Facades\Request;
use Filament\Facades\Filament;
use Filament\Navigation\NavigationItem;
use App\Models\JenisDokumen;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;

class FilamentServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // 
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Filament::serving(function () {
            $user = Auth::user();
            Filament::registerNavigationItems($this->getNavigationItems($user));
        });
    }

    protected function getNavigationItems($user): array
    {
        $items = [];

        $items[] = NavigationItem::make('Daftar Dokumen')
            ->group('Dokumen')
            ->icon('heroicon-o-document-text')
            ->url('#')
            ->sort(32)
            ->isActiveWhen(
                fn() =>
                request()->routeIs([
                    'filament.admin.resources.dokumens.index',
                    'filament.admin.resources.dokumens.create',
                    'filament.admin.resources.dokumens.edit',
                    'filament.admin.resources.dokumens.view',
                ])
            );

        if (!$user) {
            return [];
        }

        $jenisDokumens = JenisDokumen::whereHas('roles', function ($query) use ($user) {
            $query->whereIn('roles.id', $user->roles->pluck('id'));
        })->get();

        foreach ($jenisDokumens as $jenis) {
            $count = \App\Models\Dokumen::where('jenis_dokumen_id', $jenis->id)
                ->whereIn('status', ['Menunggu Persetujuan', 'Revisi Menunggu Persetujuan'])
                ->count();

            $items[] = NavigationItem::make($jenis->nama)
                ->group('Dokumen')
                ->badge($count > 0 ? (string)$count : null)
                ->badgeTooltip('Jumlah dokumen ' . $jenis->nama . ' dengan status Menunggu')
                ->url(fn() => ListDokumens::getUrl(['jenis_dokumen_id' => $jenis->id]))
                ->sort(33)
                ->isActiveWhen(
                    fn() =>
                    request()->routeIs([
                        'filament.admin.resources.dokumens.index',
                        'filament.admin.resources.dokumens.create',
                        'filament.admin.resources.dokumens.edit',
                        'filament.admin.resources.dokumens.view',
                    ]) && (int) request('jenis_dokumen_id') === $jenis->id
                );
        }

        return $items;
    }
}
