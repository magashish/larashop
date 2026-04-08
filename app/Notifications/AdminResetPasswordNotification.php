<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification; // Alias the original

class AdminResetPasswordNotification extends ResetPasswordNotification // Extend the original
{
    use Queueable;

    /**
     * The password reset token.
     *
     * @var string
     */
    public $token;

    /**
     * Create a new notification instance.
     *
     * @param  string  $token
     * @return void
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        // This is the key change! Use the named route for admin password reset.
        $url = route('admin.password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(), // Pass email for convenience if your reset form needs it
        ]);

        // Get the password reset link expiration time from config
        $expireMinutes = config('auth.passwords.admins.expire');

        return (new MailMessage)
                    ->subject('Password Reset for Your Xhale Admin Account') // Updated subject
                    ->view('emails.admin-password-reset', [ // <-- Point to your new custom HTML view
                        'url' => $url,
                        'expireMinutes' => $expireMinutes,
                        'notifiable' => $notifiable, // Pass notifiable if you use name in template
                    ]);
    }
}