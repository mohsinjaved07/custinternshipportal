<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResponseMail extends Mailable
{
    use Queueable, SerializesModels;

    public $student, $message;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($student, $message)
    {
        $this->student = $student;
        $this->message = $message;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $student = $this->student;
        $message = $this->message;
        return $this->markdown('Email.response', compact('student', 'message'));
    }
}
