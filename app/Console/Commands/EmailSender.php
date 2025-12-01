<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\{User, Reservation};
use App\Traits\BookingLessonEmailTrait;
use Illuminate\Support\Carbon;

class EmailSender extends Command
{   
    use BookingLessonEmailTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send reminder emails to teachers/students for upcoming reservations with missing links';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // 1. Define the notification intervals (in minutes) descending order
        // 12h, 6h, 2h, 1h, 30m, 20m, 10m, 5m, 1m
        $notificationTiers = [
            // 720,
            // 360,
            // 120,
            // 60,
            // 30,
            20,
            // 10,
            // 5, 
            // 1
        ];


        // 2. Fetch reservations
        // Optimized query: Eager load relationships and filter only necessary records
        $reservationsInFuture = Reservation::upcoming()
            ->whereHas('teacher', fn($q) => $q->active())
            ->whereHas('student', fn($q) => $q->active())
            ->with([
                'teacher' => fn($q) => $q->active(),
                'student' => fn($q) => $q->active(),
            ])
            ->get();
        
        $this->info("Checking " . $reservationsInFuture->count() . " upcoming reservations...");

        foreach($reservationsInFuture as $reservation) {
                // $reservation->last_email_at = null;
                // $reservation->save();
            // Safety check for schedule data
            $minutesToStart = $reservation->schedule_array['student']['time_to_start_lesson_in_minutes'] ?? null;

            if (is_null($minutesToStart) || $minutesToStart <= 0) {
                continue; 
            }

            foreach ($notificationTiers as $index => $tier) {
                // Logic:
                // 1. Check if the lesson starts within this tier's timeframe ($minutesToStart <= $tier)
                // 2. Check if we haven't already passed the *next* smaller tier (to prevent handling the '12h' tier when there are only 5 mins left)
                
                $nextTier = $notificationTiers[$index + 1] ?? 0; // Get the next smallest tier or 0
                
                // If the time remaining falls into this bucket (e.g., between 60 and 120 minutes)
                // OR if it's the smallest bucket (less than 1 minute)
                if ($minutesToStart <= $tier) {
                    
                    // 3. Spam Protection: Check 'last_email_at'
                    // We calculate the gap between this tier and the next to determine a "safe" throttle time.
                    // For 5m -> 1m, the gap is 4m. We shouldn't send if we sent one 2 mins ago.
                    // We default to a 3-minute throttle for small intervals, longer for large ones.
                    
                    $shouldSend = false;

                    if (is_null($reservation->last_email_at)) {
                        $shouldSend = true;
                    } else {
                        $lastEmailSentAt = Carbon::parse($reservation->last_email_at);
                        $minutesSinceLastEmail = $lastEmailSentAt->diffInMinutes(now());

                        // Dynamic throttle: ensure we don't double-send for the same "event"
                        // If the gap between tiers is small (e.g., 5m to 1m), we need a tight throttle.
                        // If we are at the 12h mark, and the last email was 10 hours ago, we send.
                        // If the last email was 5 minutes ago, we skip.
                        
                        $throttleLimit = ($tier - $nextTier) > 10 ? 10 : 3; 

                        if ($minutesSinceLastEmail > $throttleLimit) {
                            $shouldSend = true;
                        }
                    }

                    if ($shouldSend) {
                        $this->info("Sending {$tier}m reminder for Reservation ID: {$reservation->id}");
                        if(is_null($reservation->link)){
                            $this->sendPendingLessonLinkReminderTeacher($reservation);
                        }
                        $this->sendBookingStartingSoonEmail($reservation);
                        // Update the timestamp to prevent duplicates
                        $reservation->update(['last_email_at' => now()]);
                        
                        // Break the inner loop; we processed the most relevant tier for this reservation
                        break; 
                    }
                    
                    // If we matched the tier condition ($minutesToStart <= $tier) but decided NOT to send
                    // (because we sent one recently), we still break. 
                    // We don't want to check larger tiers if we are already inside a smaller tier.
                    break;
                }
            }
        }

        $this->info('Email check completed.');
    }
}