<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (!app()->environment('local')) {
            URL::forceScheme('https');
        }

        \App\Models\User::observe(\App\Observers\UserObserver::class);
        \App\Models\Setting::observe(\App\Observers\SettingObserver::class);
        \App\Models\SettingItem::observe(\App\Observers\SettingItemObserver::class);
        \App\Models\Bagian::observe(\App\Observers\BagianObserver::class);
        \App\Models\Dokumen::observe(\App\Observers\DokumenObserver::class);
        \App\Models\FileDokumen::observe(\App\Observers\FileDokumenObserver::class);
        \App\Models\Panduan::observe(\App\Observers\PanduanObserver::class);
        \App\Models\Pengaduan::observe(\App\Observers\PengaduanObserver::class);
        \App\Models\TemplatDokumen::observe(\App\Observers\TemplatDokumenObserver::class);
        \App\Models\FileTemplatDokumen::observe(\App\Observers\FileTemplatDokumenObserver::class);
        \App\Models\IndeksKinerjaUtama::observe(\App\Observers\IndeksKinerjaUtamaObserver::class);
        \App\Models\DataDukungPerencanaan::observe(\App\Observers\DataDukungPerencanaanObserver::class);
    }
}
