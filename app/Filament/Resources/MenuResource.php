<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MenuResource\Pages;
use App\Models\recipes;
use App\Models\Ingredients;
use App\Models\Menu;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

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
        return $table
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
                    ->formatStateUsing(fn($state) => nl2br(str_replace(',', "\n", $state)))
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
                        // Get the selected filter value using request()->input()
                        $selectedIngredient = request()->input('filters')['ingredient'] ?? null;
    
                        // If there's a selected ingredient, filter by it
                        if ($selectedIngredient) {
                            return $query->whereJsonContains('ingredient', $selectedIngredient);
                        }
    
                        return $query;
                    }),
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
            'edit' => Pages\EditMenu::route('/{record}/edit'),
        ];
    }
}
