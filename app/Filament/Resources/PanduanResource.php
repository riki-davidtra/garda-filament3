<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PanduanResource\Pages;
use App\Filament\Resources\PanduanResource\RelationManagers;
use App\Models\Panduan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PanduanResource extends Resource
{
    protected static ?string $model = Panduan::class;

    protected static ?string $navigationIcon   = 'heroicon-o-book-open';
    protected static ?string $navigationGroup  = 'Panduan & Bantuan';
    protected static ?string $navigationLabel  = 'Panduan';
    protected static ?string $pluralModelLabel = 'Daftar Panduan';
    protected static ?string $modelLabel       = 'Panduan';
    protected static ?int $navigationSort      = 51;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('judul')
                    ->label('Judul')
                    ->required()
                    ->string()
                    ->maxLength(255)
                    ->columnSpanFull(),
                Forms\Components\RichEditor::make('deskripsi')
                    ->label('Deskripsi')
                    ->nullable()
                    ->maxLength(3000)
                    ->toolbarButtons([
                        'bold',
                        'italic',
                        'underline',
                        'strike',
                        'bulletList',
                        'orderedList',
                        'link',
                        'undo',
                        'redo',
                    ])
                    ->columnSpanFull(),
                Forms\Components\FileUpload::make('file')
                    ->label('File')
                    ->nullable()
                    ->disk('public')
                    ->directory('panduan')
                    ->maxSize(102400)
                    ->acceptedFileTypes([
                        // Images
                        'image/jpeg',
                        'image/png',
                        'image/gif',
                        'image/webp',

                        // Videos
                        'video/mp4',
                        'video/webm',
                        'video/ogg',

                        // Documents
                        'application/pdf',
                        'application/msword',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                        'application/vnd.ms-excel',
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    ])
                    ->helperText('Maks. 100 MB. Format: JPEG, PNG, GIF, WebP, MP4, WebM, OGG, PDF, Word, Excel.')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderable('order')
            ->defaultSort('order', 'asc')
            ->columns([
                Tables\Columns\TextColumn::make('judul')
                    ->label('Judul')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime()
                    ->since()
                    ->dateTimeTooltip()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Diperbarui Pada')
                    ->dateTime()
                    ->since()
                    ->dateTimeTooltip()
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
            'index'  => Pages\ListPanduans::route('/'),
            // 'create' => Pages\CreatePanduan::route('/create'),
            // 'edit'   => Pages\EditPanduan::route('/{record}/edit'),
        ];
    }
}
