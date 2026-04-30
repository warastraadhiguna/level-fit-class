<?php

namespace App\Http\Controllers;

use App\Models\ClassDetail;
use App\Models\ClassSchedule;
use App\Models\Member;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
class BookingController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'class_schedule_id' => ['required','integer','exists:class_schedules,id'],
        ]);

        $member = Auth::guard('member')->user();

        // dd();
        $schedule = ClassSchedule::with(['branchStore', 'classSession'])->findOrFail($data['class_schedule_id']);

        if (! $schedule->is_active || ! ($schedule->classSession?->is_active ?? false)) {
            return redirect()->to(url()->previous() . '#schedule')
                ->with('error', 'Kelas sedang libur dan tidak bisa dibooking.');
        }

        $activeMembership = $this->getEligibleActiveMembership($member, (int) $schedule->branchStore->id);

        if (! $activeMembership) {
            return redirect()->to(url()->previous() . '#schedule')
                ->with('error', 'Booking ditolak. Membership harus aktif dan paket harus sesuai cabang ini atau bertipe ALL club.');
        }

        $today    = now()->startOfDay();
        $tomorrow = now()->copy()->addDay()->startOfDay();
        $classDate = Carbon::parse($schedule->class_date)->startOfDay();

        // only today / tomorrow
        if (!($classDate->equalTo($today) || $classDate->equalTo($tomorrow))) {
            return redirect()->to(url()->previous() . '#schedule')
                ->with('error', 'Booking hanya bisa untuk hari ini atau besok.');
        }

        /**
         * BATAS BOOKING: maksimal 1 jam sebelum kelas mulai
         */
        $classStart = Carbon::parse($schedule->class_date.' '.$schedule->time_start);
        $bookingDeadline = $classStart->copy()->subHour();

        if (now()->gte($bookingDeadline)) {
            return redirect()->to(url()->previous() . '#schedule')
                ->with('error', 'Booking ditutup. Maksimal 1 jam sebelum kelas dimulai.');
        }

        /**
         * SUSPEND 1 MINGGU jika no-show (status=0) pada kelas terakhir untuk class_session yang sama
         */
        $classSessionId = $schedule->class_session_id; // pastikan field ini ada di class_schedules

        // cari booking terakhir yang SUDAH SELESAI (waktunya sudah lewat) untuk class_session yang sama
        $lastDetail = ClassDetail::query()
            ->join('class_schedules as cs', 'cs.id', '=', 'class_details.class_schedule_id')
            ->where('class_details.member_id', $member->id)
            ->whereNull('class_details.canceled_at')
            ->where('cs.class_session_id', $classSessionId)
            ->whereRaw("STR_TO_DATE(CONCAT(cs.class_date,' ',cs.time_start), '%Y-%m-%d %H:%i:%s') < ?", [now()])
            ->orderByRaw("STR_TO_DATE(CONCAT(cs.class_date,' ',cs.time_start), '%Y-%m-%d %H:%i:%s') DESC")
            ->select('class_details.*', 'cs.class_date', 'cs.time_start')
            ->first();

        if ($lastDetail && (int)$lastDetail->status === 0) {
            $lastStart = Carbon::parse($lastDetail->class_date.' '.$lastDetail->time_start);
            $suspendUntil = $lastStart->copy()->addWeek(); // 7 hari setelah kelas terakhir

            if (now()->lt($suspendUntil)) {
                return redirect()->to(url()->previous() . '#schedule')->with(
                    'error',
                    'Dikarenakan tidak berangkat saat kelas terakhir, booking anda ditangguhkan selama 1 minggu.'
                );
            }
        }

        // kapasitas dihitung dari booking aktif; attendance default belum berangkat.
        $bookedCount = ClassDetail::where('class_schedule_id', $schedule->id)
            ->whereNull('canceled_at')
            ->count();

        if ($bookedCount >= (int) $schedule->capacity) {
            return redirect()->to(url()->previous() . '#schedule')
                ->with('error', 'Kelas sudah penuh.');
        }

        // simpan booking => attendance default belum berangkat.
        ClassDetail::updateOrCreate(
            ['class_schedule_id' => $schedule->id, 'member_id' => $member->id],
            ['canceled_at' => null, 'status' => 0]
        );

        return redirect()->to(url()->previous() . '#schedule')
            ->with('success', 'Booking berhasil.');
    }
    public function cancel(Request $request)
    {
        $data = $request->validate([
            'class_schedule_id' => ['required','integer','exists:class_schedules,id'],
        ]);

        $member = Auth::guard('member')->user();
        $schedule = ClassSchedule::findOrFail($data['class_schedule_id']);

        $today = now()->startOfDay();
        $tomorrow = now()->copy()->addDay()->startOfDay();
        $classDate = Carbon::parse($schedule->class_date)->startOfDay();

        if (!($classDate->equalTo($today) || $classDate->equalTo($tomorrow))) {
            return redirect()->to(url()->previous() . '#schedule')->with('error', 'Cancel hanya bisa untuk hari ini atau besok.');
        }

        $booking = ClassDetail::where('class_schedule_id', $schedule->id)
            ->where('member_id', $member->id)
            ->whereNull('canceled_at')
            ->first();

        if (! $booking) {
            return redirect()->to(url()->previous() . '#schedule')->with('error', 'Booking tidak ditemukan / sudah dibatalkan.');
        }

        $booking->update(['canceled_at' => now()]);

        return redirect()->to(url()->previous() . '#schedule')->with('success', 'Booking dibatalkan.');
    }

    private function getEligibleActiveMembership(Member $member, int $branchStoreId): ?object
    {
        $leaveDays = DB::table('leave_days')
            ->select('member_registration_id', DB::raw('COALESCE(SUM(days), 0) as total_days'))
            ->groupBy('member_registration_id');

        $payments = DB::table('member_registration_payments')
            ->select('member_registration_id', DB::raw('COALESCE(SUM(value), 0) as payment_summary'))
            ->groupBy('member_registration_id');

        return DB::table('member_registrations as mbr_reg')
            ->join('member_packages as mbr_pkg', 'mbr_pkg.id', '=', 'mbr_reg.member_package_id')
            ->leftJoinSub($leaveDays, 'lds_view', fn ($join) => $join
                ->on('lds_view.member_registration_id', '=', 'mbr_reg.id'))
            ->leftJoinSub($payments, 'payments_view', fn ($join) => $join
                ->on('payments_view.member_registration_id', '=', 'mbr_reg.id'))
            ->where('mbr_reg.member_id', $member->id)
            ->where('mbr_reg.days', '>', 1)
            ->whereRaw('NOW() BETWEEN mbr_reg.start_date AND DATE_ADD(mbr_reg.start_date, INTERVAL (mbr_reg.days + COALESCE(lds_view.total_days, 0)) DAY)')
            ->whereRaw('COALESCE(payments_view.payment_summary, 0) >= (mbr_reg.package_price + mbr_reg.admin_price)')
            ->where(function ($query) use ($branchStoreId) {
                $query->where('mbr_pkg.branch_store_id', $branchStoreId)
                    ->orWhere('mbr_pkg.is_all_club', 1);
            })
            ->select([
                'mbr_reg.id',
                'mbr_reg.start_date',
                'mbr_reg.days',
                'mbr_pkg.package_name',
                'mbr_pkg.branch_store_id as member_package_branch_store_id',
                'mbr_pkg.is_all_club',
            ])
            ->orderByDesc('mbr_reg.start_date')
            ->first();
    }
}
