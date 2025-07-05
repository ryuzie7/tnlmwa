<?php

namespace App\Notifications;

use App\Models\AssetRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AssetRequestSubmitted extends Notification implements ShouldQueue
{
    use Queueable;

    public $request;

    public function __construct(AssetRequest $request)
    {
        $this->request = $request;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('New Asset Request Submitted')
            ->greeting('Hello Admin,')
            ->line('A user has submitted an asset ' . $this->request->action . ' request.')
            ->line('Requested by: ' . $this->request->user->name)
            ->line('Asset Type: ' . $this->request->type)
            ->line('Brand: ' . $this->request->brand . ' | Model: ' . $this->request->model)
            ->line('Location: ' . $this->request->location)
            ->action('Review Request', route('logs.requests'))
            ->line('Thank you.');
    }

    public function toArray($notifiable)
    {
        return [
            'request_id' => $this->request->id,
            'user' => $this->request->user->name,
            'action' => $this->request->action,
        ];
    }
}
