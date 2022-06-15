<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrientationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $student, $message, $date;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($student, $message, $date)
    {
        $this->student = $student;
        $this->message = $message;
        $this->date = $date;
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
        $date = $this->date;
        return $this->markdown('Email.orientation', compact('student', 'message', 'date'));
    }
}
