<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MenuResource\Pages;
use App\Models\Menu;
use App\Models\Ingredients;
use App\Models\recipes;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class MenuResource extends Resource
{
    protected static ?string $model = Menu::class;

    protected static ?string $navigationIcon = 'http://localhost:8000/icons/Component-1-4.svg';

    protected static ?string $navigationLabel = 'Rekomendasi Menu';

    

    public static function canCreate(): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        $availableIngredients = Ingredients::all()->pluck('name')->toArray();

        $query = recipes::query();

        // // Get the recipe with the most ingredients
        // $recipeWithMostIngredients = recipes::select('id', 'name', 'ingredient')
        // ->selectRaw('LENGTH(ingredient) - LENGTH(REPLACE(ingredient, ",", "")) + 1 as ingredient_count')
        // ->groupBy('id', 'name', 'ingredient')
        // ->orderBy('ingredient_count', 'desc')
        // ->first();

        // // If a recipe is found, you can access its ingredient count
        // if ($recipeWithMostIngredients) {
        //     $mostIngredientsCount = $recipeWithMostIngredients->ingredient_count;
        //     // You can return or use this count value as needed
        //     // For example, you could log it or set it to a property
        //     // Log::info('Recipe with most ingredients has count: ' . $mostIngredientsCount);
        // }

        // Check if there are any available ingredients
        if (count($availableIngredients) > 0) {
            $query->where(function ($query) use ($availableIngredients) {
                // For each recipe, check if all required ingredients are available
                foreach ($availableIngredients as $ingredient) {
                    // Check if the recipe contains this ingredient
                    $query->orWhere(function ($subQuery) use ($ingredient) {
                        $subQuery->where('ingredient', 'like', '%' . $ingredient . '%');
                    });
                }
            });

            // Ensure that the recipe contains all required ingredients
            // Use a condition that counts the number of required ingredients in the recipe
            $query->whereRaw('LENGTH(ingredient) - LENGTH(REPLACE(ingredient, ",", "")) + 1 <= ?', [count($availableIngredients)]);
        }

        return $table
            ->query($query)
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('Gambar')
                    ->width(width: 70)
                    ->height(70)
                    ->disk('public'),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Menu')    
                    ->searchable(),
                Tables\Columns\TextColumn::make('description')
                    ->label('Deskripsi')
                    ->html()
                    ->wrap()
                    ->limit(250)
                    ->searchable(),
                Tables\Columns\TextColumn::make('ingredient')
                    ->label('Bahan')
                    
                    ->html()
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                //Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                //
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
            'index' => Pages\ListMenus::route('/'),
        ];
    }
}
