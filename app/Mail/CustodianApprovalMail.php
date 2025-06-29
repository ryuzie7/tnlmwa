<?php

namespace App\Mail;

use App\Models\User;  // Changed to User model
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CustodianApprovalMail extends Mailable
{
    use Queueable, SerializesModels;

    public $custodian;
    public $status;

    public function __construct(User $custodian, $status) // Changed Custodian to User model
    {
        $this->custodian = $custodian;
        $this->status = $status;
    }

    public function build()
    {
        return $this->view('emails.custodian_approval')
                    ->with([
                        'custodianName' => $this->custodian->name,
                        'status' => $this->status,
                    ]);
    }
}
