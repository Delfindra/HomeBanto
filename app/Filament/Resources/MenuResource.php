<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MenuResource\Pages;
use App\Models\Diet;
use App\Models\Menu;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use App\Models\allergies;
use App\Models\MasterData;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
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

    public static function table(Table $table): Table
    {
        $user = Auth::user();

        // Retrieve allergies for the user and get the corresponding ingredient names
        $allergyIds = Allergies::where('user_id', $user->id)->pluck('masterdata_id')->toArray();
        $allergyIngredients = MasterData::whereIn('id', $allergyIds)->pluck('name')->toArray();

        // Retrieve diet name
        $dietId = $user->diet_id;
        $dietType = Diet::where('id', $dietId)->pluck('name')->first();

        // Display the user's allergies
        $allergiesDisplay = !empty($allergyIngredients) ? implode(', ', $allergyIngredients) : 'No allergies selected';

        return $table
            ->heading('Recommended Recipe')
            ->headerActions([
                Tables\Actions\Action::make('showAllergies')
                    ->label('Your Allergies: [ ' . $allergiesDisplay.' ]')
                    ->color('secondary'),
                Tables\Actions\Action::make('showDiet')
                    ->label('Your Diet: [ ' .$dietType. ' ]')
                    ->color('secodary'),
            ])
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('Gambar')
                    ->width(100)
                    ->height(100)
                    ->disk('public'),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Menu')
                    ->searchable(),
                Tables\Columns\TextColumn::make('description')
                    ->label('Deskripsi')
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
        ];
    }
}
