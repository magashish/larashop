<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class UnregisteredUserFollowUp extends Notification implements ShouldQueue
{
    use Queueable;

    public int $step;
    public string $message;

    public function __construct(int $step)
    {
        $this->step = $step;
        $this->message = $this->resolveMessage($step);
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $subjects = [
            1 => 'Complete your registration',
            2 => 'Reminder: finish your registration',
            3 => 'Almost there – complete your signup',
            4 => 'Don’t miss out – finish registration',
            5 => 'Final reminder to complete registration',
        ];

        return (new MailMessage)
            ->subject($subjects[$this->step] ?? 'Complete your registration')
            ->view('emails.unregistered-user-followup', [
                'user'        => $notifiable,
                'messageText'=> $this->message,
                'registerUrl'=> route('register'),
            ]);
    }

    /**
     * 🧠 Step-based message resolver
     */
    private function resolveMessage(int $step): string
    {
        return match ($step) {
            1 => 'You started creating your Xhale account but didn’t finish.',
            2 => 'This is a friendly reminder to complete your registration.',
            3 => 'You’re just one step away from joining Xhale.',
            4 => 'Don’t miss out on prizes, rewards and exclusive offers.',
            5 => 'Final reminder: complete your registration before it expires.',
            default => 'Complete your registration to continue.',
        };
    }
}
