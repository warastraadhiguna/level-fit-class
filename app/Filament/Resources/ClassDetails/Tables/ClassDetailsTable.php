<?php

namespace App\Filament\Resources\ClassDetails\Tables;

use App\Models\ClassSchedule;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ClassDetailsTable
{
    public static function configure(
        Table $table,
        bool $showClassScheduleColumn = true,
        bool $showClassScheduleFilter = true,
        bool $showDateFilter = true,
    ): Table
    {
        $columns = [];

        if ($showClassScheduleColumn) {
            $columns[] = TextColumn::make('classSchedule.name')
                ->label('Class Schedule')
                ->formatStateUsing(function ($state, $record) {
                    $sc = $record->classSchedule;
                    if (! $sc) return '-';

                    // hari dari class_date (paling benar)
                    $dayName = $sc->class_date
                        ? Carbon::parse($sc->class_date)->locale('id')->isoFormat('dddd')
                        : '-';

                    // waktu: ambil dari schedule dulu, kalau kosong ambil dari classSession
                    $startRaw = $sc->time_start ?? $sc->classSession?->time_start;
                    $endRaw   = $sc->time_end   ?? $sc->classSession?->time_end;

                    $start = $startRaw ? Carbon::parse($startRaw)->format('H:i') : '--:--';
                    $end   = $endRaw   ? Carbon::parse($endRaw)->format('H:i') : '--:--';

                    return "{$sc->name} ({$dayName}, {$start}–{$end})";
                })
                ->searchable();
        }

        $columns = [
            ...$columns,
            TextColumn::make('display_name')
                ->label('Name')
                ->state(fn ($record) => $record->member_id
                    ? ($record->member?->full_name ?? '-')
                    : ($record->name ?? '-')
                )
                ->searchable(query: function ($query, string $search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhereHas('member', fn ($q) => $q->where('full_name', 'like', "%{$search}%"));
                }),
            TextColumn::make('display_phone')
                ->label('Phone')
                ->state(fn ($record) => $record->member_id
                    ? ($record->member?->phone_number ?? '-')
                    : ($record->phone ?? '-')
                )
                ->searchable(query: function ($query, string $search) {
                    $query->where('phone', 'like', "%{$search}%")
                        ->orWhereHas('member', fn ($q) => $q->where('phone_number', 'like', "%{$search}%"));
                }),
            TextColumn::make('display_email')
                ->label('Email')
                ->state(fn ($record) => $record->member_id
                    ? ($record->member?->email ?? '-')
                    : ($record->email ?? '-')
                )
                ->searchable(query: function ($query, string $search) {
                    $query->where('email', 'like', "%{$search}%")
                        ->orWhereHas('member', fn ($q) => $q->where('email', 'like', "%{$search}%"));
                }),
            TextColumn::make('canceled_at')
                ->label('Canceled At')
                ->dateTime()
                ->placeholder('-'),
            ToggleColumn::make('canceled_toggle')
                ->label('Canceled')
                ->getStateUsing(fn ($record) => ! is_null($record->canceled_at)) // ON jika canceled_at ada
                ->updateStateUsing(function ($record, $state) {
                    $record->update([
                        'canceled_at' => $state ? now() : null,
                    ]);
                }),
            ToggleColumn::make('status')
                ->label('Attendance')
                ->onIcon('heroicon-m-check')
                ->offIcon('heroicon-m-x-mark')
                ->getStateUsing(fn ($record): bool => (int) $record->status === 1)
                ->updateStateUsing(function ($record, bool $state): void {
                    $record->update([
                        'status' => $state ? 1 : 0,
                    ]);
                }),
        ];

        $filters = [];

        if ($showClassScheduleFilter) {
            $filters[] = SelectFilter::make('class_schedule_id')
                ->label('Class Schedule')
                ->searchable()
                ->preload()
                ->options(fn () => ClassSchedule::query()
                    ->with(['branchStore', 'classSession'])
                    ->orderByDesc('class_date')
                    ->orderBy('time_start')
                    ->limit(200)
                    ->get()
                    ->mapWithKeys(function (ClassSchedule $schedule): array {
                        $dayName = $schedule->class_date
                            ? Carbon::parse($schedule->class_date)->locale('id')->isoFormat('dddd')
                            : '-';

                        $date = $schedule->class_date
                            ? Carbon::parse($schedule->class_date)->format('d/m/Y')
                            : '-';

                        $start = $schedule->time_start
                            ? Carbon::parse($schedule->time_start)->format('H:i')
                            : '--:--';

                        $end = $schedule->time_end
                            ? Carbon::parse($schedule->time_end)->format('H:i')
                            : '--:--';

                        $branch = $schedule->branchStore?->name ?? '-';
                        $status = $schedule->is_active ? 'Aktif' : 'Libur';

                        return [
                            $schedule->id => "{$schedule->name} - {$dayName}, {$date} {$start}-{$end} ({$branch}) [{$status}]",
                        ];
                    }));
        }

        if ($showDateFilter) {
            $filters[] = Filter::make('class_date_range')
                ->label('Tanggal')
                ->form([
                    DatePicker::make('start')->default(now()->startOfWeek()),
                    DatePicker::make('end')->default(now()->endOfWeek()),
                ])
                ->query(function (Builder $query, array $data): Builder {
                    return $query
                        ->when($data['start'] ?? null, fn (Builder $q, $date) =>
                            $q->whereHas('classSchedule', fn (Builder $sq) =>
                                $sq->whereDate('class_date', '>=', $date)
                            )
                        )
                        ->when($data['end'] ?? null, fn (Builder $q, $date) =>
                            $q->whereHas('classSchedule', fn (Builder $sq) =>
                                $sq->whereDate('class_date', '<=', $date)
                            )
                        );
                })
                ->indicateUsing(function (array $data): array {
                    $indicators = [];

                    if (!empty($data['start'])) {
                        $indicators[] = 'Start: ' . Carbon::parse($data['start'])->format('d/m/Y');
                    }
                    if (!empty($data['end'])) {
                        $indicators[] = 'End: ' . Carbon::parse($data['end'])->format('d/m/Y');
                    }

                    return $indicators;
                });
        }

        return $table
            ->recordUrl(fn ($record) => null)        
            ->modifyQueryUsing(fn (Builder $query) => $query
                ->with(['classSchedule.classSession', 'member'])
                ->orderBy(
                    ClassSchedule::select('name')
                        ->whereColumn('class_schedules.id', 'class_details.class_schedule_id'),
                    'asc'
                )           
            )
            ->columns($columns)
            ->filters($filters)
            ->recordActions([
                // EditAction::make(),
            ])
            ->toolbarActions([
            ]);
    }
}
