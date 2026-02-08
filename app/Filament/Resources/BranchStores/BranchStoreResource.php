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

class BranchStoreResource extends Resource
{
    protected static ?string $model = BranchStore::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return BranchStoreForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BranchStoresTable::configure($table);
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