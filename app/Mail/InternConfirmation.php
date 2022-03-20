<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InternConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public $link, $internconfirm;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($link, $internconfirm)
    {
        $this->link = $link;
        $this->internconfirm = $internconfirm;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $link = $this->link;
        $student = $this->internconfirm;
        return $this->markdown('Email.internconfirmation', compact('link', 'student'));
    }
}
