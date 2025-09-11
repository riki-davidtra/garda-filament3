<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TemplatDokumenResource\Pages;
use App\Filament\Resources\TemplatDokumenResource\RelationManagers;
use App\Models\TemplatDokumen;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TemplatDokumenResource extends Resource
{
    protected static ?string $model = TemplatDokumen::class;

    protected static ?string $navigationIcon   = 'heroicon-o-document-text';
    protected static ?string $navigationLabel  = 'Template Dokumen';
    protected static ?string $pluralModelLabel = 'Daftar Template Dokumen';
    protected static ?string $modelLabel       = 'Template Dokumen';
    protected static ?int $navigationSort      = 14;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama')
                    ->label('Nama')
                    ->required()
                    ->maxLength(255),

                Forms\Components\Select::make('jenis_dokumen_id')
                    ->label('Jenis Dokumen')
                    ->nullable()
                    ->searchable()
                    ->preload()
                    ->relationship('jenisDokumen', 'nama', function ($query) {
                        $query->orderBy('nama', 'asc');
                    })
                    ->unique(ignoreRecord: true),

                Forms\Components\FileUpload::make('path')
                    ->label('File')
                    ->nullable()
                    ->disk('public')
                    ->directory('templat-dokumen')
                    ->maxSize(20480)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        $user           = Auth::user();
        $isSuperOrAdmin = $user->hasAnyRole(['Super Admin', 'admin']);
        $isPerencana    = $user->hasRole('perencana');
        $isSubbagian    = $user->hasRole('subbagian');

        return $table
            ->modifyQueryUsing(function (Builder $query, $livewire) use ($user, $isSuperOrAdmin, $isPerencana) {
                if (!$isSuperOrAdmin && !$isPerencana) {
                    $query->whereHas('jenisDokumen.roles', function ($q) use ($user) {
                        $q->whereIn('roles.id', $user->roles->pluck('id'));
                    })->orWhereDoesntHave('jenisDokumen.roles');
                }
                return $query;
            })
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('jenisDokumen.nama')
                    ->label('Jenis Dokumen')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime('d-m-Y H:i')
                    ->since()
                    ->dateTimeTooltip('d-m-Y H:i')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Diperbarui Pada')
                    ->dateTime('d-m-Y H:i')
                    ->since()
                    ->dateTimeTooltip('d-m-Y H:i')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                // Tampilkan aksi hanya jika ada file dokumen terbaru yang tersedia di storage
                Tables\Actions\Action::make('unduh')
                    ->label('Unduh')
                    ->button()
                    ->color('info')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn($record) => route('template.unduh', $record->id))
                    ->openUrlInNewTab()
                    ->visible(fn($record) => filled($record?->path) && Storage::disk('public')->exists($record->path)),

                Tables\Actions\ViewAction::make()
                    ->button(),

                Tables\Actions\EditAction::make()
                    ->button()
                    ->color('warning'),
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
            'index' => Pages\ListTemplatDokumens::route('/'),
            // 'create' => Pages\CreateTemplatDokumen::route('/create'),
            // 'edit' => Pages\EditTemplatDokumen::route('/{record}/edit'),
        ];
    }
}
