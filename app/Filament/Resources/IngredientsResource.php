<?php

namespace App\Filament\Resources;

use App\Filament\Resources\IngredientsResource\Pages;
use App\Models\Ingredients;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\MasterData;
use App\Notifications\FoodExpirationNotification;

class IngredientsResource extends Resource
{
    protected static ?string $model = Ingredients::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getEloquentQuery(): Builder
    {
        // Filter data hanya untuk user yang sedang login
        return parent::getEloquentQuery()->where('users_id', Auth::id());
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Hidden::make('users_id')
                    ->default(fn () => Auth::id()) // Set default ID pengguna yang sedang login
                    ->required(),

                Forms\Components\Select::make('name')
                    ->required()
                    ->label('Ingredient Name')
                    ->options(MasterData::pluck('name', 'name')) // Mengambil nama sebagai nilai dan id sebagai kunci
                    ->searchable() // Opsional: Membuat dropdown dapat dicari
                    ->placeholder('Select ingredient name'),

                Forms\Components\Select::make('category')
                    ->required()
                    ->label('Food Category')
                    ->options(MasterData::pluck('category', 'category')) // Mengambil nama sebagai nilai
                    ->searchable() // Opsional: Membuat dropdown dapat dicari
                    ->placeholder('Select category')
                    ->reactive() // Makes the form field react to changes
                    ->afterStateUpdated(fn (callable $set) => $set('quantity', null)), // Reset quantity when category changes

                Forms\Components\Select::make('category')
                    ->required()
                    ->label('Food Category')
                    ->options([
                        'fruit' => 'Fruit',
                        'vegetable' => 'Vegetable',
                        'livestock' => 'Livestock',
                        'snack' => 'Snack',
                        'beverage' => 'Beverage',
                        'dry_food' => 'Dry Food',
                        'staple_food' => 'Staple Food',
                        'seafood' => 'Seafood',
                        'seasonings' => 'Seasonings',
                    ])
                    ->reactive() // Makes the form field react to changes
                    ->afterStateUpdated(fn (callable $set) => $set('quantity', null)), // Reset quantity when category changes

                Forms\Components\TextInput::make('quantity')
                    ->required()
                    ->label('Quantity')
                    ->placeholder('Enter quantity')
                    ->suffix(fn ($get) => match ($get('category')) {
                        'fruit' => 'kg',
                        'vegetable' => 'kg',
                        'livestock' => 'kg',
                        'snack' => 'pack',
                        'beverage' => 'liters',
                        'dry_food' => 'kg',
                        'staple_food' => 'kg',
                        'seafood' => 'gr',
                        'seasonings' => 'gr',
                        default => 'units',
                    }),

                Forms\Components\DatePicker::make('purchase_date')
                    ->required()
                    ->maxDate(now())
                    ->label('Purchase Date'),

                Forms\Components\DatePicker::make('expiry_date')
                    ->required()
                    ->label('Expiry Date')
                    ->after('purchase_date')
                    ->placeholder('Enter expiry date'),
                

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Ingredient Name')
                    ->searchable(),

                Tables\Columns\TextColumn::make('category')
                    ->sortable()
                    ->searchable()
                    ->label('Food Category'),

                Tables\Columns\TextColumn::make('quantity')
                    ->searchable()
                    ->label('Quantity')
                    ->formatStateUsing(fn ($record) => match ($record->category) {
                        'fruit' => $record->quantity . ' kg',
                        'vegetable' => $record->quantity . ' kg',
                        'livestock' => $record->quantity . ' kg',
                        'snack' =>  $record->quantity .' pack',
                        'beverage' => $record->quantity . ' liters',
                        'dry_food' => $record->quantity .' kg',
                        'staple_food' => $record->quantity .' kg',
                        'seafood' => $record->quantity .' gr',
                        'seasonings' => $record->quantity . ' gr',
                        default => $record->quantity .' units',
                    }),

                Tables\Columns\TextColumn::make('purchase_date')
                    ->sortable()
                    ->searchable()
                    ->label('Purchase Date')
                    ->date(),

                Tables\Columns\TextColumn::make('expiry_date')
                    ->sortable()
                    ->searchable()
                    ->label('Expiry Date')
                    ->date(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->sortable()
                    ->searchable()
                
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
    
    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListIngredients::route('/'),
            'create' => Pages\CreateIngredients::route('/create'),
            'edit' => Pages\EditIngredients::route('/{record}/edit'),
        ];
    }
}

