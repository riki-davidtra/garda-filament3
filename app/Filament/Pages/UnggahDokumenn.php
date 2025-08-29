<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\JenisDokumen;
use Illuminate\Support\Facades\Auth;

class UnggahDokumenn extends Page
{
    protected static ?string $navigationIcon  = 'heroicon-o-arrow-up-tray';
    protected static ?string $navigationGroup = 'Dokumen';
    protected static ?string $navigationLabel = 'Unggah Dokumen2';
    protected static ?string $title           = 'Unggah Dokumen2';
    protected static ?int $navigationSort     = 31;

    protected static string $view = 'filament.pages.unggah-dokumenn';

    public static function canAccess(): bool
    {
        return Auth::user()?->can('create Dokumen', \App\Models\Dokumen::class) ?? false;
    }

    public $jenisDokumens;

    public function mount()
    {
        $user = Auth::user();

        $this->jenisDokumens = JenisDokumen::whereHas('roles', function ($query) use ($user) {
            $query->whereIn('roles.id', $user->roles->pluck('id'));
        })->get();
    }
}
