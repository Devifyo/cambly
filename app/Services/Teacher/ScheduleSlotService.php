<?php

namespace App\Services\Teacher;

use App\Models\Availability;
use Carbon\Carbon; // Import Carbon

class ScheduleSlotService
{
    public function getSlotsForTeacher($teacherId)
    {
        return Availability::where('teacher_id', $teacherId)
                           ->orderBy('start_utc')
                           ->get();
    }

    /**
     * Creates a new slot only if it does not overlap.
     */
    public function createSlot($teacherId, $start, $end)
    {   
        $maxDuration = config('app.max_meeting_duration', 25);
        $startTime = Carbon::parse($start);
        $endTime = Carbon::parse($end);
        
        // Use abs() just in case, but diffInMinutes should be positive
        if ($startTime->diffInMinutes($endTime) > $maxDuration) {
            return -1; // Signal duration error
        }
        // Check for any overlaps
        $isOverlapping = $this->hasOverlap($teacherId, $start, $end);

        if ($isOverlapping) {
            return null; // Signal overlap
        }

        return Availability::create([
            'teacher_id' => $teacherId,
            'start_utc'  => $start,
            'end_utc'    => $end,
            'is_booked'  => false,
        ]);
    }

    /**
     * Updates an existing slot.
     */
    public function updateSlot($teacherId, $slotId, $start, $end)
    {   

        $maxDuration = config('app.max_meeting_duration', 25);
        $startTime = Carbon::parse($start);
        $endTime = Carbon::parse($end);
        
        if ($startTime->diffInMinutes($endTime) > $maxDuration) {
            return -1; // Signal duration error
        }
        // Check for overlaps, EXCLUDING the current slot ID
        $isOverlapping = $this->hasOverlap($teacherId, $start, $end, $slotId);

        if ($isOverlapping) {
            return null; // Signal overlap
        }

        $slot = Availability::where('teacher_id', $teacherId)
                            ->where('id', $slotId)
                            ->where('is_booked', false) // Failsafe: Don't update booked slots
                            ->first();

        if (!$slot) {
            return false; // Not found or is booked
        }

        $slot->update([
            'start_utc' => $start,
            'end_utc'   => $end,
        ]);

        return $slot;
    }

    /**
     * Deletes a slot based on business rules.
     *
     * @return int (1 = success, 0 = not found, -1 = booked, -2 = too close to start)
     */
    public function deleteSlot($teacherId, $slotId)
    {   
        $slot = Availability::where('teacher_id', $teacherId)
        ->where('id', (int)$slotId)
        ->first();

        if (!$slot) {
            return 0; // Not found
        }

        if ($slot->is_booked) {
            return -1; // Is booked
        }

        $startTime = Carbon::parse($slot->start_utc);
        $now = Carbon::now('UTC');
        
        // false = allow negative (past slots will be < 0)
        $hoursUntilStart = $now->diffInHours($startTime, false); 

        // Check if slot is in the past OR starts within 12 hours
        if ($hoursUntilStart <= 12) {
            return -2; // Too close to start time (or in the past)
        }
        $slot->delete();
        return 1; // Returns 1 on success
    }

    /**
     * Helper function to check for overlapping slots.
     */
    private function hasOverlap($teacherId, $start, $end, $excludeSlotId = null)
    {
        $query = Availability::where('teacher_id', $teacherId)
                             ->where('start_utc', '<', $end) // Existing starts *before* new one ends
                             ->where('end_utc', '>', $start); // AND existing ends *after* new one starts

        if ($excludeSlotId) {
            $query->where('id', '!=', $excludeSlotId);
        }

        return $query->exists();
    }
}