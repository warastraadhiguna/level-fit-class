<?php

namespace App\Filament\Resources\ClassDetails\Pages;

use App\Filament\Resources\ClassDetails\ClassDetailResource;
use App\Filament\Resources\ClassDetails\Tables\ClassDetailsTable;
use App\Models\ClassSchedule;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;

class ClassDetailBookings extends ManageRelatedRecords
{
    protected static string $resource = ClassDetailResource::class;

    protected static string $relationship = 'classDetails';

    protected static ?string $relationshipTitle = 'Bookings';

    protected static bool $shouldSkipAuthorization = true;

    public function getTitle(): string | Htmlable
    {
        $record = $this->getRecord();

        if (! $record instanceof ClassSchedule) {
            return 'Class Bookings';
        }

        $date = $record->class_date
            ? Carbon::parse($record->class_date)->format('d/m/Y')
            : '-';

        return "{$record->name} - {$date}";
    }

    public function table(Table $table): Table
    {
        return ClassDetailsTable::configure(
            $table,
            showClassScheduleColumn: false,
            showClassScheduleFilter: false,
            showDateFilter: false,
        );
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('backToClasses')
                ->label('Daftar Class')
                ->icon(Heroicon::OutlinedArrowLeft)
                ->url(ClassDetailResource::getUrl('index')),
        ];
    }
}
