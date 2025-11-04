<?php

namespace App\Services;

use App\Models\User;
use App\Models\Availability;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\TicketLedger;

class BookingService
{   

    protected UseCreditService $useCreditService;

    public function __construct(UseCreditService $useCreditService)
    {
        $this->useCreditService = $useCreditService;
    }
    /**
     * Get available slots for a teacher on a given date, converted to viewer timezone.
     *
     * @param  int  $teacherId
     * @param  \Carbon\Carbon  $date
     * @param  \App\Models\User|null  $viewer
     * @return array
     */
    public function getSlots(int $teacherId, Carbon $date, ?User $viewer = null): array
    {
        $teacher = User::find($teacherId);
        if (! $teacher) {
            return ['error' => 'Teacher not found'];
        }

        $viewerTz = $this->resolveViewerTimezone($viewer, $teacher);

        $startUtc = $date->copy()->startOfDay()->toDateTimeString();
        $endUtc   = $date->copy()->endOfDay()->toDateTimeString();

        $availabilities = Availability::where('teacher_id', $teacher->id)
            ->where('is_booked', false)
            ->whereBetween('start_utc', [$startUtc, $endUtc])
            ->orderBy('start_utc')
            ->get();

        $slots = ['morning' => [], 'afternoon' => [], 'evening' => []];

        foreach ($availabilities as $av) {
            $dtUtc = Carbon::parse($av->start_utc, 'UTC');
            $dtUser = $dtUtc->copy()->setTimezone($viewerTz);
            $hourUser = (int) $dtUser->format('H');

            $entry = [
                'id' => function_exists('encryptId') ? encryptId($av->id) : $av->id,
                'raw_id' => $av->id,
                'iso_utc' => $dtUtc->toDateTimeString(),
                'iso_user' => $dtUser->toDateTimeString(),
                'time_utc' => $dtUtc->format('H:i'),
                'time_user' => $dtUser->format('H:i'),
                'label_user' => $dtUser->format('g:i A'),
            ];

            if ($hourUser < 12) {
                $slots['morning'][] = $entry;
            } elseif ($hourUser < 17) {
                $slots['afternoon'][] = $entry;
            } else {
                $slots['evening'][] = $entry;
            }
        }

        return [
            'date' => $date->format('Y-m-d'),
            'viewer_timezone' => $viewerTz,
            'slots' => $slots,
        ];
    }

    /**
     * Confirm booking by availability id (integer).
     * Returns reservation or throws.
     *
     * @param  int  $availabilityId
     * @param  \App\Models\User  $student
     * @param  int|null  $teacherId
     * @return array
     */
public function confirm(int $availabilityId, User $student, ?int $teacherId = null): array
{
    $creditInfo = $this->getCurrentMonthCreditInfo($student);
    $available = (int) ($creditInfo['available'] ?? 0);

    DB::beginTransaction();
    try {
        $availability = Availability::where('id', $availabilityId)->lockForUpdate()->first();

        if (! $availability) {
            DB::rollBack();
            return ['error' => 'Availability not found', 'status' => 404];
        }

        if ($availability->is_booked) {
            DB::rollBack();
            return ['error' => 'This slot is already booked', 'status' => 409];
        }

        if (! is_null($teacherId) && $teacherId != $availability->teacher_id) {
            DB::rollBack();
            return ['error' => 'Teacher mismatch for this availability', 'status' => 400];
        }

        // If user has at least 1 available credit => confirmed booking flow
        if ($available >= 1) {
            $reservation = Reservation::create([
                'student_id' => $student->id,
                'teacher_id' => $availability->teacher_id,
                'availability_id' => $availability->id,
                'is_hold' => false,
                'cycle_start_utc' => $availability->start_utc,
                'status' => 'booked',
            ]);

            $availability->is_booked = true;
            $availability->save();

            // consume 1 credit from current cycle
            $this->consumeCredit($student, $creditInfo, config('app.ticket_per_meeting'));

            DB::commit();

            $teacher = User::find($reservation->teacher_id);
            $teacherTz = optional($teacher->teacherProfile)->timezone ?? config('app.timezone', 'UTC');
            if (! in_array($teacherTz, \DateTimeZone::listIdentifiers())) $teacherTz = 'UTC';

            $dtUtc = Carbon::parse($reservation->cycle_start_utc, 'UTC');
            $dtTeacher = $dtUtc->copy()->setTimezone($teacherTz);

            return [
                'reservation' => $reservation,
                'teacher_timezone' => $teacherTz,
                'start_utc' => $dtUtc->toDateTimeString(),
                'start_teacher' => $dtTeacher->toDateTimeString(),
                'label_start_teacher' => $dtTeacher->format('g:i A, M j, Y'),
            ];
        }

        // No available credits: create reservation on-hold + schedule 1 hold credit for next cycle
        $reservation = Reservation::create([
            'student_id' => $student->id,
            'teacher_id' => $availability->teacher_id,
            'availability_id' => $availability->id,
            'is_hold' => true,
            'cycle_start_utc' => $availability->start_utc,
            'status' => 'booked',
        ]);

        // mark availability reserved (so others can't book)
        $availability->is_booked = true;
        $availability->save();

        // create a hold for next cycle (1 credit)
        $hold = $this->createHoldForNextCycle($student, $creditInfo, config('app.ticket_per_meeting'));

        DB::commit();

        $teacher = User::find($reservation->teacher_id);
        $teacherTz = optional($teacher->teacherProfile)->timezone ?? config('app.timezone', 'UTC');
        if (! in_array($teacherTz, \DateTimeZone::listIdentifiers())) $teacherTz = 'UTC';

        $dtUtc = Carbon::parse($reservation->cycle_start_utc, 'UTC');
        $dtTeacher = $dtUtc->copy()->setTimezone($teacherTz);

        return [
            'message' => 'Reservation created on hold; 1 credit scheduled for next month.',
            'reservation' => $reservation,
            'hold' => $hold,
            'teacher_timezone' => $teacherTz,
            'start_utc' => $dtUtc->toDateTimeString(),
            'start_teacher' => $dtTeacher->toDateTimeString(),
            'label_start_teacher' => $dtTeacher->format('g:i A, M j, Y'),
        ];
    } catch (\Throwables $e) {
        DB::rollBack();
        Log::error('BookingService::confirm error: '.$e->getMessage(), [
            'availability' => $availabilityId,
            'student' => $student->id ?? null,
        ]);
        return ['error' => 'Failed to confirm booking', 'status' => 500];
    }
}


