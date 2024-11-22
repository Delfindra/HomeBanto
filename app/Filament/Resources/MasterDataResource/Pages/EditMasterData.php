<?php

namespace App\Filament\Resources\MasterDataResource\Pages;

use App\Filament\Resources\MasterDataResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMasterData extends EditRecord
{
    protected static string $resource = MasterDataResource::class;

    protected function getRedirectUrl(): string
    {
        // Redirect back to the index page after editing an ingredient
        return $this->getResource()::getUrl('index');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
