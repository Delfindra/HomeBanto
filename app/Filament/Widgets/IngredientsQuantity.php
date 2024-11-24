<?php

namespace App\Filament\Widgets;

use App\Models\Ingredients;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;

class IngredientsQuantity extends BaseWidget
{
    protected static ?string $heading = 'Low Stock Ingredients';

    public function getTableRecordKey($record): string
    {
        return (string) $record->id;
    }

    public function table(Table $table): Table
    {
        $user = Auth::user();
        return $table
            ->query(
                Ingredients::where('users_id', $user->id)
                    ->where('quantity', '<=', 5)
            )
            ->columns([
                TextColumn::make('No.')
                    ->getStateUsing(static function ($record, $rowLoop): string {
                        return (string) $rowLoop->iteration;
                    }),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('quantity')
                    ->badge()
                    ->label('Stock')
                    ->colors([
                        'success' => static fn($record) => $record->quantity > 3,
                        'warning' => static fn($record) => $record->quantity > 0 && $record->quantity <= 3,
                        'danger' => static fn($record) => $record->quantity == 0,
                    ])
            ]);
    }
}
