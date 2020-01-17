<?php

namespace App\Mail;

use App\Contact;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ContactMe extends Mailable
{
    use Queueable, SerializesModels;

    public $contact;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Contact $contact)
    {
        $this->contact = $contact;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from("mik.vornanen@gmail.com")
                    ->markdown('email.contactme')
                    ->with([
                        'contactName' => $this->contact->name,
                        'contactEmail' => $this->contact->email,
                        'contactContent' => $this->contact->content,
                    ]);
        
    }
}
