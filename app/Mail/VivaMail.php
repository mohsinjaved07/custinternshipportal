<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VivaMail extends Mailable
{
    use Queueable, SerializesModels;
    
    public $student, $link, $coordinator, $term;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($student, $link, $coordinator, $term)
    {
        $this->student = $student;
        $this->link = $link;
        $this->coordinator = $coordinator;
        $this->term = $term;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $student = $this->student;
        $link = $this->link;
        $coordinator = $this->coordinator;
        $term = $this->term;
        return $this->markdown('Email.viva', compact('student', 'link', 'coordinator', 'term'));
    }
}

