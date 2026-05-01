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
            ->where('member_id', $member->id)
            ->whereNull('canceled_at')
            ->whereHas('classSchedule', fn ($query) => $query->whereDate('class_date', today()))
            ->get();

        if ($bookings->isEmpty()) {
            $this->setMessage("{$member->full_name} tidak punya booking kelas hari ini.", 'error');
            $this->checkedMemberName = $member->full_name;
            $this->reset('cardNumber');
            return;
        }

        $alreadyCheckedIn = $bookings->every(fn (ClassDetail $booking): bool => (int) $booking->status === 1);

        $bookings
            ->where('status', '!=', 1)
            ->each(fn (ClassDetail $booking) => $booking->update(['status' => 1]));

        $this->checkedMemberName = $member->full_name;

        if ($alreadyCheckedIn) {
            $this->setMessage("{$member->full_name} sudah check in untuk kelas hari ini.", 'info');
        } else {
            $count = $bookings->count();
            $this->setMessage("Check in berhasil untuk {$member->full_name}. Total booking hari ini: {$count}.", 'success');
        }

        $this->reset('cardNumber');
    }

    public function markPresent(int $classDetailId): void
    {
        $booking = ClassDetail::query()
            ->whereKey($classDetailId)
            ->whereNull('canceled_at')
            ->whereHas('classSchedule', fn ($query) => $query->whereDate('class_date', today()))
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
            ->whereHas('classSchedule', fn ($query) => $query->whereDate('class_date', today()))
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
            ->whereHas('classSchedule', fn ($query) => $query->whereDate('class_date', today()))
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

    public function render()
    {
        return view('livewire.class-check-in', [
            'bookings' => $this->todayBookings,
            'summary' => $this->summary,
            'todayLabel' => Carbon::today()->locale('id')->isoFormat('dddd, D MMMM Y'),
        ])->title('Check In Kelas');
    }

    private function setMessage(string $message, string $type): void
    {
        $this->message = $message;
        $this->messageType = $type;
    }
}
