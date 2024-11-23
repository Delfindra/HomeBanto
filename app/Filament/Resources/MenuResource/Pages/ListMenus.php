<?php

namespace App\Filament\Resources\MenuResource\Pages;

use App\Filament\Resources\MenuResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Models\Ingredients;
use App\Models\Menu;
use Illuminate\Database\Eloquent\Builder;

class ListMenus extends ListRecords
{
    protected static string $resource = MenuResource::class;

    protected function getTableQuery(): Builder
    {
        // Get all available ingredients
        $availableIngredients = Ingredients::all()->pluck('name')->toArray();

        // Build the query
        return Menu::query()->where(function (Builder $query) use ($availableIngredients) {
            foreach ($availableIngredients as $ingredient) {
                $query->orWhereJsonContains('ingredient', $ingredient);
            }
        });
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
