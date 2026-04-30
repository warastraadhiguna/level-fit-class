<?php

namespace App\Filament\Resources\ClassInstructors;

use App\Filament\Resources\ClassInstructors\Pages\CreateClassInstructor;
use App\Filament\Resources\ClassInstructors\Pages\EditClassInstructor;
use App\Filament\Resources\ClassInstructors\Pages\ListClassInstructors;
use App\Filament\Resources\ClassInstructors\Schemas\ClassInstructorForm;
use App\Filament\Resources\ClassInstructors\Tables\ClassInstructorsTable;
use App\Models\ClassInstructor;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class ClassInstructorResource extends Resource
{
    protected static ?string $model = ClassInstructor::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::UserCircle;

    protected static ?string $recordTitleAttribute = 'ClassInstructor';

    public static function form(Schema $schema): Schema
    {
        return ClassInstructorForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ClassInstructorsTable::configure($table);
    }

    public static function isAdmin(): bool
    {
        return (bool) auth()->user()?->isAdmin();
    }

    public static function canCreate(): bool
    {
        return static::isAdmin();
    }

    public static function canEdit(Model $record): bool
    {
        return static::isAdmin();
    }

    public static function canDelete(Model $record): bool
    {
        return static::isAdmin();
    }

    public static function canDeleteAny(): bool
    {
        return static::isAdmin();
    }

    public static function canForceDelete(Model $record): bool
    {
        return static::isAdmin();
    }

    public static function canForceDeleteAny(): bool
    {
        return static::isAdmin();
    }

    public static function canRestore(Model $record): bool
    {
        return static::isAdmin();
    }

    public static function canRestoreAny(): bool
    {
        return static::isAdmin();
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
            'index' => ListClassInstructors::route('/'),
            'create' => CreateClassInstructor::route('/create'),
            'edit' => EditClassInstructor::route('/{record}/edit'),
        ];
    }
}
