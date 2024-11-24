<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MenuResource\Pages;
use App\Models\Ingredients;
use App\Models\Menu;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use App\Models\allergies;
use App\Models\MasterData;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class MenuResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = Menu::class;

    protected static ?string $navigationIcon = 'menu';

    protected static ?string $navigationLabel = 'Rekomendasi Menu';


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

    public static function canCreate(): bool
    {
        return false;
    }


    // public static function form(Form $form): Form
    // {
    //     return $form
    //         ->schema([
    //             Forms\Components\TextInput::make('name')
    //                 ->required(),
    //             Forms\Components\TextInput::make('description')
    //                 ->required(),
    //             Forms\Components\Textarea::make('intruction')
    //                 ->required()
    //                 ->rows(10)
    //                 ->cols(20),
    //             Forms\Components\TextInput::make('cooking_time')
    //                 ->required()
    //                 ->numeric(),
    //             Forms\Components\TextInput::make('diffcutly_level')
    //                 ->required(),
    //             Forms\Components\Select::make('ingredients')
    //                 ->label('Ingredients')
    //                 ->multiple()
    //                 ->options(options: ingredients::all()->pluck('name', 'id'))
    //                 ->searchable()
    //                 ->required(),
    //             Forms\Components\FileUpload::make('image')
    //                 ->image()
    //                 ->disk('public')
    //                 ->preserveFilenames()
    //                 ->required(),
    //         ]);
    // }

    public static function table(Table $table): Table
    {
        $availableIngredients = Ingredients::all()->pluck('name')->toArray();

        $user = Auth::user();

        // Retrieve allergies for the user and get the corresponding ingredient names
        $allergyIds = Allergies::where('user_id', $user->id)->pluck('masterdata_id')->toArray();
        $allergyIngredients = MasterData::whereIn('id', $allergyIds)->pluck('name')->toArray();

        // Display the user's allergies
        $allergiesDisplay = !empty($allergyIngredients) ? implode(', ', $allergyIngredients) : 'No allergies selected';

        $query = recipes::query();

        // Build the query to check for available ingredients
        if (!empty($availableIngredients)) {
            // Build the query to check for available ingredients
            $query->where(function ($subQuery) use ($availableIngredients) {
                foreach ($availableIngredients as $ingredient) {
                    $subQuery->orWhere('ingredient', 'like', '%' . $ingredient . '%');
                }
            });
        } else {
            // If there are no available ingredients, return an empty query
            $query->whereRaw('1 = 0'); // This will always evaluate to false
        }

        // Exclude recipes that contain allergens
        if (!empty($allergyIngredients)) {
            $query->where(function ($subQuery) use ($allergyIngredients) {
                foreach ($allergyIngredients as $allergen) {
                    $subQuery->where('ingredient', 'not like', '%' . $allergen . '%');
                }
            });
        }


        return $table
            ->headerActions([
                Tables\Actions\Action::make('showAllergies')
                    ->label('Your Allergies: [ ' . $allergiesDisplay.' ]')
                    ->color('secondary')
                    ->url('#'), // You can customize the URL or action if needed
            ])
            ->query($query)
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
                    ->searchable(),
                Tables\Columns\TextColumn::make('intruction')
                    ->label('Instruksi')    
                    ->searchable()
                    ->formatStateUsing(fn ($state) => nl2br(e($state)))
                    ->html()
                    ->wrap()
                    ->limit(250)
                    ->searchable(),
                Tables\Columns\TextColumn::make('ingredient')
                    ->label('Bahan')
                    
                    ->html()
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                //Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    //Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListMenus::route('/'),
            //'create' => Pages\CreateMenu::route('/create'),
            //'edit' => Pages\EditMenu::route('/{record}/edit'),
        ];
    }
}
