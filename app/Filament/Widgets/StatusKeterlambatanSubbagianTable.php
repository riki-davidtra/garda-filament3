<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Subbagian;

class StatusKeterlambatanSubbagianTable extends BaseWidget
{
    protected static ?string $heading = 'Status Keterlambatan per Subbagian';
    protected int | string | array $columnSpan = 'full';
    protected static ?int $sort = 3;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Subbagian::query()
                    ->withCount(['dokumens as file_terlambat_count' => function (Builder $query) {
                        $query->whereHas('fileDokumens', function (Builder $queryFile) {
                            $queryFile->whereColumn('created_at', '>', 'dokumens.tenggat_waktu');
                        });
                    }])
            )
            ->columns([
                Tables\Columns\TextColumn::make('nama')
                    ->label('Subbagian')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('file_terlambat_count')
                    ->label('Jumlah File Terlambat')
                    ->color(fn(int $state): string => $state > 0 ? 'danger' : 'success')
                    ->sortable(),
            ]);
    }
}
