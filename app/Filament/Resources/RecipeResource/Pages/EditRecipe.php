<?php

namespace App\Filament\Resources\MenuResource\Pages;

use App\Filament\Resources\RecipeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRecipe extends EditRecord
{
    protected function afterSave(): void
    {
        // Redirect back to the list page
        $this->redirect(static::getResource()::getUrl('index'));
    }
    
    protected static string $resource = RecipeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
