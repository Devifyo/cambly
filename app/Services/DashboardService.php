<?php

namespace App\Services;

use App\Models\Reservation;
use App\Models\User;
use Carbon\Carbon;

class DashboardService
{
    // Column names from your Availability model
    private const START_TIME_COLUMN = 'start_utc';
    private const END_TIME_COLUMN = 'end_utc';

    /**
     * Get comprehensive dashboard data for a student
     */
    public function getStudentDashboardData(User $user): array
    {
        return [
            'top_upcoming_lessons' => $this->getTopUpcomingLessons($user, 5),
            'total_upcoming_lessons' => $this->getTotalUpcomingLessons($user),
            'total_lessons' => $this->getTotalLessons($user),
            'completed_lessons' => $this->getCompletedLessons($user),
            'cancelled_lessons' => $this->getCancelledLessons($user),
            'lesson_stats' => $this->getLessonStats($user),
        ];
    }

    /**
     * Get top N upcoming lessons - Pure Laravel ORM
     */
    public function getTopUpcomingLessons(User $user, int $limit = 5)
    {
        $now = Carbon::now();
        
        return Reservation::with(['teacher.teacherProfile', 'availability'])
            ->where('student_id', $user->id)
            ->where('status', 'booked')
            ->where('is_hold', false)
            ->whereHas('availability', function ($query) use ($now) {
                $query->where('start_utc', '>', $now);
            })
            ->get()
            ->sortBy(function ($reservation) {
                return $reservation->availability->start_utc;
            })
            ->take($limit)
            ->map(function ($reservation) {
                return $this->formatLesson($reservation);
            })
            ->values();
    }

    /**
     * Get total upcoming lessons count
     */
    public function getTotalUpcomingLessons(User $user): int
    {
        return Reservation::where('student_id', $user->id)
            ->where('status', 'booked')
            ->where('is_hold', false)
            ->whereHas('availability', function ($query) {
                $query->where('start_utc', '>', Carbon::now());
            })
            ->count();
    }

    /**
     * Get total lessons count
     */
    public function getTotalLessons(User $user): int
    {
        return Reservation::where('student_id', $user->id)
            ->whereIn('status', ['booked', 'completed', 'cancelled'])
            ->count();
    }

    /**
     * Get completed lessons count
     */
    public function getCompletedLessons(User $user): int
    {
        return Reservation::where('student_id', $user->id)
            ->where('status', 'completed')
            ->count();
    }

    /**
     * Get cancelled lessons count
     */
    public function getCancelledLessons(User $user): int
    {
        return Reservation::where('student_id', $user->id)
            ->where('status', 'cancelled')
            ->count();
    }

    /**
     * Get comprehensive lesson statistics
     */
    public function getLessonStats(User $user): array
    {
        $reservations = Reservation::where('student_id', $user->id)
            ->select('status')
            ->get();
        
        $total = $reservations->count();
        $booked = $reservations->where('status', 'booked')->count();
        $completed = $reservations->where('status', 'completed')->count();
        $cancelled = $reservations->where('status', 'cancelled')->count();
        $upcomingCount = $this->getTotalUpcomingLessons($user);
        
        $completionRate = $total > 0 
            ? round(($completed / $total) * 100, 2) 
            : 0;

        return [
            'total' => $total,
            'booked' => $booked,
            'upcoming' => $upcomingCount,
            'completed' => $completed,
            'cancelled' => $cancelled,
            'completion_rate' => $completionRate,
        ];
    }

    /**
     * Get lessons grouped by month
     */
    public function getLessonsByMonth(User $user, int $months = 6)
    {
        $startDate = Carbon::now()->subMonths($months);
        
        return Reservation::where('student_id', $user->id)
            ->where('created_at', '>=', $startDate)
            ->get()
            ->groupBy(function ($reservation) {
                return Carbon::parse($reservation->created_at)->format('Y-m');
            })
            ->map(function ($group, $month) {
                return [
                    'month' => $month,
                    'month_name' => Carbon::parse($month . '-01')->format('F Y'),
                    'total' => $group->count(),
                    'completed' => $group->where('status', 'completed')->count(),
                    'cancelled' => $group->where('status', 'cancelled')->count(),
                    'booked' => $group->where('status', 'booked')->count(),
                ];
            })
            ->sortByDesc('month')
            ->values();
    }

