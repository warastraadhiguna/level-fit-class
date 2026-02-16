<?php

namespace App\Filament\Resources\ClassInstructors\Pages;

use App\Filament\Resources\ClassInstructors\ClassInstructorResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditClassInstructor extends EditRecord
{
    protected static string $resource = ClassInstructorResource::class;

    protected function getRedirectUrl(): string
    {
        // Redirect ke halaman index setelah create
        return $this->getResource()::getUrl('index');
    }
}