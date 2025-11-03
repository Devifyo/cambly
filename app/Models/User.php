<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Cashier\Billable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, Billable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name', 'email', 'password', 'status',
        'stripe_id', 'pm_type', 'pm_last_four', 'trial_ends_at'
    ];


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

     public function studentProfile() {
        return $this->hasOne(StudentProfile::class);
    }

    public function teacherProfile() {
        return $this->hasOne(TeacherProfile::class);
    }

    public function availabilities() {
        return $this->hasMany(Availability::class, 'teacher_id');
    }

    public function reservationsAsStudent() {
        return $this->hasMany(Reservation::class, 'student_id');
    }

    public function reservationsAsTeacher() {
        return $this->hasMany(Reservation::class, 'teacher_id');
    }

    public function ticketLedgers() {
        return $this->hasMany(TicketLedger::class, 'student_id');
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

        public function isTeacher(): bool
    {
        return $this->hasRole(config('roles.teacher'));
    }

    /**
     * Check if the user has the 'student' role.
     */
    public function isStudent(): bool
    {
        return $this->hasRole(config('roles.student'));
    }

        /**
     * Scope a query to only include teachers.
     */
    public function scopeTeachers($query)
    {
        return $query->role(config('roles.teacher'));
    }

    /**
     * Scope a query to only include students.
     */
    public function scopeStudents($query)
    {
        return $query->role(config('roles.student'));
    }

    /**
     * Get the active and valid subscription for the user.
     * A subscription is considered active if:
     * - Status is 'active'
     * - Current period has not ended (current_period_end > now())
     * - Not ended (ends_at is null or in the future)
     */
    public function activeSubscription()
    {
        return $this->hasOne(\App\Models\Subscription::class)
            ->where('status', 'active')
            ->where(function ($query) {
                $query->whereNull('ends_at')
                    ->orWhere('ends_at', '>', now());
            })
            ->where('current_period_start', '<=', now()) // make sure period has started
            ->where('current_period_end', '>=', now())   // and not expired yet
            ->latest('created_at');
    }
    /**
     * Check if the user has an active and valid subscription.
     */
    public function hasActiveSubscription(): bool
    {
        return $this->activeSubscription()->exists();
    }

    /**
     * Get the active plan from the active subscription.
     */
    public function activePlan()
    {
        return $this->activeSubscription()->with('plan')->first()?->plan;
    }

    public function hasEverSubscribed()
    {
        return $this->subscriptions()
            ->whereNotNull('plan_id')
            ->exists();
    }


/*********************Teacher filter scopes*******************************/
public function scopeWithFilter($query, array $filters = [])
{
    // Check what kind of filters we have
    $hasNameFilter = !empty($filters['name']);
    $hasAvailabilityFilter = !empty($filters['start_utc']) 
        || !empty($filters['end_utc']) 
        || isset($filters['include_past']);
    return $query
        // 1️⃣ Filter by teacher name if provided
        ->when($hasNameFilter, function ($q) use ($filters) {
            $q->where('name', 'like', '%' . trim($filters['name']) . '%');
        })

        // 2️⃣ Only apply whereHas if availability-related filters are provided
        ->when($hasAvailabilityFilter, function ($q) use ($filters) {
            $q->whereHas('availabilities', function ($subQ) use ($filters) {
                $this->applyAvailabilityFilters($subQ, $filters);
            });
        })

        // 3️⃣ Always eager load availabilities (filtered or not)
        ->with(['availabilities' => function ($q) use ($filters, $hasAvailabilityFilter) {
            if ($hasAvailabilityFilter) {
                $this->applyAvailabilityFilters($q, $filters);
            }
            $q->orderBy('start_utc', 'asc');
        }]);
}

/**
 * Apply common availability filters
 */
protected function applyAvailabilityFilters($query, array $filters)
{   
    return $query
        ->where('is_booked', false)
        ->when(!empty($filters['start_utc']), function ($q) use ($filters) {
            $q->whereDate('start_utc', '>=', $filters['start_utc']);
        })
        ->when(!empty($filters['end_utc']), function ($q) use ($filters) {
            $q->whereDate('end_utc', '<=', $filters['end_utc']);
        })
        ->when(empty($filters['include_past']), function ($q) {
            $q->where('start_utc', '>=', now());
        });
}


}
