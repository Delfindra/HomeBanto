<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MenuResource\Pages;
use App\Models\Ingredients;
use App\Models\Menu;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use App\Models\allergies;
use App\Models\MasterData;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class MenuResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = Menu::class;

    protected static ?string $navigationIcon = 'http://localhost:8000/icons/Component-1-4.svg';

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

        // Build the query to check for available ingredients
        if (!empty($availableIngredients)) {
            // Build the query to check for available ingredients
            $query->where(function ($subQuery) use ($availableIngredients) {
                foreach ($availableIngredients as $ingredient) {
                    $subQuery->orWhere('ingredient', 'like', '%' . $ingredient . '%');
                }
            });
        } else {
            // If there are no available ingredients, return an empty query
            $query->whereRaw('1 = 0'); // This will always evaluate to false
        }

        // Exclude recipes that contain allergens
        if (!empty($allergyIngredients)) {
            $query->where(function ($subQuery) use ($allergyIngredients) {
                foreach ($allergyIngredients as $allergen) {
                    $subQuery->where('ingredient', 'not like', '%' . $allergen . '%');
                }
            });
        }


        return $table
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
