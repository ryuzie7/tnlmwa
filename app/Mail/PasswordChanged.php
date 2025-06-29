<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordChanged extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $timestamp;
    public $ip;

    public function __construct($user, $timestamp, $ip)
    {
        $this->user = $user;
        $this->timestamp = $timestamp;
        $this->ip = $ip;
    }

    public function build()
    {
        return $this->subject('Your password has been changed')
                    ->markdown('emails.password_changed');
    }
}
