<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\User;

class AccountDeleted extends Notification implements ShouldQueue
{
    use Queueable;

    public $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param object $notifiable
     * @return MailMessage
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your Account Has Been Permanently Deleted')
            ->view('emails.account-deleted', [ // <-- Point to your custom Blade view
                'user' => $this->user,
                'contactUrl' => url('/contact'),
            ]);
    }
}