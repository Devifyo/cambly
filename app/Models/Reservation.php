<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class Reservation extends Model
{
    use HasFactory;
    protected $fillable = [
        'student_id',
        'teacher_id',
        'availability_id',
        'is_hold',
        'cycle_start_utc',
        'status',
        'lesson_meeting_link',
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }
    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }
    public function availability()
    {
        return $this->belongsTo(Availability::class);
    }

    public function getStatusAttribute($originalStatus)
    {
        // 1. Check if status is 'booked'
        if ($originalStatus === 'booked') {

            // 2. Check if the availability start time is in the past
            // We must have the 'availability' relation loaded to check this
            if ($this->relationLoaded('availability') && $this->availability) {
                
                $startTime = Carbon::parse($this->availability->start_utc);

                if ($startTime->isPast()) {
                    // 3. Mark status as 'completed' (the update)
                    // This is the "side effect" - it writes to the database
                    $this->attributes['status'] = 'completed'; 
                    $this->save();

                    // 4. Pass status as 'completed' (the return)
                    return 'completed';
                }
            }
        }

        // If the conditions are not met, return the original status
        return $originalStatus;
    }


    /**
     * Scope query to reservations for a specific student.
     */
    public function scopeForStudent(Builder $query, User $user)
    {
        $query->where('student_id', $user->id);
    }

    /**
     * Scope query to upcoming lessons.
     */
    public function scopeUpcoming(Builder $query)
    {
        $query->where('status', '!=', 'cancelled')
            ->whereHas('availability', fn($q) => $q->where('start_utc', '>', now()));
    }

    /**
     * Scope query to completed lessons.
     */
    public function scopeCompleted(Builder $query)
    {
        $query->where(function ($q) {
            $q->where('status', 'completed')
                ->orWhere(function ($sq) {
                    $sq->where('status', '!=', 'cancelled')
                        ->whereHas('availability', fn($aq) => $aq->where('start_utc', '<', now()));
                });
        });
    }

    /**
     * Scope query to cancelled lessons.
     */
    public function scopeCancelled(Builder $query)
    {
        $query->where('status', 'cancelled');
    }

    /**
     * Apply date filter.
     */
    public function scopeFilterByDate(Builder $query, string $date)
    {
        $query->whereHas('availability', function ($q) use ($date) {
            $q->whereDate('start_utc', $date);
        });
    }

    /**
     * Apply teacher name filter.
     */
    public function scopeFilterByTeacher(Builder $query, string $teacherName)
    {
        $query->whereHas('teacher', function ($q) use ($teacherName) {
            $q->where('name', 'like', '%' . $teacherName . '%')
              ->orWhereHas('teacherProfile', function ($sq) use ($teacherName) {
                  $sq->where('preferred_name', 'like', '%' . $teacherName . '%');
              });
        });
    }
}
