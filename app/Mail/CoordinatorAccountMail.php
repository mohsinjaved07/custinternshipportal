<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CoordinatorAccountMail extends Mailable
{
    use Queueable, SerializesModels;

    public $coordinator;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($coordinator)
    {
        $this->coordinator = $coordinator;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $coordinator = $this->coordinator;
        return $this->markdown('Email.CoordinatorAccount', compact('coordinator'));
    }
}
