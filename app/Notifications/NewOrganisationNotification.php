<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Organisation;

class NewOrganisationNotification extends Notification
{
    use Queueable;

    protected $organisation;

    public function __construct(Organisation $organisation)
    {
        $this->organisation = $organisation;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
        ->subject('New Organisation Account Created - Awaiting Approval')
        ->markdown('emails.new-organisation', ['organisation' => $this->organisation]);
    }
}