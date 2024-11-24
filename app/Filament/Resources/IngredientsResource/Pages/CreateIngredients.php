<?php

namespace App\Filament\Resources\IngredientsResource\Pages;

use App\Filament\Resources\IngredientsResource;
use Filament\Resources\Pages\CreateRecord;
use App\Models\MasterData;

class CreateIngredients extends CreateRecord
{
    protected function afterSave(): void
    {
        // Redirect back to the list page
        $this->redirect(static::getResource()::getUrl('index'));
    }

    protected static string $resource = IngredientsResource::class;

}

