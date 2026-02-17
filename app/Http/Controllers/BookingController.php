<?php

namespace App\Http\Controllers;

use App\Models\ClassDetail;
use App\Models\ClassSchedule;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class BookingController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'class_schedule_id' => ['required','integer','exists:class_schedules,id'],
        ]);

        $member = Auth::guard('member')->user();

        // dd();
        $schedule = ClassSchedule::with('branchStore')->findOrFail($data['class_schedule_id']);

        if($member->branch_store_id != $schedule->branchStore->id){
            return redirect()->to(url()->previous() . '#schedule')
                ->with('error', 'Member dari cabang lain, tidak dapat booking di cabang ini..');
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

        // kapasitas (HITUNG hanya yang aktif & status=1)
        $bookedCount = ClassDetail::where('class_schedule_id', $schedule->id)
            ->whereNull('canceled_at')
            ->where('status', 1) // penting: no-show tidak dihitung mengisi seat lagi (kalau kamu mau begitu)
            ->count();

        if ($bookedCount >= (int) $schedule->capacity) {
            return redirect()->to(url()->previous() . '#schedule')
                ->with('error', 'Kelas sudah penuh.');
        }

        // simpan booking => status default 1 (berangkat)
        ClassDetail::updateOrCreate(
            ['class_schedule_id' => $schedule->id, 'member_id' => $member->id],
            ['canceled_at' => null, 'status' => 1]
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
}