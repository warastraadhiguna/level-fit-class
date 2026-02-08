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
        $schedule = ClassSchedule::findOrFail($data['class_schedule_id']);

        $today = now()->startOfDay();
        $tomorrow = now()->copy()->addDay()->startOfDay();
        $classDate = Carbon::parse($schedule->class_date)->startOfDay();

        // soon only
        if (!($classDate->equalTo($today) || $classDate->equalTo($tomorrow))) {
            return redirect()->to(url()->previous() . '#schedule')->with('auth_error', 'Booking hanya bisa untuk hari ini atau besok.');
        }

        // kapasitas
        $bookedCount = ClassDetail::where('class_schedule_id', $schedule->id)
            ->whereNull('canceled_at')
            ->count();

        if ($bookedCount >= (int) $schedule->capacity) {
            return redirect()->to(url()->previous() . '#schedule')->with('auth_error', 'Kelas sudah penuh.');
        }

        ClassDetail::updateOrCreate(
            ['class_schedule_id' => $schedule->id, 'member_id' => $member->id],
            ['canceled_at' => null]
        );

        return redirect()->to(url()->previous() . '#schedule')->with('success', 'Booking berhasil.');
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
            return redirect()->to(url()->previous() . '#schedule')->with('auth_error', 'Cancel hanya bisa untuk hari ini atau besok.');
        }

        $booking = ClassDetail::where('class_schedule_id', $schedule->id)
            ->where('member_id', $member->id)
            ->whereNull('canceled_at')
            ->first();

        if (! $booking) {
            return redirect()->to(url()->previous() . '#schedule')->with('auth_error', 'Booking tidak ditemukan / sudah dibatalkan.');
        }

        $booking->update(['canceled_at' => now()]);

        return redirect()->to(url()->previous() . '#schedule')->with('success', 'Booking dibatalkan.');
    }
}