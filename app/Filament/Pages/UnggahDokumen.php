<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\JenisDokumen;
use Illuminate\Support\Facades\Auth;

class UnggahDokumen extends Page
{
    protected static ?string $navigationIcon  = 'heroicon-o-arrow-up-tray';
    protected static ?string $navigationGroup = 'Dokumen';
    protected static ?int $navigationSort     = 31;

    protected static string $view = 'filament.pages.unggah-dokumen';

    // public static function canAccess(): bool
    // {
    //     return Auth::user()?->can('create dokumen', \App\Models\Dokumen::class) ?? false;
    // }

    public $jenisDokumens;

    public function mount()
    {
        $this->jenisDokumens = JenisDokumen::all();
    }
}
