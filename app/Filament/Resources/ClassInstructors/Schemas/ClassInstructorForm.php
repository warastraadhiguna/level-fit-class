<?php

namespace App\Filament\Resources\ClassInstructors\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ClassInstructorForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('full_name')
                    ->required(),
                Select::make('gender')
                    ->label('Gender')
                    ->required()
                    ->options([
                        'Male' => 'Male',
                        'Female' => 'Female',
                    ])
                    ->native(false),
                TextInput::make('email')
                    ->email()
                    ->required(),                    
                TextInput::make('phone_number')
                    ->tel()
                    ->required(),
                Textarea::make('description')
                    ->required()
                    ->columnSpanFull(),
            ]);
    }
}