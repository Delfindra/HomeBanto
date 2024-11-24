<?php

namespace App\Filament\Resources\IngredientsResource\Pages;

use App\Filament\Resources\IngredientsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditIngredients extends EditRecord
{
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    
    protected static string $resource = IngredientsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
