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
            // ->where('current_period_start', '<=', now()) // make sure period has started
            // ->where('current_period_end', '>=', now())   // and not expired yet
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


/********************* Teacher filter scopes *******************************/
    
    /**
     * Filter teachers by name
     */
    public function scopeFilterByName($query, $name)
    {
        return $query->when($name, function ($q) use ($name) {
            $q->where('name', 'like', "%{$name}%");
        });
    }

    /**
     * Filter teachers who have availabilities at specific datetime
     */
    public function scopeFilterByAvailability($query, $startUtc)
    {
        return $query->when($startUtc, function ($q) use ($startUtc) {
            $start = \Carbon\Carbon::parse($startUtc);
            
            $q->whereHas('availabilities', function ($query) use ($start) {
                $query->where('is_booked', false)
                    ->whereBetween('start_utc', [
                        $start->copy()->startOfMinute(),
                        $start->copy()->endOfMinute(),
                    ]);
            });
        });
    }

    /**
     * Eager load teacher profile and filtered availabilities
     */
    public function scopeWithTeacherData($query, $startUtc = null)
    {
        return $query->with([
            'teacherProfile',
            'availabilities' => function ($q) use ($startUtc) {
                $q->where('is_booked', false);

                if ($startUtc) {
                    $start = \Carbon\Carbon::parse($startUtc);
                    $q->whereBetween('start_utc', [
                        $start->copy()->startOfMinute(),
                        $start->copy()->endOfMinute(),
                    ]);
                }

                $q->select('id', 'teacher_id', 'start_utc', 'end_utc', 'is_booked')
                  ->orderBy('start_utc');
            }
        ]);
    }


    /**
     * Filter teachers by gender
     */
    public function scopeFilterByGender($query, $gender)
    {  
        if(!is_null($gender)){
            return $query->when($gender, function ($q) use ($gender) {
                $q->whereHas('teacherProfile', function ($query) use ($gender) {
                    $query->where('gender', $gender);
                });
            });
         }
    }

    /**
     * Filter teachers by native language
     */
    public function scopeFilterByLanguage($query, $languages)
    {
        return $query->when($languages, function ($q) use ($languages) {
            // Handle both array and single value
            $languageArray = is_array($languages) ? $languages : [$languages];
            // dd($languageArray);
            $languageArray = ['english'];
            $q->whereHas('teacherProfile', function ($query) use ($languageArray) {
                $query->whereIn('native_language', $languageArray);
            });
        });
    }






}
