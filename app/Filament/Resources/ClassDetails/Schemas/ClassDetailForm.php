<?php

namespace App\Filament\Resources\ClassDetails\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ClassDetailForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('class_schedule_id')
                    ->required()
                    ->numeric(),
                TextInput::make('user_id')
                    ->numeric(),
                TextInput::make('member_id')
                    ->numeric(),
                TextInput::make('name'),
                TextInput::make('phone')
                    ->tel(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email(),
                DateTimePicker::make('canceled_at'),
            ]);
    }
}
