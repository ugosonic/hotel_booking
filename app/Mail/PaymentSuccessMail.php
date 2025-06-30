<?php

namespace App\Mail;

use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PaymentSuccessMail extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;
    public $payment;

    public function __construct(Booking $booking, Payment $payment)
    {
        $this->booking = $booking;
        $this->payment = $payment;
    }

    public function build()
    {
        // TODO: place your own from() address if needed
        return $this->subject('Your Payment was Successful!')
                    ->view('emails.payment_success');
    }
}
