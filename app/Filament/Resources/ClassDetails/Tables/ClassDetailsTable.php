<?php

namespace App\Filament\Resources\ClassDetails\Tables;

use App\Models\ClassSchedule;
use Carbon\Carbon;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ClassDetailsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->recordUrl(fn ($record) => null)        
            ->modifyQueryUsing(fn (Builder $query) => $query
                ->with(['classSchedule.classSession', 'member'])
                ->orderBy(
                    ClassSchedule::select('name')
                        ->whereColumn('class_schedules.id', 'class_details.class_schedule_id'),
                    'asc'
                )
                ->whereHas('classSchedule', fn (Builder $sq) =>
                    $sq->whereBetween('class_date', [now()->startOfWeek(), now()->endOfWeek()])
                )                
            )  
            ->columns([
                TextColumn::make('classSchedule.name')
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
                    ->searchable(),

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
                //     ->sortable(),
                // TextColumn::make('created_at')
                //     ->dateTime()
                //     ->sortable()
                //     ->toggleable(isToggledHiddenByDefault: true),
                // TextColumn::make('updated_at')
                //     ->dateTime()
                //     ->sortable()
                //     ->toggleable(isToggledHiddenByDefault: true),
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
                    }),
            ])
            ->recordActions([
                // EditAction::make(),
            ])
            ->toolbarActions([
            ]);
    }
}