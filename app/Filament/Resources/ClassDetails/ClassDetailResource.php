<?php

namespace App\Filament\Resources\ClassDetails;

use App\Filament\Resources\ClassDetails\Pages\ClassDetailBookings;
use App\Filament\Resources\ClassDetails\Pages\ListClassDetails;
use App\Filament\Resources\ClassDetails\Schemas\ClassDetailForm;
use App\Filament\Resources\ClassDetails\Tables\ClassScheduleDetailsTable;
use App\Models\ClassSchedule;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ClassDetailResource extends Resource
{
    protected static ?string $model = ClassSchedule::class;
    protected static ?int $navigationSort = 2;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::UserGroup;

    protected static ?string $modelLabel = 'Class Detail';
    protected static ?string $pluralModelLabel = 'Class Details';
    protected static ?string $recordTitleAttribute = 'name';
    protected static bool $shouldSkipAuthorization = true;

    public static function form(Schema $schema): Schema
    {
        return ClassDetailForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ClassScheduleDetailsTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->when(static::getUserBranchStoreId(), fn (Builder $query, int $branchStoreId) => $query->where('branch_store_id', $branchStoreId));
    }

    public static function getUserBranchStoreId(): ?int
    {
        $branchStoreId = auth()->user()?->branch_store_id;

        return filled($branchStoreId) ? (int) $branchStoreId : null;
    }

    public static function belongsToUserBranch(Model $record): bool
    {
        $branchStoreId = static::getUserBranchStoreId();

        return blank($branchStoreId) || (int) $record->branch_store_id === $branchStoreId;
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canView(Model $record): bool
    {
        return static::belongsToUserBranch($record);
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
            'bookings' => ClassDetailBookings::route('/{record}'),
        ];
    }
}
