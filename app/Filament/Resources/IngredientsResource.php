<?php

namespace App\Filament\Resources;

use App\Filament\Resources\IngredientsResource\Pages;
use App\Models\Ingredients;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class IngredientsResource extends Resource
{
    protected static ?string $model = Ingredients::class;

    protected static ?string $navigationIcon = 'http://localhost:8000/icons/Component-1-3.svg';

    protected static ?string $navigationLabel = 'Inventaris Kulkas';
    

    

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

                Forms\Components\TextInput::make('name')
                    ->required()
                    ->label('Ingredient Name')
                    ->placeholder('Enter ingredient name'),

                Forms\Components\TextInput::make('quantity')
                    ->required()
                    ->label('Quantity')
                    ->placeholder('Enter quantity'),

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
                    ->label('Nama Bahan'),

                Tables\Columns\TextColumn::make('quantity')
                    ->label('Stok'),

                Tables\Columns\TextColumn::make('purchase_date')
                    ->label('Tanggal Pembelian')
                    ->date(),

                Tables\Columns\TextColumn::make('expiry_date')
                    ->label('Tanggal Kadaluarsa')
                    ->date(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Edit') ,
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

