<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MenuResource\Pages;
use App\Models\recipes;
use App\Models\ingredients;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;


class RecipeResource extends Resource
{
    protected static ?string $model = recipes::class;

    protected static ?string $navigationIcon = 'http://localhost:8000/icons/Component-1-5.svg';

    protected static ?string $navigationLabel = 'Resep';

    public static function getLabel(): string
    {
        return 'Resep';
    }


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required(),
                Forms\Components\TextInput::make('description')
                    ->required(),
                Forms\Components\Textarea::make('instruction')
                    ->required()
                    ->rows(10)
                    ->cols(20),
                Forms\Components\TextInput::make('cooking_time')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('dificulty_level')
                    ->required(),
                Forms\Components\Select::make('ingredients')
                    ->label('Ingredients')
                    ->multiple()
                    ->options(options: ingredients::all()->pluck('name', 'id'))
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
                    ->label('Gambar')
                    ->width(200)
                    ->height(200)
                    ->disk('public'),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Menu')    
                    ->searchable(),
                Tables\Columns\TextColumn::make('description')
                    ->label('Deskripsi')
                    ->wrap()
                    ->limit(250)
                    ->searchable(),
                Tables\Columns\TextColumn::make('instruction')
                    ->label('Instruksi')    
                    ->searchable()
                    ->formatStateUsing(fn ($state) => nl2br(e($state)))
                    ->html()
                    ->wrap()
                    ->limit(250),
                Tables\Columns\TextColumn::make('cooking_time')
                    ->label('Waktu/menit')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('dificulty_level')
                    ->label('Kesulitan')
                    ->sortable(),   
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
            'index' => RecipeResource\Pages\ListRecipes::route('/'),
            'create' => RecipeResource\Pages\CreateRecipe::route('/create'),
            'edit' => RecipeResource\Pages\EditRecipe::route('/{record}/edit'),
        ];
    }
}