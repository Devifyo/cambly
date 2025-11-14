<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Reservation;
use App\Services\BookingService;

class BookingController extends Controller
{   
    protected BookingService $bookingService;

    public function __construct(BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }

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
