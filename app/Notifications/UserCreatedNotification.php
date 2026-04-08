<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\User;

class UserCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $user;
    public $password;

    /**
     * Create a new notification instance.
     *
     * @param User   $user
     * @param string $password  Temporary password
     */
    public function __construct(User $user, string $password)
    {
        $this->user = $user;
        $this->password = $password;
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
        $dashboardUrl = url('/subscriber-dashboard');

        return (new MailMessage)
            ->subject('Welcome to Xhale, ' . $this->user->name . '!')
            ->view('emails.user-created', [
                'user'         => $this->user,
                'password'     => $this->password,   // ✅ PASS PASSWORD
                'dashboardUrl' => $dashboardUrl,
            ]);
    }

    /**
     * Array representation (optional, future-proof).
     */
    public function toArray(object $notifiable): array
    {
        return [
            'user_id' => $this->user->id,
            'user_name' => $this->user->name,
            'message'   => 'Welcome to Xhale! Your account has been created.',
            'url'       => url('/subscriber-dashboard'),
        ];
    }
}
