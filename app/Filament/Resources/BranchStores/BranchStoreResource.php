<?php

namespace App\Filament\Resources\BranchStores;

use App\Filament\Resources\BranchStores\Pages\CreateBranchStore;
use App\Filament\Resources\BranchStores\Pages\EditBranchStore;
use App\Filament\Resources\BranchStores\Pages\ListBranchStores;
use App\Filament\Resources\BranchStores\Schemas\BranchStoreForm;
use App\Filament\Resources\BranchStores\Tables\BranchStoresTable;
use App\Models\BranchStore;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class BranchStoreResource extends Resource
{
    protected static ?string $model = BranchStore::class;
    protected static bool $shouldRegisterNavigation = false;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return BranchStoreForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BranchStoresTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->when(static::getUserBranchStoreId(), fn (Builder $query, int $branchStoreId) => $query->whereKey($branchStoreId));
    }

    public static function getUserBranchStoreId(): ?int
    {
        $branchStoreId = auth()->user()?->branch_store_id;

        return filled($branchStoreId) ? (int) $branchStoreId : null;
    }

    public static function canEdit(Model $record): bool
    {
        $branchStoreId = static::getUserBranchStoreId();

        return blank($branchStoreId) || (int) $record->getKey() === $branchStoreId;
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
            'index' => ListBranchStores::route('/'),
            //'create' => CreateBranchStore::route('/create'),
            'edit' => EditBranchStore::route('/{record}/edit'),
        ];
    }
}
