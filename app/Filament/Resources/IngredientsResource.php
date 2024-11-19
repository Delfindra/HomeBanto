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

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Hidden::make('users_id')
                ->default(fn () => Auth::id()) // Menggunakan ID pengguna yang sedang login
                ->required(),

                Forms\Components\TextInput::make('name')
                    ->required()
                    ->label('Ingredient Name')
                    ->placeholder('Enter ingredient name'),
                    
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
                    ->label('Ingredient Name'),
                    
                Tables\Columns\TextColumn::make('purchase_date')
                    ->label('Purchase Date')
                    ->date(),
                    
                Tables\Columns\TextColumn::make('expiry_date')
                    ->label('Expiry Date')
                    ->date(),
                    
                Tables\Columns\TextColumn::make('created_by')
                    ->label('Created By')
                    ->getStateUsing(fn ($record) => $record->user->name ?? 'N/A'),
            ])
            ->filters([
                Tables\Filters\Filter::make('Owned by User')
                    ->query(fn (Builder $query) => $query->where('created_by', Auth::id())),
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
        return [
            // Define any relationships here if needed
        ];
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
