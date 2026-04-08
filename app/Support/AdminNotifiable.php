<?php

namespace App\Support;

use Illuminate\Notifications\Notifiable;

class AdminNotifiable
{
    use Notifiable;

    public $email;

    public function routeNotificationForMail()
    {
        return $this->email;
    }
}