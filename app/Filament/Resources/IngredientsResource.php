<?php

namespace App\Filament\Resources;

use App\Filament\Resources\IngredientsResource\Pages;
use App\Models\Ingredients;
use App\Models\User;
use App\Models\MasterData;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Notifications\FoodExpirationNotification;


class IngredientsResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = Ingredients::class;

    protected static ?string $navigationIcon = 'http://localhost:8000/icons/Component-1-3.svg';

    protected static ?string $navigationLabel = 'Inventaris Kulkas';

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

    public static function getLabel(): string
    {
        return 'Inventaris Kulkas';
    }


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
                    ->default(fn() => Auth::id()) // Set default ID pengguna yang sedang login
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
                        'meat' => 'Meat',
                        'snack' => 'Snack',
                        'beverage' => 'Beverage',
                        'dry_food' => 'Dry Food',
                        'staple_food' => 'Staple Food',
                        'seafood' => 'Seafood',
                        'seasonings' => 'Seasonings',
                    ])
                    ->reactive() // Makes the form field react to changes
                    ->afterStateUpdated(fn(callable $set) => $set('quantity', null)), // Reset quantity when category changes

                Forms\Components\TextInput::make('quantity')
                    ->required()
                    ->label('Quantity')
                    ->placeholder('Enter quantity')
                    ->suffix(fn($get) => match ($get('category')) {
                        'fruit' => 'pcs',
                        'vegetable' => 'kg',
                        'livestock' => 'kg',
                        'snack' => 'pack',
                        'beverage' => 'liters',
                        'dry_food' => 'kg',
                        'staple_food' => 'kg',
                        'seafood' => 'gr',
                        'seasonings' => 'gr',
                        'meat' => 'kg',
                        'beverage' => 'liters',
                        'seasonings' => 'g',
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

                Tables\Columns\ImageColumn::make('ingredient_image')  // Custom column name
                ->label('Image')
                    ->getStateUsing(function ($record) {
                        // Get the image URL from the MasterData model based on the ingredient name
                        $ingredient = MasterData::where('name', $record->name)->first();
                        return $ingredient ? $ingredient->image : null;  // Fetch image from 'image' field
                    })
                    ->width(50)
                    ->height(50),

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
                        'seasonings' => $record->quantity . ' g',
                        'meat' => $record->quantity . ' kg',
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
                    ->label('Nama Bahan'),

                Tables\Columns\TextColumn::make('quantity')
                    ->label('Stok'),

                Tables\Columns\TextColumn::make('purchase_date')
                    ->label('Tanggal Pembelian')
                    ->date(),

                Tables\Columns\TextColumn::make('expiry_date')
                    ->label('Tanggal Kadaluarsa')
                    ->date(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->getStateUsing(function ($record) {
                        $currentDate = Carbon::now();
                        $expiryDate = Carbon::parse($record->expiry_date);
                        $daysLeft = intval($currentDate->diffInDays($expiryDate, false)); // Cast to integer
                
                        if ($daysLeft > 3) {
                            return "{$daysLeft} " . ($daysLeft === 1 ? 'day left' : 'days left') . " (Fresh)";
                        } elseif ($daysLeft > 0) {
                            return "{$daysLeft} " . ($daysLeft === 1 ? 'day left' : 'days left') . " (Nearly Expired)";
                        } elseif ($daysLeft === 0) {
                            return "Expires Tommorow";
                        } else {
                            return "Expired (" . abs($daysLeft) . " " . (abs($daysLeft) === 1 ? 'day ago' : 'days ago') . ")";
                        }
                    })
                    ->sortable()
                    ->searchable()

            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Edit'),
                Tables\Actions\DeleteAction::make()
                    ->label('Hapus')
                    ->modalHeading('Hapus Bahan')
                    ->modalSubheading('Apakah anda yakin ingin menghapus bahan?'),
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



