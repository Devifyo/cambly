<?php

namespace App\Services;

use App\Models\Reservation;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon; // Make sure to import Carbon

class LessonService
{
    /**
     * Get a paginated list of lessons for a student, with filters applied.
     */
    public function getPaginatedLessons(User $user, array $filters = []): LengthAwarePaginator
    {
        $perPage = $filters['per_page'] ?? 10;
        // Get the user's timezone once
        $viewerTimezone = $this->getViewerTimezone($user);

        $query = Reservation::query()
            ->forStudent($user)
            ->with([
                'teacher:id,name',
                'teacher.teacherProfile:user_id,preferred_name,discord_id,tz',
                'availability:id,start_utc,end_utc,is_booked'
            ])
            ->select([ // Ensure we select all necessary fields from reservations
                'id',
                'teacher_id',
                'availability_id',
                'status',
                'is_hold',
                'cycle_start_utc',
                'created_at',
                'updated_at',
                'lesson_meeting_link', // <-- Added meeting link
            ]);

        // Apply filters using our new scopes
        if (!empty($filters['date'])) {
            $query->filterByDate($filters['date']);
        }

        if (!empty($filters['teacher'])) {
            $query->filterByTeacher($filters['teacher']);
        }

        if (!empty($filters['filter'])) {
            match ($filters['filter']) {
                'upcoming' => $query->upcoming(),
                'completed' => $query->completed(),
                'cancelled' => $query->cancelled(),
                default => null,
            };
        }

        // Paginate results - sort by availability start time
        $reservations = $query
            ->join('availabilities', 'reservations.availability_id', '=', 'availabilities.id')
            ->orderBy('availabilities.start_utc', 'desc')
            ->orderBy('reservations.updated_at', 'desc')
            ->select('reservations.*') // Re-select all from reservations after join
            ->paginate($perPage)
            ->withQueryString();

        // Transform the collection for view consumption
        $reservations->getCollection()->transform(
            // Pass the timezone to the transformer
            fn(Reservation $res) => $this->transformLesson($res, $viewerTimezone)
        );
 
        return $reservations;
    }

    /**
     * Get the counts for upcoming, completed, and cancelled lessons.
     */
    public function getLessonStats(User $user): array
    {
        return [
            'upcoming' => Reservation::forStudent($user)->upcoming()->count(),
            'completed' => Reservation::forStudent($user)->completed()->count(),
            'cancelled' => Reservation::forStudent($user)->cancelled()->count(),
        ];
    }

    /**
     * Get the viewer's (student's) timezone.
     */
    public function getViewerTimezone(User $viewer): string
    {
        $isImpersonating = is_impersonating();
        if ($isImpersonating) {
            $tz = getTimeZone();
        }else{
            // This logic assumes the $viewer is the student.
            $tz = optional($viewer->studentProfile)->tz
                ?? optional($viewer->teacherProfile)->tz; // In case a teacher views it

            $tz = $tz ?? config('app.timezone', 'UTC');
        }
        return in_array($tz, \DateTimeZone::listIdentifiers()) ? $tz : 'UTC';
    }

    /**
     * Transform a single Reservation model into the desired object for the view.
     */
    public function transformLesson(Reservation $res, ?string $viewerTimezone = null): object
    {   
        $viewerTimezone = $viewerTimezone ?? config('app.timezone', 'UTC');
        $teacher = $res->teacher?->name ?? $res->teacher?->teacherProfile?->preferred_name
            ?? 'Unknown Teacher';
        $startTime = $res->availability?->start_utc;
        $endTime = $res->availability?->end_utc;
        
        $duration = null;
        if ($startTime && $endTime) {
            $duration = $startTime->diffInMinutes($endTime);
        }
        $canCancel = in_array($res->status, ['booked', 'confirmed'], true)
            && $startTime && $startTime > now();

        $displayStatus = $res->status;
        if ($res->status !== 'cancelled' && $res->status !== 'completed' && $startTime && $startTime < now()) {
            $displayStatus = 'completed';
        }
        
        // Check if lesson is starting soon (e.g., within 15 mins) or in progress
        $canJoin = $startTime 
            && $displayStatus !== 'completed'
            && $displayStatus !== 'cancelled'
            && $res->lesson_meeting_link
            && now()->between($startTime->copy()->subMinutes(15), $endTime);
        return (object) [
            'id' => $res->id,
            'teacher_name' => $teacher,
            'teacher_zoom_link' => $res->teacher?->teacherProfile?->zoom_link ?? null,
            'teacher_discord_user_name' => $res->teacher?->teacherProfile?->discord_id ?? null,
            'student_discord_user_name' => $res->student?->studentProfile?->discord_id ?? null,
            'start_at_utc' => $startTime, // Keep UTC for internal logic
            'end_at_utc' => $endTime,     // Keep UTC for internal logic
            'start_at_local' => $startTime ? $startTime->copy()->setTimezone($viewerTimezone) : null,
            'end_at_local' => $endTime ? $endTime->copy()->setTimezone($viewerTimezone) : null,
            'duration' => $duration,
            'status' => $res->status,
            'display_status' => $displayStatus,
            'is_hold' => $res->is_hold,
            'can_cancel' => $canCancel,
            'can_join' => $canJoin, // <-- New property
            'lesson_meeting_link' => $res->lesson_meeting_link, // <-- New property
            'cycle_start_utc' => $res->cycle_start_utc,
        ];
    }
}