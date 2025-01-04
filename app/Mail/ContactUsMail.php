<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\ContactUs;

class ContactUsMail extends Mailable
{
    use Queueable, SerializesModels;

    public $contact;

    /**
     * Create a new message instance.
     */
    public function __construct(ContactUs $contact)
    {
        $this->contact = $contact;
    }

    /**
     * Get the message envelope.
     */
    public function build()
    {
        return $this->view('emails.contact-us')
            ->subject('New Enquiry From Contact Us')
            ->from(config('mail.from.address'), config('mail.from.name'));
    }
}
