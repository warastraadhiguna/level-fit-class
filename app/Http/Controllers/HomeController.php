<?php

namespace App\Http\Controllers;

use App\Models\BranchStore;
use App\Models\ClassSchedule;
use App\Models\ClassSession;
use App\Services\ClassSessionScheduleSynchronizer;
use Carbon\Carbon;

class HomeController extends Controller
{
    public function index()
    {
        $branchStores = BranchStore::get();
        return view("home.index", compact('branchStores'));    
    }

    public function detail($slug)
    {
        $branchStore = BranchStore::where('slug', $slug)->firstOrFail();

        app(ClassSessionScheduleSynchronizer::class)
            ->reconcileFutureSchedulesForBranch((int) $branchStore->id);

        // 1) Tentukan tanggal "base"
        // - Jika hari ini Sabtu/Minggu => base = hari ini
        // - Selain itu => base = Senin minggu ini
        $today = now()->startOfDay();
        $base = $today->copy();

        if ($today->isoWeekday() < 6) { // 1=Senin..5=Jumat
            $base = $today->copy()->startOfWeek(Carbon::MONDAY);
        }

        // 2) Susun tanggal header per kolom (dayNum kamu: Minggu=1, Senin=2, dst)
        // Weekend mode: Sabtu/Minggu sekarang + Senin-Jumat minggu depan
        if ($today->isoWeekday() >= 6) { // 6=Sabtu, 7=Minggu
            $weekDates = [
                2 => $base->copy()->addDays(2), // Senin (weekend+2)
                3 => $base->copy()->addDays(3), // Selasa
                4 => $base->copy()->addDays(4), // Rabu
                5 => $base->copy()->addDays(5), // Kamis
                6 => $base->copy()->addDays(6), // Jumat
                7 => $base->copy(),             // Sabtu (hari ini)
                1 => $base->copy()->addDay(),   // Minggu (besok)
            ];
        } else {
            // Weekday mode: Senin..Minggu minggu ini normal
            $weekDates = [
                2 => $base->copy(),              // Senin
                3 => $base->copy()->addDay(),     // Selasa
                4 => $base->copy()->addDays(2),   // Rabu
                5 => $base->copy()->addDays(3),   // Kamis
                6 => $base->copy()->addDays(4),   // Jumat
                7 => $base->copy()->addDays(5),   // Sabtu
                1 => $base->copy()->addDays(6),   // Minggu
            ];
        }

        // 3) Pastikan schedule tanggal-tanggal ini sudah ada (copy dari ClassSession)
        $this->ensureWeekSchedules($branchStore->id, $weekDates);

        // 4) Range query harus pakai MIN/MAX dari tanggal yang tampil
        $weekStart = collect($weekDates)->min()->toDateString();
        $weekEnd   = collect($weekDates)->max()->toDateString();

        // 5) Ambil semua ClassSchedule dalam range itu
        $schedules = ClassSchedule::with(['classInstructor', 'classSession'])
            ->withCount([
                'classDetails as booked_count' => fn ($q) => $q
                    ->whereNull('canceled_at'),
            ])
            ->where('branch_store_id', $branchStore->id)
            ->whereBetween('class_date', [$weekStart, $weekEnd])
            ->orderBy('time_start')
            ->get();

        // 6) Header hari (urut Senin..Minggu) sesuai mapping kamu (Minggu=1)
        $days = [
            2 => 'Senin',
            3 => 'Selasa',
            4 => 'Rabu',
            5 => 'Kamis',
            6 => 'Jumat',
            7 => 'Sabtu',
            1 => 'Minggu',
        ];

        // 7) Slot jam unik
        $timeSlots = $schedules
            ->map(fn($x) => Carbon::parse($x->time_start)->format('H:i'))
            ->unique()
            ->sort()
            ->values();

        // 8) Map tanggal->dayNum agar grid cocok walaupun tanggal campur (weekend mode)
        $dateToDayNum = [];
        foreach ($weekDates as $dayNum => $dt) {
            $dateToDayNum[$dt->toDateString()] = $dayNum;
        }

        // 9) Bentuk grid jadwal: scheduleGrid[time][dayNum] = list schedule
        $scheduleGrid = [];
        foreach ($schedules as $sc) {
            $time = Carbon::parse($sc->time_start)->format('H:i');
            $dayNum = $dateToDayNum[$sc->class_date] ?? null;

            if (!$dayNum) continue;

            $scheduleGrid[$time][$dayNum][] = $sc;
        }

        return view("home.detail", compact(
            'branchStore',
            'days',
            'timeSlots',
            'scheduleGrid',
            'weekDates'
        ));
    }
    private function ensureWeekSchedules(int $branchStoreId, array $weekDates): void
    {
        $sessions = ClassSession::where('branch_store_id', $branchStoreId)
            ->get();

        foreach ($sessions as $s) {
            // $s->day: Minggu=1, Senin=2, dst
            if (!isset($weekDates[$s->day])) continue;

            $date = $weekDates[$s->day]->toDateString();

            $schedule = ClassSchedule::firstOrNew(
                [
                    'class_session_id' => $s->id,
                    'class_date' => $date,
                ]
            );

            if (! $schedule->exists || ! $schedule->classDetails()->exists()) {
                $schedule->fill([
                    'branch_store_id' => $branchStoreId,
                    'class_instructor_id' => $s->class_instructor_id ?? null,
                    'name' => $s->name,
                    'note' => $s->note,
                    'price' => $s->price,
                    'capacity' => $s->capacity,
                    'time_start' => $s->time_start,
                    'time_end' => $s->time_end,
                    'is_active' => (bool) $s->is_active,
                ]);
            }

            if (! $s->is_active) {
                $schedule->is_active = false;
            }

            $schedule->save();
        }
    }
}
