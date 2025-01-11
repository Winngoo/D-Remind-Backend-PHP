<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VerifyUserMail extends Mailable
{
    use Queueable, SerializesModels;

    public $viewLink;
    /**
     * Create a new message instance.
     */
    public function __construct($viewLink)
    {
        $this->viewLink = $viewLink;
    }

    public function build()
    {
        return $this->subject('Mail Verification Link')
                    ->view('emails.viewlogin')
                    ->with([
                        'viewLink' => $this->viewLink,
                    ]);
    }
    

}
