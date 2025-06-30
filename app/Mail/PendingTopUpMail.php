<?php

namespace App\Mail;

use App\Models\TopUp;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PendingTopUpMail extends Mailable
{
    use Queueable, SerializesModels;

    public $topup;

    public function __construct(TopUp $topup)
    {
        $this->topup = $topup;
    }

    public function build()
    {
        return $this->subject("New Bank Transfer Top-Up #{$this->topup->id} Pending Approval")
                    ->view('emails.topups.pending');
    }
}
