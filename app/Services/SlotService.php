<?php

namespace App\Services;

use App\Models\Availability;
use App\Models\Reservation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SlotService
{
    /**
     * Return availability/reservation events for a teacher in a given start..end range.
     *
     * @param User|null $student  - currently authenticated student (can be null)
     * @param int|string $teacherId - raw database teacher id (not encrypted)
     * @param string $startIso    - ISO datetime string or Carbon-parsable
     * @param string $endIso      - ISO datetime string or Carbon-parsable
     * @return \Illuminate\Support\Collection of arrays shaped for FullCalendar
     */
    public function getWeekSlotsForTeacher(?User $student, $teacherId, string $startIso, string $endIso): Collection
    {   
        // dd("lol");
        try {
            $start = Carbon::parse($startIso);
            $end = Carbon::parse($endIso);
        } catch (\Throwable $e) {
            // fallback to current week
            $start = Carbon::now()->startOfWeek();
            $end = Carbon::now()->endOfWeek();
        }

        // Load Availabilities in the range for that teacher.
        // NOTE: adjust 'teacher_id' column to match your schema; change model if you use a different table.
        $availabilities = Availability::with(['reservation', 'teacher.teacherProfile'])
            ->where('teacher_id', $teacherId)
            ->whereBetween('start_utc', [$start, $end])
            ->orderBy('start_utc')
            ->get();
        // If you don't have an Availability model, you can use Reservation->whereHas('availability') similarly.
        // Map availabilities to FullCalendar events
        $events = $availabilities->map(function ($avail) use ($student) {

            // reservation relationship may be null if nobody booked it yet
            $reservation = $avail->reservation ?? null;

            $status = $reservation ? $reservation->status : 'available';
            $isBooked = $status !== 'available';
            $bookedByViewer = $reservation && $student && ($reservation->student_id == $student->id);

            $teacherName = $avail->teacher->teacherProfile->preferred_name
                ?? $avail->teacher->name
                ?? 'Teacher';

            // form human-friendly user formatted time according to viewer timezone if available
            $isImpersonating = is_impersonating();
            $viewerTz = 'UTC';
            if($isImpersonating){
                $viewerTz = getTimeZone();
            }else{
                $viewerTz = $student?->studentProfile?->tz ?? ($avail->teacher->teacherProfile->tz ?? 'UTC');
            }

            $dtStartUtc = Carbon::parse($avail->start_utc, 'UTC');
            $dtUser = $dtStartUtc->copy()->setTimezone($viewerTz);

            $title = $isBooked
                ? ($bookedByViewer ? 'Your booking' : "{$teacherName} (Booked)")
                : "{$teacherName} - Available";
            // dd( Carbon::parse($avail->end_utc ?? Carbon::parse($avail->start_utc)->addMinutes(25))->toIso8601String());
            return [
                // event id: prefix to differentiate
                'id' => 'avail_' . $avail->id,
                'title' => $title,
                'start' => Carbon::parse($avail->start_utc)->toIso8601String(),
                'end' => Carbon::parse($avail->end_utc ?? Carbon::parse($avail->start_utc)->addMinutes(25))->toIso8601String(),
                'allDay' => false,
                // styling hints (FullCalendar accepts these)
                'backgroundColor' => $isBooked ? ($bookedByViewer ? '#198754' : '#6c757d') : '#0d6efd',
                'borderColor' => $isBooked ? ($bookedByViewer ? '#0f5132' : '#5a6268') : '#0b5ed7',
                'textColor' => '#ffffff',
                // pass everything else in extendedProps
                'extendedProps' => [
                    'status' => $status,
                    'is_booked' => $isBooked,
                    'booked_by_viewer' => (bool) $bookedByViewer,
                    'availability_id' => $avail->id,
                    'reservation_id' => $reservation?->id,
                    'teacher_id' => $avail->teacher_id,
                    'teacher_avatar' => $avail->teacher->avatar ?? null,
                    'user_formatted_datetime' => $dtUser->format('M d, Y h:i A'),
                    'user_formatted_time' => $dtUser->format('g:i A'),
                ],
            ];
        });

        return $events->values();
    }
}
