<?php

namespace App\Filament\Resources\MenuResource\Pages;

use App\Filament\Resources\MenuResource;
use App\Models\allergies;
use App\Models\Diet;
use App\Models\MasterData;
use App\Models\recipes;
use App\Models\User;
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

        // Get the user's diet_id
        $dietId = $user->diet_id;

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

        // Build the query for recipes with diet restrictions (if applicable)
        $query2 = function (Builder $query) use ($availableIngredients, $allergyIngredients, $dietId) {
            // Build the query to check for available ingredients
            if (!empty($availableIngredients)) {
                $query->where(function ($subQuery) use ($availableIngredients) {
                    foreach ($availableIngredients as $ingredient) {
                        // Check if the ingredient is in the JSON array
                        $subQuery->orWhereRaw('json_contains(`ingredient`, ?)', json_encode($ingredient));
                    }
                });
            } else {
                $query->whereRaw('1 = 0'); // If no available ingredients, return an empty query
            }

            // Exclude recipes that contain allergens
            if (!empty($allergyIngredients)) {
                $query->where(function ($subQuery) use ($allergyIngredients) {
                    foreach ($allergyIngredients as $allergen) {
                        // Exclude recipes that contain any of the allergens
                        $subQuery->whereRaw('not json_contains(`ingredient`, ?)', json_encode($allergen));
                    }
                });
            }

            // If you have any diet restrictions to apply, you can add that logic here.
            // Currently, we are not using dietId since it's not in the recipes table.
        };

        return[
            'Available Ingredient + Allergies' => Tab::make()->query($query),
            'Available Ingredient + Allergies + Diet' => Tab::make()->query($query2),//use $query2 for allergies + diet
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
