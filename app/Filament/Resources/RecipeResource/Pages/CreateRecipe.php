<?php

namespace App\Filament\Resources\MenuResource\Pages;

use App\Filament\Resources\RecipeResource;
use Filament\Resources\Pages\CreateRecord;

class CreateRecipe extends CreateRecord
{   
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    
    protected static string $resource = RecipeResource::class;

    public function getTitle(): string
    {
        return 'Tambah Resep';
    }
}
