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

        // Add conditions for each available ingredient
        foreach ($availableIngredients as $ingredient) {
            $query->orWhere('ingredient', 'like', '%' . $ingredient . '%');
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
