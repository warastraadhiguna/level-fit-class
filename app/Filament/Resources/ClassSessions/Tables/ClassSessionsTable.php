<?php

namespace App\Filament\Resources\ClassSessions\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ClassSessionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query
                ->orderBy('day')
                ->orderBy('time_start'))        
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('branchStore.name')->label('Branch')->searchable()->sortable(),
                TextColumn::make('classInstructor.full_name')->label('Instructor')->searchable()->sortable(),
                TextColumn::make('price')
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format((int) $state, 0, ',', '.'))
                    ->sortable(),
                TextColumn::make('capacity')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('day')
                    ->label('Day')
                    ->formatStateUsing(fn ($state) => [
                        1 => 'Minggu',
                        2 => 'Senin',
                        3 => 'Selasa',
                        4 => 'Rabu',
                        5 => 'Kamis',
                        6 => 'Jumat',
                        7 => 'Sabtu',
                    ][$state] ?? '-')
                    ->sortable(),
                TextColumn::make('time_start')
                    ->time()
                    ->sortable(),
                TextColumn::make('time_end')
                    ->time()
                    ->sortable(),
                ToggleColumn::make('is_active')
                    ->label('Status')
                    ->sortable()               
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
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()
            ])
            ->toolbarActions([

            ]);
    }
}