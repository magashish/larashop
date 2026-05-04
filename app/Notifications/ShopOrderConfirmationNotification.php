<?php

namespace App\Notifications;

use App\Models\ShopOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ShopOrderConfirmationNotification extends Notification
{
    use Queueable;

    public function __construct(public ShopOrder $order) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Order Confirmed – #' . $this->order->order_number)
            ->view('emails.shop-order-confirmation', ['order' => $this->order]);
    }
}
