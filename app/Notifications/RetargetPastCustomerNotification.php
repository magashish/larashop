<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\PrizeDraw;

class RetargetPastCustomerNotification extends Notification
{
    use Queueable;

    protected $draw;

    public function __construct(PrizeDraw $draw)
    {
        $this->draw = $draw;
    }

    public function via($notifiable)
    {
        // Now returns 'mail' AND 'database'
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('🔥 Don’t miss your chance at a prize!')
            ->view('emails.retarget-past-customer', [
                'user' => $notifiable,
                'draw' => $this->draw,
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
            'title' => 'Xhale Draw Tomorrow!',
            'message' => 'Tomorrow\'s draw is for ' . $this->draw->title . '! Reactivate your package to get back in the game.',
            'draw_id' => $this->draw->id,
            'draw_name' => $this->draw->title,
            'draw_date' => $this->draw->draw_date,
        ];
    }
}