<?php

namespace App\Observers;

use App\Models\ClassSession;
use App\Services\ClassSessionScheduleSynchronizer;

class ClassSessionObserver
{
    public function __construct(
        private ClassSessionScheduleSynchronizer $synchronizer,
    ) {
    }

    public function updated(ClassSession $classSession): void
    {
        $this->synchronizer->synchronizeAfterUpdate($classSession);
    }

    public function deleted(ClassSession $classSession): void
    {
        if (! $classSession->isForceDeleting()) {
            $this->synchronizer->handleSoftDelete($classSession);
        }
    }

    public function forceDeleting(ClassSession $classSession): void
    {
        $this->synchronizer->prepareForForceDelete($classSession);
    }

    public function restored(ClassSession $classSession): void
    {
        $this->synchronizer->handleRestore($classSession);
    }
}
