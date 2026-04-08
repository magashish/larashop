<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\User;
use App\Models\UserPackageSubscription;

class NewPrizeDrawEntryNotification extends Notification
{
    use Queueable;

    public $user;
    public $subscription;
    public $numberOfEntries;

    public function __construct(User $user, UserPackageSubscription $subscription, int $numberOfEntries)
    {
        $this->user = $user;
        $this->subscription = $subscription;
        $this->numberOfEntries = $numberOfEntries;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
    // The view() method takes the path to your Blade file.
    // The second parameter is an array of data to pass to the view.
        return (new MailMessage)
        ->subject('Congratulations! You have new prize draw entries!')
        ->view('emails.new-prize-draw-entry', [
            'user' => $this->user,
            'subscription' => $this->subscription,
            'numberOfEntries' => $this->numberOfEntries,
        ]);
    }
}

