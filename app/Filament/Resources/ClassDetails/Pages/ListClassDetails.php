<?php

namespace App\Filament\Resources\ClassDetails\Pages;

use App\Filament\Resources\ClassDetails\ClassDetailResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListClassDetails extends ListRecords
{
    protected static string $resource = ClassDetailResource::class;

}