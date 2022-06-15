<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CoordinatorAccountMail extends Mailable
{
    use Queueable, SerializesModels;

    public $coordinator, $password;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($coordinator, $password)
    {
        $this->coordinator = $coordinator;
        $this->password = $password;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $coordinator = $this->coordinator;
        $password = $this->password;
        return $this->markdown('Email.coordinatoraccount', compact('coordinator', 'password'));
    }
}
