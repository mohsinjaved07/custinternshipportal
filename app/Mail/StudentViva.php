<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class StudentViva extends Mailable
{
    use Queueable, SerializesModels;
    public $student, $evaluator, $term;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($student, $evaluator, $term)
    {
        $this->student = $student;
        $this->evaluator = $evaluator;
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
        $evaluator = $this->evaluator;
        $term = $this->term;
        return $this->markdown('Email.studentviva', compact('student', 'evaluator', 'term'));
    }
}
