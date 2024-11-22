<?php

namespace App\Filament\Resources\IngredientsResource\Pages;

use App\Filament\Resources\IngredientsResource;
use Filament\Resources\Pages\CreateRecord;

class CreateIngredients extends CreateRecord
{
    protected static string $resource = IngredientsResource::class;

    protected function getRedirectUrl(): string
    {
        // Redirect back to the index page after creating a new ingredient
        return $this->getResource()::getUrl('index');
    }

}

