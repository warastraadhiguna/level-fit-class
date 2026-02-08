<?php

namespace App\Filament\Resources\BranchStores\Pages;

use App\Filament\Resources\BranchStores\BranchStoreResource;
use Filament\Resources\Pages\EditRecord;

class EditBranchStore extends EditRecord
{
    protected static string $resource = BranchStoreResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }    
}