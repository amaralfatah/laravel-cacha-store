<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactFormMail extends Mailable
{
    use Queueable, SerializesModels;

    public $name;
    public $email;
    public $message;
    public $store;

    /**
     * Create a new message instance.
     *
     * @param array $data
     * @param \App\Models\Store $store
     * @return void
     */
    public function __construct(array $data, $store)
    {
        $this->name = $data['name'];
        $this->email = $data['email'];
        $this->message = $data['message'];
        $this->store = $store;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('New Contact Form Submission - ' . $this->store->name)
            ->markdown('emails.contact-form');
    }
}
