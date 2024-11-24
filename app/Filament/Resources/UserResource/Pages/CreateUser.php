<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected function afterSave(): void
    {
        // Redirect back to the list page
        $this->redirect(static::getResource()::getUrl('index'));
    }
    
    protected static string $resource = UserResource::class;
}
