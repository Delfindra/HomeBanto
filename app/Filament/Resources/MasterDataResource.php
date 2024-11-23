<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MasterDataResource\Pages;
use App\Filament\Resources\MasterDataResource\RelationManagers;
use App\Models\MasterData;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class MasterDataResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = MasterData::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Admin';

    protected static ?string $modelLabel = 'Ingredient Datas';

    protected static ?string $navigationLabel = 'Ingredient Datas';

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

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Ingredient Name')
                    ->required()
                    ->maxLength(255),
                
                Forms\Components\Select::make('category')
                    ->required()
                    ->label('Food Category')
                    ->options([
                        'fruit' => 'Fruit',
                        'vegetable' => 'Vegetable',
                        'livestock' => 'Livestock',
                        'snack' => 'Snack',
                        'beverage' => 'Beverage',
                        'dry food' => 'Dry Food',
                        'staple_food' => 'Staple Food',
                        'seafood' => 'Seafood',
                        'seasonings' => 'Seasonings',
                    ])
                    ->reactive() // Makes the form field react to changes
                    ->afterStateUpdated(fn (callable $set) => $set('quantity', null)),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Ingredient')
                    ->searchable(),
                Tables\Columns\TextColumn::make('category')
                    ->label('category')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListMasterData::route('/'),
            'create' => Pages\CreateMasterData::route('/create'),
            'edit' => Pages\EditMasterData::route('/{record}/edit'),
        ];
    }
}
