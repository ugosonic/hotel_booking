<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RegistrationWelcome extends Mailable
{
    use Queueable, SerializesModels;

    public $userName;

    /**
     * Create a new message instance.
     */
    public function __construct($userName)
    {
        $this->userName = $userName;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this
            ->subject('Welcome to Our Hotel Booking Site!')
            ->view('emails.registration_welcome');
    }
}
