<?php

namespace App\Traits;

use App\Helpers\EmailHelper;
use App\Models\Reservation;
use App\Notifications\CommonNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use Illuminate\Support\Facades\Notification;

trait BookingLessonEmailTrait
{
    /**
     * Entry point to send booking creation emails.
     */
    public function sendBookingCreatedEmail(Reservation $reservation)
    {
        if ($reservation->student) {
            $this->sendLessonBookingMail($reservation, $reservation->student);
        }
    }

    /**
     * Handle Booking Created - Sends to Student AND Teacher.
     */
    protected function sendLessonBookingMail($reservation, User $student): void
    {
        try {
            $teacher = User::find($reservation->teacher_id);
            if (!$teacher) return; 

            $creator = User::find($reservation->created_by);

            // --- 1. PREPARE COMMON DATA ---
            $isCreatedByStudent = ($reservation->created_by == $student->id);
            
            // Format lesson time
            // Note: We calculate time based on the specific recipient's timezone later in the helper
            // But for the specific logic you requested, we define base variables here.

            $lessonLinkText = $reservation->lesson_meeting_link
                ? '<a href="' . $reservation->lesson_meeting_link . '" target="_blank" style="background-color:#0E82FD; color:#fff; padding:10px 15px; text-decoration:none; border-radius:5px;">Join Lesson</a>'
                : 'Not added yet';

            $bookingId = function_exists('encryptId') ? encryptId($reservation->id) : $reservation->id;

            // Base Placeholders
            $placeholders = [
                'student_name'     => $student->name,
                'tutor_name'       => $teacher->name ?? 'Tutor',
                'teacher_name'     => $teacher->name ?? 'Tutor', // Alias
                'lesson_duration'  => $reservation->duration ?? '60 Minutes',
                'lesson_link'      => $reservation->lesson_meeting_link ?? '',
                'lesson_link_text' => $lessonLinkText,
                'booking_id'       => $bookingId,
                'dashboard_link'   => url('/dashboard'),
                'admin_name'       => $creator->name ?? 'Admin',
                'app_name'         => config('app.name'),
            ];

            // --- 2. SEND TO STUDENT ---
            $studentTemplateSlug = $isCreatedByStudent ? 'booking-created-by-student' : 'booking-created-by-admin';
            $this->processAndSend($reservation, $student, $studentTemplateSlug, $placeholders);

            // --- 3. SEND TO TEACHER ---
            $this->processAndSend($reservation, $teacher, 'booking-created-notification-teacher', $placeholders);

        } catch (\Throwable $e) {
            Log::error('BookingLessonEmailTrait::sendLessonBookingMail failed', [
                'reservation_id' => $reservation->id ?? null,
                'student_id' => $student->id ?? null,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Handle Booking Cancelled by Student
     * 1. Notify Teacher
     * 2. Send Confirmation to Student
     */
    public function sendBookingCancelledByStudentEmail(Reservation $reservation): void
    {
        if ($reservation->teacher) {
            $this->sendEmailNotification($reservation, $reservation->teacher, 'booking-cancelled-by-student-notification');
        }

        if ($reservation->student) {
            $this->sendEmailNotification($reservation, $reservation->student, 'booking-cancelled-by-student-confirmation');
        }
    }

    /**
     * Handle Booking Cancelled by Tutor (Sent to Student)
     */
    public function sendBookingCancelledByTutorEmail(Reservation $reservation, $is_refund): void
    {
        if ($reservation->student) {
            $this->sendEmailNotification($reservation, $reservation->student, 'booking-cancelled-by-tutor', $is_refund);
        }
        if($reservation->teacher){
              $this->sendEmailNotification($reservation,$reservation->teacher, 'booking-cancelled-by-teacher-confirmation');
        }
    }

    /**
     * Handle Lesson Link Updated (Sent to Student)
     */
    public function sendLessonLinkUpdatedEmail(Reservation $reservation): void
    {
        if ($reservation->student) {
            $this->sendEmailNotification($reservation, $reservation->student, 'lesson-link-updated');
        }
    }

    /**
     * Handle Booking Starting Soon (Sent to Student AND Teacher)
     */
    public function sendBookingStartingSoonEmail(Reservation $reservation): void
    {
        if ($reservation->student) {
            $this->sendEmailNotification($reservation, $reservation->student, 'booking-starting-soon');
        }
        if ($reservation->teacher) {
            $this->sendEmailNotification($reservation, $reservation->teacher, 'booking-starting-soon');
        }
    }

    /**
     * Handle Pending Lesson Link Reminder (Sent to Teacher)
     */
    public function sendPendingLessonLinkReminderTeacher(Reservation $reservation): void
    {
        if ($reservation->teacher) {
            $this->sendEmailNotification($reservation, $reservation->teacher, 'pending-lesson-link-reminder-teacher');
        }
    }

    /**
     * Wrapper for sendEmailNotification to easily call from other methods
     */
    private function sendEmailNotification(Reservation $reservation, User $recipient, string $slug, $is_refund = false): void
    {   
        // Calculate basic placeholders
        $lessonLinkText = $reservation->lesson_meeting_link
            ? '<a href="' . $reservation->lesson_meeting_link . '" target="_blank" style="background-color:#0E82FD; color:#fff; padding:10px 15px; text-decoration:none; border-radius:5px;">Join Lesson</a>'
            : 'Link not available yet';

        $bookingId = function_exists('encryptId') ? encryptId($reservation->id) : $reservation->id;
        $placeholders = [
            'student_name'     => $reservation->student->name ?? 'Student',
            'tutor_name'       => $reservation->teacher->name ?? 'Tutor',
            'teacher_name'     => $reservation->teacher->name ?? 'Tutor',
            'lesson_duration'  => '25 minutes',
            'lesson_link'      => $reservation->lesson_meeting_link ?? '',
            'lesson_update_url' => route('teacher.lessons.details',['id' => encryptId($reservation->id)]),
            'lesson_link_text' => $lessonLinkText,
            'booking_id'       => $bookingId,
            'dashboard_link'   => url('/dashboard'),
            'app_name'         => config('app.name'),
            'refund_status' =>  $is_refund ? 'Completed' : 'No valid refund,',
            'remaining_time' => formatRemainingTime($reservation->schedule_array['student']['time_to_start_lesson_in_minutes'])
        ];

        $this->processAndSend($reservation, $recipient, $slug, $placeholders);
    }

    /**
     * Core processor: Calculates Timezone, Fetches Template (DB or Fallback), Sends Email
     */
    private function processAndSend(Reservation $reservation, User $recipient, string $slug, array $basePlaceholders): void
    {
        try {
            // 1. Calculate Timezone (Recipient Preference)
            $timezone = $recipient->timezone ?? config('app.timezone', 'UTC');
            if ($recipient->studentProfile) $timezone = $recipient->studentProfile->tz ?? $timezone;
            if ($recipient->teacherProfile) $timezone = $recipient->teacherProfile->tz ?? $timezone;
            if (!in_array($timezone, \DateTimeZone::listIdentifiers())) $timezone = 'UTC';

            // 2. Format Date based on recipient timezone
            $dtUtc = Carbon::parse($reservation->cycle_start_utc, 'UTC');
            $localTime = $dtUtc->copy()->setTimezone($timezone);
            $lessonTime = $localTime->format('g:i A, M j, Y') . " (" . $timezone . ")";

            // 3. Merge specific placeholders
            $placeholders = array_merge($basePlaceholders, [
                'user_name'   => $recipient->name,
                'lesson_time' => $lessonTime, // Overwrite with localized time
            ]);

            // 4. Get Template from DB
            $template = EmailHelper::getTemplateBySlug($slug, $placeholders);

            // 5. Fallback to hardcoded if DB fails
            if (!$template) {
                $fallback = $this->getFallbackTemplate($slug);
                if ($fallback) {
                    $template = (object) [
                        'subject' => $this->replacePlaceholders($fallback['subject'], $placeholders),
                        'body'    => $this->replacePlaceholders($fallback['body'], $placeholders),
                    ];
                }
            }

            // 6. Send
            if ($template) {
                Notification::route('mail', $recipient->email)
                    ->notify(new CommonNotification(
                        $template->subject,
                        'emails.common_template',
                        ['subject' => $template->subject, 'content' => $template->body]
                    ));
            } else {
                Log::warning("Email template [{$slug}] not found for user {$recipient->id}");
            }

        } catch (\Throwable $e) {
            Log::error("BookingLessonEmailTrait::processAndSend failed for slug {$slug}", [
                'reservation_id' => $reservation->id ?? null,
                'recipient_id'   => $recipient->id ?? null,
                'error'          => $e->getMessage(),
            ]);
        }
    }

    private function replacePlaceholders(string $content, array $placeholders): string
    {
        foreach ($placeholders as $key => $value) {
            $content = str_replace('[' . $key . ']', $value, $content);
        }
        return $content;
    }

    /**
     * Hardcoded Defaults (Backup if DB templates missing)
     */
    private function getFallbackTemplate(string $slug): ?array
    {
        $templates = [
            'booking-created-by-student' => [
                'subject' => 'Booking Confirmed: [lesson_time]',
                'body' => '<p>Hi [user_name],</p><p>Your lesson with <strong>[tutor_name]</strong> is confirmed.</p><p><strong>Time:</strong> [lesson_time]</p><p>[lesson_link_text]</p>'
            ],
            'booking-created-by-admin' => [
                'subject' => 'New Lesson Scheduled by Admin',
                'body' => '<p>Hi [user_name],</p><p>An administrator has scheduled a lesson for you with <strong>[tutor_name]</strong>.</p><p><strong>Time:</strong> [lesson_time]</p>'
            ],
            'booking-created-notification-teacher' => [
                'subject' => 'New Booking: [student_name]',
                'body' => '<p>Hi [user_name],</p><p>You have a new booking with <strong>[student_name]</strong>.</p><p><strong>Time:</strong> [lesson_time]</p><p>Please add the meeting link.</p>'
            ],
            'booking-cancelled-by-tutor' => [
                'subject' => 'Lesson Cancelled by Tutor',
                'body' => '<p>Hi [user_name],</p><p>We regret to inform you that your lesson with <strong>[tutor_name]</strong> on [lesson_time] has been cancelled by the tutor.</p><p>Your credits have been refunded.</p>'
            ],
            'booking-cancelled-by-student-notification' => [
                'subject' => 'Lesson Cancelled by Student: [student_name]',
                'body' => '<p>Hi [tutor_name],</p><p><strong>[student_name]</strong> has cancelled the lesson scheduled for <strong>[lesson_time]</strong>.</p><p>Please check your dashboard for updates.</p>'
            ],
            'booking-cancelled-by-student-confirmation' => [
                'subject' => 'Booking Cancelled Successfully',
                'body' => '<p>Hi [student_name],</p><p>You have successfully cancelled your lesson with <strong>[tutor_name]</strong> scheduled for <strong>[lesson_time]</strong>.</p>'
            ],
            'lesson-link-updated' => [
                'subject' => 'Update: Lesson Meeting Link Changed',
                'body' => '<p>Hi [user_name],</p><p>The meeting link for your lesson with <strong>[tutor_name]</strong> has been updated.</p><p>[lesson_link_text]</p>'
            ],
            'booking-starting-soon' => [
                'subject' => 'Reminder: Lesson starting soon',
                'body' => '<p>Hi [user_name],</p><p>Your lesson with <strong>[tutor_name]</strong> is starting soon at [lesson_time].</p><p>[lesson_link_text]</p>'
            ],
            'pending-lesson-link-reminder-teacher' => [
                'subject' => 'Action Required: Add Meeting Link',
                'body' => '<p>Hi [user_name],</p><p>You have an upcoming lesson with <strong>[student_name]</strong> at [lesson_time], but no meeting link has been added.</p><p>Please add it immediately.</p>'
            ],
        ];

        return $templates[$slug] ?? null;
    }
}