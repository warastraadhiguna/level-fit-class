<?php

namespace App\Filament\Resources\ClassInstructors\Pages;

use App\Filament\Resources\ClassInstructors\ClassInstructorResource;
use Filament\Resources\Pages\CreateRecord;

class CreateClassInstructor extends CreateRecord
{
    protected static string $resource = ClassInstructorResource::class;
    protected function getFormActions(): array
    {
        return [
          $this->getCreateFormAction()  // Tombol Create default
        ->label('Create')
        ->color('primary'),
          $this->getCancelFormAction()  // Tombol Cancel default
        ->label('Batal')
            ->color('gray'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        // Redirect ke halaman index setelah create
        return $this->getResource()::getUrl('index');
    }    
}