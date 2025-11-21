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
    protected function sendLessonBookingMail($reservation, User $student): void
    {
        try {
            $teacher = User::find($reservation->teacher_id);
            $creator = User::find($reservation->created_by);

            $isCreatedByStudent = ($reservation->created_by == $student->id);
            $templateSlug = $isCreatedByStudent ? 'booking-created-by-student' : 'booking-created-by-admin';

            // Format lesson time in teacher timezone
            $teacherTz = optional($teacher->teacherProfile)->timezone ?? config('app.timezone', 'UTC');
            if (!in_array($teacherTz, \DateTimeZone::listIdentifiers())) $teacherTz = 'UTC';

            $dtUtc = Carbon::parse($reservation->cycle_start_utc, 'UTC');
            $dtTeacher = $dtUtc->copy()->setTimezone($teacherTz);
            $lessonTime = $dtTeacher->format('g:i A, M j, Y');

            // Lesson link display
            $lessonLinkText = $reservation->lesson_meeting_link
                ? '<a href="' . $reservation->lesson_meeting_link . '" target="_blank">Join Lesson</a>'
                : 'Not added yet';

            $bookingId = function_exists('encryptId') ? encryptId($reservation->id) : $reservation->id;

            // Prepare placeholders
            $placeholders = [
                'student_name'     => $student->name,
                'tutor_name'       => $teacher->name ?? 'Tutor',
                'lesson_time'      => $lessonTime,
                'lesson_duration'  => $reservation->duration ?? '',
                'lesson_link'      => $reservation->lesson_meeting_link ?? '',
                'lesson_link_text' => $lessonLinkText,
                'booking_id'       => $bookingId,
                'dashboard_link'   => url('/dashboard'),
                'admin_name'       => $creator->name ?? 'Admin',
                'app_name' => config('app.name'),
            ];

            // Get processed template
            $template = EmailHelper::getTemplateBySlug($templateSlug, $placeholders);

            if ($template) {
                Notification::route('mail', $student->email)
                    ->notify(new CommonNotification(
                        $template->subject,
                        'emails.common_template',
                        ['subject' => $template->subject, 'content' => $template->body]
                    ));
            }
        } catch (\Throwable $e) {
            Log::error('BookingEmails::sendLessonBookingMail failed', [
                'reservation_id' => $reservation->id ?? null,
                'student_id' => $student->id ?? null,
                'error' => $e->getMessage(),
            ]);
        }
    }
}