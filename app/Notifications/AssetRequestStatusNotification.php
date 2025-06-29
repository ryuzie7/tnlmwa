<?php

namespace App\Notifications;

use App\Models\AssetRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class AssetRequestStatusNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $request;
    protected $status; // 'approved' or 'rejected'

    /**
     * Create a new notification instance.
     *
     * @param AssetRequest $request
     * @param string $status
     */
    public function __construct(AssetRequest $request, string $status)
    {
        $this->request = $request;
        $this->status = $status;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via(mixed $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
   public function toMail(mixed $notifiable): MailMessage
{
    $statusText = ucfirst($this->status);
    $ticketNumber = 'ARQ-' . str_pad($this->request->id, 5, '0', STR_PAD_LEFT);
    $url = url('/my-requests'); // Make sure you have a route for this page

    return (new MailMessage)
        ->subject("[$ticketNumber] Asset Request $statusText")
        ->greeting("Hello {$notifiable->name},")
        ->line("Your asset request (Ticket: **{$ticketNumber}**) for model: {$this->request->model} has been **{$statusText}**.")
        ->line("Requested Action: {$this->request->action}")
        ->line("Status: {$statusText}")
        ->when($this->status === 'approved', fn($msg) => $msg->line("✅ The request has been successfully applied to the database."))
        ->when($this->status === 'rejected', fn($msg) => $msg->line("❌ The request has been rejected and will not be applied."))
        ->action('View My Requests', $url)
        ->line('Thank you for using the Teaching and Learning Inventory System.');
}



    /**
     * Get the array representation of the notification (optional).
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray(mixed $notifiable): array
    {
        return [
            'asset_request_id' => $this->request->id,
            'status' => $this->status,
        ];
    }
}
