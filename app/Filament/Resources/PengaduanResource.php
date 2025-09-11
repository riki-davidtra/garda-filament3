<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PengaduanResource\Pages;
use App\Filament\Resources\PengaduanResource\RelationManagers;
use App\Models\Pengaduan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use FilamentTiptapEditor\TiptapEditor;
use FilamentTiptapEditor\Enums\TiptapOutput;

class PengaduanResource extends Resource
{
    protected static ?string $model = Pengaduan::class;

    protected static ?string $navigationIcon   = 'heroicon-o-exclamation-triangle';
    protected static ?string $navigationGroup  = 'Panduan & Bantuan';
    protected static ?string $navigationLabel  = 'Pengaduan';
    protected static ?string $pluralModelLabel = 'Daftar Pengaduan';
    protected static ?string $modelLabel       = 'Pengaduan';
    protected static ?int $navigationSort      = 53;

    public static function getNavigationBadge(): ?string
    {
        $user           = Auth::user();
        $isSuperOrAdmin = $user->hasAnyRole(['Super Admin', 'admin']);
        $query = static::getModel()::whereIn('status', ['menunggu']);
        if (!$isSuperOrAdmin) {
            $query->where('dibuat_oleh', $user->id);
        }
        return (string) $query->count();
    }
    protected static ?string $navigationBadgeTooltip = 'Jumlah pengaduan dengan status Menunggu.';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('judul')
                    ->label('Judul')
                    ->required()
                    ->string()
                    ->maxLength(255)
                    ->columnSpanFull()
                    ->disabledOn('edit'),

                // Forms\Components\RichEditor::make('pesan')
                //     ->label('Pesan')
                //     ->required()
                //     ->maxLength(3000)
                //     ->fileAttachmentsDisk('public')
                //     ->fileAttachmentsDirectory('pengaduan/pesan')
                //     ->columnSpanFull()
                //     ->disabledOn('edit')
                //     ->afterStateHydrated(function ($component) {
                //         $component->extraAttributes(['target' => '_blank']);
                //     }),

                TiptapEditor::make('pesan')
                    ->label('Pesan')
                    ->required()
                    ->tools([
                        'bold',
                        'italic',
                        'strike',
                        'underline',
                        'link',
                        'media',
                        'oembed',
                        'code-block',
                        'table'
                    ])
                    ->disk('public')
                    ->directory('pengaduan/pesan')
                    ->maxSize(5120)
                    ->output(TiptapOutput::Html)
                    ->columnSpanFull()
                    ->disabledOn('edit'),

                TiptapEditor::make('tanggapan')
                    ->label('Tanggapan')
                    ->nullable()
                    ->tools([
                        'bold',
                        'italic',
                        'strike',
                        'underline',
                        'link',
                        'media',
                        'oembed',
                        'code-block',
                        'table'
                    ])
                    ->disk('public')
                    ->directory('pengaduan/tanggapan')
                    ->maxSize(5120)
                    ->output(TiptapOutput::Html)
                    ->columnSpanFull()
                    ->hiddenOn('create'),

                Forms\Components\Radio::make('status')
                    ->label('Status')
                    ->nullable()
                    ->inline()
                    ->options([
                        'menunggu' => 'Menunggu',
                        'proses'   => 'Proses',
                        'selesai'  => 'Selesai',
                    ])
                    ->default('menunggu')
                    ->hiddenOn('create'),
            ]);
    }

    public static function table(Table $table): Table
    {
        $user           = Auth::user();
        $isSuperOrAdmin = $user->hasAnyRole(['Super Admin', 'admin']);
        $isPerencana    = $user->hasRole('perencana');
        $isSubbagian    = $user->hasRole('subbagian');

        return $table
            ->modifyQueryUsing(function (Builder $query) use ($user, $isSuperOrAdmin) {
                if (!$isSuperOrAdmin) {
                    $query->where('dibuat_oleh', $user->id);
                }
            })
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('judul')
                    ->label('Judul')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'menunggu' => 'warning',
                        'proses'   => 'primary',
                        'selesai'  => 'success',
                        default    => 'gray',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('pembuat.name')
                    ->label('Dikirim Oleh')
                    ->description(function ($record) {
                        $user      = $record->pembuat;
                        $bagian    = $user?->subbagian?->bagian?->nama;
                        $subbagian = $user?->subbagian?->nama;
                        $tanggal   = $record->dibuat_pada;
                        $parts     = [
                            $user?->nip ? 'NIP: ' . $user?->nip                           : null,
                            $bagian     ? $bagian . ($subbagian ? ' - ' . $subbagian : '') : null,
                            $tanggal    ? $tanggal->format('d-m-Y H:i')                   : null,
                        ];
                        return implode(' | ', array_filter($parts));
                    })
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('pembaru.name')
                    ->label('Dibalas Oleh')
                    ->description(function ($record) {
                        $user      = $record->pembaru;
                        $bagian    = $user?->subbagian?->bagian?->nama;
                        $subbagian = $user?->subbagian?->nama;
                        $tanggal   = $record->diperbarui_pada;
                        $parts     = [
                            $user?->nip ? 'NIP: ' . $user?->nip                           : null,
                            $bagian     ? $bagian . ($subbagian ? ' - ' . $subbagian : '') : null,
                            $tanggal    ? $tanggal->format('d-m-Y H:i')                   : null,
                        ];
                        return implode(' | ', array_filter($parts));
                    })
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPengaduans::route('/'),
            'create' => Pages\CreatePengaduan::route('/create'),
            'edit' => Pages\EditPengaduan::route('/{record}/edit'),
        ];
    }
}
