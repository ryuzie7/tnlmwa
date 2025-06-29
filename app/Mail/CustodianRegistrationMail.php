<?php

namespace App\Mail;

use App\Models\User; // Changed to User model
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CustodianRegistrationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $custodian;

    public function __construct(User $custodian) // Changed Custodian to User model
    {
        $this->custodian = $custodian;
    }

    public function build()
    {
        return $this->view('emails.custodian_registration')
                    ->with([
                        'custodianName' => $this->custodian->name,
                        'custodianEmail' => $this->custodian->email,
                    ]);
    }
}
