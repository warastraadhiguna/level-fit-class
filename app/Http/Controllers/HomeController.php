<?php

namespace App\Http\Controllers;

use App\Models\BranchStore;
use App\Models\ClassSchedule;
use App\Models\ClassSession;
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

        // A) Tentukan minggu ini (Senin sebagai awal minggu)
        $start = now()->startOfWeek(Carbon::MONDAY);

        // B) Pastikan schedule minggu ini sudah dibuat (copy dari template ClassSession)
        $this->ensureWeekSchedules($branchStore->id, $start);

        // C) Ambil semua ClassSchedule minggu ini untuk ditampilkan
        $weekStart = $start->copy()->toDateString();
        $weekEnd   = $start->copy()->addDays(6)->toDateString();

        $schedules = ClassSchedule::with(['classInstructor']) // optional
            ->where('branch_store_id', $branchStore->id)
            ->whereBetween('class_date', [$weekStart, $weekEnd])
            ->where('is_active', 1)
            ->orderBy('time_start')
            ->get();

        // D) Header hari (urut Senin..Minggu) sesuai mapping kamu (Minggu=1)
        $days = [
            2 => 'Senin',
            3 => 'Selasa',
            4 => 'Rabu',
            5 => 'Kamis',
            6 => 'Jumat',
            7 => 'Sabtu',
            1 => 'Minggu',
        ];

        // E) Tanggal untuk header (Senin minggu ini dst)
        $weekDates = [
            2 => $start->copy(),              // Senin
            3 => $start->copy()->addDay(),     // Selasa
            4 => $start->copy()->addDays(2),   // Rabu
            5 => $start->copy()->addDays(3),   // Kamis
            6 => $start->copy()->addDays(4),   // Jumat
            7 => $start->copy()->addDays(5),   // Sabtu
            1 => $start->copy()->addDays(6),   // Minggu
        ];

        // F) Ambil slot jam unik
        $timeSlots = $schedules->map(fn($x) => Carbon::parse($x->time_start)->format('H:i'))
            ->unique()->sort()->values();

        // G) Bentuk grid jadwal: scheduleGrid[time][dayNum] = list schedule
        $scheduleGrid = [];

        foreach ($schedules as $sc) {
            $time = Carbon::parse($sc->time_start)->format('H:i');

            // Tentukan dayNum dari class_date
            $iso = Carbon::parse($sc->class_date)->isoWeekday(); // 1=Mon..7=Sun
            $dayNum = match ($iso) {
                1 => 2, // Mon -> Senin (2)
                2 => 3,
                3 => 4,
                4 => 5,
                5 => 6,
                6 => 7,
                7 => 1, // Sun -> Minggu (1)
            };

            $scheduleGrid[$time][$dayNum][] = $sc;
        }

        return view("home.detail", compact('branchStore', 'days', 'timeSlots', 'scheduleGrid','weekDates'));    
    }    

    private function ensureWeekSchedules(int $branchStoreId, Carbon $startOfWeek): void
    {
        // mapping day template (Minggu=1, Senin=2, ...) ke offset dari Senin
        $offset = [
            2 => 0, // Senin
            3 => 1,
            4 => 2,
            5 => 3,
            6 => 4,
            7 => 5,
            1 => 6, // Minggu
        ];

        $sessions = ClassSession::where('branch_store_id', $branchStoreId)
            ->where('is_active', 1)
            ->get();

        foreach ($sessions as $s) {
            $date = $startOfWeek->copy()->addDays($offset[$s->day])->toDateString();

            ClassSchedule::firstOrCreate(
                [
                    'class_session_id' => $s->id,
                    'class_date' => $date,
                ],
                [
                    'branch_store_id' => $branchStoreId,
                    'class_instructor_id' => $s->class_instructor_id ?? null,
                    'name' => $s->name,
                    'price' => $s->price,
                    'capacity' => $s->capacity,
                    'time_start' => $s->time_start,
                    'time_end' => $s->time_end,
                    'is_active' => 1,
                ]
            );
        }
    }    
}