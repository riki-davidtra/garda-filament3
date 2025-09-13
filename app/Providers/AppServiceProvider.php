<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Database\Schema\Blueprint;

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
        \App\Models\JadwalDokumen::observe(\App\Observers\JadwalDokumenObserver::class);
        \App\Models\Dokumen::observe(\App\Observers\DokumenObserver::class);
        \App\Models\Panduan::observe(\App\Observers\PanduanObserver::class);
        \App\Models\Pengaduan::observe(\App\Observers\PengaduanObserver::class);
        \App\Models\TemplatDokumen::observe(\App\Observers\TemplatDokumenObserver::class);
        \App\Models\IndeksKinerjaUtama::observe(\App\Observers\IndeksKinerjaUtamaObserver::class);
        \App\Models\DataDukungPerencanaan::observe(\App\Observers\DataDukungPerencanaanObserver::class);
        \App\Models\File::observe(\App\Observers\FileObserver::class);

        Blueprint::macro('auditColumns', function () {
            /** @var Blueprint $this */
            $this->foreignId('dibuat_oleh')->nullable()->constrained('users')->nullOnDelete();
            $this->timestamp('dibuat_pada')->nullable()->index();
            $this->foreignId('diperbarui_oleh')->nullable()->constrained('users')->nullOnDelete();
            $this->timestamp('diperbarui_pada')->nullable()->index();
            $this->foreignId('dihapus_oleh')->nullable()->constrained('users')->nullOnDelete();
            $this->timestamp('dihapus_pada')->nullable()->index();
            $this->foreignId('dipulihkan_oleh')->nullable()->constrained('users')->nullOnDelete();
            $this->timestamp('dipulihkan_pada')->nullable()->index();
        });
    }
}
