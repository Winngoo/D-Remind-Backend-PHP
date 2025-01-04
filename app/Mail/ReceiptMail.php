<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReceiptMail extends Mailable
{
    use Queueable, SerializesModels;

    public $receiptURL;
    /**
     * Create a new message instance.
     */
    public function __construct($receiptURL)
    {
        $this->receiptURL = $receiptURL;
    }

    public function build()
    {
        return $this->subject('Your Payment Receipt')
                    ->view('emails.receipt')
                    ->with([
                        'receiptURL' => $this->receiptURL,
                    ]);
    }


}
