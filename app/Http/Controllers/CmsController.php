<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\ContactFormRequest;
use App\Models\SupportTicket;
use App\Models\User;
use App\Notifications\CommonNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use App\Helpers\EmailHelper; // <-- Import the helper
use Illuminate\View\View;

class CmsController extends Controller
{
    /**
     * Display the "About Us" page.
     */
    public function about(): View
    {
        // This will load the view from the path in the next step
        return view('student.cms.about');
    }

    /**
     * Display the "Contact Us" page.
     */
    public function contact(): View
    {
        // You can create this view later
        return view('student.cms.contact');
    }

    /**
     * Display the "Terms and Conditions" page.
     */
    public function terms(): View
    {
        // You can create this view later
        return view('student.cms.terms');
    }

    /**
     * Display the "Privacy Policy" page.
     */
    public function privacy(): View
    {
        // You can create this view later
        return view('student.cms.privacy');
    }

    public function howItWorks(){
        return view('student.cms.how-work');
    }


public function storeContact(ContactFormRequest $request): RedirectResponse
    {
        // 1. Get validated data
        $validatedData = $request->validated();
        
        // 2. Prepare ticket data
        $ticketData = [
            'phone_number' => $validatedData['phone_number'],
            'subject' => $validatedData['subject'],
            'message' => $validatedData['message'],
            'status' => 'open',
        ];

        // 3. --- THIS IS THE AUTH-AWARE LOGIC ---
        if ($user = $request->user()) {
            // User is LOGGED IN
            $ticketData['user_id'] = $user->id;
            $ticketData['name'] = $user->name;
            $ticketData['email'] = $user->email;
        } else {
            // User is a GUEST
            $ticketData['user_id'] = null;
            $ticketData['name'] = $validatedData['name'];
            $ticketData['email'] = $validatedData['email'];
        }
        // --- END OF AUTH-AWARE LOGIC ---

        // 4. Create the ticket
        $ticket = SupportTicket::create($ticketData);

        // 5. Notify Admin
        $adminEmail = config('app.admin_email', 'admin@example.com');
        $adminPlaceholders = [
            'user_name' => $ticket->name,
            'user_email' => $ticket->email,
            'user_phone' => $ticket->phone_number,
            'ticket_subject' => $ticket->subject,
            'ticket_message' => nl2br(e($ticket->message)),
        ];
        
        $adminTemplate = EmailHelper::getTemplateBySlug('new-support-ticket-admin', $adminPlaceholders);

        if ($adminTemplate) {
            Notification::route('mail', $adminEmail)
                ->notify(new CommonNotification(
                    $adminTemplate->subject,
                    'emails.common_template',
                    ['subject' => $adminTemplate->subject, 'content' => $adminTemplate->body]
                ));
        }

        // 6. Send "Thank You" email to the user
        $userPlaceholders = [
            'user_name' => $ticket->name,
            'ticket_id' => $ticket->id,
            'ticket_subject' => $ticket->subject,
        ];

        $userTemplate = EmailHelper::getTemplateBySlug('support-ticket-confirmation-user', $userPlaceholders);
        
        if ($userTemplate) {
            Notification::route('mail', $ticket->email)
                ->notify(new CommonNotification(
                    $userTemplate->subject,
                    'emails.common_template',
                    ['subject' => $userTemplate->subject, 'content' => $userTemplate->body]
                ));
        }

        return redirect()->route('cms.contact')
                         ->with('success', "Thanks for your message! We'll get back to you soon.");
    }
}