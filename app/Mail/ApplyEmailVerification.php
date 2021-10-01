<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ApplyEmailVerification extends Mailable
{
    use Queueable, SerializesModels;


    public $verificationCode;

    /**
     * Create a new message instance.
     *
     * @param String $verificationCode
     */
    public function __construct(String $verificationCode)
    {
        $this->verificationCode = $verificationCode;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject('Verify your email address')
            ->view('emails.apply_email_verification');
    }
}
