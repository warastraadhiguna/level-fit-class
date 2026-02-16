<?php

namespace App\Filament\Resources\ClassDetails;

use App\Filament\Resources\ClassDetails\Pages\CreateClassDetail;
use App\Filament\Resources\ClassDetails\Pages\EditClassDetail;
use App\Filament\Resources\ClassDetails\Pages\ListClassDetails;
use App\Filament\Resources\ClassDetails\Schemas\ClassDetailForm;
use App\Filament\Resources\ClassDetails\Tables\ClassDetailsTable;
use App\Models\ClassDetail;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ClassDetailResource extends Resource
{
    protected static ?string $model = ClassDetail::class;
    protected static ?int $navigationSort = 2;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::UserGroup;

    protected static ?string $recordTitleAttribute = 'ClassDetail';

    public static function form(Schema $schema): Schema
    {
        return ClassDetailForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ClassDetailsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListClassDetails::route('/'),
            //'create' => CreateClassDetail::route('/create'),
            'edit' => EditClassDetail::route('/{record}/edit'),
        ];
    }
}