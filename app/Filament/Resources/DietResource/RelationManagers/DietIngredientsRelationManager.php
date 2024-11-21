<?php

namespace App\Filament\Resources\DietResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DietIngredientsRelationManager extends RelationManager
{
    protected static string $relationship = 'dietIngredients';
    
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('masterdata_id')
                ->relationship('masterData', 'name')
                ->label('Ingredient')
                ->native(false)
                ->required(),
                Forms\Components\Select::make('diet_id')
                ->disabled()
                ->default(fn ($record) => $this->getOwnerRecord()->id)
                ->relationship('diet', 'name')
                ->native(false)
                ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('masterData.name')
                ->label('Ingredients'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                ->label('Add Ingredients')
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
