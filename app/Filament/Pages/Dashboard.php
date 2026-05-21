<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\DashboardStats;
use App\Models\BranchStore;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class Dashboard extends Page
{    
    protected static ?int $navigationSort = -100;
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-home';
    protected string $view = 'filament.pages.dashboard';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('switchBranchStore')
                ->label(fn (): string => $this->getCurrentBranchStoreName())
                ->icon('heroicon-o-building-storefront')
                ->button()
                ->visible(fn (): bool => (bool) auth()->user()?->isAdmin())
                ->modalHeading('Pilih Store Branch')
                ->modalSubmitActionLabel('Update Branch')
                ->fillForm(fn (): array => [
                    'branch_store_id' => auth()->user()?->branch_store_id,
                ])
                ->schema([
                    Select::make('branch_store_id')
                        ->label('Store Branch')
                        ->options(fn () => BranchStore::query()
                            ->orderBy('name')
                            ->pluck('name', 'id')
                            ->all())
                        ->searchable()
                        ->preload()
                        ->required(),
                ])
                ->action(function (array $data): void {
                    abort_unless(auth()->user()?->isAdmin(), 403);

                    auth()->user()?->forceFill([
                        'branch_store_id' => (int) $data['branch_store_id'],
                    ])->save();

                    Notification::make()
                        ->title('Store branch berhasil diubah')
                        ->success()
                        ->send();
                }),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
           DashboardStats::class,
        ];
    }

    private function getCurrentBranchStoreName(): string
    {
        $branchStoreId = auth()->user()?->branch_store_id;

        if (! $branchStoreId) {
            return 'Pilih Store Branch';
        }

        return BranchStore::query()->whereKey($branchStoreId)->value('name') ?? 'Pilih Store Branch';
    }

}
