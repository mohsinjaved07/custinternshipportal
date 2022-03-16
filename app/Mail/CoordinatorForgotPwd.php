<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Crypt;

class CoordinatorForgotPwd extends Mailable
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
        $name = Crypt::encryptString($coordinator->name);
        return $this->markdown('Email.coordinatorforgotpassword', compact('coordinator', 'name'));
    }
}
