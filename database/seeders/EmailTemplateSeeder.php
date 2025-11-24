<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EmailTemplate;

class EmailTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. New Support Ticket (for Admin)
        EmailTemplate::updateOrCreate(['slug' => 'new-support-ticket-admin'], [
            'name' => 'New Support Ticket (Admin)',
            'subject' => 'New Support Ticket: [ticket_subject]',
            'body' => "
                <p>A new support ticket has been submitted.</p>
                <ul>
                    <li><strong>Name:</strong> [user_name]</li>
                    <li><strong>Email:</strong> [user_email]</li>
                    <li><strong>Phone:</strong> [user_phone]</li>
                </ul>
                <p><strong>Subject:</strong> [ticket_subject]</p>
                <p><strong>Message:</strong><br>[ticket_message]</p>
            ",
            'status' => true,
        ]);

        // 2. Support Ticket Confirmation (for User)
        EmailTemplate::updateOrCreate(['slug' => 'support-ticket-confirmation-user'], [
            'name' => 'Support Ticket Confirmation (User)',
            'subject' => 'We\'ve received your request ([ticket_id])',
            'body' => "
                <p>Hi [user_name],</p>
                <p>Thank you for contacting us. We have received your support request and a member of our team will get back to you as soon as possible.</p>
                <p><strong>Your Inquiry:</strong> [ticket_subject]</p>
                <p>Your ticket ID is #[ticket_id].</p>
            ",
            'status' => true,
        ]);

        // 3. Forgot Password
        EmailTemplate::updateOrCreate(['slug' => 'forgot-password'], [
            'name' => 'Forgot Password',
            'subject' => 'Reset Your Password',
            'body' => "
                <p>Hi [user_name],</p>
                <p>You are receiving this email because we received a password reset request for your account.</p>
                <p>Please click the button below to reset your password:</p>
                <p style=\"text-align:center;\">
                    <a href=\"[action_url]\" style=\"display:inline-block; padding:12px 20px; background-color:#0E82FD; color:#fff; text-decoration:none; border-radius:8px;\">Reset Password</a>
                </p>
                <p>This password reset link will expire in 60 minutes.</p>
                <p>If you did not request a password reset, no further action is required.</p>
            ",
            'status' => true,
        ]);

        // 4. Booking Starting Soon
        EmailTemplate::updateOrCreate(['slug' => 'booking-starting-soon'], [
            'name' => 'Booking Starting Soon',
            'subject' => 'Your lesson with [tutor_name] starts in [remaining_time]!',
            'body' => "
                <p>Hi [user_name],</p>
                <p>This is a reminder that your lesson with <strong>[tutor_name]</strong> is starting soon.</p>
                <ul>
                    <li><strong>Lesson Time:</strong> [lesson_time]</li>
                </ul>
                <p>Please be ready to join the lesson at the scheduled time.</p>
                <p style=\"text-align:center;\">
                    <a href=\"[lesson_link]\" style=\"display:inline-block; padding:12px 20px; background-color:#0E82FD; color:#fff; text-decoration:none; border-radius:8px;\">Join Lesson Now</a>
                </p>
            ",
            'status' => true,
        ]);
        
        // 5. Subscription Renewed
        EmailTemplate::updateOrCreate(['slug' => 'subscription-renewed'], [
            'name' => 'Subscription Renewed',
            'subject' => 'Your subscription has been successfully renewed',
            'body' => "
                <p>Hi [user_name],</p>
                <p>Your <strong>[plan_name]</strong> subscription has been successfully renewed. Your payment of <strong>[amount]</strong> was processed.</p>
                <p>Your next billing date is <strong>[next_billing_date]</strong>.</p>
                <p>Thank you for learning with us!</p>
            ",
            'status' => true,
        ]);

        // 6. Subscription Renewal Failed
        EmailTemplate::updateOrCreate(['slug' => 'subscription-failed'], [
            'name' => 'Subscription Renewal Failed',
            'subject' => 'Action Required: Your subscription payment failed',
            'body' => "
                <p>Hi [user_name],</p>
                <p>We were unable to process the payment for your <strong>[plan_name]</strong> subscription.</p>
                <p><strong>Error:</strong> [error_message]</p>
                <p>To avoid service interruption, please update your payment method.</p>
                <p style=\"text-align:center;\">
                    <a href=\"[update_payment_link]\" style=\"display:inline-block; padding:12px 20px; background-color:#dc3545; color:#fff; text-decoration:none; border-radius:8px;\">Update Payment Method</a>
                </p>
            ",
            'status' => true,
        ]);

        // 7. Booking Cancelled by Tutor
        EmailTemplate::updateOrCreate(['slug' => 'booking-cancelled-by-tutor'], [
            'name' => 'Booking Cancelled by Teacher',
            'subject' => 'Your upcoming lesson with [tutor_name] has been cancelled',
            'body' => "
                <p>Hi [user_name],</p>
                <p>We're writing to inform you that your upcoming lesson with <strong>[tutor_name]</strong> on <strong>[lesson_time]</strong> has been cancelled by the teacher.</p>
                <p><strong>Refund Status:</strong> [refund_status]</p>
                <p>We apologize for any inconvenience. Please feel free to book a new lesson with another teacher.</p>
                <p style=\"text-align:center;\">
                    <a href=\"[find_tutor_link]\" style=\"display:inline-block; padding:12px 20px; background-color:#0E82FD; color:#fff; text-decoration:none; border-radius:8px;\">Book a new Lesson</a>
                </p>
            ",
            'status' => true,
        ]);

        // 8a. Booking Cancelled by Student - Notification to Teacher
        EmailTemplate::updateOrCreate(['slug' => 'booking-cancelled-by-student-notification'], [
            'name' => 'Booking Cancelled by Student (Teacher Notification)',
            'subject' => 'Lesson Cancelled by Student: [student_name]',
            'body' => "
                <p>Hi [tutor_name],</p>
                <p>We're writing to inform you that <strong>[student_name]</strong> has cancelled the lesson scheduled for <strong>[lesson_time]</strong>.</p>
                <p>This slot is now available for other students to book.</p>
                <p>Please check your <a href=\"[dashboard_link]\">dashboard</a> for more details.</p>
            ",
            'status' => true,
        ]);

        // 8b. Booking Cancelled by Student - Confirmation to Student
        EmailTemplate::updateOrCreate(['slug' => 'booking-cancelled-by-student-confirmation'], [
            'name' => 'Booking Cancelled (Student Confirmation)',
            'subject' => 'Booking Cancelled Successfully',
            'body' => "
                <p>Hi [student_name],</p>
                <p>You have successfully cancelled your lesson with <strong>[tutor_name]</strong> scheduled for <strong>[lesson_time]</strong>.</p>
                <p>If you have any credits refunded, they are now available in your account.</p>
            ",
            'status' => true,
        ]);

                // 8c. Booking Cancelled by teacher - Confirmation to teacher
        EmailTemplate::updateOrCreate(['slug' => 'booking-cancelled-by-teacher-confirmation'], [
            'name' => 'Booking Cancelled (teacher Confirmation)',
            'subject' => 'Booking Cancelled Successfully',
            'body' => "
                <p>Hi [teacher_name],</p>
                <p>You have successfully cancelled your lesson with <strong>[student_name]</strong> scheduled for <strong>[lesson_time]</strong>.</p>
                <p>If you have any credits refunded, they are now available in your account.</p>
            ",
            'status' => true,
        ]);


        // 9. Booking created by student (student confirmation)
        EmailTemplate::updateOrCreate(['slug' => 'booking-created-by-student'], [
            'name' => 'Booking Created (Student)',
            'subject' => 'Your lesson with [tutor_name] is confirmed — [lesson_time]',
            'body' => "
                <p>Hi [student_name],</p>
                <p>Your lesson with <strong>[tutor_name]</strong> has been successfully booked.</p>
                <ul>
                    <li><strong>Lesson ID:</strong> [booking_id]</li>
                    <li><strong>Lesson Time:</strong> [lesson_time]</li>
                    <li><strong>Duration:</strong> [lesson_duration]</li>
                    <li><strong>Lesson Link:</strong> [lesson_link_text]</li>
                </ul>
                <p>Thank you for booking — see you soon!</p>
                <p><em>[app_name]</em></p>
            ",
            'status' => true,
        ]);

        // 10. Booking created by admin (student notification when admin books for them)
        EmailTemplate::updateOrCreate(['slug' => 'booking-created-by-admin'], [
            'name' => 'Booking Created by Admin (Student Notification)',
            'subject' => 'A lesson has been scheduled for you by [admin_name] — [lesson_time]',
            'body' => "
                <p>Hi [student_name],</p>
                <p>An administrator (<strong>[admin_name]</strong>) has scheduled a lesson for you with <strong>[tutor_name]</strong>.</p>
                <ul>
                    <li><strong>Lesson ID:</strong> [booking_id]</li>
                    <li><strong>Lesson Time:</strong> [lesson_time]</li>
                    <li><strong>Duration:</strong> [lesson_duration]</li>
                    <li><strong>Lesson Link:</strong> [lesson_link_text]</li>
                </ul>
                <p>If this was not expected, or you need changes, please contact support or visit your <a href=\"[dashboard_link]\">dashboard</a>.</p>
                <p>Thank you!</p>
                <p><em>[app_name]</em></p>
            ",
            'status' => true,
        ]);

        // 11. Lesson link added by teacher
        EmailTemplate::updateOrCreate(['slug' => 'lesson-link-added'], [
            'name' => 'Lesson Link Added by Teacher',
            'subject' => 'Your upcoming lesson now has a lesson link',
            'body' => "
                <p>Hi [student_name],</p>
                <p>Your teacher <strong>[tutor_name]</strong> has added a lesson link for your upcoming lesson.</p>
                <ul>
                    <li><strong>Lesson ID:</strong> [booking_id]</li>
                    <li><strong>Lesson Time:</strong> [lesson_time]</li>
                    <li><strong>Lesson Link:</strong> <a href=\"[lesson_link]\" target=\"_blank\">Join Lesson</a></li>
                </ul>
                <p>Please use this link to join the lesson on time.</p>
                <p>Thank you!</p>
            ",
            'status' => true,
        ]);

        // 12. Lesson link updated by teacher
        EmailTemplate::updateOrCreate(['slug' => 'lesson-link-updated'], [
            'name' => 'Lesson Link Updated by Teacher',
            'subject' => 'Your lesson link has been updated',
            'body' => "
                <p>Hi [student_name],</p>
                <p>Your teacher <strong>[tutor_name]</strong> has updated the lesson link for your upcoming lesson.</p>
                <ul>
                    <li><strong>Lesson ID:</strong> [booking_id]</li>
                    <li><strong>Lesson Time:</strong> [lesson_time]</li>
                    <li><strong>New Lesson Link:</strong> <a href=\"[lesson_link]\" target=\"_blank\">Join Lesson</a></li>
                </ul>
                <p>Please make sure to use this updated link.</p>
                <p>Thank you!</p>
            ",
            'status' => true,
        ]);

        // 13. Reminder to teacher for lessons with missing lesson link
        EmailTemplate::updateOrCreate(['slug' => 'pending-lesson-link-reminder-teacher'], [
            'name' => 'Pending Lesson Link Reminder (Teacher)',
            'subject' => 'Reminder: You have lessons without lesson links',
            'body' => "
                <p>Hi [tutor_name],</p>
                <p>This is a reminder that you have upcoming lessons that do not yet have a lesson link added.</p>

                <p><strong>Pending Lessons:</strong></p>
                <p>[lessons_list]</p>

                <p>Please add the lesson link as soon as possible so students can join their lessons on time.</p>

                <p>You can add links from your teacher dashboard.</p>

                <p>Thank you!</p>
            ",
            'status' => true,
        ]);

        // 14. New Booking Notification for Teacher
        EmailTemplate::updateOrCreate(['slug' => 'booking-created-notification-teacher'], 
            [
                'name' => 'New Booking Notification (Teacher)',
                'subject' => 'New Booking: [student_name] — [lesson_time]',
                'body' => '
                    <p>Hi [user_name],</p>
                    <p>You have a new lesson booked with <strong>[student_name]</strong>.</p>
                    <div style="background-color:#f0f9ff; border-left: 4px solid #0E82FD; padding: 15px; margin: 20px 0;">
                        <p style="margin:0;"><strong>Student:</strong> [student_name]</p>
                        <p style="margin:0;"><strong>Time:</strong> [lesson_time]</p>
                        <p style="margin:0;"><strong>Duration:</strong> [lesson_duration]</p>
                    </div>
                    <p>Please ensure you add the meeting link before the lesson starts.</p>
                    <p style="text-align:center; margin-top:30px;">
                        <a href="[dashboard_link]" style="background-color:#0E82FD; color:#ffffff; padding: 12px 24px; text-decoration:none; border-radius:5px; font-weight:bold;">View Booking</a>
                    </p>
                ',
                'status' => true,
            ],
        );
    }
}