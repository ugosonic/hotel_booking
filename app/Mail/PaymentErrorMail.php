<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PaymentErrorMail extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;
    public $reason;

    public function __construct(Booking $booking, $reason)
    {
        $this->booking = $booking;
        $this->reason  = $reason;
    }

    public function build()
    {
        // TODO: place your own from() address if needed
        return $this->subject('Payment Issue with your Booking')
                    ->view('emails.payment_error');
    }
}
