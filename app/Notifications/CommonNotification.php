<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
// We no longer need the EmailHelper in this file
// use App\Helpers\EmailHelper; 

class CommonNotification extends Notification
{
    use Queueable;

    protected $subject;
    protected $viewName;
    protected $data;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $subject, string $viewName, array $data = [])
    {
        $this->subject = $subject;
        $this->viewName = $viewName;
        $this->data = $data;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        // --- THIS IS THE CORRECTED METHOD ---
        
        // The EmailHelper::renderTemplate call was redundant.
        // We just pass the view name and data directly to the MailMessage.
        $this->data['appName'] = config('app.name');
        return (new MailMessage)
                    ->subject($this->subject)
                    ->view($this->viewName, $this->data);
    }
}