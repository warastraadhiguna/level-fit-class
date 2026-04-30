<?php

namespace App\Filament\Resources\ClassInstructors\Tables;

use App\Filament\Resources\ClassInstructors\ClassInstructorResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class ClassInstructorsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('full_name')
                    ->searchable(),
                TextColumn::make('gender')
                    ->searchable(),
                TextColumn::make('phone_number')
                    ->searchable(),
                TextColumn::make('email')
                    ->searchable(),                    
                // TextColumn::make('created_at')
                //     ->dateTime()
                //     ->sortable()
                //     ->toggleable(isToggledHiddenByDefault: true),
                // TextColumn::make('updated_at')
                //     ->dateTime()
                //     ->sortable()
                //     ->toggleable(isToggledHiddenByDefault: true),
                // TextColumn::make('deleted_at')
                //     ->dateTime()
                //     ->sortable()
                //     ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make()
                    ->visible(fn () => ClassInstructorResource::isAdmin()),
                DeleteAction::make()
                    ->visible(fn ($record) => ClassInstructorResource::isAdmin() && ! $record->trashed()),
                RestoreAction::make()
                    ->visible(fn ($record) => ClassInstructorResource::isAdmin() && $record->trashed()),
                ForceDeleteAction::make()
                    ->visible(fn ($record) => ClassInstructorResource::isAdmin() && $record->trashed()),
            ])
            ->toolbarActions([
            ]);
    }
}
