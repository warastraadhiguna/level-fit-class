<?php

namespace App\Livewire;

use App\Models\ClassDetail;
use App\Models\Member;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.check-in')]
class ClassCheckIn extends Component
{
    public string $cardNumber = '';

    public ?string $message = null;

    public string $messageType = 'info';

    public ?string $checkedMemberName = null;

    public function checkIn(): void
    {
        $cardNumber = trim($this->cardNumber);

        if ($cardNumber === '') {
            $this->setMessage('Card number wajib diisi.', 'error');
            return;
        }

        $member = Member::where('card_number', $cardNumber)->first();

        if (! $member) {
            $this->setMessage('Member tidak ditemukan untuk card number ini.', 'error');
            $this->checkedMemberName = null;
            $this->reset('cardNumber');
            return;
        }

        $bookings = ClassDetail::query()
            ->with('classSchedule')
            ->where('member_id', $member->id)
            ->whereNull('canceled_at')
            ->whereHas('classSchedule', fn ($query) => $this->scopeTodayClassSchedule($query))
            ->join('class_schedules as cs_sort', 'cs_sort.id', '=', 'class_details.class_schedule_id')
            ->orderBy('cs_sort.time_start')
            ->orderBy('class_details.id')
            ->select('class_details.*')
            ->get();

        if ($bookings->isEmpty()) {
            $this->setMessage("{$member->full_name} tidak punya booking kelas hari ini.", 'error');
            $this->checkedMemberName = $member->full_name;
            $this->reset('cardNumber');
            return;
        }

        $now = now();

        $eligibleBooking = $bookings
            ->filter(function (ClassDetail $booking) use ($now): bool {
                if ((int) $booking->status === 1) {
                    return false;
                }

                $schedule = $booking->classSchedule;

                if (! $schedule) {
                    return false;
                }

                $classEnd = Carbon::parse($schedule->class_date . ' ' . $schedule->time_end);

                return $now->lt($classEnd);
            })
            ->first();

        $this->checkedMemberName = $member->full_name;

        if (! $eligibleBooking) {
            $alreadyCheckedIn = $bookings->every(fn (ClassDetail $booking): bool => (int) $booking->status === 1);

            $message = $alreadyCheckedIn
                ? "{$member->full_name} sudah check in untuk semua kelas hari ini."
                : "{$member->full_name} tidak punya kelas hari ini yang masih bisa check in.";

            $this->setMessage($message, 'info');
            $this->reset('cardNumber');
            return;
        }

        $eligibleBooking->update(['status' => 1]);

        $schedule = $eligibleBooking->classSchedule;
        $time = $schedule?->time_start ? Carbon::parse($schedule->time_start)->format('H:i') : '--:--';

        $this->setMessage("Check in berhasil untuk {$member->full_name}: {$schedule?->name} jam {$time}.", 'success');

        $this->reset('cardNumber');
    }

    public function markPresent(int $classDetailId): void
    {
        $booking = ClassDetail::query()
            ->whereKey($classDetailId)
            ->whereNull('canceled_at')
            ->whereHas('classSchedule', fn ($query) => $this->scopeTodayClassSchedule($query))
            ->first();

        if (! $booking) {
            $this->setMessage('Booking hari ini tidak ditemukan.', 'error');
            return;
        }

        $booking->update(['status' => 1]);
        $this->setMessage('Attendance berhasil diubah manual.', 'success');
    }

    public function markAbsent(int $classDetailId): void
    {
        $booking = ClassDetail::query()
            ->whereKey($classDetailId)
            ->whereNull('canceled_at')
            ->whereHas('classSchedule', fn ($query) => $this->scopeTodayClassSchedule($query))
            ->first();

        if (! $booking) {
            $this->setMessage('Booking hari ini tidak ditemukan.', 'error');
            return;
        }

        $booking->update(['status' => 0]);
        $this->setMessage('Attendance berhasil diubah manual.', 'success');
    }

    public function getTodayBookingsProperty(): Collection
    {
        return ClassDetail::query()
            ->with(['member', 'classSchedule.classInstructor', 'classSchedule.branchStore'])
            ->whereNull('canceled_at')
            ->whereHas('classSchedule', fn ($query) => $this->scopeTodayClassSchedule($query))
            ->join('class_schedules as cs_sort', 'cs_sort.id', '=', 'class_details.class_schedule_id')
            ->orderBy('cs_sort.time_start')
            ->orderBy('class_details.status')
            ->orderBy('class_details.id')
            ->select('class_details.*')
            ->get();
    }

    public function getSummaryProperty(): array
    {
        $bookings = $this->todayBookings;

        return [
            'total' => $bookings->count(),
            'present' => $bookings->where('status', 1)->count(),
            'absent' => $bookings->where('status', '!=', 1)->count(),
        ];
    }

    public function getClassSummariesProperty(): Collection
    {
        return $this->todayBookings
            ->groupBy('class_schedule_id')
            ->map(function (Collection $bookings): array {
                $firstBooking = $bookings->first();
                $schedule = $firstBooking?->classSchedule;

                return [
                    'key' => (string) ($schedule?->id ?? $firstBooking?->class_schedule_id ?? $firstBooking?->id),
                    'name' => $schedule?->name ?? '-',
                    'branch' => $schedule?->branchStore?->name ?? '-',
                    'time_start' => $schedule?->time_start ? Carbon::parse($schedule->time_start)->format('H:i') : '--:--',
                    'time_end' => $schedule?->time_end ? Carbon::parse($schedule->time_end)->format('H:i') : '--:--',
                    'total' => $bookings->count(),
                    'present' => $bookings->where('status', 1)->count(),
                    'absent' => $bookings->where('status', '!=', 1)->count(),
                ];
            })
            ->sortBy('time_start')
            ->values();
    }

    public function render()
    {
        return view('livewire.class-check-in', [
            'bookings' => $this->todayBookings,
            'summary' => $this->summary,
            'classSummaries' => $this->classSummaries,
            'todayLabel' => Carbon::today()->locale('id')->isoFormat('dddd, D MMMM Y'),
        ])->title('Check In Kelas');
    }

    private function setMessage(string $message, string $type): void
    {
        $this->message = $message;
        $this->messageType = $type;
    }

    private function getUserBranchStoreId(): ?int
    {
        $branchStoreId = auth()->user()?->branch_store_id;

        return filled($branchStoreId) ? (int) $branchStoreId : null;
    }

    private function scopeTodayClassSchedule($query)
    {
        return $query
            ->whereDate('class_date', today())
            ->when($this->getUserBranchStoreId(), fn ($query, int $branchStoreId) => $query->where('branch_store_id', $branchStoreId));
    }
}
