<?php

namespace App\Filament\Resources\MasterDataResource\Pages;

use App\Filament\Resources\MasterDataResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMasterData extends EditRecord
{
    protected static string $resource = MasterDataResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
