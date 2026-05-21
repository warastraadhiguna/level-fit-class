<?php

namespace App\Filament\Resources\ClassSessions\Pages;

use App\Filament\Resources\ClassSessions\ClassSessionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditClassSession extends EditRecord
{
    protected static string $resource = ClassSessionResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if ($branchStoreId = ClassSessionResource::getUserBranchStoreId()) {
            $data['branch_store_id'] = $branchStoreId;
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        // Redirect ke halaman index setelah create
        return $this->getResource()::getUrl('index');
    }
}
