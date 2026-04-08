<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserTwoFactorCode extends Notification implements ShouldQueue // Added ShouldQueue for consistency
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @param string|null $code The two-factor code, assuming it's passed or available via notifiable
     */
    public function __construct() // If you're passing the code in, the constructor should accept it
    {
        // If your two-factor code is always on $notifiable, constructor can remain empty.
        // However, it's often better practice to explicitly pass data needed by the notification.
        // For example: public function __construct(string $twoFactorCode) { $this->twoFactorCode = $twoFactorCode; }
        // And then use $this->twoFactorCode in the template.
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
        // The route for user 2FA verification. Make sure 'verify.index' is correct for users.
        $verifyUrl = route('verify.index');

        return (new MailMessage)
                    ->subject('Your Two-Factor Authentication Code - Xhale') // Set your email subject
                    ->view('emails.user-two-factor-code', [ // <-- Point to your new custom HTML view
                        'notifiable' => $notifiable, // Pass the user object
                        'verifyUrl' => $verifyUrl,   // Pass the verification URL
                        // If your two_factor_code is not on $notifiable directly,
                        // ensure it's passed here, e.g., 'two_factor_code' => $this->twoFactorCode
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
            'two_factor_code' => $notifiable->two_factor_code,
            'message' => 'Your two-factor code has been sent.',
            'url' => route('verify.index'),
        ];
    }
}