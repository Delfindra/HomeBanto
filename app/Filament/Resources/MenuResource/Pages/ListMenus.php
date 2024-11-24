<?php

namespace App\Filament\Resources\MenuResource\Pages;

use App\Filament\Resources\MenuResource;
use App\Models\allergies;
use App\Models\MasterData;
use App\Models\recipes;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ListRecords\Tab;
use App\Models\Ingredients;
use App\Models\Menu;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class ListMenus extends ListRecords
{
    protected static string $resource = MenuResource::class;

    public function getTabs() : array {

        $availableIngredients = Ingredients::all()->pluck('name')->toArray();

        $user = Auth::user();

        // Retrieve allergies for the user and get the corresponding ingredient names
        $allergyIds = allergies::where('user_id', $user->id)->pluck('masterdata_id')->toArray();
        $allergyIngredients = MasterData::whereIn('id', $allergyIds)->pluck('name')->toArray();

        // Build the query for recipes
        $query = function (Builder $query) use ($availableIngredients, $allergyIngredients) {
            // Check if the recipes table has the 'ingredient' column
            if (Schema::hasColumn('recipes', 'ingredient')) {
                // Build the query to check for available ingredients
                if (!empty($availableIngredients)) {
                    $query->where(function ($subQuery) use ($availableIngredients) {
                        foreach ($availableIngredients as $ingredient) {
                            $subQuery->orWhere('ingredient', 'like', '%' . $ingredient . '%');
                        }
                    });
                } else {
                    $query->whereRaw('1 = 0'); // If no available ingredients, return an empty query
                }

                // Exclude recipes that contain allergens
                if (!empty($allergyIngredients)) {
                    $query->where(function ($subQuery) use ($allergyIngredients) {
                        foreach ($allergyIngredients as $allergen) {
                            $subQuery->where('ingredient', 'not like', '%' . $allergen . '%');
                        }
                    });
                }
            } else {
                // Handle the case where the 'ingredient' column does not exist
                $query->whereRaw('1 = 0'); // This will always evaluate to false
            }
        };

        return[
            'Available Ingredient + Allergies' => Tab::make()->query($query),
            'Available Ingredient + Allergies + Diet' => Tab::make(),//use $query2 for allergies + diet here
        ];
    }

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
