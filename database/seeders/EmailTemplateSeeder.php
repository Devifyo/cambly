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
            "
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
            "
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
            "
        ]);

        // 4. Booking Starting Soon
        EmailTemplate::updateOrCreate(['slug' => 'booking-starting-soon'], [
            'name' => 'Booking Starting Soon',
            'subject' => 'Your lesson with [tutor_name] starts in [remaining_time]!',
            'body' => "
                <p>Hi [user_name],</p>
                <p>This is a reminder that your 1-to-1 lesson with <strong>[tutor_name]</strong> is starting soon.</p>
                <ul>
                    <li><strong>Lesson Time:</strong> [lesson_time]</li>
                </ul>
                <p>Please be ready to join the lesson at the scheduled time.</p>
                <p style=\"text-align:center;\">
                    <a href=\"[lesson_link]\" style=\"display:inline-block; padding:12px 20px; background-color:#0E82FD; color:#fff; text-decoration:none; border-radius:8px;\">Join Lesson Now</a>
                </p>
            "
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
            "
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
            "
        ]);

        // 7. Booking Cancelled by Tutor
        EmailTemplate::updateOrCreate(['slug' => 'booking-cancelled-by-tutor'], [
            'name' => 'Booking Cancelled by Tutor',
            'subject' => 'Your upcoming lesson with [tutor_name] has been cancelled',
            'body' => "
                <p>Hi [user_name],</p>
                <p>We're writing to inform you that your upcoming lesson with <strong>[tutor_name]</strong> on <strong>[lesson_time]</strong> has been cancelled by the tutor.</p>
                <p><strong>Refund Status:</strong> [refund_status]</p>
                <p>We apologize for any inconvenience. Please feel free to book a new lesson with another tutor.</p>
                <p style=\"text-align:center;\">
                    <a href=\"[find_tutor_link]\" style=\"display:inline-block; padding:12px 20px; background-color:#0E82FD; color:#fff; text-decoration:none; border-radius:8px;\">Find a New Tutor</a>
                </p>
            "
        ]);
    }
}