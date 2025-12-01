<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Cashier\Billable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\Storage;
use Lab404\Impersonate\Models\Impersonate;
// for forget password
use Illuminate\Support\Facades\Notification;
use App\Notifications\CommonNotification;
use App\Helpers\EmailHelper;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, Billable, HasRoles;
    use Impersonate;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name', 'email', 'password', 'status',
        'stripe_id', 'pm_type', 'pm_last_four', 'trial_ends_at',   'profile_picture','gender'
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

    protected $appends = ['profile_link'];


    public function canImpersonate(): bool
    {
        return $this->isAdmin() || $this->isSubadmin() || $this->isOps();
    }


    public function canBeImpersonated(): bool
    {
        return !$this->isAdmin();
    }
    
    public function getRoleNameAttribute()
    {
        return $this->getRoleNames()->first() ?? 'No Role';
    }
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

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }


    /**
     * Check if the user has the 'student' role.
     */
    public function isStudent(): bool
    {
        return $this->hasRole(config('roles.student'));
    }

    public function isAdmin(): bool
    {
        return $this->hasRole(config('roles.admin'));
    }

    public function isSubadmin(): bool
    {
        return $this->hasRole(config('roles.subadmin'));
    }

    public function isOps(): bool
    {
        return $this->hasRole(config('roles.ops'));
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

    public function scopeSubadmins($query)
    {
        return $query->role(config('roles.subadmin', 'subadmin'));
    }


    public function currentSubscription()
    {
        return $this->hasOne(\App\Models\Subscription::class)
            ->whereIn('status', ['active', 'cancelled']) 
            ->where('current_period_end', '>', now())
            ->where(function ($query) {
                $query->whereNull('ends_at')
                    ->orWhere('ends_at', '>', now());
            })
            ->latest('created_at');
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
            ->where('current_period_end', '>', now())
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

    public function lastSubscriptionIsCancelled(): bool
    {
        $last = $this->hasOne(\App\Models\Subscription::class)
            ->latest('created_at')
            ->first();

        return $last && $last->status === 'cancelled';
    }


    public function webhookEvents()
    {
        return $this->hasMany(\App\Models\WebhookEvent::class);
    }

    public function getProfileLinkAttribute(): string
    {
        if ($this->profile_picture) {
            // If you’re storing using Laravel Storage (e.g. 'public' disk)
            return Storage::url($this->profile_picture);
        }
        if($this->isTeacher()){
            return asset('assets/img/teacher/teacher-avatar.jpeg');
        }
        // Default placeholder image    
        return asset('assets/img/dashboard/user.png');
    }


    public function createdReservations()
    {
        return $this->hasMany(Reservation::class, 'created_by');
    }

    public function sendPasswordResetNotification($token)
    {
        // 1. Generate the Reset URL
        $url = route('auth.password.reset', ['token' => $token, 'email' => $this->email]);

        $placeholders = [
            'user_name' => $this->name,
            'action_url' => $url,
            'user_email' => $this->email,
        ];

        // 3. Fetch the Custom Template
        $template = EmailHelper::getTemplateBySlug('forgot-password', $placeholders);
        if ($template) {
            // 4. Send using your existing CommonNotification class
            $this->notify(new CommonNotification(
                $template->subject,
                'emails.common_template', // The blade wrapper view you use
                [
                    'subject' => $template->subject, 
                    'content' => $template->body
                ]
            ));
        } else {
            // Optional: Fallback to default or log error if template is missing
        }
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
     * Filter teachers who have availabilities at specific date
     */
    public function scopeFilterByAvailability($query, $startUtc)
    {
        return $query->when($startUtc, function ($q) use ($startUtc) {
            // Parse the date and force it to the beginning of that day
            $start = \Carbon\Carbon::parse($startUtc)->startOfDay();
            
            $q->whereHas('availabilities', function ($query) use ($start) {
                $query->where('is_booked', false)
                    // Look for slots between the start and end of that specific day
                    ->whereBetween('start_utc', [
                        $start, // e.g., 2025-11-20 00:00:00
                        $start->copy()->endOfDay(), // e.g., 2025-11-20 23:59:59
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
                    // Parse the date and force it to the beginning of that day
                    $start = \Carbon\Carbon::parse($startUtc)->startOfDay();

                    // Look for slots between the start and end of that specific day
                    $q->whereBetween('start_utc', [
                        $start, // e.g., 2025-11-20 00:00:00
                        $start->copy()->endOfDay(), // e.g., 2025-11-20 23:59:59
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
