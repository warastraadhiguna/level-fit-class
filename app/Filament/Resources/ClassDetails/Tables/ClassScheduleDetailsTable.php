<?php

namespace App\Filament\Resources\ClassDetails\Tables;

use App\Filament\Resources\ClassDetails\ClassDetailResource;
use App\Models\ClassSchedule;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ClassScheduleDetailsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->recordUrl(fn (ClassSchedule $record): string => ClassDetailResource::getUrl('bookings', ['record' => $record]))
            ->modifyQueryUsing(fn (Builder $query) => $query
                ->with(['branchStore', 'classInstructor', 'classSession'])
                ->withCount([
                    'classDetails as booked_count' => fn (Builder $query) => $query->whereNull('canceled_at'),
                    'classDetails as canceled_count' => fn (Builder $query) => $query->whereNotNull('canceled_at'),
                    'classDetails as attendance_count' => fn (Builder $query) => $query
                        ->whereNull('canceled_at')
                        ->where('status', 1),
                ])
                ->orderByDesc('class_date')
                ->orderBy('time_start'))
            ->columns([
                TextColumn::make('name')
                    ->label('Class')
                    ->description(function (ClassSchedule $record): string {
                        $dayName = $record->class_date
                            ? Carbon::parse($record->class_date)->locale('id')->isoFormat('dddd')
                            : '-';

                        $date = $record->class_date
                            ? Carbon::parse($record->class_date)->format('d/m/Y')
                            : '-';

                        $start = $record->time_start
                            ? Carbon::parse($record->time_start)->format('H:i')
                            : '--:--';

                        $end = $record->time_end
                            ? Carbon::parse($record->time_end)->format('H:i')
                            : '--:--';

                        return "{$dayName}, {$date} {$start}-{$end}";
                    })
                    ->searchable()
                    ->sortable(),
                TextColumn::make('branchStore.name')
                    ->label('Branch')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('classInstructor.full_name')
                    ->label('Instructor')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('booked_count')
                    ->label('Booked')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('attendance_count')
                    ->label('Attendance')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('canceled_count')
                    ->label('Canceled')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('capacity')
                    ->label('Capacity')
                    ->numeric()
                    ->sortable(),
                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->sortable(),
            ])
            ->filters([
                Filter::make('class_date_range')
                    ->label('Tanggal')
                    ->form([
                        DatePicker::make('start')->default(now()->startOfWeek()),
                        DatePicker::make('end')->default(now()->endOfWeek()),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['start'] ?? null, fn (Builder $query, $date) => $query->whereDate('class_date', '>=', $date))
                            ->when($data['end'] ?? null, fn (Builder $query, $date) => $query->whereDate('class_date', '<=', $date));
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if (! empty($data['start'])) {
                            $indicators[] = 'Start: ' . Carbon::parse($data['start'])->format('d/m/Y');
                        }

                        if (! empty($data['end'])) {
                            $indicators[] = 'End: ' . Carbon::parse($data['end'])->format('d/m/Y');
                        }

                        return $indicators;
                    }),
            ])
            ->recordActions([
                Action::make('viewBookings')
                    ->label('Lihat Booking')
                    ->icon(Heroicon::OutlinedEye)
                    ->url(fn (ClassSchedule $record): string => ClassDetailResource::getUrl('bookings', ['record' => $record])),
            ])
            ->toolbarActions([]);
    }
}
