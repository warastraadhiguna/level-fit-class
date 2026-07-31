<?php

namespace App\Filament\Resources\ClassDetails\Pages;

use App\Filament\Resources\ClassDetails\ClassDetailResource;
use App\Models\BranchStore;
use App\Services\ClassSessionScheduleSynchronizer;
use Filament\Resources\Pages\ListRecords;

class ListClassDetails extends ListRecords
{
    protected static string $resource = ClassDetailResource::class;

    public function mount(): void
    {
        parent::mount();

        $synchronizer = app(ClassSessionScheduleSynchronizer::class);
        $branchStoreId = ClassDetailResource::getUserBranchStoreId();

        if ($branchStoreId) {
            $synchronizer->reconcileFutureSchedulesForBranch($branchStoreId);
            return;
        }

        BranchStore::query()
            ->orderBy('id')
            ->pluck('id')
            ->each(fn ($id) => $synchronizer->reconcileFutureSchedulesForBranch((int) $id));
    }
}
