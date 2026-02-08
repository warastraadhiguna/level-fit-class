<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\DashboardStats;
use App\Filament\Widgets\InfoTable;
use Filament\Pages\Page;

class Dashboard extends Page
{    
    protected static ?int $navigationSort = -100;
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-home';
    protected string $view = 'filament.pages.dashboard';

    protected function getHeaderWidgets(): array
    {
        return [
           DashboardStats::class,
        ];
    }

}