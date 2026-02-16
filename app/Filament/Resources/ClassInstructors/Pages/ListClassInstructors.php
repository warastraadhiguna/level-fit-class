<?php

namespace App\Filament\Resources\ClassInstructors\Pages;

use App\Filament\Resources\ClassInstructors\ClassInstructorResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListClassInstructors extends ListRecords
{
    protected static string $resource = ClassInstructorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
