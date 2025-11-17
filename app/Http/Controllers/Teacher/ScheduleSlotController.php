<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Teacher\ScheduleSlotService;
use App\Models\Availability; // Use your model

class ScheduleSlotController extends Controller
{
    protected $slots;

    public function __construct(ScheduleSlotService $slots)
    {
        $this->slots = $slots;
    }

    public function index(Request $request)
    {
        $teacher = auth()->user();

        if ($request->ajax() || $request->wantsJson()) {
            
            $slots = Availability::where('teacher_id', $teacher->id)
                                ->where('start_utc', '>=', $request->start)
                                ->where('end_utc', '<=', $request->end)
                                ->get();

            $events = $slots->map(function ($slot) {
                return [
                    'id'          => $slot->id,
                    'start'       => $slot->start_utc,
                    'end'         => $slot->end_utc,
                    'extendedProps' => [
                        'is_booked' => (bool)$slot->is_booked,
                    ]
                ];
            });
            return response()->json($events);
        }

        return view('teacher.schedule.slots');
    }

    public function store(Request $request)
    {
        $teacher = auth()->user();
        $request->validate([
            'start_time' => 'required|date|before:end_time',
            'end_time'   => 'required|date|after:start_time',
        ]);

        $slot = $this->slots->createSlot($teacher->id, $request->start_time, $request->end_time);
        if ($slot === -1) {
             return response()->json(['message' => 'Slot duration cannot be more than ' . $duration . ' minutes.'], 422);
        }
        if (!$slot) {
            return response()->json(['message' => 'This time slot overlaps with an existing one.'], 422);
        }

        return response()->json(['message' => 'Slot added successfully.', 'slot' => $slot], 201);
    }

    public function update(Request $request, $id)
    {
        $teacher = auth()->user();

        $request->validate([
            'start_time' => 'required|date|before:end_time',
            'end_time'   => 'required|date|after:start_time',
        ]);

        $slot = $this->slots->updateSlot(
            $teacher->id, 
            $id, 
            $request->start_time, 
            $request->end_time
        );

        if ($slot === -1) {
             return response()->json(['message' => 'Slot duration cannot be more than ' . $duration . ' minutes.'], 422);
        }

        if ($slot === null) {
            return response()->json(['message' => 'This time slot overlaps with an existing one.'], 422);
        }
        
        if ($slot === false) {
             return response()->json(['message' => 'Slot not found or it is already booked and cannot be edited.'], 404);
        }

        return response()->json(['message' => 'Slot updated successfully.', 'slot' => $slot]);
    }

    /**
     * Updated Destroy Method
     */
    public function destroy($id)
    {
        $teacher = auth()->user();
        
        $deleteResult = $this->slots->deleteSlot($teacher->id, $id);

        if ($deleteResult === 1) {
            return response()->json(['message' => 'Slot removed.']);
        }
        if ($deleteResult === 0) {
            return response()->json(['message' => 'Slot not found.'], 404);
        }
        if ($deleteResult === -1) {
            return response()->json(['message' => 'Cannot delete a booked slot.'], 422);
        }
        if ($deleteResult === -2) {
            return response()->json(['message' => 'Cannot delete a slot that starts within 12 hours (or is in the past).'], 422);
        }

        return response()->json(['message' => 'Could not delete slot.'], 500);
    }
}