<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Contracts\Queue\ShouldQueue;

class UserAccessNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $event;
    protected $user;

    public function __construct($user, $event)
    {
        $this->user = $user;
        $this->event = $event;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject("User {$this->event} Notification")
            ->line("User {$this->user->name} ({$this->user->email}) just {$this->event}.");
    }
}
