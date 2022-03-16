<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LetterMail extends Mailable
{
    use Queueable, SerializesModels;

    public $student, $file;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($student, $file)
    {
        $this->student = $student;
        $this->file = $file;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $student = $this->student;
        return $this->markdown('Email.letter', compact('student'))->attach($this->file);
    }
}
