<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\UserPackageSubscription;

class PackageExpiryReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $subscription;

    public function __construct(UserPackageSubscription $subscription)
    {
        $this->subscription = $subscription;
    }

    public function via($notifiable)
    {
        // Now returns 'mail' AND 'database'
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('⚡ Xhale Package Nearing Expiry')
            ->view('emails.package-expiry-reminder', [
                'subscription' => $this->subscription,
                'notifiable' => $notifiable,
            ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'title' => 'Xhale Package Nearing Expiry!',
            'message' => 'Your subscription for ' . $this->subscription->plan->name . ' is about to expire. Please renew soon!',
            'subscription_id' => $this->subscription->id,
            'expiry_date' => $this->subscription->expiry_date,
        ];
    }
}