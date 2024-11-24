<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MenuResource\Pages;
use App\Models\Ingredients;
use App\Models\Menu;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use App\Models\recipes;
use App\Models\allergies;
use App\Models\MasterData;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class MenuResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = Menu::class;

    protected static ?string $navigationIcon = 'menu';

    protected static ?string $navigationLabel = 'Rekomendasi Menu';


    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'create',
            'update',
            'delete',
            'delete_any',
            'publish'
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        $availableIngredients = Ingredients::all()->pluck('name')->toArray();

        $user = Auth::user();

        // Retrieve allergies for the user and get the corresponding ingredient names
        $allergyIds = Allergies::where('user_id', $user->id)->pluck('masterdata_id')->toArray();
        $allergyIngredients = MasterData::whereIn('id', $allergyIds)->pluck('name')->toArray();

        // Display the user's allergies
        $allergiesDisplay = !empty($allergyIngredients) ? implode(', ', $allergyIngredients) : 'No allergies selected';

        $query = recipes::query();

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
            // You might want to return an empty result set or log an error
            $query->whereRaw('1 = 0'); // This will always evaluate to false
        }

        return $table
            ->heading('Recommended Recipe (Available Ingredient + Allergies)')
            ->headerActions([
                Tables\Actions\Action::make('showAllergies')
                    ->label('Your Allergies: [ ' . $allergiesDisplay.' ]')
                    ->color('secondary')
                    ->url('#'), // You can customize the URL or action if needed
            ])
            ->query($query)
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('Gambar')
                    ->width(200)
                    ->height(200)
                    ->disk('public'),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Menu')
                    ->searchable(),
                Tables\Columns\TextColumn::make('description')
                    ->label('Deskripsi')
                    ->searchable(),
                Tables\Columns\TextColumn::make('intruction')
                    ->label('Instruksi')    
                    ->searchable()
                    ->formatStateUsing(fn ($state) => nl2br(e($state)))
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
                // Example of how to filter ingredients based on availability in the Ingredients table
                Tables\Filters\SelectFilter::make('ingredient')
                    ->label('Available Ingredients')
                    ->options(function () {
                        return Ingredients::all()->pluck('name', 'id')->toArray();
                    })
                    ->query(function (Builder $query) {
                        // Fetch the list of available ingredients
                        $availableIngredients = Ingredients::all()->pluck('name')->toArray();

                        // Get the selected ingredient value from the request
                        $selectedIngredient = request()->input('filters')['ingredient'] ?? null;

                        // If a specific ingredient is selected, filter by that ingredient
                        if ($selectedIngredient) {
                            return $query->whereJsonContains('ingredient', $selectedIngredient);
                        }

                        // If no ingredient is selected, filter by any available ingredient
                        return $query->where(function ($query) use ($availableIngredients) {
                            foreach ($availableIngredients as $ingredient) {
                                // Check if the recipe's ingredient JSON contains any of the available ingredients
                                $query->orWhereJsonContains('ingredient', $ingredient);
                            }
                        });
                    }),
            ])
            ->actions([
                //Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    //Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListMenus::route('/'),
        ];
    }
}
