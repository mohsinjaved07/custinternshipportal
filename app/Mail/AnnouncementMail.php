<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AnnouncementMail extends Mailable
{
    use Queueable, SerializesModels;

    public $description;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($description)
    {
        $this->description = $description;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $description = $this->description;
        return $this->markdown('Email.announcement', compact('description'));
    }
}
