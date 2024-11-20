<?php

namespace App\Filament\Resources\DietResource\Pages;

use App\Filament\Resources\DietResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateDiet extends CreateRecord
{
    protected static string $resource = DietResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    
}
