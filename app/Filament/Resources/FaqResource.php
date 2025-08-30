<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FaqResource\Pages;
use App\Filament\Resources\FaqResource\RelationManagers;
use App\Models\Faq;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FaqResource extends Resource
{
    protected static ?string $model = Faq::class;

    protected static ?string $navigationIcon   = 'heroicon-o-question-mark-circle';
    protected static ?string $navigationGroup  = 'Panduan & Bantuan';
    protected static ?string $navigationLabel  = 'FAQ';
    protected static ?string $pluralModelLabel = 'Daftar FAQ';
    protected static ?string $modelLabel       = 'FAQ';
    protected static ?int $navigationSort      = 52;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('pertanyaan')
                    ->label('Pertanyaan')
                    ->required()
                    ->string()
                    ->maxLength(255)
                    ->columnSpanFull(),

                Forms\Components\RichEditor::make('jawaban')
                    ->label('Jawaban')
                    ->required()
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
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderable('order')
            ->defaultSort('order', 'asc')
            ->columns([
                Tables\Columns\TextColumn::make('pertanyaan')
                    ->label('Pertanyaan')
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
            'index' => Pages\ListFaqs::route('/'),
            // 'create' => Pages\CreateFaq::route('/create'),
            // 'edit' => Pages\EditFaq::route('/{record}/edit'),
        ];
    }
}
