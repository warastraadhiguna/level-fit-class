<?php

namespace App\Filament\Resources\ClassSessions\Schemas;

use App\Models\BranchStore;
use App\Models\ClassInstructor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;

class ClassSessionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('branch_store_id')
                    ->label('Branch')
                    ->options(
                        fn () =>
                        BranchStore::query()
                            ->get()
                            ->mapWithKeys(fn ($s) => [
                                $s->id => $s->name ?? '—',
                            ])
                    )
                    ->searchable()
                    ->preload()   // opsional, kalau datanya kecil
                    ->required(),                  
                Select::make('class_instructor_id')
                    ->label('Instructor')
                    ->options(
                        fn () =>
                        ClassInstructor::query()
                            ->get()
                            ->mapWithKeys(fn ($s) => [
                                $s->id => $s->full_name ?? '—',
                            ])
                    )
                    ->searchable()
                    ->preload()   // opsional, kalau datanya kecil
                    ->required(),                    
                TextInput::make('name')
                    ->required(),
                Textarea::make('note'),
                Grid::make(2)
                    ->schema([                    
                    TextInput::make('price')
                        ->required()
                        ->numeric()
                        ->prefix('Rp'),
                    TextInput::make('capacity')
                        ->required()
                        ->numeric(),
                    ]),
                Grid::make(3)
                    ->schema([
                        Select::make('day')
                            ->label('Hari')
                            ->required()
                            ->options([
                                1 => 'Minggu',
                                2 => 'Senin',
                                3 => 'Selasa',
                                4 => 'Rabu',
                                5 => 'Kamis',
                                6 => 'Jumat',
                                7 => 'Sabtu',
                            ])
                            ->native(false),

                        TimePicker::make('time_start')
                            ->label('Mulai')
                            ->required()
                            ->seconds(false),

                        TimePicker::make('time_end')
                            ->label('Selesai')
                            ->required()
                            ->seconds(false),
                    ])
            ]);
    }
}