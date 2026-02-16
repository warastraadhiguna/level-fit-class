<?php

namespace App\Filament\Resources\ClassDetails\Pages;

use App\Filament\Resources\ClassDetails\ClassDetailResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditClassDetail extends EditRecord
{
    protected static string $resource = ClassDetailResource::class;

}