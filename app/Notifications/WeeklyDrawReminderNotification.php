<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\PrizeDraw;
use Carbon\Carbon;

class WeeklyDrawReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $draw;

    public function __construct(PrizeDraw $draw)
    {
        $this->draw = $draw;
    }

    public function via($notifiable)
    {
        // Add the 'database' channel here
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('🎉 Xhale Draw Tomorrow!')
            ->view('emails.weekly-draw-reminder', [
                'draw' => $this->draw,
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
            'title' => 'Xhale Draw Tomorrow!',
            'message' => 'Get ready! The prize draw for ' . $this->draw->name . ' is happening tomorrow. Good luck!',
            'draw_id' => $this->draw->id,
            'draw_name' => $this->draw->name,
            'draw_date' => $this->draw->draw_date,
        ];
    }
}