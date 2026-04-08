<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue; // Recommended for email notifications

class UserResetPasswordNotification extends ResetPasswordNotification implements ShouldQueue
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
        // Call the parent constructor to ensure the token is handled correctly by Laravel's core logic
        parent::__construct($token);
        // You can re-assign $this->token if you specifically want to use $this->token
        // in your toMail method, though parent::__construct usually makes it available.
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
        // Generate the URL for the user's password reset form.
        // Make sure 'password.reset' route is correctly defined in your web.php for standard users.
        $url = route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(), // Passes the user's email to the reset form
        ]);

        // Get the password reset link expiration time from config.
        // This value is defined in config/auth.php under 'passwords.users.expire'.
        // It's typically in minutes. This handles the "user time" aspect by giving a relative time.
        $expireMinutes = config('auth.passwords.users.expire');

        return (new MailMessage)
                    ->subject('Password Reset for Your Xhale Account') // Email subject
                    ->view('emails.user-password-reset', [ // Point to your custom HTML view
                        'url' => $url,
                        'expireMinutes' => $expireMinutes, // Pass expiration in minutes to the template
                        'notifiable' => $notifiable,       // Pass the user object to access user's name
                    ]);
    }
}