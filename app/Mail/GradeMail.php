<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class GradeMail extends Mailable
{
    use Queueable, SerializesModels;
    public $root, $grade, $description;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($root, $grade, $description)
    {
        $this->root = $root;
        $this->grade = $grade;
        $this->description = $description;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $root = $this->root;
        $description = $this->description;
        $grade = $this->grade;
        return $this->markdown('Email.grade', compact('root', 'description', 'grade'));
    }
}
