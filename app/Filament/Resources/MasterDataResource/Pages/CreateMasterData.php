<?php

namespace App\Filament\Resources\MasterDataResource\Pages;

use App\Filament\Resources\MasterDataResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateMasterData extends CreateRecord
{
    protected static string $resource = MasterDataResource::class;

    protected function getRedirectUrl(): string
    {
        // Redirect back to the index page after editing an ingredient
        return $this->getResource()::getUrl('index');
    }
}
