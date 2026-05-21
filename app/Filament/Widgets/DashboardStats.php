<?php

namespace App\Filament\Widgets;

use App\Models\ClassInstructor;
use App\Models\ClassSession;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DashboardStats extends BaseWidget
{
    protected function getStats(): array
    {
        $branchStoreId = auth()->user()?->branch_store_id;

        return [
            Stat::make('Class Total', ClassSession::query()
                ->when($branchStoreId, fn ($query, $branchStoreId) => $query->where('branch_store_id', $branchStoreId))
                ->count())
                ->icon('heroicon-o-building-office'), 
            Stat::make('Instructor Total', ClassInstructor::query()
                ->when($branchStoreId, fn ($query, $branchStoreId) => $query->whereIn(
                    'id',
                    ClassSession::query()
                        ->where('branch_store_id', $branchStoreId)
                        ->whereNotNull('class_instructor_id')
                        ->select('class_instructor_id')
                ))
                ->count())
                ->icon('heroicon-o-user-circle'),                 
        ];
    }
}
