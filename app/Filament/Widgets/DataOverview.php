<?php

namespace App\Filament\Widgets;

use App\Models\Ingredients;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class DataOverview extends BaseWidget
{

    protected function getStats(): array
    {
        $user = Auth::user();
        $ingredientsCount = Ingredients::where('users_id', $user->id)->count();
        $ingredientsExpiry = Ingredients::where('users_id', $user->id)
            ->whereDate('expiry_date', '>=', now()->startOfDay())
            ->whereDate('expiry_date', '<=', now()->addDays(7)->endOfDay())
            ->count();
        $ingredientsStock = Ingredients::where('users_id', $user->id)
            ->where('quantity', '<=', 5)
            ->count();
        return [
            Stat::make('Ingredients Item', $ingredientsCount),
            Stat::make('Low Stock Ingredients', $ingredientsStock),
            Stat::make('Ingredients Near Expiry', $ingredientsExpiry),
        ];
    }
}
