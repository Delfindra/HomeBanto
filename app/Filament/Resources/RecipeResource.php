<?php

namespace App\Filament\Resources;

use App\Models\MasterData;
use App\Models\recipes;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;


class RecipeResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = recipes::class;

    protected static ?string $navigationIcon = 'http://localhost:8000/icons/Component-1-5.svg';

    protected static ?string $navigationLabel = 'Resep';

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
                    ->numeric()
                    ->suffix(' menit'),
                Forms\Components\TextInput::make('dificulty_level')
                    ->required(),
                Forms\Components\Select::make('ingredient')
                    ->label('Ingredients')
                    ->multiple()
                    ->options(options: MasterData::all()->pluck('name', 'name'))
                    ->searchable()
                    ->required()
                    ->default(function ($record) {
                        // Check if the field has a value and decode it into an array
                        return $record && isset($record->preferences) ? json_decode($record->preferences, true) : [];
                    }),
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
                    ->width(150)
                    ->height(150)
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
                    ->formatStateUsing(fn($state) => nl2br(e($state)))
                    ->html()
                    ->wrap()
                    ->limit(250),
                Tables\Columns\TextColumn::make('cooking_time')
                    ->label('Waktu')
                    ->numeric()
                    ->suffix(' menit')
                    ->sortable(),
                Tables\Columns\TextColumn::make('dificulty_level')
                    ->label('Kesulitan')
                    ->sortable(),
                Tables\Columns\TextColumn::make('ingredient')
                    ->label('Ingredients')
                    ->formatStateUsing(fn($state) => nl2br(str_replace(',', "\n", $state)))
                    ->html()
                    ->wrap()
                    ->limit(250),
                // Tables\Columns\TextColumn::make('created_at')
                //     ->dateTime()
                //     ->sortable()
                //     ->toggleable(isToggledHiddenByDefault: true),
                // Tables\Columns\TextColumn::make('updated_at')
                //     ->dateTime()
                //     ->sortable()
                //     ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
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
