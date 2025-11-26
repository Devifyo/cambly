<?php

namespace App\Http\Controllers\Student;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Availability;
use App\Models\Reservation;
use App\Services\BookingService;
use App\Services\SlotService;

class BookingController extends Controller
{   

    protected BookingService $bookingService;
     protected SlotService $slotService;
    public function __construct(BookingService $bookingService, SlotService $slotService)
    {
        $this->bookingService = $bookingService;
         $this->slotService = $slotService;
    }

       public function showDateTime(Request $request, $teacherId)
    {     
          $teacher = User::find(decryptId($teacherId));
         return view('student.inner.teacher.book-tutor',compact('teacher'));
    }

    public function slots(Request $request, $teacherId)
    {   
        if (function_exists('decryptId')) {
            try { $teacherId = decryptId($teacherId); } catch (\Throwable $e) {}
        } else {
            try { $maybe = decrypt($teacherId); $teacherId = $maybe ?: $teacherId; } catch (\Throwable $e) {}
        }

        $date = $request->query('date') ? Carbon::parse($request->query('date'))->startOfDay() : Carbon::today();
        $viewer = auth()->check() ? auth()->user() : null;

        $res = $this->bookingService->getSlots((int)$teacherId, $date, $viewer);
        if (isset($res['error'])) {
            $status = $res['status'] ?? 404;
            return response()->json(['message' => $res['error']], $status);
        }

        return response()->json($res);
    }

    public function weekSlots(Request $request, $teacherId)
    {   
        $user = $request->user();
        // decrypt teacher id if you use encryptId helper (adjust as needed)
        try {
            $teacherRawId = function_exists('decryptId') ? decryptId($teacherId) : (int) $teacherId;
        } catch (\Throwable $e) {
            $teacherRawId = (int) $teacherId;
        }
        $start = $request->query('start');
        $end = $request->query('end');

        if (! $start || ! $end) {
            return response()->json(['message' => 'Missing start or end'], 422);
        }

        try {
            $events = $this->slotService->getWeekSlotsForTeacher($user, $teacherRawId, $start, $end);
            return response()->json(['events' => $events], 200);
        } catch (\Throwables $e) {
            Log::error('slotService error: '.$e->getMessage());
            return response()->json(['message' => 'Failed to fetch slots'], 500);
        }
    }


    /**
     * POST /student/booking/confirm
     * Body: { availability_id: int, teacher_id: (optional) }
     */
    public function confirm(Request $request)
    {   

        $data = $request->validate([
            'availability_id' => ['required'],
            'teacher_id' => ['required'],
        ]);

        try {
            $availabilityId = $data['availability_id'];
            $teacherId = decryptId($data['teacher_id']);
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Invalid identifiers.'], 422);
        }

        if (! $availabilityId) {
            return response()->json(['message' => 'Invalid availability id.'], 422);
        }

        $student = $request->user();
        $iImpersonating = is_impersonating();
    
        if (!$student->hasActiveSubscription() && !$student->currentSubscription() && !$iImpersonating) {
            return response()->json([
                'message' => 'You need an active subscription to make a booking.'
            ], 403);
        }
        
        $result = $this->bookingService->confirm((int)$availabilityId, $student, (int)$teacherId);

        if (isset($result['error'])) {
            $status = $result['status'] ?? 500;
            return response()->json(['message' => $result['error']], $status);
        }

        return response()->json(array_merge(['message' => 'Booking confirmed'], $result), 201);
    }

    /**
     * POST /student/booking/{reservation}/cancel
     */
    public function cancel(Request $request,$reservation)
    {
        $user = $request->user();
        $reservation = Reservation::find(decryptId($reservation));
        $result = $this->bookingService->cancel($reservation, $user);
        if (isset($result['error'])) {
            $status = $result['status'] ?? 500;
            return response()->json(['message' => $result['error']], $status);
        }

        return back()->with('success', 'Reservation canceled.');
    }


}
