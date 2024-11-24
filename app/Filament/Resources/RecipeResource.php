<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MenuResource\Pages;
use App\Models\Ingredients;
use App\Models\recipes;
use App\Models\MasterData;
use App\Models\Menu;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Textarea;


class RecipeResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = Menu::class;

    protected static ?string $navigationIcon = 'recipe';

    protected static ?string $navigationLabel = 'Recipe';

    public static function getLabel(): string
    {
        return 'Resep';
    }

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


    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required(),
                Forms\Components\TextInput::make('description')
                    ->required(),
                Forms\Components\Textarea::make('intruction')
                    ->label('instruction')
                    ->required()
                    ->rows(10)
                    ->cols(20),
                Forms\Components\TextInput::make('cooking_time')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('diffcutly_level')
                    ->required()
                    ->label('Difficulty Level'),
                Forms\Components\Select::make('ingredients')
                    ->label('Ingredients')
                    ->multiple()
                    ->options(fn() => MasterData::pluck('name', 'id')->toArray())
                    ->searchable()
                    ->required(),
                Forms\Components\FileUpload::make('image')
                    ->image()
                    ->disk('public')
                    ->preserveFilenames()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->width(200)
                    ->height(200)
                    ->disk('public'),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('description')
                    ->searchable(),
                Tables\Columns\TextColumn::make('intruction')
                    ->label('Instruction')
                    ->searchable()
                    ->formatStateUsing(fn($state) => nl2br(e($state)))
                    ->html()
                    ->wrap()
                    ->limit(250),
                Tables\Columns\TextColumn::make('cooking_time')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('diffcutly_level')
                    ->label('Difficulty Level')
                    ->sortable(),
                Tables\Columns\TextColumn::make('ingredients')
                    ->formatStateUsing(function ($state) {
                        $ingredientIds = explode(',', $state);

                        if (is_array($ingredientIds)) {
                            $ingredientNames = MasterData::whereIn('id', $ingredientIds)->pluck('name')->toArray();
                            return implode(', ', $ingredientNames);
                        }

                        return '-';
                    })
                    ->wrap()
                    ->html(),

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
            'index' => Pages\ListRecipes::route('/'),
            'create' => Pages\CreateRecipe::route('/create'),
            'edit' => Pages\EditRecipe::route('/{record}/edit'),
        ];
    }
}
