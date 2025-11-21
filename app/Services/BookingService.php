<?php

namespace App\Services;

use App\Models\User;
use App\Models\Availability;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\TicketLedger;
use Illuminate\Support\Collection;
class BookingService
{   

    protected UseCreditService $useCreditService;
    protected CreditService $creditService;
    public function __construct(UseCreditService $useCreditService, CreditService $creditService)
    {
        $this->useCreditService = $useCreditService;
        $this->creditService = $creditService;
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
                'slot_status' => $av->is_booked ? 'booked' : 'available',
                'booked_by_viewer' => $av->is_booked && $viewer ? bookedBy($av, $viewer) : false,
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
    $isAdminImpersonating = is_impersonating() && impersonator()->isAdmin();
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

        $creditsNeeded = config('app.ticket_per_meeting', 1);
        // If user has at least 1 available credit => confirmed booking flow
        if ($available >= $creditsNeeded || $isAdminImpersonating) {
            $reservation = Reservation::updateOrCreate(
            [
            'student_id' => $student->id,
            'teacher_id' => $availability->teacher_id,
            'availability_id' => $availability->id,
            ],
            [
                'is_hold' => false,
                'cycle_start_utc' => $availability->start_utc,
                'status' => 'booked',
            ]);

            $availability->is_booked = true;
            $availability->save();
            // consume 1 credit from current cycle

            $encryptedReservationId = encryptId($reservation->id);
            if(!$isAdminImpersonating){
                $this->consumeCredit($student, $creditInfo, $creditsNeeded);
                // Record transaction in CreditService
                
                $this->creditService->recordCreditTransaction(
                    $student->id,
                    $creditInfo['cycle_number'] ?? 1,
                    $creditsNeeded,
                    'debt',
                    'booking_confirmed',
                    "Booking confirmed for booking #{$encryptedReservationId}",
                    "reservation_{$encryptedReservationId}",
                    null, // plan
                    $creditInfo['ledger_id'] ?? null,
                    $reservation->id,
                    'reservation_confirm'
                );
            }else{
                  $this->creditService->recordCreditTransaction(
                    $student->id,
                    $creditInfo['cycle_number'] ?? 1,
                    0,
                    'debt',
                    'booking_confirmed',
                    "Booking created by Admin for booking #{$encryptedReservationId}",
                    "reservation_{$encryptedReservationId}",
                    null, // plan
                    $creditInfo['ledger_id'] ?? null,
                    $reservation->id,
                    'reservation_confirm'
                );
            }
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
        $reservation = Reservation::updateOrCreate(           [
            'student_id' => $student->id,
            'teacher_id' => $availability->teacher_id,
            'availability_id' => $availability->id,
            ],[
            'is_hold' => true,
            'cycle_start_utc' => $availability->start_utc,
            'status' => 'booked',
        ]);

        // mark availability reserved (so others can't book)
        $availability->is_booked = true;
        $availability->save();

        // create a hold for next cycle (1 credit)
        $hold = $this->createHoldForNextCycle($student, $creditInfo, $creditsNeeded);
                // Record hold transaction in CreditService
        $this->creditService->recordCreditTransaction(
            $student->id,
            $creditInfo['cycle_number'] ?? 1,
             $creditsNeeded,
            'hold',
            'booking_hold',
            "Booking on hold for booking #{$reservation->id} - will deduct from next cycle",
            "booking_{$reservation->id}",
            null, // plan
            $creditInfo['ledger_id'] ?? null,
            $reservation->id,
            'booking_hold'
        );
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
/**
 * Cancel a reservation. Authorization: only the student or teacher can cancel.
 * - Always mark reservation as canceled (even if meeting already happened)
 * - If meeting start is in the future, unlock availability (is_booked = false) under lock
 * - Attempt refund via refundCreditsOnCancel() (that function enforces the 12-hour rule)
 *
 * @param  \App\Models\Reservation  $reservation
 * @param  \App\Models\User         $actor
 * @return array
 */
    public function cancel(Reservation $reservation, User $actor): array
    {   
        // Authorization
        // if ($reservation->student_id !== $actor->id && $reservation->teacher_id !== $actor->id) {
        //     return ['error' => 'Forbidden', 'status' => 403];
        // }



        // Short-circuit if already cancelled
        if ($reservation->status === 'canceled') {
            return ['error' => 'Reservation already canceled', 'status' => 400];
        }

        try {
            $result = DB::transaction(function () use ($reservation, $actor) {
                $availability = $reservation->availability;
                $nowUtc = Carbon::now('UTC');

                $response = [
                    'ok' => true,
                    'reservation_id' => $reservation->id,
                    'canceled_by' => $actor->id,
                    'availability_reopened' => false,
                    'refund' => null,
                    'note' => null,
                ];

                // Determine start time (prefer canonical start_utc)
                if ($availability) {
                    $rawStart = $availability->start_utc
                        ?? ($availability->{self::START_TIME_COLUMN} ?? $reservation->created_at);
                    $startUtc = Carbon::parse($rawStart, 'UTC')->setTimezone('UTC');

                    // If meeting is strictly in the future, reopen availability under lock
                    if ($startUtc->greaterThan($nowUtc)) {
                        $availRow = \App\Models\Availability::where('id', $availability->id)
                            ->lockForUpdate()
                            ->first();
                        if ($availRow) {
                            $availRow->is_booked = false;
                            $availRow->save();
                            $response['availability_reopened'] = true;
                        }
                    } else {
                        $response['note'] = 'Meeting already started/finished; availability left unchanged.';
                    }
                }

                // Mark reservation canceled and optionally record cancellation metadata if available
                $reservation->status = 'cancelled';
                // Optionally set canceled_by_id / canceled_at if your schema has them
                if (property_exists($reservation, 'canceled_by_id')) {
                    $reservation->canceled_by_id = $actor->id;
                }
                if (property_exists($reservation, 'canceled_at')) {
                    $reservation->canceled_at = Carbon::now();
                }
                $reservation->save();

                // Attempt refund (refundCreditsOnCancel enforces the 12-hour rule)
                $refundResult = $this->useCreditService->refundCreditsOnCancel($reservation);
                $encryptedReservationId = encryptId($reservation->id);
                $response['refund'] = isset($refundResult['refunded']) ? $refundResult['refunded'] : null;
                 if ($refundResult && isset($refundResult['refunded']) && $refundResult['refunded']) {
                    // Credits were refunded
                    $creditInfo = $this->getCurrentMonthCreditInfo($reservation->student);
                    $creditsRefunded = $refundResult['credits_refunded'] ?? config('app.ticket_per_meeting', 1);
                    $this->creditService->recordCreditTransaction(
                        $reservation->student_id,
                        $creditInfo['cycle_number'] ?? 1,
                        $creditsRefunded,
                        'refund', // This is a credit (refund), not debit
                        'booking_cancelled_refund',
                        "Booking cancelled and refunded for booking #{$encryptedReservationId}",
                        "booking_cancel_{$encryptedReservationId}",
                        null, // plan
                        $creditInfo['ledger_id'] ?? null,
                        $reservation->id,
                        'booking_cancel'
                    );
            } else {
                // No refund (past 12-hour window or other reason)
                $creditInfo = $this->getCurrentMonthCreditInfo($reservation->student);

                $this->creditService->recordCreditTransaction(
                    $reservation->student_id,
                    $creditInfo['cycle_number'] ?? 1,
                    0, // No credits changed
                    'no_refund',
                    'booking_cancelled_no_refund',
                    "Booking cancelled without refund for booking #{$encryptedReservationId} - " . 
                    ($refundResult['reason'] ?? 'outside refund window'),
                    "booking_cancel_{$encryptedReservationId}",
                    null, // plan
                    $creditInfo['ledger_id'] ?? null,
                    $reservation->id,
                    'booking_cancel'
                );
            }
                return $response;
            }, 5); // retry up to 5 times for deadlock safety

            // Log outcome
            Log::info('Reservation canceled', [
                'reservation' => $reservation->id ?? null,
                'actor' => $actor->id ?? null,
                'result' => $result,
            ]);

            return $result;
        } catch (\Throwables $e) {
            Log::error('BookingService::cancel error: '.$e->getMessage(), [
                'reservation' => $reservation->id ?? null,
                'actor' => $actor->id ?? null,
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
            $info = $this->useCreditService->getCurrentMonthCredits($student, 'show_all');
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
                if ($count <= 0) {
                    return false;
                }

                $studentId = $student->id;
                $currentLedgerId = $currentCreditInfo['ledger_id'] ?? null;

                // 1) Try the provided ledger id first (atomic)
                if ($currentLedgerId) {
                    $affected = \App\Models\TicketLedger::where('id', $currentLedgerId)
                        ->where('student_id', $studentId)
                        ->whereRaw('(issued_credits - used_credits - hold_credits) >= ?', [$count])
                        ->increment('used_credits', $count);

                    if ($affected) {
                        Log::info('consumeCredit: consumed from current ledger', [
                            'student_id' => $studentId,
                            'ledger_id'  => $currentLedgerId,
                            'needed'     => $count,
                        ]);
                        return true;
                    }

                    Log::info('consumeCredit: current ledger insufficient or unavailable, will try other ledgers', [
                        'student_id' => $studentId,
                        'ledger_id'  => $currentLedgerId,
                        'needed'     => $count,
                    ]);
                }

                // 2) Use the ledgers collection supplied in $currentCreditInfo if available
                $candidateLedgers = collect();

                if (!empty($currentCreditInfo['ledgers'])) {
                    $raw = $currentCreditInfo['ledgers'];

                    // Normalize to a Collection of models
                    if ($raw instanceof Collection) {
                        $candidateLedgers = $raw;
                    } elseif (is_array($raw)) {
                        $candidateLedgers = collect($raw);
                    }

                    // keep only ledgers that belong to this student and have an id
                    $candidateLedgers = $candidateLedgers->filter(function ($l) use ($studentId) {
                        // handle both model and array shapes
                        $lid = data_get($l, 'id');
                        $sid = data_get($l, 'student_id');
                        return $lid && ((int)$sid === (int)$studentId);
                    })->sortBy(function ($l) {
                        return data_get($l, 'created_at') ?? null;
                    })->values();
                }

                // 3) Iterate and try atomic increment on each candidate ledger
                foreach ($candidateLedgers as $ledger) {
                    $ledgerId = data_get($ledger, 'id');

                    // skip already attempted current ledger id
                    if ($currentLedgerId && (int)$ledgerId === (int)$currentLedgerId) {
                        continue;
                    }

                    $affected = \App\Models\TicketLedger::where('id', $ledgerId)
                        ->where('student_id', $studentId)
                        ->whereRaw('(issued_credits - used_credits - hold_credits) >= ?', [$count])
                        ->increment('used_credits', $count);

                    if ($affected) {
                        Log::info('consumeCredit: consumed from fallback ledger', [
                            'student_id' => $studentId,
                            'ledger_id'  => $ledgerId,
                            'needed'     => $count,
                        ]);
                        return true;
                    }

                    // concurrent consumption may have happened — try next ledger
                    Log::info('consumeCredit: candidate ledger had insufficient credits at increment time, trying next', [
                        'student_id' => $studentId,
                        'ledger_id'  => $ledgerId,
                        'needed'     => $count,
                    ]);
                }

                // 4) Nothing consumed
                Log::warning('consumeCredit: no ledger could be consumed (all insufficient)', [
                    'student_id' => $studentId,
                    'needed'     => $count,
                    'candidate_count' => $candidateLedgers->count(),
                ]);

                return false;

            } catch (\Throwable $e) {
                Log::error('consumeCredit error: ' . $e->getMessage(), [
                    'student_id' => $student->id ?? null,
                    'trace' => $e->getTraceAsString(),
                ]);
                return false;
            }
    }

}
