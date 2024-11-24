<?php

namespace App\Filament\Widgets;

use App\Models\Ingredients;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class IngredientsExpiry extends BaseWidget
{
    protected static ?string $heading = 'Ingredients Near Expiry';

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
                    ->whereDate('expiry_date', '>=', now()->startOfDay())
                    ->whereDate('expiry_date', '<=', now()->addDays(7)->endOfDay()))
            ->columns([
                TextColumn::make('No.')
                    ->getStateUsing(static function ($record, $rowLoop): string {
                        return (string) $rowLoop->iteration;
                    }),
                TextColumn::make('expiry_date')
                    ->date('d M Y'),
                TextColumn::make('name')
                ->searchable(),
                BadgeColumn::make('status')
                    ->getStateUsing(static function ($record): string {
                        $daysLeft = intval(now()->diffInDays($record->expiry_date, false));
                        if ($daysLeft > 0) {
                            return "{$daysLeft}" . ($daysLeft === 1 ? ' day left ' : ' days left ');
                        } elseif ($daysLeft === 0) {
                            return "Expires today";
                        } else {
                            return "Expired";
                        }
                    })
                    ->colors([
                        'success' => static fn ($record) => now()->diffInDays($record->expiry_date, false) > 3,
                        'warning' => static fn ($record) => now()->diffInDays($record->expiry_date, false) > 0 && now()->diffInDays($record->expiry_date, false) <= 3,
                        'danger' => static fn ($record) => now()->diffinDays($record->expiry_date, false) == 0,
                        'secondary' => static fn ($record) => now()->diffInDays($record->expiry_date, false) < 0,
                    ])
            ]);
    }
}
