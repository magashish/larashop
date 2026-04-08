<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\UserPackageSubscription;
use App\Models\User;

class PurchaseConfirmationNotification extends Notification
{
    use Queueable;

    public $user;
    public $subscription;
    public $prizeDrawEntries;

    /**
     * Create a new notification instance.
     *
     * @param User $user
     * @param UserPackageSubscription $subscription
     * @param int $prizeDrawEntries
     * @return void
     */
    public function __construct(User $user, UserPackageSubscription $subscription, $prizeDrawEntries)
    {
        $this->user = $user;
        $this->subscription = $subscription;
        $this->prizeDrawEntries = $prizeDrawEntries;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
    // The view() method takes the path to your Blade file.
    // The second parameter is an optional array of data to pass to the view.
        return (new MailMessage)
        ->subject('Thank you for your purchase!')
        ->view('emails.purchase-confirmation', [
            'user' => $this->user,
            'subscription' => $this->subscription,
            'prizeDrawEntries' => $this->prizeDrawEntries,
        ]);
    }

     /**
     * Get the array representation of the notification for database storage.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'title' => 'Purchase Confirmed!',
            'user_id' => $this->user->id,
            'user_name' => $this->user->name,
            'subscription_id' => $this->subscription->id,
            'prizeDrawEntries' => $this->prizeDrawEntries,
            'message' => 'Your purchase of the ' . $this->subscription->plan->name . ' package is confirmed. You have received ' . $this->prizeDrawEntries . ' prize draw entries!',
        ];
    }


}