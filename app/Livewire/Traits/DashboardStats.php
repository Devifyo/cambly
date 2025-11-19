<?php

namespace App\Livewire\Traits;

use App\Models\User;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

trait DashboardStats
{
    // Properties to hold the computed stats
    public $totalTeachers;
    public $totalStudents;
    public $totalCompletedLessons;
    public $totalActiveSubscriptions;

    /**
     * Compute all dashboard statistics.
     */
    public function computeStats()
    {
        // 1. Total Teachers (Assuming status = 1 is active, or count all)
        $this->totalTeachers = User::teachers()->count();

        // 2. Total Students
        $this->totalStudents = User::students()->count();

        // 3. Total Completed Lessons
        // Note: The Reservation model has dynamic status checks and a 'completed' scope.
        // We rely on the completed scope to calculate the final count efficiently.
        $this->totalCompletedLessons = Reservation::completed()->count();

        // 4. Total Active Subscriptions
        // This relies on the 'Subscription' model and checking the status field.
        $this->totalActiveSubscriptions = User::students()
            ->whereHas('activeSubscription')
            ->count();
    }
}