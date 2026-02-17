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
        return [
            Stat::make('Class Total', ClassSession::count())
                ->icon('heroicon-o-building-office'), 
            Stat::make('Instructor Total', ClassInstructor::count())
                ->icon('heroicon-o-user-circle'),                 
        ];
    }
}