<?php

namespace App\Filament\Resources\ClassDetails\Pages;

use App\Filament\Resources\ClassDetails\ClassDetailResource;
use App\Models\ClassDetail;
use App\Models\Member;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListClassDetails extends ListRecords
{
    protected static string $resource = ClassDetailResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('checkIn')
                ->label('Check In')
                ->color('success')
                ->icon('heroicon-m-check-circle')
                ->modalHeading('Check In Member')
                ->modalSubmitActionLabel('Check In')
                ->form([
                    TextInput::make('card_number')
                        ->label('Card Number')
                        ->required()
                        ->autofocus(),
                ])
                ->action(function (array $data, $livewire): void {
                    $classScheduleId = $this->getSelectedClassScheduleId($livewire);

                    if (! $classScheduleId) {
                        Notification::make()
                            ->title('Pilih Class Schedule dulu')
                            ->body('Gunakan filter Class Schedule sebelum melakukan check in.')
                            ->warning()
                            ->send();

                        return;
                    }

                    $member = Member::where('card_number', $data['card_number'])->first();

                    if (! $member) {
                        Notification::make()
                            ->title('Member tidak ditemukan')
                            ->body('Card number tidak terdaftar.')
                            ->danger()
                            ->send();

                        return;
                    }

                    $booking = ClassDetail::where('class_schedule_id', $classScheduleId)
                        ->where('member_id', $member->id)
                        ->whereNull('canceled_at')
                        ->first();

                    if (! $booking) {
                        Notification::make()
                            ->title('Booking tidak ditemukan')
                            ->body("{$member->full_name} belum booking di class schedule ini atau booking sudah dibatalkan.")
                            ->danger()
                            ->send();

                        return;
                    }

                    $booking->update(['status' => 1]);

                    Notification::make()
                        ->title('Check in berhasil')
                        ->body("{$member->full_name} ditandai hadir.")
                        ->success()
                        ->send();
                }),
        ];
    }

    private function getSelectedClassScheduleId($livewire): ?int
    {
        $filter = $livewire->tableFilters['class_schedule_id'] ?? null;

        $value = is_array($filter)
            ? ($filter['value'] ?? $filter['values'][0] ?? null)
            : $filter;

        return filled($value) ? (int) $value : null;
    }
}