    /**
     * Get recent activity
     */
    public function getRecentActivity(User $user, int $limit = 10)
    {
        return Reservation::with(['teacher.teacherProfile', 'availability'])
            ->where('student_id', $user->id)
            ->latest('created_at')
            ->limit($limit)
            ->get()
            ->map(function ($reservation) {
                return $this->formatLesson($reservation);
            });
    }

    /**
     * Get past lessons (completed or cancelled)
     */
    public function getPastLessons(User $user, int $limit = 10)
    {
        return Reservation::with(['teacher.teacherProfile', 'availability'])
            ->where('student_id', $user->id)
            ->whereIn('status', ['completed', 'cancelled'])
            ->latest('created_at')
            ->limit($limit)
            ->get()
            ->map(function ($reservation) {
                return $this->formatLesson($reservation);
            });
    }

    /**
     * Format lesson data for consistent output
     */
    private function formatLesson(Reservation $reservation): array
    {
        $availability = $reservation->availability;
        
        if (!$availability) {
            return $this->getDefaultLessonFormat($reservation);
        }

        $startTime = $availability->{self::START_TIME_COLUMN} ?? $reservation->created_at;
        $endTime = $availability->{self::END_TIME_COLUMN} 
            ?? Carbon::parse($startTime)->addMinutes(30);
        $viewerTz = auth()->user()?->studentProfile?->tz ?? auth()->user()?->teacherProfile?->tz ?? 'UTC';
        $dtUtc = Carbon::parse($availability->start_utc, 'UTC');
        $dtUser = $dtUtc->copy()->setTimezone($viewerTz);
        return [
            'id' => $reservation->id,
            'teacher_name' => $reservation->teacher->teacherProfile->preferred_name 
                ?? $reservation->teacher->name 
                ?? 'Unknown Teacher',
            'teacher_title' => $reservation->teacher->teacherProfile->title ?? '',
            'teacher_avatar' => $reservation->teacher->avatar 
                ?? asset('assets/img/dashboard/profile-06.jpg'),
            'start_time' => $startTime,
            'end_time' => $endTime,
            'date_utc' => $dtUtc->toDateTimeString(),
            'time_utc' => $dtUtc->toIso8601String(),
            'datetime_utc' => $dtUtc->toIso8601String(),
            'user_formatted_date' => $dtUser->toDateTimeString(),
            'user_formatted_time' => $dtUser->format('g:i A'),
            'user_formatted_datetime' => $dtUser->format('M d, Y h:i A'),
            'time_from_now' => Carbon::parse($startTime)->diffForHumans(),
            'is_upcoming' => Carbon::parse($startTime)->isFuture(),
            'is_today' => Carbon::parse($startTime)->isToday(),
            'status' => $reservation->status,
            'status_badge' => $this->getStatusBadgeClass($reservation->status),
        ];
    }

    /**
     * Get default lesson format when availability is missing
     */
    private function getDefaultLessonFormat(Reservation $reservation): array
    {   
        
        return [
            'id' => $reservation->id,
            'teacher_name' => $reservation->teacher->name ?? 'Unknown Teacher',
            'teacher_title' => '',
            'teacher_avatar' => asset('assets/img/default-avatar.jpg'),
            'start_time' => $reservation->created_at,
            'end_time' => $reservation->created_at,
            'formatted_date' => Carbon::parse($reservation->created_at)->format('M d, Y'),
            'formatted_time' => 'TBD',
            'formatted_datetime' => Carbon::parse($reservation->created_at)->format('M d, Y') . ' - TBD',
            'time_from_now' => 'Not scheduled',
            'is_upcoming' => false,
            'is_today' => false,
            'status' => $reservation->status,
            'status_badge' => $this->getStatusBadgeClass($reservation->status),
        ];
    }

    /**
     * Get Bootstrap badge class for status
     */
    private function getStatusBadgeClass(string $status): string
    {
        return match($status) {
            'booked' => 'badge-primary',
            'completed' => 'badge-success',
            'cancelled' => 'badge-danger',
            default => 'badge-secondary',
        };
    }
}