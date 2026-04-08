<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PrizeDrawWinnerNotification extends Notification
{
    use Queueable;

    protected $prizeDraw;

    /**
     * Create a new notification instance.
     *
     * @param \App\Models\PrizeDraw $prizeDraw
     * @return void
     */
    public function __construct($prizeDraw)
    {
        $this->prizeDraw = $prizeDraw;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        // Now supporting both email and database notifications
        return ['mail', 'database']; 
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        // Assuming you have a Blade view at 'emails.prize-draw-winner'
        return (new MailMessage)
            ->subject("CONGRATULATIONS! You've Won a Prize!")
            ->view('emails.prize-draw-winner', [
                // Pass the winner's user model ($notifiable)
                'user' => $notifiable,
                // Pass the prize draw details
                'prizeDraw' => $this->prizeDraw,
            ]);
    }

    /**
     * Get the array representation of the notification. (For Database)
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'title' => 'Prize Draw Winner!',
            'winner_user_id' => $notifiable->id, // Winner's user ID
            'winner_user_name' => $notifiable->name, // Winner's user name
            'prize_draw_id' => $this->prizeDraw->id,
            'prize_name' => $this->prizeDraw->name,
            'prize_details' => $this->prizeDraw->prize_details,
            'message' => "Congratulations, {$notifiable->name}! You won the {$this->prizeDraw->name} prize draw!"
        ];
    }
}
