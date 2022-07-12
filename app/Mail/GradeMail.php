<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class GradeMail extends Mailable
{
    use Queueable, SerializesModels;
    public $root;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($root)
    {
        $this->root = $root;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $root = $this->root;
        return $this->markdown('Email.grade', compact('root'));
    }
}