    /**
     * Cancel reservation (owner or teacher allowed).
     *
     * @param  \App\Models\Reservation  $reservation
     * @param  \App\Models\User  $actor
     * @return array
     */
    public function cancel(Reservation $reservation, User $actor): array
    {
        if ($reservation->student_id !== $actor->id && $reservation->teacher_id !== $actor->id) {
            return ['error' => 'Forbidden', 'status' => 403];
        }

        DB::beginTransaction();
        try {
            if ($reservation->status === 'canceled') {
                DB::rollBack();
                return ['error' => 'Reservation already canceled', 'status' => 400];
            }

            if ($reservation->availability_id) {
                $availability = Availability::where('id', $reservation->availability_id)->lockForUpdate()->first();
                if ($availability) {
                    $availability->is_booked = false;
                    $availability->save();
                }
            }

            $reservation->status = 'canceled';
            $reservation->save();

            DB::commit();
            return ['ok' => true];
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('BookingService::cancel error: '.$e->getMessage(), [
                'reservation' => $reservation->id ?? null,
            ]);
            return ['error' => 'Failed to cancel reservation', 'status' => 500];
        }
    }

    /**
     * Resolve timezone for viewer; fallback to teacher/app/UTC.
     */
    protected function resolveViewerTimezone(?User $viewer, User $teacher): string
    {
        $tz = null;
        if ($viewer) {
            $tz = optional($viewer->studentProfile)->tz
                ?? optional($viewer->teacherProfile)->tz;
        }

        $tz = $tz ?? optional($teacher->teacherProfile)->tz ?? config('app.timezone', 'UTC');

        return in_array($tz, \DateTimeZone::listIdentifiers()) ? $tz : 'UTC';
    }

    /**
     * Protected: get current month credit info via UseCreditService.
     */
    protected function getCurrentMonthCreditInfo(User $student): array
    {
        try {
            $info = $this->useCreditService->getCurrentMonthCredits($student);
            return is_array($info) ? $info : [];
        } catch (\Throwable $e) {
            Log::error('UseCreditService error: '.$e->getMessage(), ['student' => $student->id ?? null]);
            return [];
        }
    }

    /**
     * Protected: create a TicketLedger row for the next cycle with hold_credits.
     *
     * @param User $student
     * @param array $currentCreditInfo
     * @param int $holdCount
     * @return \App\Models\TicketLedger|null
     */
    protected function createHoldForNextCycle(User $student, array $currentCreditInfo, int $holdCount = 1)
    {
        try {
            $ledger = TicketLedger::where('id', $currentCreditInfo['ledger_id'])->first();

            // 1. Add a check to ensure the ledger was found
            if (!$ledger) {
                Log::warning('BookingService::createHoldForNextCycle ledger not found', [
                    'ledger_id' => $currentCreditInfo['ledger_id'],
                    'student' => $student->id ?? null,
                ]);
                return null;
            }

            // Increment the value in the database
            $ledger->increment('hold_credits', $holdCount);

            // 2. Refresh the object to get the new value from the database
            $ledger->refresh();

            return $ledger;

        } catch (\Throwable $e) {
            Log::error('BookingService::createHoldForNextCycle error: '.$e->getMessage(), [
                'student' => $student->id ?? null,
            ]);
            return null;
        }
    }

    /**
     * Protected: consume credits from the given cycle by incrementing used_credits.
     *
     * @param User $student
     * @param int $cycleNumber
     * @param int $count
     * @return bool
     */
    protected function consumeCredit(User $student, array $currentCreditInfo, int $count = 1): bool
    {
        try {
            $ledger = TicketLedger::where('id', $currentCreditInfo['ledger_id'])->first();

            if (!$ledger) {
                Log::warning('BookingService::consumeCredit ledger not found', [
                    'ledger_id' => $currentCreditInfo['ledger_id'],
                    'student' => $student->id ?? null,
                ]);
                return false;
            }

            // This is the line you asked about. 
            // It handles the database increment and returns the number of affected rows (usually 1).
            $affected = $ledger->increment('used_credits', $count);

            return (bool) $affected;

        } catch (\Throwable $e) {
            // ... your existing catch block ...
            Log::error('BookingService::consumeCredit error: '.$e->getMessage(), [
                'student' => $student->id ?? null,
            ]);
            return false;
        }
    }
}
