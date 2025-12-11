<?php

namespace App\Services;

use App\Models\Availability;
use App\Models\Reservation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
class SlotService
{
    public function getWeekSlotsForTeacher(?User $student, $teacherId, string $startIso, string $endIso): Collection
    {   
        // 1. Parse Dates
        try {
            $start = Carbon::parse($startIso);
            $end = Carbon::parse($endIso);
        } catch (\Throwable $e) {
            $start = Carbon::now()->startOfWeek();
            $end = Carbon::now()->endOfWeek();
        }

        // ---------------------------------------------------------
        // OPTIMIZATION 1: Calculate Timezone ONCE before the loop
        // ---------------------------------------------------------
        $viewerTz = 'UTC';
        if (function_exists('is_impersonating') && is_impersonating()) {
            $viewerTz = getTimeZone(); 
        } elseif ($student) {
            // Ensure we don't query the DB for student profile 50 times
            if (!$student->relationLoaded('studentProfile')) {
                $student->load('studentProfile');
            }
            $viewerTz = $student->studentProfile->tz ?? 'UTC';
        }

        // ---------------------------------------------------------
        // OPTIMIZATION 2: Eager Load 'avatar' if it's a relationship
        // ---------------------------------------------------------
        // If 'avatar' is a relationship on the User model, you MUST add it here.
        // If 'avatar' is just a column in the 'users' table, this is fine.
        $availabilities = Availability::with(['reservation', 'teacher.teacherProfile'])
            ->where('teacher_id', $teacherId)
            ->whereBetween('start_utc', [$start, $end])
            ->orderBy('start_utc')
            ->get();

        return $availabilities->map(function ($avail) use ($student, $viewerTz) {
            
            $reservation = $avail->reservation; // Already eager loaded
            $isBooked = (bool) $avail->is_booked;
            $status = $isBooked ? ($reservation->status ?? 'booked') : 'available';
            
            // Fast ID comparison (no DB calls)
            $bookedByViewer = $isBooked && $reservation && $student && ($reservation->student_id === $student->id);

            // Access eager loaded relationships
            $teacherProfile = $avail->teacher->teacherProfile;
            $teacherName = $teacherProfile->preferred_name ?? $avail->teacher->name ?? 'Teacher';

            // Fallback to Teacher TZ if Student/Impersonator didn't provide one
            $finalTz = $viewerTz;
            if ($finalTz === 'UTC' && $teacherProfile) {
                $finalTz = $teacherProfile->tz ?? 'UTC';
            }

            // Date Calculations
            $dtStartUtc = Carbon::parse($avail->start_utc); // Assumes DB is UTC
            $dtUser = $dtStartUtc->copy()->setTimezone($finalTz);
            
            $title = $isBooked
                ? ($bookedByViewer ? 'Your booking' : "{$teacherName} (Booked)")
                : "{$teacherName} - Available";

            return [
                'id' => 'avail_' . $avail->id,
                'title' => $title,
                'start' => $avail->start_utc, // Send raw ISO if possible, or $dtStartUtc->toIso8601String()
                'end' => $avail->end_utc ?? $dtStartUtc->addMinutes(25)->toIso8601String(),
                'allDay' => false,
                'backgroundColor' => $isBooked ? ($bookedByViewer ? '#198754' : '#6c757d') : '#0d6efd',
                'borderColor' => $isBooked ? ($bookedByViewer ? '#0f5132' : '#5a6268') : '#0b5ed7',
                'textColor' => '#ffffff',
                'extendedProps' => [
                    'status' => $status,
                    'is_booked' => $isBooked,
                    'booked_by_viewer' => $bookedByViewer,
                    'availability_id' => $avail->id,
                    'reservation_id' => $reservation?->id,
                    'teacher_id' => $avail->teacher_id,
                    // Ensure this is not triggering a query per row!
                    'teacher_avatar' => $avail->teacher->avatar ?? null, 
                    'user_formatted_datetime' => $dtUser->format('M d, Y h:i A'),
                    'user_formatted_time' => $dtUser->format('g:i A'),
                ],
            ];
        });
    }
}
