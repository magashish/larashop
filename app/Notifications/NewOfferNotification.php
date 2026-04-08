<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Offer;

class NewOfferNotification extends Notification
{
    use Queueable;

    protected $offer;

    public function __construct(Offer $offer)
    {
        $this->offer = $offer;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
        ->subject('New Offer Created - Awaiting Approval')
        ->markdown('emails.new-offer', ['offer' => $this->offer]);
    }
}