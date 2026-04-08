<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminTwoFactorCode extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        // No change needed here.
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
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
        // Prepare the verification URL to pass to the view
        $verifyUrl = route('admin.verify.index');
        return (new MailMessage)
                    ->subject('Your Two-Factor Authentication Code - Xhale') // Set the email subject
                    ->markdown('emails.admin-two-factor-code', [ // <-- Point to your new custom HTML view
                        'notifiable' => $notifiable, // Pass the notifiable object (admin user)
                        'verifyUrl' => $verifyUrl,   // Pass the verification URL
                    ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}