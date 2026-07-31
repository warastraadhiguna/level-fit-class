<?php

namespace App\Services;

use App\Models\ClassSchedule;
use App\Models\ClassSession;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ClassSessionScheduleSynchronizer
{
    public function synchronizeAfterUpdate(ClassSession $session): void
    {
        $dayChanged = $session->wasChanged('day');
        $statusChanged = $session->wasChanged('is_active');

        DB::transaction(function () use ($session, $dayChanged, $statusChanged): void {
            ClassSchedule::query()
                ->where('class_session_id', $session->id)
                ->whereDate('class_date', '>=', today())
                ->withCount('classDetails')
                ->orderBy('id')
                ->chunkById(200, function ($schedules) use ($session, $dayChanged, $statusChanged): void {
                    foreach ($schedules as $schedule) {
                        if ($dayChanged && ! $this->matchesSessionDay($schedule, $session)) {
                            $this->deleteOrDeactivate($schedule);
                            continue;
                        }

                        if ((int) $schedule->class_details_count === 0) {
                            $schedule->fill($this->attributesFromSession($session))->save();
                            continue;
                        }

                        if ($statusChanged) {
                            $schedule->forceFill(['is_active' => (bool) $session->is_active])->save();
                        }
                    }
                });
        });
    }

    public function handleSoftDelete(ClassSession $session): void
    {
        DB::transaction(function () use ($session): void {
            ClassSchedule::query()
                ->where('class_session_id', $session->id)
                ->whereDate('class_date', '>=', today())
                ->withCount('classDetails')
                ->orderBy('id')
                ->chunkById(200, function ($schedules): void {
                    foreach ($schedules as $schedule) {
                        $this->deleteOrDeactivate($schedule);
                    }
                });
        });
    }

    public function prepareForForceDelete(ClassSession $session): void
    {
        DB::transaction(function () use ($session): void {
            ClassSchedule::query()
                ->where('class_session_id', $session->id)
                ->withCount('classDetails')
                ->orderBy('id')
                ->chunkById(200, function ($schedules): void {
                    foreach ($schedules as $schedule) {
                        if ((int) $schedule->class_details_count === 0) {
                            $schedule->delete();
                            continue;
                        }

                        $attributes = ['class_session_id' => null];

                        if (
                            $schedule->class_date
                            && Carbon::parse($schedule->class_date)->startOfDay()->gte(today())
                        ) {
                            $attributes['is_active'] = false;
                        }

                        $schedule->forceFill($attributes)->save();
                    }
                });
        });
    }

    public function handleRestore(ClassSession $session): void
    {
        DB::transaction(function () use ($session): void {
            ClassSchedule::query()
                ->where('class_session_id', $session->id)
                ->whereDate('class_date', '>=', today())
                ->withCount('classDetails')
                ->orderBy('id')
                ->chunkById(200, function ($schedules) use ($session): void {
                    foreach ($schedules as $schedule) {
                        if (! $this->matchesSessionDay($schedule, $session)) {
                            $this->deleteOrDeactivate($schedule);
                            continue;
                        }

                        if ((int) $schedule->class_details_count === 0) {
                            $schedule->fill($this->attributesFromSession($session))->save();
                            continue;
                        }

                        $schedule->forceFill(['is_active' => (bool) $session->is_active])->save();
                    }
                });
        });
    }

    public function reconcileFutureSchedulesForBranch(int $branchStoreId): void
    {
        DB::transaction(function () use ($branchStoreId): void {
            ClassSchedule::query()
                ->where('branch_store_id', $branchStoreId)
                ->whereDate('class_date', '>=', today())
                ->withCount('classDetails')
                ->orderBy('id')
                ->chunkById(200, function ($schedules): void {
                    $sessions = ClassSession::withTrashed()
                        ->whereIn('id', $schedules->pluck('class_session_id')->filter()->unique())
                        ->get()
                        ->keyBy('id');

                    foreach ($schedules as $schedule) {
                        $session = $sessions->get($schedule->class_session_id);

                        if (! $session || $session->trashed()) {
                            $this->deleteOrDeactivate($schedule);
                            continue;
                        }

                        if (! $this->matchesSessionDay($schedule, $session)) {
                            $this->deleteOrDeactivate($schedule);
                            continue;
                        }

                        if ((int) $schedule->class_details_count === 0) {
                            $schedule->fill($this->attributesFromSession($session))->save();
                            continue;
                        }

                        if (! $session->is_active && $schedule->is_active) {
                            $schedule->forceFill(['is_active' => false])->save();
                        }
                    }
                });
        });
    }

    private function deleteOrDeactivate(ClassSchedule $schedule): void
    {
        if ((int) $schedule->class_details_count === 0) {
            $schedule->delete();
            return;
        }

        if ($schedule->is_active) {
            $schedule->forceFill(['is_active' => false])->save();
        }
    }

    private function matchesSessionDay(ClassSchedule $schedule, ClassSession $session): bool
    {
        if (! $schedule->class_date) {
            return false;
        }

        // Carbon: Minggu=0..Sabtu=6. class_sessions: Minggu=1..Sabtu=7.
        return Carbon::parse($schedule->class_date)->dayOfWeek + 1 === (int) $session->day;
    }

    private function attributesFromSession(ClassSession $session): array
    {
        return [
            'branch_store_id' => $session->branch_store_id,
            'class_instructor_id' => $session->class_instructor_id,
            'name' => $session->name,
            'note' => $session->note,
            'price' => $session->price,
            'capacity' => $session->capacity,
            'time_start' => $session->time_start,
            'time_end' => $session->time_end,
            'is_active' => (bool) $session->is_active,
        ];
    }
}
