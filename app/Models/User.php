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
}
