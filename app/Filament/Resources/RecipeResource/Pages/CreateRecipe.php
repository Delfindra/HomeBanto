<?php

namespace App\Filament\Resources\RecipeResource\Pages;

use App\Filament\Resources\RecipeResource;
use Filament\Resources\Pages\CreateRecord;

class CreateRecipe extends CreateRecord
{
    protected static string $resource = RecipeResource::class;

    protected function getRedirectUrl(): string
    {
        // Redirect back to the index page after editing an ingredient
        return $this->getResource()::getUrl('index');
    }
}
